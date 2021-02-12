<?php

namespace App\Repositories;

use App\Actions\Transaction\TriggerTransaction;
use App\Components\Currency\CurrencyConverter;
use App\Events\Payment\PaymentWasCreated;
use App\Events\Payment\PaymentWasUpdated;
use App\Models\Account;
use App\Models\Payment;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Requests\SearchRequest;
use App\Search\PaymentSearch;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    /**
     * PaymentRepository constructor.
     * @param Payment $payment
     */
    public function __construct(Payment $payment)
    {
        parent::__construct($payment);
        $this->model = $payment;
    }

    /**
     *  Return the payment
     * @param int $id
     * @return Payment
     */
    public function findPaymentById(int $id): Payment
    {
        return $this->findOneOrFail($id);
    }

    /**
     * @param SearchRequest $search_request
     * @param Account $account
     * @return array|LengthAwarePaginator
     */
    public function getAll(SearchRequest $search_request, Account $account)
    {
        return (new PaymentSearch($this))->filter($search_request, $account);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function deletePayment()
    {
        return $this->delete();
    }

    public function getModel()
    {
        return $this->model;
    }

    public function createPayment(Payment $payment, array $data, $create_transaction = false): Payment
    {
        if (!empty($data)) {
            $payment->fill($data);
        }

        $payment = $this->convertCurrencies($payment);

        $payment->setNumber();
        $payment->setStatus(empty($data['status_id']) ? Payment::STATUS_COMPLETED : $data['status_id']);
        $payment->save();

        if ($create_transaction) {
            (new TriggerTransaction($payment))->execute(
                $payment->amount * -1,
                $payment->customer->balance,
                "New Payment {$payment->number}"
            );
        }

        event(new PaymentWasCreated($payment));

        return $payment->fresh();
    }

    public function updatePayment(Payment $payment, array $data)
    {
        if (!empty($data)) {
            $payment->fill($data);
        }

        $payment->setStatus(empty($data['status_id']) ? Payment::STATUS_COMPLETED : $data['status_id']);
        $payment->save();

        event(new PaymentWasUpdated($payment));

        return $payment->fresh();
    }


    /**
     * @param array $data
     * @param Payment $payment
     * @param bool $create_transaction
     * @return Payment|null
     */
    public function save(array $data, Payment $payment, $create_transaction = false): ?Payment
    {
        if(!empty($payment->id)) {
            return $this->updatePayment($payment, $data);
        }

        return $this->createPayment($payment, $data, $create_transaction);
    }

    /**
     * @param Payment $payment
     * @return Payment
     */
    private function convertCurrencies(Payment $payment)
    {
        $converted_amount = $objCurrencyConverter = (new CurrencyConverter())
            ->setAmount($payment->amount)
            ->setBaseCurrency($payment->account->getCurrency())
            ->setExchangeCurrency($payment->customer->currency)
            ->setDate($payment->date)
            ->calculate();

        if ($converted_amount) {
            $payment->exchange_rate = $converted_amount;
            $payment->currency_id = $payment->account->getCurrency()->id;
            $payment->exchange_currency_id = $payment->customer->currency;
        }

        return $payment;
    }
}

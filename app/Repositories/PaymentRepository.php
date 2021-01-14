<?php

namespace App\Repositories;

use App\Components\Currency\CurrencyConverter;
use App\Events\Payment\PaymentWasCreated;
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


    /**
     * @param array $data
     * @param Payment $payment
     * @param bool $create_transaction
     * @return Payment|null
     */
    public function save(array $data, Payment $payment, $create_transaction = false): ?Payment
    {
        $send_event = false;

        if (!empty($data)) {
            $payment->fill($data);
        }

        if (!$payment->id) {
            $payment = $this->convertCurrencies($payment);
            $send_event = true;
        }

        $payment->setNumber();
        $payment->setStatus(empty($data['status_id']) ? Payment::STATUS_COMPLETED : $data['status_id']);
        $payment->save();

        if ($create_transaction) {
            $payment->transaction_service()->createTransaction($payment->amount * -1, $payment->customer->balance);
        }

        if ($send_event) {
            event(new PaymentWasCreated($payment));
        }

        return $payment->fresh();
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

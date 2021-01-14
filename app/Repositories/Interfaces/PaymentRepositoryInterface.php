<?php

namespace App\Repositories\Interfaces;

use App\Models\Account;
use App\Models\Payment;
use App\Repositories\Base\BaseRepositoryInterface;
use App\Requests\SearchRequest;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{

    /**
     * @param int $id
     * @return Payment
     */
    public function findPaymentById(int $id): Payment;

    /**
     * @param SearchRequest $search_request
     * @param Account $account
     * @return mixed
     */
    public function getAll(SearchRequest $search_request, Account $account);

    public function deletePayment();

    /**
     * @param array $request
     * @param Payment $payment
     * @return Payment|null
     */
    public function save(array $request, Payment $payment): ?Payment;
}

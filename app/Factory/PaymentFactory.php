<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Carbon;

class PaymentFactory
{
    /**
     * @param Customer $customer
     * @param User $user
     * @param Account $account
     * @return Payment
     */
    public static function create(Customer $customer, User $user, Account $account): Payment
    {
        $payment = new Payment;

        $payment->user_id = $user->id;
        $payment->customer_id = $customer->id;
        $payment->account_id = $account->id;
        $payment->date = Carbon::now()->format('Y-m-d');
        $payment->is_deleted = false;
        $payment->status_id = Payment::STATUS_PENDING;

        return $payment;
    }
}

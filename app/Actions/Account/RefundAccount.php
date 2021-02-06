<?php

namespace App\Actions\Account;


use App\Models\Account;
use Carbon\Carbon;

class RefundAccount
{

    private Account $account;

    /**
     * ConvertLead constructor.
     * @param Account $account
     */
    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function execute()
    {
        $start_date = Carbon::createFromFormat('d-m-Y', '1-5-2015');
        $end_date = Carbon::now();
        $different_days = $start_date->diffInDays($end_date);
        $daily_rate = 25;
        $should_pay = $different_days * $daily_rate;
        $total -= $should_pay;
    }
}
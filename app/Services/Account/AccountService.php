<?php

namespace App\Services\Account;

use App\Models\Account;
use App\Services\ServiceBase;
use Carbon\Carbon;

/**
 * Class TaskService
 * @package App\Services\Task
 */
class AccountService extends ServiceBase
{
    /**
     * @var Account
     */
    protected Account $account;

    /**
     * LeadService constructor.
     * @param Account $account
     */
    public function __construct(Account $account)
    {
        parent::__construct($account);
        $this->account = $account;
    }

    /**
     * @return Account|null
     */
    public function convertAccount(): ?Account
    {
        $account = (new ConvertAccount($this->account))->execute();

        return $account;
    }

    public function refund()
    {
        $start_date = Carbon::createFromFormat('d-m-Y', '1-5-2015');
        $end_date = Carbon::now();
        $different_days = $start_date->diffInDays($end_date);
        $daily_rate = 25;
        $should_pay = $different_days * $daily_rate;
        $total -= $should_pay;
    }

}

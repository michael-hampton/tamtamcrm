<?php

namespace App\Actions\Account;

use App\Factory\AccountFactory;
use App\Factory\UserFactory;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Domain;
use App\Models\Plan;
use App\Models\User;
use App\Notifications\Account\NewAccount;
use App\Repositories\AccountRepository;
use App\Repositories\DomainRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;

class AttachPlanToDomain
{
    use WithFaker;

    public function execute(Domain $domain): Domain
    {
        //Standard Monthly by default
        $plan = Plan::where('code', '=', 'STDM')->first();

        // create plan
        $customer = Customer::find($domain->customer_id);

        $account = $domain->default_company;

        $customer->newSubscription('main', $plan, $account);

        $subscription = $customer->subscriptions()->first();

        $domain->plan_id = $subscription->plan_id;
        $domain->save();


        return $domain;
    }
}

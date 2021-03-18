<?php

namespace App\Actions\Plan;


use App\Models\Domain;
use App\Models\Plan;
use App\Repositories\PlanRepository;

class CreatePlan
{

    /**
     * @param Domain $domain
     * @param array $data
     * @return Plan
     */
    public function execute(Domain $domain, array $data): Plan
    {
        $data['domain_id'] = $domain->id;
        $data['customer_id'] = $domain->customer_id;
        $data['user_id'] = $domain->user_id;
        $data['is_active'] = true;
        $data['plan_started'] = now();
        $data['due_date'] = $data['plan_period'] === 'MONTHLY' || $data['plan'] === 'TRIAL' ? now()->addMonthNoOverflow(
        )
            : now()->addYearNoOverflow();

        if ($data['plan'] === 'TRIAL') {
            $data['number_of_licences'] = 99999;
            $data['expiry_date'] = now()->addMonthNoOverflow();
        } else {
            if (empty($data['number_of_licences'])) {
                $data['number_of_licences'] = $data['plan'] === 'STANDARD'
                    ? env('STANDARD_NUMBER_OF_LICENCES')
                    : env(
                        'ADVANCED_NUMBER_OF_LICENCES'
                    );
            }

            $data['expiry_date'] = now()->addYearNoOverflow();
        }

        if($data['plan'] === 'STANDARD') {
            $data['price_paid'] = $data['plan_period'] === 'MONTHLY' ? env('STANDARD_MONTHLY_ACCOUNT_PRICE') : env('STANDARD_YEARLY_ACCOUNT_PRICE');
        } else {
            $data['price_paid'] = $data['plan_period'] === 'MONTHLY' ? env('ADVANCED_MONTHLY_ACCOUNT_PRICE') : env('ADVANCED_YEARLY_ACCOUNT_PRICE');
        }

        return (new PlanRepository(new Plan()))->create($data);
    }
}
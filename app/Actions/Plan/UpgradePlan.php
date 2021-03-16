<?php

namespace App\Actions\Plan;


use App\Models\Domain;
use App\Models\Plan;
use App\Repositories\PlanRepository;

class UpgradePlan
{

    /**
     * @param Domain $domain
     * @param array $data
     * @return Plan
     */
    public function execute(Domain $domain, array $data = []): Plan
    {
        // complete trial
        $plan = $domain->plans()->where('is_active', '=', 1)->first();
        $plan->plan_ended = now();
        $plan->is_active = false;
        $plan->save();

        //TODO What happens if finished early

        // create new plan
        $plan = (new CreatePlan())->execute(
            $domain,
            [
                'plan_period' => !empty($data['plan_period']) ? $data['plan_period'] : Plan::PLAN_PERIOD_MONTH,
                'plan'        => !empty($data['plan']) ? $data['plan'] : Plan::PLAN_STANDARD
            ]
        );

        return $plan;
    }
}
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Repositories;

use App\Models\Lead;
use App\Models\Plan;
use App\Models\PlanSubscription;
use App\Repositories\Base\BaseRepository;

/**
 * Class PlanRepository
 * @package App\Repositories
 */
class PlanSubscriptionRepository extends BaseRepository
{

    /**
     * PlanSubscriptionRepository constructor.
     * @param PlanSubscription $plan_subscription
     */
    public function __construct(PlanSubscription $plan_subscription)
    {
        parent::__construct($plan_subscription);
        $this->model = $plan_subscription;
    }

    /**
     * @param array $data
     * @return Plan|null
     */
    public function create(array $data): ?PlanSubscription
    {
        $plan = PlanSubscription::create($data);

        return $plan;
    }

    /**
     * @param Lead $lead
     * @param array $data
     * @return Lead|null
     */
    public function update(array $data, PlanSubscription $plan): ?PlanSubscription
    {
        $plan->update($data);

        return $plan;
    }

    public function getPlans()
    {
        return $this->model->all();
    }

    public function getModel()
    {
        return $this->model;
    }

}

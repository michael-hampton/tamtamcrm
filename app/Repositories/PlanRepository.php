<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Repositories;

use App\Events\Plan\PlanWasCreated;
use App\Models\Lead;
use App\Models\Plan;
use App\Repositories\Base\BaseRepository;

/**
 * Class PlanRepository
 * @package App\Repositories
 */
class PlanRepository extends BaseRepository
{

    /**
     * PlanRepository constructor.
     * @param Plan $plan
     */
    public function __construct(Plan $plan)
    {
        parent::__construct($plan);
        $this->model = $plan;
    }

    /**
     * @param array $data
     * @return Plan|null
     */
    public function create(array $data): ?Plan
    {
        $plan = Plan::create($data);

        event(new PlanWasCreated($plan));

        return $plan;
    }

    /**
     * @param Lead $lead
     * @param array $data
     * @return Lead|null
     */
    public function update(array $data, Plan $plan): ?Plan
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

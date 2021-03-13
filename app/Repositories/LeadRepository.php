<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Repositories;

use App\Events\Lead\LeadWasCreated;
use App\Events\Lead\LeadWasUpdated;
use App\Models\Lead;
use App\Repositories\Base\BaseRepository;

/**
 * Description of MessageRepository
 *
 * @author michael.hampton
 */
class LeadRepository extends BaseRepository
{

    /**
     * MessageRepository constructor.
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        parent::__construct($lead);
        $this->model = $lead;
    }

    /**
     * @param Lead $lead
     * @param array $data
     * @return Lead|null
     */
    public function create(array $data, Lead $lead): ?Lead
    {
        $lead->fill($data);
        $lead->setNumber();
        $lead->save();

        event(new LeadWasCreated($lead));

        return $lead;
    }

    /**
     * @param Lead $lead
     * @param array $data
     * @return Lead|null
     */
    public function update(array $data, Lead $lead): ?Lead
    {
        $lead->update($data);

        event(new LeadWasUpdated($lead));

        return $lead;
    }

    public function getLeads()
    {
        return $this->model->all();
    }

    /**
     * @param int $id
     * @return Lead
     */
    public function findLeadById(int $id): Lead
    {
        return $this->findOneOrFail($id);
    }

    public function getModel()
    {
        return $this->model;
    }

}

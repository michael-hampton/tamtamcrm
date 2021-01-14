<?php

namespace App\Transformations;

use App\Models\Event;

trait EventTransformable
{

    /**
     * @param Event $event
     * @return array
     */
    protected function transformEvent(Event $event)
    {
        $customer = $event->customer;

        return [
            'id'          => (int)$event->id,
            'location'    => $event->location,
            'customer_id' => $customer->id,
            'name'        => $customer->name,
            'title'       => $event->title,
            'beginDate'   => date("D M d Y H:i:s", strtotime($event->beginDate)),
            'endDate'     => date("D M d Y H:i:s", strtotime($event->endDate)),
            'attendees'   => $event->users,
            'event_type'  => $event->event_type,
            'description' => $event->description,
            'status'      => isset($event->status) ? $event->status : null,
            'owner'       => $event->createdBy,
            'deleted_at'  => $event->deleted_at,
            'is_deleted'  => (bool)$event->is_deleted,

        ];
    }

}

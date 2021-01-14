<?php

namespace App\Transformations;


use App\Models\Subscription;

trait SubscriptionTransformable
{

    /**
     * @param Subscription $subscription
     * @return array
     */
    public function transform(Subscription $subscription)
    {
        return [
            'id'         => (int)$subscription->id,
            'account_id' => (int)$subscription->account_id,
            'user_id'    => (int)$subscription->user_id,
            'updated_at' => $subscription->updated_at,
            'created_at' => $subscription->created_at,
            'is_deleted' => (bool)$subscription->is_deleted,
            'target_url' => $subscription->target_url ? (string)$subscription->target_url : '',
            'event_id'   => (int)$subscription->event_id,
            'format'     => (string)$subscription->format,
            'name'       => (string)$subscription->name,
        ];
    }
}

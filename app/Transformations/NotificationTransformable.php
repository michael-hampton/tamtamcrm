<?php

namespace App\Transformations;

use App\Models\Notification;

trait NotificationTransformable
{

    /**
     * @param Notification $notification
     * @return array
     */
    protected function transformNotification(Notification $notification)
    {
        $user = $notification->user;

        return [
            'id'         => (int)$notification->id,
            'type'       => $notification->type,
            'action'     => $notification->action,
            'entity'     => class_basename($notification->notifiable_type),
            'user_id'    => $notification->notifiable_id,
            'author'     => $user->first_name . ' ' . $user->last_name,
            'data'       => json_decode($notification->data, true),
            'created_at' => $notification->created_at
        ];
    }

}

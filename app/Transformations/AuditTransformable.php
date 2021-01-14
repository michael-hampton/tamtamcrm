<?php

namespace App\Transformations;

use App\Models\Audit;
use App\Models\Notification;

class AuditTransformable
{
    use NotificationTransformable;

    /**
     * @param Audit $audit
     * @return array
     */
    public function transformAudit(Audit $audit)
    {
        $notification = Notification::where('id', '=', $audit->notification_id)->first();

        return [
            'id'              => (int)$audit->id,
            'notification_id' => (int)$audit->notification_id,
            'notification'    => $this->transformNotification($notification),
            'data'            => $audit->data,
            'updated_at'      => $audit->updated_at,
            'created_at'      => $audit->created_at,
        ];
    }
}

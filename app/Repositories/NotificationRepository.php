<?php

namespace App\Repositories;

use App\Models\Audit;
use App\Models\Notification;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use Illuminate\Support\Collection;

class NotificationRepository extends BaseRepository implements NotificationRepositoryInterface
{
    /**
     * NotificationRepository constructor.
     *
     * @param Notification $notification
     */
    public function __construct(Notification $notification)
    {
        parent::__construct($notification);
        $this->model = $notification;
    }

    /**
     * @param array $columns
     * @param string $orderBy
     * @param string $sortBy
     *
     * @return Collection
     */
    public function listNotifications($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc'): Collection
    {
        return $this->all($columns, $orderBy, $sortBy);
    }

    /**
     * @param array $data
     * @return Notification
     */
    public function create(array $data): Notification
    {
        $test = $this->create($data);
    }

    /**
     * @param Notification $notification
     * @param array $data
     * @return bool
     */
    public function save(Notification $notification, array $data)
    {
        $notification->fill($data);

        $response = $notification->save();

        if (!$response) {
            return false;
        }

        $this->audit($notification);

        return true;
    }

    /**
     * @param Notification $notification
     * @return bool
     */
    private function audit(Notification $notification)
    {
        $entity_class = $notification->notifiable_type;

        if ($entity_class === 'App\Models\User') {
            $entity = $entity_class::withTrashed()->find($notification->entity_id)->first();
        } elseif (in_array(
            $entity_class,
            ['App\Models\Company', 'App\Models\Customer', 'App\Models\Lead', 'App\Models\PurchaseOrder']
        )) {
            $entity = $entity_class::withTrashed()->find($notification->entity_id)->with('account')->first();
        } else {
            $entity = $entity_class::withTrashed()->where('id', '=', $notification->entity_id)->with(
                'customer',
                'account'
            )->first();
        }

        if (!$entity) {
            return true;
        }

        Audit::create(
            [
                'data'            => $entity,
                'entity_class'    => $notification->notifiable_type,
                'entity_id'       => $entity->id,
                'notification_id' => $notification->id
            ]
        );

        return true;
    }
}

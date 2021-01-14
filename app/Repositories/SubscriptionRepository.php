<?php

namespace App\Repositories;

use App\Models\Account;
use App\Models\Subscription;
use App\Repositories\Base\BaseRepository;

class SubscriptionRepository extends BaseRepository
{
    /**
     * SubscriptionRepository constructor.
     * @param Subscription $subscription
     */
    public function __construct(Subscription $subscription)
    {
        parent::__construct($subscription);
        $this->model = $subscription;
    }

    /**
     * Gets the class name.
     *
     * @return     string The class name.
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param int $id
     * @return Subscription
     */
    public function findSubscriptionById(int $id): Subscription
    {
        return $this->findOneOrFail($id);
    }

    public function findSubscriptionByEvent(int $event_id, Account $account)
    {
        return $this->model->where('event_id', '=', $event_id)->where('account_id', '=', $account->id)->first();
    }

    /**
     * @param array $data
     * @param Subscription $subscription
     * @return Subscription|null
     */
    public function save(array $data, Subscription $subscription): ?Subscription
    {
        $subscription->fill($data);

        $subscription->save();

        return $subscription;
    }
}

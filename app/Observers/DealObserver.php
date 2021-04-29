<?php


namespace App\Observers;


use App\Models\Deal;
use App\Models\TaskStatus;

class DealObserver
{
    /**
     * @param Deal $deal
     */
    public function creating(Deal $deal)
    {
        if (empty($deal->task_status_id)) {
            $task_status = TaskStatus::ByTaskType(2)->orderBy('order_id', 'asc')->first();
            $deal->task_status_id = $task_status->id;
        }

        if (is_null($deal->order_id)) {
            $deal->order_id = Deal::max('order_id') + 1;
            return;
        }

        $lowerPriorityDeals = Deal::where('order_id', '>=', $deal->order_id)->get();

        foreach ($lowerPriorityDeals as $lowerPriorityDeal) {
            $lowerPriorityDeal->order_id++;
            $lowerPriorityDeal->saveQuietly();
        }
    }

    /**
     * @param Deal $deal
     */
    public function updating(Deal $deal)
    {
        if ($deal->isClean('order_id')) {
            return;
        }

        if (is_null($deal->order_id)) {
            $deal->order_id = Deal::max('order_id');
        }

        if ($deal->getOriginal('order_id') > $deal->order_id) {
            $positionRange = [
                $deal->order_id,
                $deal->getOriginal('order_id')
            ];
        } else {
            $positionRange = [
                $deal->getOriginal('order_id'),
                $deal->order_id
            ];
        }

        $lowerPriorityDeals = Deal::where('id', '!=', $deal->id)
                                  ->whereBetween('order_id', $positionRange)
                                  ->get();

        foreach ($lowerPriorityDeals as $lowerPriorityDeal) {
            if ($deal->getOriginal('order_id') < $deal->order_id) {
                $lowerPriorityDeal->order_id--;
            } else {
                $lowerPriorityDeal->order_id++;
            }
            $lowerPriorityDeal->saveQuietly();
        }
    }

    /**
     * @param Deal $deal
     */
    public function deleted(Deal $deal)
    {
        $lowerPriorityDeals = Deal::where('order_id', '>', $deal->order_id)->get();

        foreach ($lowerPriorityDeals as $lowerPriorityDeal) {
            $lowerPriorityDeal->order_id--;
            $lowerPriorityDeal->saveQuietly();
        }
    }
}
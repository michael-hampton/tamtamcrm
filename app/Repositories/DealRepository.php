<?php

namespace App\Repositories;

use App\Events\Deal\DealWasCreated;
use App\Events\Deal\DealWasUpdated;
use App\Models\Account;
use App\Models\Deal;
use App\Models\User;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\DealRepositoryInterface;
use App\Requests\SearchRequest;
use App\Search\DealSearch;
use Carbon\Carbon;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as Support;
use Illuminate\Support\Facades\DB;

class DealRepository extends BaseRepository implements DealRepositoryInterface
{


    /**
     * DealRepository constructor.
     *
     * @param Deal $deal
     */
    public function __construct(Deal $deal)
    {
        parent::__construct($deal);
        $this->model = $deal;
    }

    /**
     * @param int $id
     *
     * @return Deal
     */
    public function findDealById(int $id): Deal
    {
        return $this->findOneOrFail($id);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function deleteDeal(): bool
    {
        return $this->delete();
    }

    /**
     * @param SearchRequest $search_request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function getAll(SearchRequest $search_request, Account $account)
    {
        return (new DealSearch($this))->filter($search_request, $account);
    }

    public function getDeals($limit = null, User $objUser = null): Support
    {
        $query = $this->model->orderBy('deals.created_at', 'desc');

        if ($objUser !== null) {
            $query->where('assigned_to', '=', $objUser->id);
        }

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function getModel()
    {
        return $this->model;
    }


    /**
     * @param int $task_type
     * @param $account_id
     * @return Support
     */
    public function getSourceTypeCounts(int $task_type, $account_id): Support
    {
        return $this->model->join('source_type', 'source_type.id', '=', 'deals.source_type')
                           ->select('source_type.name', DB::raw('count(*) as value'))
                           ->where('deals.account_id', $account_id)->groupBy('source_type.name')->get();
    }

    /**
     * @param int $task_type
     * @param int $account_id
     * @return Support
     */
    public function getStatusCounts(int $task_type, int $account_id): Support
    {
        return $this->model->join('task_statuses', 'task_statuses.id', '=', 'tasks.task_status_id')
                           ->select(
                               'task_statuses.name',
                               DB::raw('CEILING(count(*) * 100 / (select count(*) from tasks)) as value')
                           )
                           ->where('tasks.task_type', $task_type)->where('tasks.account_id', $account_id)
                           ->groupBy('task_statuses.name')->get();
    }

    /**
     * @param int $task_type
     * @param int $number_of_days
     * @param int $account_id
     * @return int
     */
    public function getRecentTasks(int $task_type, int $number_of_days, int $account_id)
    {
        $date = Carbon::today()->subDays($number_of_days);
        $result = $this->model->select(DB::raw('count(*) as total'))->where('created_at', '>=', $date)
                              ->where('account_id', $account_id)->get();

        return !empty($result[0]) ? $result[0]['total'] : 0;
    }

    /**
     * @param int $account_id
     * @return int
     */
    public function getNewDeals(int $account_id)
    {
        $result = $this->model->select(DB::raw('count(*) as total'))
                              ->where('account_id', $account_id)->get();

        return !empty($result[0]) ? $result[0]['total'] : 0;
    }

    /**
     *
     * @param int $account_id
     * @return type
     */
    public function getTotalEarnt(int $account_id)
    {
        return $this->model->where('account_id', $account_id)->sum('valued_at');
    }

    /**
     * @param array $data
     * @param Deal $deal
     * @return Deal|Task|null
     * @throws Exception
     */
    public function createDeal(array $data, Deal $deal): ?Deal
    {
        $data['source_type'] = empty($data['source_type']) ? 1 : $data['source_type'];
        $deal = $this->save($data, $deal);

        event(new DealWasCreated($deal));

        return $deal;
    }

    /**
     * @param $data
     * @param Deal $deal
     * @return Deal|null
     */
    public function save($data, Deal $deal): ?Deal
    {
        $deal->fill($data);
        $deal->setNumber();
        $deal->save();

        return $deal->fresh();
    }

    /**
     * @param array $data
     * @param Deal $deal
     * @return Deal|Task|null
     * @throws Exception
     */
    public function updateDeal(array $data, Deal $deal): ?Deal
    {
        $deal = $this->save($data, $deal);

        event(new DealWasUpdated($deal));

        return $deal->fresh();
    }

}

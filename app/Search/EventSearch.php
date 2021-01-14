<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Event;
use App\Repositories\EventRepository;
use App\Requests\SearchRequest;
use App\Transformations\EventTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

class EventSearch extends BaseSearch
{
    use EventTransformable;

    private $eventRepository;

    private Event $model;

    /**
     * CompanySearch constructor.
     * @param EventRepository $eventRepository
     */
    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->model = $eventRepository->getModel();
    }

    /**
     * @param SearchRequest $request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function filter(SearchRequest $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'title' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query = $this->model->select('*');

        if ($request->has('search_term') && !empty($request->search_term)) {
            $this->searchFilter($request->search_term);
        }

        if ($request->has('status_id')) {
            $this->status('events', $request->status_id);
        }

        $this->addAccount($account->id);

        $this->orderBy($orderBy, $orderDir);

        $events = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->eventRepository->paginateArrayResults($events, $recordsPerPage);
            return $paginatedResults;
        }

        return $events;
    }

    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where(
            function ($query) use ($filter) {
                $query->where('location', 'like', '%' . $filter . '%')->orWhere('title', 'like', '%' . $filter . '%')
                      ->orWhere('description', 'like', '%' . $filter . '%');
            }
        );

        return true;
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();
        $events = $list->map(
            function (Event $event) {
                return $this->transformEvent($event);
            }
        )->all();

        return $events;
    }

    /**
     * @param $filters
     * @param int $account_id
     * @return mixed
     */
    public function filterBySearchCriteria($filters, int $account_id)
    {
        $this->query = $this->model->select('events.*')->join('event_user', 'event_user.user_id', '=', 'events.id');
        foreach ($filters as $column => $value) {
            if (empty($value)) {
                continue;
            }

            if ($column === 'status_id') {
                $this->status($value);
                continue;
            }
            $this->query->where($column, '=', $value);
        }

        $this->addAccount($account_id);

        return $this->transformList();
    }

}

<?php


namespace App\Repositories;


use App\Actions\Email\DispatchEmail;
use App\Events\Cases\CaseWasCreated;
use App\Events\Cases\CaseWasUpdated;
use App\Events\Cases\RecurringQuoteWasUpdated;
use App\Factory\CommentFactory;
use App\Models\Account;
use App\Models\Cases;
use App\Models\CaseTemplate;
use App\Models\User;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\CaseRepositoryInterface;
use App\Requests\SearchRequest;
use App\Search\CaseSearch;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class CaseRepository extends BaseRepository implements CaseRepositoryInterface
{
    /**
     * CaseRepository constructor.
     * @param Cases $case
     */
    public function __construct(Cases $case)
    {
        parent::__construct($case);
        $this->model = $case;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param int $id
     * @return Cases
     */
    public function findCaseById(int $id): Cases
    {
        return $this->findOneOrFail($id);
    }

    /**
     * @param SearchRequest $search_request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function getAll(SearchRequest $search_request, Account $account)
    {
        return (new CaseSearch($this))->filter($search_request, $account);
    }

    public function createCase(array $data, Cases $case): ?Cases
    {
        $case = $this->save($data, $case);

        event(new CaseWasCreated($case));

        return $case;
    }

    /**
     * @param array $data
     * @param Cases $case
     * @return Cases|null
     * @return Cases|null
     */
    public function save(array $data, Cases $case): ?Cases
    {
        $case->fill($data);
        $case->setNumber();
        $case->save();

        $this->saveInvitations($case, $data);

        return $case;
    }

    /**
     * @param array $data
     * @param Cases $case
     * @param User $user
     * @return Cases|null
     */
    public function updateCase(array $data, Cases $case, User $user): ?Cases
    {
        $case = $this->save($data, $case);

        event(new CaseWasUpdated($case));

        return $case;
    }

    public function getOverdueCases()
    {
        return Cases::whereDate('due_date', '>=', Carbon::today())
                    ->where('hide', '=', false)
                    ->where('overdue_email_sent', 0)
                    ->whereIn(
                        'status_id',
                        [Cases::STATUS_OPEN]
                    )->get();
    }
}

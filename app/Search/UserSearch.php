<?php

namespace App\Search;

use App\Models\Account;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Requests\SearchRequest;
use App\Transformations\UserTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

class UserSearch extends BaseSearch
{
    use UserTransformable;

    private UserRepository $userRepository;

    private User $model;

    /**
     * UserSearch constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->model = $userRepository->getModel();
    }

    /**
     * @param SearchRequest $request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function filter(SearchRequest $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column || $request->column === 'name' ? 'first_name' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query =
            $this->model->select('users.*')->leftJoin('department_user', 'users.id', '=', 'department_user.user_id')
                        ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id');

        if ($request->has('status')) {
            $this->status('users', $request->status);
        }

        if ($request->filled('role_id')) {
            $this->query->where('role_id', '=', $request->role_id);
        }

        if ($request->filled('department_id')) {
            $this->query->where('department_user.department_id', '=', $request->department_id);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->orderBy($orderBy, $orderDir);

        $this->query->whereHas(
            'account_users',
            function ($query) use ($account) {
                $query->where('account_id', '=', $account->id);
            }
        );

        if ($request->filled('search_term')) {
            $this->searchFilter($request->search_term);
        }

        $this->query->groupBy('users.id');

        $users = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->userRepository->paginateArrayResults($users, $recordsPerPage);
            return $paginatedResults;
        }

        return $users;
    }

    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where(
            function ($query) use ($filter) {
                $query->where('users.first_name', 'like', '%' . $filter . '%')
                      ->orWhere('users.last_name', 'like', '%' . $filter . '%')
                      ->orWhere('users.email', 'like', '%' . $filter . '%');
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
        $users = $list->map(
            function (User $user) {
                return $this->transformUser($user);
            }
        )->all();

        return $users;
    }
}

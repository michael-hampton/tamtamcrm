<?php

namespace App\Search;

use App\Models\Account;
use App\Models\CaseTemplate;
use App\Repositories\CaseTemplateRepository;
use App\Requests\SearchRequest;
use App\Transformations\CaseTemplateTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class CaseTemplateSearch
 * @package App\Search
 */
class CaseTemplateSearch extends BaseSearch
{
    use CaseTemplateTransformable;

    /**
     * @var CaseTemplateRepository
     */
    private CaseTemplateRepository $brand_repo;

    private CaseTemplate $model;

    /**
     * BrandSearch constructor.
     * @param CaseTemplateRepository $template_repo
     */
    public function __construct(CaseTemplateRepository $template_repo)
    {
        $this->brand_repo = $template_repo;
        $this->model = $template_repo->getModel();
    }

    /**
     * @param SearchRequest $request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function filter(SearchRequest $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'created_at' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query = $this->model->select('case_templates.*');

        if ($request->has('status')) {
            $this->status('case_templates', $request->status);
        }

        if ($request->has('search_term') && !empty($request->search_term)) {
            $this->searchFilter($request->search_term);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->orderBy($orderBy, $orderDir);

        $templates = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->brand_repo->paginateArrayResults($templates, $recordsPerPage);
            return $paginatedResults;
        }

        return $templates;
    }

    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where('case_templates.name', 'like', '%' . $filter . '%');

        return true;
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();
        $templates = $list->map(
            function (CaseTemplate $template) {
                return $this->transformCaseTemplate($template);
            }
        )->all();

        return $templates;
    }
}

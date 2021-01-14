<?php

namespace App\Http\Controllers;

use App\Factory\CaseCategoryFactory;
use App\Models\CompanyToken;
use App\Repositories\CaseCategoryRepository;
use App\Requests\CaseCategory\CreateCategoryRequest;
use App\Requests\CaseCategory\UpdateCategoryRequest;
use App\Requests\SearchRequest;
use App\Search\CaseCategorySearch;
use App\Transformations\CaseCategoryTransformable;
use Illuminate\Http\JsonResponse;

use function request;

class CaseCategoryController extends Controller
{

    use CaseCategoryTransformable;

    /**
     * @var CaseCategoryRepository
     */
    private CaseCategoryRepository $category_repo;

    /**
     * CaseCategoryController constructor.
     * @param CaseCategoryRepository $categoryRepository
     */
    public function __construct(CaseCategoryRepository $categoryRepository)
    {
        $this->category_repo = $categoryRepository;
    }

    /**
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $token_sent = request()->bearerToken();
        $token = CompanyToken::whereToken($token_sent)->first();
        $account = $token->account;

        $categories = (new CaseCategorySearch($this->category_repo))->filter(
            $request,
            $account
        );
        return response()->json($categories);
    }

    /**
     * @param CreateCategoryRequest $request
     * @return JsonResponse
     */
    public function store(CreateCategoryRequest $request)
    {
        $category = $this->category_repo->save(
            $request->all(),
            CaseCategoryFactory::create(auth()->user()->account_user()->account, auth()->user())
        );

        return response()->json($this->transformCategory($category));
    }

    /**
     * @param UpdateCategoryRequest $request
     * @param int $id
     */
    public function update(UpdateCategoryRequest $request, int $id)
    {
        $category = $this->category_repo->findCategoryById($id);
        $update = new CaseCategoryRepository($category);
        $update->save($request->except('_token', '_method'), $category);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id)
    {
        $category = $this->category_repo->findCategoryById($id);
        $category->deleteEntity();
        return response()->json($category);
    }

    public function getRootCategories()
    {
        $categories = $this->category_repo->rootCategories();
        return response()->json($categories);
    }
}

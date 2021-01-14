<?php

namespace App\Http\Controllers;

use App\Factory\ExpenseCategoryFactory;
use App\Repositories\ExpenseCategoryRepository;
use App\Requests\ExpenseCategory\CreateCategoryRequest;
use App\Requests\ExpenseCategory\UpdateCategoryRequest;
use App\Requests\SearchRequest;
use App\Search\ExpenseCategorySearch;
use App\Transformations\ExpenseCategoryTransformable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class ExpenseCategoryController extends Controller
{

    use ExpenseCategoryTransformable;

    /**
     * @var ExpenseCategoryRepository
     */
    private ExpenseCategoryRepository $category_repo;

    /**
     * ExpenseCategoryController constructor.
     * @param ExpenseCategoryRepository $categoryRepository
     */
    public function __construct(ExpenseCategoryRepository $categoryRepository)
    {
        $this->category_repo = $categoryRepository;
    }

    /**
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $categories = (new ExpenseCategorySearch($this->category_repo))->filter(
            $request,
            auth()->user()->account_user()->account
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
            ExpenseCategoryFactory::create(auth()->user()->account_user()->account, auth()->user())
        );

        return response()->json($this->transformCategory($category));
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateCategoryRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateCategoryRequest $request, int $id)
    {
        $category = $this->category_repo->findCategoryById($id);
        $category = $this->category_repo->save($request->all(), $category);
        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     * @throws AuthorizationException
     */
    public function destroy(int $id)
    {
        $category = $this->category_repo->findCategoryById($id);
        $this->authorize('delete', $category);
        $category->deleteEntity();
    }

    public function getRootCategories()
    {
        $categories = $this->category_repo->rootCategories();
        return response()->json($categories);
    }
}

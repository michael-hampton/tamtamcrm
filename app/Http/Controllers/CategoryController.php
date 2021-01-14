<?php

namespace App\Http\Controllers;

use App\Factory\CategoryFactory;
use App\Models\CompanyToken;
use App\Repositories\CategoryRepository;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Requests\Category\CreateCategoryRequest;
use App\Requests\Category\UpdateCategoryRequest;
use App\Requests\SearchRequest;
use App\Search\CategorySearch;
use App\Transformations\CategoryTransformable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    use CategoryTransformable;

    /**
     * @var CategoryRepositoryInterface
     */
    private $category_repo;

    /**
     * CategoryController constructor.
     *
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->category_repo = $categoryRepository;
    }

    /**
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $categories = (new CategorySearch($this->category_repo))->filter(
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
        $category = $this->category_repo->createCategory(
            $request->all(),
            CategoryFactory::create(auth()->user()->account_user()->account, auth()->user())
        );

        return response()->json($this->transformCategory($category));
    }

    /**
     * @param UpdateCategoryRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateCategoryRequest $request, int $id)
    {
        $category = $this->category_repo->findCategoryById($id);
        $update = new CategoryRepository($category);
        $update->updateCategory($request->except('_token', '_method'), $category);
        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     */
    public function destroy(int $id)
    {
        $category = $this->category_repo->findCategoryById($id);
        $category->products()->sync([]);
        $category->deleteEntity();
    }

    /**
     * @param Request $request
     * @return void
     */
    public function removeImage(Request $request)
    {
        $this->category_repo->deleteFile($request->only('category'));
    }

    public function getRootCategories()
    {
        $categories = $this->category_repo->rootCategories();
        return response()->json($categories);
    }

    /**
     * @param string $slug
     * @return JsonResponse
     */
    public function getCategory(string $slug)
    {
        $token_sent = \request()->bearerToken();
        $token = CompanyToken::whereToken($token_sent)->first();
        $account = $token->account;

        $category = $this->category_repo->findCategoryBySlug($slug, $account);
        return response()->json($category);
    }

    /**
     * @param string $slug
     * @return JsonResponse
     */
    public function getChildCategories(string $slug)
    {
        $token_sent = \request()->bearerToken();
        $token = CompanyToken::whereToken($token_sent)->first();
        $account = $token->account;

        $category = $this->category_repo->findCategoryBySlug($slug, $account);
        $categoryRepo = new CategoryRepository($category);
        $categories = $categoryRepo->findChildren();
        return response()->json($categories);
    }

    /**
     * @param int $category_id
     * @return JsonResponse
     */
    public function getForm(int $category_id)
    {
        $category = $this->category_repo->findCategoryById($category_id);
        $form = (new CategoryRepository($category))->getFormForCategory();
        return response()->json($form);
    }
}

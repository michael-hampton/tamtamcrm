<?php

namespace App\Repositories;

use App\Models\Account;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Transformations\ProductTransformable;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{

    use ProductTransformable;

    /**
     * CategoryRepository constructor.
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        parent::__construct($category);
        $this->model = $category;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * List all the categories
     *
     * @param string $order
     * @param string $sort
     * @param Account $account
     * @param array $except
     * @return Collection
     */
    public function listCategories(
        string $order = 'id',
        string $sort = 'desc',
        Account $account,
        $except = []
    ): Collection {
        return $this->model
            ->where('account_id', '=', $account->id)
            ->orderBy($order, $sort)
            ->get()
            ->except($except);
    }

    /**
     * List all root categories
     *
     * @param string $order
     * @param string $sort
     * @param array $except
     * @return Collection
     */
    public function rootCategories(string $order = 'id', string $sort = 'desc', $except = []): Collection
    {
        return $this->model->whereIsRoot()->orderBy($order, $sort)->get()->except($except);
    }

    /**
     * @param array $params
     * @param Category $category
     * @return Category
     */
    public function createCategory(array $params, Category $category): Category
    {
        $collection = collect($params);
        if (isset($params['name'])) {
            $params['slug'] = Str::slug($params['name']);
        }

        if (isset($params['cover']) && ($params['cover'] instanceof UploadedFile)) {
            $params['cover'] = $this->saveCoverImage($params['cover']);
        }

        $category->fill($params);

        if (isset($params['parent']) && !empty($params['parent'])) {
            $parent = $this->findCategoryById($params['parent']);
            $category->parent()->associate($parent);
        }
        $category->save();
        return $category;
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function saveCoverImage(UploadedFile $file): string
    {
        return $file->store('categories', ['disk' => 'public']);
    }

    /**
     * @param int $id
     * @return Category
     */
    public function findCategoryById(int $id): Category
    {
        return $this->findOneOrFail($id);
    }

    /**
     * Update the category
     *
     * @param array $params
     *
     * @param Category $category
     * @return Category
     */
    public function updateCategory(array $params, Category $category): Category
    {
        $collection = collect($params)->except('_token');
        $slug = Str::slug($collection->get('name'));
        $cover = '';

        if (isset($params['cover']) && ($params['cover'] instanceof UploadedFile)) {
            $cover = $this->saveCoverImage($params['cover']);
        }

        $merge = $collection->merge(compact('slug', 'cover'));

        // set parent attribute default value if not set
        $params['parent'] = $params['parent'] ?? 0;

        // If parent category is not set on update
        // just make current category as root
        // else we need to find the parent
        // and associate it as child
        if ((int)$params['parent'] == 0) {
            $category->saveAsRoot();
        } else {
            $parent = $this->findCategoryById($params['parent']);
            $category->parent()->associate($parent);
        }

        $category->update($merge->all());

        return $category;
    }

    /**
     * Delete a category
     *
     * @return bool
     * @throws Exception
     */
    public function deleteCategory(): bool
    {
        return $this->model->delete();
    }

    /**
     * Associate a product in a category
     *
     * @param Product $product
     * @return Model
     */
    public function associateProduct(Product $product)
    {
        return $this->model->products()->save($product);
    }

    /**
     * Return all the products associated with the category
     *
     * @return mixed
     */
    public function findProducts(): Collection
    {
        return $this->model->products;
    }

    /**
     * @param array $params
     */
    public function syncProducts(array $params)
    {
        $this->model->products()->sync($params);
    }

    /**
     * Detach the association of the product
     *
     */
    public function detachProducts()
    {
        $this->model->products()->detach();
    }

    /**
     * @param $file
     * @param null $disk
     * @return bool
     */
    public function deleteFile(array $file, $disk = null): bool
    {
        return $this->update(['cover' => null], $file['category']);
    }

    /**
     * @param string $slug
     * @param Account $account
     * @return Category
     */
    public function findCategoryBySlug(string $slug, Account $account): Category
    {
        return $this->findOneByOrFail(['slug' => $slug, 'account_id' => $account->id]);
    }

    /**
     * @return mixed
     */
    public function findParentCategory()
    {
        return $this->model->parent;
    }

    /**
     * @return mixed
     */
    public function findChildren()
    {
        return $this->model->children;
    }

    public function getFormForCategory()
    {
        return DB::table('form_category')->select('*')->where('category_id', $this->model->id)->get();
    }

}

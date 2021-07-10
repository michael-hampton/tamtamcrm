<?php


namespace App\Components\Import;


use App\Components\Product\CreateProduct;
use App\Factory\BrandFactory;
use App\Factory\CategoryFactory;
use App\Factory\ProductFactory;
use App\Models\Account;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Deal;
use App\Models\Product;
use App\Models\User;
use App\Repositories\BrandRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\DealRepository;
use App\Repositories\ProductRepository;
use App\Requests\SearchRequest;
use App\Search\DealSearch;
use App\Search\ProductSearch;
use App\Transformations\ProductTransformable;

class ProductImporter extends BaseCsvImporter
{
    use ImportMapper;
    use ProductTransformable;

    /**
     * @var string
     */
    protected string $json;

    protected $entity;
    private array $export_columns = [
        'name'          => 'name',
        'description'   => 'description',
        'price'         => 'price',
        'cost'          => 'cost',
        'length'        => 'length',
        'width'         => 'width',
        'height'        => 'height',
        'weight'        => 'weight',
        'quantity'      => 'quantity',
        'sku'           => 'sku',
        'category_id'   => 'category name',
        'brand_id'      => 'brand name',
        'customer_note'  => 'public notes',
        'internal_note' => 'private notes'
    ];
    /**
     * @var array|string[]
     */
    private array $mappings = [
        'name'          => 'name',
        'description'   => 'description',
        'price'         => 'price',
        'cost'          => 'cost',
        'length'        => 'length',
        'width'         => 'width',
        'height'        => 'height',
        'weight'        => 'weight',
        'quantity'      => 'quantity',
        'sku'           => 'sku',
        'category name' => 'category_name',
        'brand name'    => 'brand_id',
        'public notes'  => 'customer_note',
        'private notes' => 'internal_note'
    ];
    /**
     * @var Account
     */
    private Account $account;
    /**
     * @var User
     */
    private User $user;
    /**
     * @var Export
     */
    private Export $export;

    /**
     * InvoiceImporter constructor.
     * @param Account $account
     * @param User $user
     * @throws CsvImporterException
     */
    public function __construct(Account $account, User $user)
    {
        parent::__construct('Product');
        $this->entity = 'Product';

        $this->account = $account;
        $this->user = $user;
        $this->export = new Export($this->account, $this->user);
    }

    /**
     *  Specify mappings and rules for the csv importer, you also may create csv files to write csv entities
     *  and overwrite global configurations
     *
     * @return array
     */
    public function csvConfigurations()
    {
        return [
            'mappings' => [
                'name'          => ['validation' => 'required|unique:products', 'cast' => 'string'],
                'sku'           => ['cast' => 'string'],
                'description'   => ['required', 'cast' => 'string'],
                'price'         => ['validation' => 'required', 'cast' => 'float'],
                'cost'          => ['validation' => 'required', 'cast' => 'float'],
                'category name' => ['cast' => 'string'],
                'brand name'    => ['cast' => 'string'],
                'quantity'      => ['validation' => 'required', 'cast' => 'int'],
                'width'         => ['cast' => 'float'],
                'height'        => ['cast' => 'float'],
                'weight'        => ['cast' => 'float'],
                'length'        => ['cast' => 'float'],
            ],
            'config'   => [
                'csv_date_format' => 'Y-m-d'
            ]
        ];
    }

    /**
     * @param array $params
     * @return Product
     */
    public function factory(array $params): ?Product
    {
        return ProductFactory::create($this->user, $this->account);
    }

    /**
     * @param Product $product
     * @param array $data
     * @return Product|null
     */
    public function saveCallback(Product $product, array $data)
    {
        if (empty($data['category_name'])) {
            return $product;
        }

        $category_id = $this->getCategory($data['category_name']);
        $data['category'] = $category_id;

        $product = (new CreateProduct($this->repository(), $data, $product))->execute();

        return $product->fresh();
    }

    /**
     * @param string $value
     * @return int
     */
    public function getCategory(string $value)
    {
        if (empty($this->categories)) {
            $this->categories = Category::where('account_id', $this->account->id)->where(
                'hide',
                false
            )->get()->keyBy('name')->toArray();

            $this->categories = array_change_key_case($this->categories, CASE_LOWER);
        }

        if (empty($this->categories)) {
            return null;
        }

        if (empty($this->categories[strtolower($value)])) {
            $category = (new CategoryFactory())->create($this->account, $this->user);
            $category = (new CategoryRepository(new Category()))->createCategory(
                ['name' => $value],
                $category
            );
            return $category->id;
        }

        $category = $this->categories[strtolower($value)];

        return $category['id'];
    }

    /**
     * @return ProductRepository
     */
    public function repository(): ProductRepository
    {
        return new ProductRepository(new Product());
    }

    /**
     * @param int $id
     * @return int
     */
    public function getCategoryById(int $id)
    {
        if (empty($this->categories)) {
            $this->categories = Category::where('account_id', $this->account->id)->where(
                'hide',
                false
            )->get()->keyBy('id');
        }

        if (empty($this->categories) || empty($this->categories[$id])) {
            return null;
        }

        return $this->categories[$id];
    }

    public function export($is_json = false)
    {
        $export_columns = $this->getExportColumns();

        $search_request = new SearchRequest();
        $search_request->replace(['column' => 'created_at', 'order' => 'desc']);

        $products = (new ProductSearch(new ProductRepository(new Product())))->filter($search_request, $this->account);

        foreach ($products as $key => $product) {
            $products[$key]['category_id'] = implode(' | ', $product['category_ids']);
        }

        if ($is_json) {
            $this->export->sendJson('product', $products);
            $this->json = json_encode($products);
            return true;
        }

        $this->export->build(collect($products), $export_columns);

        $this->export->notifyUser('product');

        return true;
    }

    public function getExportColumns()
    {
        return $this->export_columns;
    }

    public function transformObject($object)
    {
        return $this->transformProduct($object);
    }

    public function getContent()
    {
        return $this->export->getContent();
    }

    /**
     * @return string
     */
    public function getJson(): string
    {
        return $this->json;
    }

    public function getTemplate()
    {
        return asset('storage/templates/products.csv');
    }

    /**
     * @param string $value
     * @return int
     */
    private function getBrand(string $value): ?int
    {
        if (empty($this->brands)) {
            $this->brands = Brand::where('account_id', $this->account->id)->where(
                'hide',
                false
            )->get()->keyBy('name')->toArray();

            $this->brands = array_change_key_case($this->brands, CASE_LOWER);
        }

        if (empty($this->brands)) {
            return null;
        }

        if (empty($this->brands[strtolower($value)])) {
            $brand = (new BrandFactory())->create($this->account, $this->user);
            $brand = (new BrandRepository(new Brand()))->save(
                ['name' => $value],
                $brand
            );
            return $brand->id;
        }

        $brand = $this->brands[strtolower($value)];

        return $brand['id'];
    }
}

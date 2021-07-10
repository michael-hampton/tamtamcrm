<?php


namespace App\Components\Import;


use App\Models\Brand;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Project;
use Illuminate\Support\Collection;

trait ExportMapper
{
    /**
     * @var Collection
     */
    private Collection $customers;

    /**
     * @var Collection
     */
    private Collection $companies;

    /**
     * @var Collection
     */
    private Collection $currencies;

    /**
     * @var Collection
     */
    private Collection $products;

    /**
     * @var Collection
     */
    private Collection $projects;

    /**
     * @var Collection
     */
    private Collection $payment_types;

    /**
     * @var array|string[]
     */
    private array $converters = [
        'product_id'          => 'getProduct',
        'customer_id'         => 'getCustomer',
        'brand_id'            => 'getBrandById',
        //'category_id'         => 'getCategoryById',
        'expense_category_id' => 'getExpenseCategory',
        'company_id'          => 'getCompany',
        'payment_method_id'   => 'getPaymentType',
        'project_id'          => 'getProject',
        'currency_id'         => 'getCurrency'
    ];

    private array $success = [];


    public function convert(string $field, ?string $value)
    {
        if (!array_key_exists($field, $this->converters) || empty($value)) {
            return $value;
        }

        $value = $this->{$this->converters[$field]}($value);

        return $value ?: '';
    }

    public function getExpenseCategory(int $id)
    {
        if (empty($this->expense_categories)) {
            $this->expense_categories = ExpenseCategory::byAccount($this->account)->active()->get()->keyBy('id');
        }

        if (empty($this->expense_categories) || empty($this->expense_categories[$id])) {
            return null;
        }

        return $this->expense_categories[$id]->name;
    }

    /**
     * @param int $id
     * @return string|null
     */
    private function getProduct(int $id): ?string
    {
        if (empty($this->products)) {
            $this->products = Product::byAccount($this->account)->active()->get(
            )->keyBy('id');
        }

        if (empty($this->products) || empty($this->products[$id])) {
            return null;
        }

        return $this->products[$id]->name;
    }

    /**
     * @param int $id
     * @return string|null
     */
    private function getCustomer(int $id): ?string
    {
        if (empty($this->customers)) {
            $this->customers = Customer::byAccount($this->account)->active()->get(
            )->keyBy(
                'id'
            );
        }

        if (empty($this->customers) || empty($this->customers[$id])) {
            return null;
        }

        return $this->customers[$id]->name;
    }

    private function getCompany(int $id): ?string
    {
        if (empty($this->companies)) {
            $this->companies = Company::byAccount($this->account)->active()->get(
            )->keyBy('id');
        }

        if (empty($this->companies) || empty($this->companies[$id])) {
            return null;
        }

        return $this->companies[$id]->name;
    }

    private function getPaymentType(int $id): ?string
    {
        if (empty($this->payment_types)) {
            $this->payment_types = PaymentMethod::all()->keyBy('name');
        }

        if (empty($this->payment_types) || empty($this->payment_types[$id])) {
            return null;
        }

        return $this->payment_types[$id]->name;
    }

    private function getProject(int $id): ?string
    {
        if (empty($this->projects)) {
            $this->projects = Project::byAccount($this->account)->active()->get(
            )->keyBy('id');
        }

        if (empty($this->projects) || empty($this->projects[$id])) {
            return null;
        }

        return $this->projects[$id]->name;
    }

    private function getCurrency(int $id): ?string
    {
        if (empty($this->currencies)) {
            $this->currencies = Currency::all()->keyBy('id');
        }

        if (empty($this->currencies) || empty($this->currencies[$id])) {
            return null;
        }

        return $this->currencies[$id]->iso_code;
    }

    /**
     * @param int $id
     * @return string|null
     */
    private function getBrandById(int $id): ?string
    {
        if (empty($this->brands)) {
            $this->brands = Brand::where('account_id', $this->account->id)->where(
                'hide',
                false
            )->get()->keyBy('id');
        }

        if (empty($this->brands) || empty($this->brands[$id])) {
            return null;
        }

        return $this->brands[$id]->name;
    }
}

<?php


namespace App\Components\Import;


use App\Factory\CompanyFactory;
use App\Models\Company;
use App\Models\CompanyContact;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Project;
use App\Models\TaskStatus;
use App\Repositories\CompanyContactRepository;
use App\Repositories\CompanyRepository;
use Illuminate\Support\Collection;

trait ImportMapper
{
    /**
     * @var array
     */
    private array $task_statuses = [];

    /**
     * @var array
     */
    private array $customers = [];

    private Collection $customer_objects;

    /**
     * @var array
     */
    private array $companies = [];

    /**
     * @var array
     */
    private array $currencies = [];

    /**
     * @var array
     */
    private array $products = [];

    /**
     * @var array
     */
    private array $projects = [];

    /**
     * @var array
     */
    private array $payment_types = [];

    /**
     * @var array|string[]
     */
    private array $converters = [
        'product'               => 'getProduct',
        'customer name'         => 'getCustomer',
        'contact email'         => 'getContact',
        'brand name'            => 'getBrand',
        'expense category name' => 'getExpenseCategory',
        'company name'          => 'getCompany',
        'payment type'          => 'getPaymentType',
        'project name'          => 'getProject',
        'currency code'         => 'getCurrency',
        'task status'           => 'getTaskStatus'
    ];

    private array $success = [];

    private array $object = [];

    public function after()
    {
        $errors = $this->getErrors();

        return empty($errors);
    }

    /**
     * Will be executed for a csv line if it passed validation
     * @param $items
     * @param bool $save_data
     * @return bool
     */
    public function handle($items, bool $save_data = false)
    {
        $this->object = $this->buildObject($items);

        if (!$save_data) {
            $this->success[] = $this->transformObject($this->object);
            return true;
        }

        $result = method_exists($this, 'saveEntity') ? $this->saveEntity($this->object) : $this->save();

        if (method_exists($this, 'saveCallback')) {
            $result = $this->saveCallback($result, $this->object);
        }

        $this->success[] = $this->transformObject($result);
    }

    private function buildObject($items)
    {
        $object = [];
        $count = 0;

        $lookup = $this->column_mappings;

        foreach ($this->mappings as $key => $columns) {
            if (is_array($columns)) {
                foreach ($columns as $column => $field) {
                    if (!isset($lookup[$column]) || !isset($items[$lookup[$column]])) {
                        continue;
                    }

                    if (isset($this->converters[$column])) {
                        $value = $this->{$this->converters[$column]}(strtolower(trim($items[$lookup[$column]])));

                        $object[$key][$count][$field] = $value;

                        continue;
                    }

                    $object[$key][$count][$field] = $items[$lookup[$column]];
                }

                continue;
            }

            if (!isset($lookup[$key]) || !isset($items[$lookup[$key]])) {
                continue;
            }

            if (isset($this->converters[$key])) {
                $value = $this->{$this->converters[$key]}(strtolower(trim($items[$lookup[$key]])));

                $object[$columns] = $value;

                continue;
            }

            $object[$columns] = $items[$lookup[$key]];

            $count++;
        }

        return $object;
    }

    private function save()
    {
        $factory = $this->factory($this->object);

        if (!$factory) {
            return false;
        }

        $repo = $this->repository();

        return $repo->save($this->object, $factory);
    }

    /**
     *  Will be executed if a csv line did not pass validation
     *
     * @param $item
     * @return void
     */
    public function invalid($item)
    {
//        echo '<pre>';
//        print_r($this->getErrors());
//        die('invalid');
    }

    /**
     * @return array
     */
    public function getSuccess(): array
    {
        return $this->success;
    }

    public function getImportColumns()
    {
        return $this->array_keys_multi($this->mappings);
    }

    private function array_keys_multi(array $array)
    {
        $keys = array();

        foreach ($array as $key => $value) {
            $keys[] = $key;

            if (is_array($array[$key])) {
                $keys = array_merge($keys, $this->array_keys_multi($array[$key]));
            }
        }

        return $keys;
    }

    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param $value
     * @return int|null
     */
    private function getProduct(string $value): ?int
    {
        if (empty($this->products)) {
            $this->products = Product::where('account_id', $this->account->id)->where('is_deleted', false)->get(
            )->keyBy('name')->toArray();
            $this->products = array_change_key_case($this->products, CASE_LOWER);
        }

        if (empty($this->products) || empty($this->products[$value])) {
            return null;
        }

        $product = $this->products[$value];

        return $product['id'];
    }

    /**
     * @param $value
     * @return int|null
     */
    private function getContact(string $value): ?int
    {
        if (empty($this->contacts)) {
            $this->contacts = CustomerContact::where('account_id', $this->account->id)->get()->keyBy('email')->toArray(
            );
            $this->contacts = array_change_key_case($this->contacts, CASE_LOWER);
        }

        if (empty($this->contacts) || empty($this->contacts[$value])) {
            return null;
        }

        $contact = $this->contacts[$value];

        return $contact['id'];
    }

    /**
     * @param $value
     * @return int|null
     */
    private function getCustomer(string $value): ?int
    {
        if (empty($this->customers)) {
            $this->customer_objects = Customer::where('account_id', $this->account->id)->where(
                'is_deleted',
                false
            )->get()->keyBy(
                'name'
            );

            $this->customers = array_change_key_case($this->customer_objects->toArray(), CASE_LOWER);
        }

        if (empty($this->customers) || empty($this->customers[strtolower($value)])) {
            return null;
        }

        $customer = $this->customers[strtolower($value)];
        $this->customer = $this->customer_objects->where('id', $customer['id'])->first();

        return $this->customer->id;
    }

    private function getCompany(string $value)
    {
        if (empty($this->companies)) {
            $this->companies = Company::where('account_id', $this->account->id)->where('is_deleted', false)->get(
            )->keyBy('name')->toArray();
            $this->companies = array_change_key_case($this->companies, CASE_LOWER);
        }

        if (empty($this->companies)) {
            return null;
        }

        if (empty($this->companies[strtolower($value)])) {
            $company = (new CompanyFactory())->create($this->user, $this->account);
            $company = (new CompanyRepository(new Company(), new CompanyContactRepository(new CompanyContact())))->save(
                ['name' => $value],
                $company
            );
            return $company->id;
        }

        $company = $this->companies[strtolower($value)];

        return $company['id'];
    }

    private function getPaymentType(string $value)
    {
        if (empty($this->payment_types)) {
            $this->payment_types = PaymentMethod::all()->keyBy('name')->toArray();
            $this->payment_types = array_change_key_case($this->payment_types, CASE_LOWER);
        }

        if (empty($this->payment_types) || empty($this->payment_types[strtolower($value)])) {
            return null;
        }

        $payment_type = $this->payment_types[strtolower($value)];

        return $payment_type['id'];
    }

    private function getTaskStatus(string $value)
    {
        if (empty($this->task_statuses)) {
            $this->task_statuses = TaskStatus::all()->keyBy('name')->toArray();
            $this->task_statuses = array_change_key_case($this->task_statuses, CASE_LOWER);
        }

        if (empty($this->task_statuses) || empty($this->task_statuses[strtolower($value)])) {
            return null;
        }

        $task_status = $this->task_statuses[strtolower($value)];

        return $task_status['id'];
    }

    private function getProject(string $value)
    {
        if (empty($this->projects)) {
            $this->projects = Project::where('account_id', $this->account->id)->where('is_deleted', false)->get(
            )->keyBy('name')->toArray();
            $this->projects = array_change_key_case($this->projects, CASE_LOWER);
        }

        if (empty($this->projects) || empty($this->projects[strtolower($value)])) {
            return null;
        }

        $project = $this->projects[strtolower($value)];

        return $project['id'];
    }

    private function getCurrency(string $value)
    {
        if (empty($this->currencies)) {
            $this->currencies = Currency::all()->keyBy('iso_code')->toArray();
            $this->currencies = array_change_key_case($this->currencies, CASE_LOWER);
        }

        if (empty($this->currencies) || empty($this->currencies[strtolower($value)])) {
            return null;
        }

        $currency = $this->currencies[strtolower($value)];

        return $currency['id'];
    }
}

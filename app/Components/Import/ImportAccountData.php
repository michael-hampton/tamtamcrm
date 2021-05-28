<?php


namespace App\Components\Import;


use App\Components\Setup\DatabaseManager;
use App\Exceptions\InvalidAccountImportDataException;
use App\Models\Account;
use App\Models\AccountUser;
use App\Models\Company;
use App\Models\CompanyContact;
use App\Models\CompanyGateway;
use App\Models\CompanyToken;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\CustomerGateway;
use App\Models\Deal;
use App\Models\Design;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Group;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentTerms;
use App\Models\Plan;
use App\Models\PlanSubscription;
use App\Models\Product;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\Quote;
use App\Models\RecurringInvoice;
use App\Models\RecurringQuote;
use App\Models\Subscription;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaxRate;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\False_;
use PHPUnit\Framework\Assert;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class ImportAccountData
{

    public array $classes = [
        'customers'          => Customer::class,
        'customer_contacts'  => CustomerContact::class,
        'customer_gateways'  => CustomerGateway::class,
        'company_gateways'   => CompanyGateway::class,
        'transactions'       => Transaction::class,
        'credits'            => Credit::class,
        'designs'            => Design::class,
        'expenses'           => Expense::class,
        'expense_categories' => ExpenseCategory::class,
        'groups'             => Group::class,
        'invoices'           => Invoice::class,
        'payment_terms'      => PaymentTerms::class,
        'payments'           => Payment::class,
        'projects'           => Project::class,
        'quotes'             => Quote::class,
        'recurring_invoices' => RecurringInvoice::class,
        'recurring_quotes'   => RecurringQuote::class,
        'webhooks'           => Subscription::class,
        'plans'              => Plan::class,
        'subscriptions'      => PlanSubscription::class,
        'tasks'              => Task::class,
        'task_statuses'      => TaskStatus::class,
        'tax_rates'          => TaxRate::class,
        'companies'          => Company::class,
        'company_contacts'   => CompanyContact::class,
        'deals'              => Deal::class,
        'leads'              => Lead::class,
        'products'           => Product::class,
        'orders'             => Order::class,
        'users'              => User::class,
        'purchase_orders'    => PurchaseOrder::class,
        'company_tokens'     => CompanyToken::class,
        'account_users'      => AccountUser::class
    ];

    /**
     * @var string
     */
    private string $filename;

    /**
     * @var string
     */
    private string $path;

    /**
     * @var Account
     */
    private Account $account;

    /**
     * @var User
     */
    private User $user;

    public function __construct(Account $account, string $path, string $filename)
    {
        $this->filename = $filename;
        $this->path = $path;
        $this->account = $account;

        if (!empty(config('taskmanager.export_database'))) {
            DB::connection(config('taskmanager.export_database'));
        }

        $owner = $account->owner();
        $this->user = !empty($owner) ? $owner : $this->account->users->first();
    }

    public function importData()
    {
        $file_contents = $this->getFileContents();

        if (!$file_contents) {
            return false;
        }

        $data = json_decode($file_contents, true);

        DB::beginTransaction();

        foreach ($this->classes as $entity => $class) {

            $objects = $data[$entity];

            foreach ($objects as $object) {

                $test = new $class();

                if ($entity === 'company_gateways') {

                    $object = $this->formatCompanyGateways($object);
                }

                if ($entity === 'plans') {

                    $plan_count = Plan::query()->where('code', $object['code'])->count();

                    if ($plan_count > 0) {
                        continue;
                    }
                }

                if ($entity === 'subscriptions') {

                    $subscription_count = PlanSubscription::query()->where('subscriber_id', $object['subscriber_id'])->where('subscriber_type', $object['subscriber_type'])->where('plan_id', $object['plan_id'])->count();

                    if ($subscription_count > 0) {
                        continue;
                    }
                }

                if ($entity === 'users') {
                    $object = $this->validateUser($object);

                    if (!$object) {
                        continue;
                    }
                }

                if (isset($object['id'])) {
                    unset($object['id']);
                }

                if ($entity !== 'users') {
                    $test->account_id = $this->account->id;
                }

                if (!in_array($entity, ['customer_gateways', 'subscriptions', 'users'])) {
                    $test->user_id = $this->user->id;
                }

                try {

                    if (in_array($entity, ['products', 'companies', 'customers', 'tax_rates', 'projects', 'payment_terms', 'tasks', 'expense_categories', 'task_statuses'])) {

                        $test = $class::firstOrNew([
                            'name' => $object['name'],
                            'account_id' => $object['account_id']
                        ]);
                    }

                    if (in_array($entity, ['credits', 'quotes', 'invoices', 'payments', 'recurring_invoices', 'recurring_quotes', 'purchase_orders', 'orders'])) {

                        $test = $class::firstOrNew([
                            'number' => $object['number'],
                            'account_id' => $object['account_id']
                        ]);
                    }

                    $test->fill($object);
                    $test->save();
                } catch (QueryException $exception) {
                    echo $exception->getMessage();
                    Log::emergency($exception->getMessage());
                    continue;
                }


            }
        }

        DB::commit();
    }

    private function formatCompanyGateways(array $data)
    {
        if (empty($data['settings'])) {
            $data['settings'] = '{}';
        }

        if (empty($data['charges'])) {
            $data['charges'] = '{}';
        }

        return $data;
    }

    private function validateUser(array $data)
    {
        $users = $this->account->users->where('email', $data['email'])->first();

        if ($users->count() > 0) {

            return false;
        }

        return $data;
    }

    private function getFileContents()
    {
        $zip = new \ZipArchive();

        if ($zip->open($this->path) === true) {

            $contents = $zip->getFromName($this->filename);
            $zip->close();

            return $contents;
        }

        return false;

    }
}
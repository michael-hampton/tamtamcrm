<?php


namespace App\Components\Import;


use App\Components\Setup\DatabaseManager;
use App\Models\Company;
use App\Models\CompanyContact;
use App\Models\CompanyGateway;
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
        'purchase_orders'    => PurchaseOrder::class
    ];

    private string $filename;

    private string $path;

    public function __construct(string $path, string $filename)
    {
        $this->filename = $filename;
        $this->path = $path;

        DB::connection('mike');
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

            if ($entity === 'subscriptions') {
                continue;
            }

            $objects = $data[$entity];

            foreach ($objects as $object) {

                $test = new $class();

                if ($entity === 'company_gateways') {

                    if (empty($object['settings'])) {
                        $object['settings'] = '{}';
                    }

                    if (empty($object['charges'])) {
                        $object['charges'] = '{}';
                    }
                }

                if (isset($object['id'])) {
                    unset($object['id']);
                }

                $test->account_id = 1;

                if ($entity !== 'customer_gateways') {
                    $test->user_id = 5;
                }

                try {
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

    private function getFileContents()
    {
        $zip = new \ZipArchive();

        if ($zip->open($this->path) === TRUE) {

            $contents = $zip->getFromName($this->filename);
            $zip->close();

            return $contents;
        }

        return false;

    }
}
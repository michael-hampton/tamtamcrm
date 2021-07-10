<?php


namespace App\Components\Import;


use App\Components\Import\ValidationFilters\NumberValidationFilter;
use App\Components\Payment\ProcessPayment;
use App\Factory\PaymentFactory;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Repositories\InvoiceRepository;
use App\Repositories\PaymentRepository;
use App\Requests\SearchRequest;
use App\Search\InvoiceSearch;
use App\Search\PaymentSearch;
use App\Transformations\PaymentTransformable;

class PaymentImporter extends BaseCsvImporter
{
    use ImportMapper;
    use PaymentTransformable;

    /**
     * @var string
     */
    protected string $json;

    protected $entity;
    private array $export_columns = [
        'number'               => 'Number',
        'customer_id'          => 'Customer name',
        'date'                 => 'Date',
        'reference_number'     => 'Reference Number',
        'amount'               => 'Amount',
        'payment_type'         => 'Payment Type',
        'paymentable_invoices' => 'Invoices',
        'paymentable_credits'  => 'Credits'
    ];
    /**
     * @var array|string[]
     */
    private array $mappings = [
        'number'           => 'number',
        'customer name'    => 'customer_id',
        'date'             => 'date',
        'amount'           => 'amount',
        'reference number' => 'reference_number',
        'payment type'     => 'payment_method_id',
        'public notes'     => 'customer_note',
        'private notes'    => 'internal_note',
        'invoices'         => 'invoices'
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
     * @var Customer
     */
    private Customer $customer;
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
        parent::__construct('Payment');

        $this->entity = 'Payment';
        $this->account = $account;
        $this->user = $user;
        $this->export = new Export($this->account, $this->user);
        self::addValidationFilter(new NumberValidationFilter());
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
                'number'        => ['validation' => 'number_validation'],
                'customer name' => ['validation' => 'required', 'cast' => 'string'],
                'amount'        => ['validation' => 'required|numeric|min:0|not_in:0', 'cast' => 'float'],
                'private notes' => ['cast' => 'string'],
                'public notes'  => ['cast' => 'string'],
                'date'          => ['validation' => 'required', 'cast' => 'date'],
                //'customer_id' => ['required', 'cast' => 'int'],
            ],
            'config'   => [
                'csv_date_format' => 'Y-m-d'
            ]
        ];
    }

    public function export($is_json = false)
    {
        $export_columns = $this->getExportColumns();

        $search_request = new SearchRequest();
        $search_request->replace(['column' => 'created_at', 'order' => 'desc']);

        $payments = (new PaymentSearch(new PaymentRepository(new Payment())))->filter($search_request, $this->account);

        foreach ($payments as $key => $payment) {

            if ($payment['invoices']->count() > 0) {
                $payments[$key]['paymentable_invoices'] = $payment['invoices']->implode('number', ' ,');
            }

            if ($payment['credits']->count() > 0) {
                $payments[$key]['paymentable_credits'] = $payment['credits']->implode('number', ' ,');
            }
        }

        if ($is_json) {
            $this->export->sendJson('payment', $payments);
            $this->json = json_encode($payments);
            return true;
        }

        $this->export->build(collect($payments), $export_columns);

        $this->export->notifyUser('payment');

        return true;
    }

    public function getExportColumns()
    {
        return $this->export_columns;
    }

    public function transformObject($object)
    {
        return $this->transformPayment($object);
    }

    public function getContent()
    {
        return $this->export->getContent();
    }

    public function getTemplate()
    {
        return asset('storage/templates/payments.csv');
    }

    private function saveEntity(array $object)
    {
        if (!empty($this->object['invoices'])) {
            $invoice_numbers = explode(',', $this->object['invoices']);

            $invoices = Invoice::select('id AS invoice_id', 'balance AS amount')->whereIn(
                'number',
                $invoice_numbers
            )->get()->toArray();
            $object['invoices'] = $invoices;
        }

        return (new ProcessPayment())->process($object, $this->repository(), $this->factory($object));
    }

    /**
     * @return PaymentRepository
     */
    public function repository(): PaymentRepository
    {
        return new PaymentRepository(new Payment());
    }

    /**
     * @param array $params
     * @return Payment|null
     */
    public function factory(array $params): ?Payment
    {
        if (empty($this->customer)) {
            return null;
        }

        return PaymentFactory::create($this->customer, $this->user, $this->account);
    }

    /**
     * @return string
     */
    public function getJson(): string
    {
        return $this->json;
    }
}

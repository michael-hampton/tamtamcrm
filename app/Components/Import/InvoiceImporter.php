<?php


namespace App\Components\Import;


use App\Components\Import\ValidationFilters\NumberValidationFilter;
use App\Factory\InvoiceFactory;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use App\Repositories\CustomerRepository;
use App\Repositories\InvoiceRepository;
use App\Requests\SearchRequest;
use App\Search\CustomerSearch;
use App\Search\InvoiceSearch;
use App\Transformations\InvoiceTransformable;

class InvoiceImporter extends BaseCsvImporter
{
    use ImportMapper;

    protected $entity;
    private array $export_columns = [
        'number'         => 'Number',
        'customer_id'    => 'Customer name',
        'date'           => 'Date',
        'po_number'      => 'po number',
        'due_date'       => 'due date',
        'terms'          => 'terms',
        'customer_note'   => 'public notes',
        'internal_note'  => 'private notes',
        'description'    => 'description',
        'product'        => 'product_id',
        'unit_price'     => 'unit_price',
        'unit_discount'  => 'unit_discount',
        'unit_tax'       => 'unit_tax',
        'quantity'       => 'quantity',
        'shipping_cost'  => 'shipping_cost',
        'tax_rate'       => 'tax_rate',
        'custom_value1'  => 'custom value1',
        'custom_value2'  => 'custom value2',
        'custom_value3'  => 'custom value3',
        'custom_value4'  => 'custom value4',
        'exchange_rate'  => 'exchange_rate',
        'payment_amount' => 'Amount Paid',
        'payments_made'  => 'Payments Made'
    ];
    /**
     * @var array|string[]
     */
    private array $mappings = [
        'number'        => 'number',
        'customer name' => 'customer_id',
        'project name'  => 'project_id',
        //'contact email'  => 'contact',
        'date'          => 'date',
        'po number'     => 'po_number',
        'due date'      => 'due_date',
        'terms'         => 'terms',
        'public notes'  => 'customer_note',
        'private notes' => 'internal_note',
        'custom value1' => 'custom_value1',
        'custom value2' => 'custom_value2',
        'custom value3' => 'custom_value3',
        'custom value4' => 'custom_value4',
        'exchange rate' => 'exchange_rate',
        'line_items'    => [
            'description'   => 'description',
            'product'       => 'product_id',
            'unit_price'    => 'unit_price',
            'unit_discount' => 'unit_discount',
            'unit_tax'      => 'unit_tax',
            'quantity'      => 'quantity',
        ],
        'invitations'   => [
            'contact email' => 'contact_id',
        ],
        'shipping_cost' => 'shipping_cost',
        'tax_rate'      => 'tax_rate'
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
        parent::__construct('Invoice');
        $this->entity = 'Invoice';

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
                'terms'         => ['cast' => 'string'],
                'custom value1' => ['cast' => 'string'],
                'custom value2' => ['cast' => 'string'],
                'custom value3' => ['cast' => 'string'],
                'custom value4' => ['cast' => 'string'],
                'private notes' => ['cast' => 'string'],
                'public notes'  => ['cast' => 'string'],
                'po number'     => ['cast' => 'string'],
                'date'          => ['validation' => 'required', 'cast' => 'date'],
                'due date'      => ['validation' => 'required', 'cast' => 'date'],
                //'customer_id' => ['required', 'cast' => 'int'],
            ],
            'config'   => [
                'csv_date_format' => 'Y-m-d'
            ]
        ];
    }

    /**
     * @param array $params
     * @return Invoice
     */
    public function factory(array $params): ?Invoice
    {
        if (empty($this->customer)) {
            return null;
        }

        return InvoiceFactory::create($this->account, $this->user, $this->customer);
    }

    /**
     * @return InvoiceRepository
     */
    public function repository(): InvoiceRepository
    {
        return new InvoiceRepository(new Invoice());
    }

    public function export($is_json = false)
    {
        $export_columns = $this->getExportColumns();

        $search_request = new SearchRequest();
        $search_request->replace(['column' => 'created_at', 'order' => 'desc']);

        $invoices = (new InvoiceSearch(new InvoiceRepository(new Invoice())))->filter($search_request, $this->account);

        foreach ($invoices as $key => $invoice) {

            if (count($invoice['paymentables']) > 0) {

                $invoices[$key]['payment_amount'] = array_sum(array_column($invoice['paymentables'], 'amount'));
                $invoices[$key]['payments_made'] = implode(' ,', array_column($invoice['paymentables'], 'number'));
            }

            if (count($invoice['line_items']) > 0) {
                foreach ($invoice['line_items'] as $line_item) {
                    $invoices[$key] = array_merge($invoices[$key], (array)$line_item);
                }
            }
        }

        if ($is_json) {
            return json_encode($invoices);
        }

        $this->export->build(collect($invoices), $export_columns);

        $this->export->notifyUser('invoice');

        return true;
    }

    public function getExportColumns()
    {
        return $this->export_columns;
    }

    public function transformObject($object)
    {
        return (new InvoiceTransformable())->transformInvoice($object);
    }

    public function getContent()
    {
        return $this->export->getContent();
    }

    public function getTemplate()
    {
        return asset('storage/templates/invoice.csv');
    }
}

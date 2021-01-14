<?php


namespace App\Components\Import;


use App\Components\Import\ValidationFilters\NumberValidationFilter;
use App\Factory\InvoiceFactory;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use App\Repositories\InvoiceRepository;
use App\Transformations\InvoiceTransformable;

class InvoiceImporter extends BaseCsvImporter
{
    use ImportMapper;

    protected $entity;
    private array $export_columns = [
        'number'        => 'Number',
        'customer_id'   => 'Customer name',
        'date'          => 'Date',
        'po_number'     => 'po number',
        'due_date'      => 'due date',
        'terms'         => 'terms',
        'public_notes'  => 'public notes',
        'private_notes' => 'private notes',
        'description'   => 'description',
        'product'       => 'product_id',
        'unit_price'    => 'unit_price',
        'unit_discount' => 'unit_discount',
        'unit_tax'      => 'unit_tax',
        'quantity'      => 'quantity',
        'shipping_cost' => 'shipping_cost',
        'tax_rate'      => 'tax_rate'
    ];
    /**
     * @var array|string[]
     */
    private array $mappings = [
        'number'        => 'number',
        'customer name' => 'customer_id',
        //'contact email'  => 'contact',
        'date'          => 'date',
        'po number'     => 'po_number',
        'due date'      => 'due_date',
        'terms'         => 'terms',
        'public notes'  => 'public_notes',
        'private notes' => 'private_notes',
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
        $list = Invoice::where('account_id', '=', $this->account->id)->get();

        $invoices = [];

        foreach ($list as $invoice) {
            $arr_invoice = $this->transformObject($invoice);

            foreach ($invoice->line_items as $line_item) {
                $invoices[] = array_merge($arr_invoice, (array)$line_item);
            }
        }

        if ($is_json) {
            return json_encode($invoices);
        }

        $this->export->build(collect($invoices), $export_columns);

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

<?php


namespace App\Components\Import;


use App\Factory\CustomerContactFactory;
use App\Factory\CustomerFactory;
use App\Jobs\Customer\StoreCustomerAddress;
use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\User;
use App\Repositories\CustomerContactRepository;
use App\Repositories\CustomerRepository;
use App\Requests\SearchRequest;
use App\Search\CustomerSearch;
use App\Transformations\ContactTransformable;
use App\Transformations\CustomerTransformable;
use Carbon\Carbon;

class CustomerImporter extends BaseCsvImporter
{
    use ImportMapper;
    use CustomerTransformable;

    protected $entity;
    private array $export_columns = [
        'number'             => 'Number',
        'first_name'         => 'first name',
        'last_name'          => 'last name',
        'email'              => 'email',
        'phone'              => 'phone',
        'website'            => 'website',
        'terms'              => 'terms',
        'public notes'       => 'public notes',
        'private notes'      => 'private notes',
        'job_title'          => 'job title',
        'address_1'          => 'address 1',
        'address_2'          => 'address 2',
        'zip'                => 'zip',
        'city'               => 'city',
        'shipping_address_1' => 'shipping address 1',
        'shipping_address_2' => 'shipping address 2',
        'shipping_zip'       => 'shipping zip',
        'shipping_city'      => 'city',
        'name'               => 'name',
        'description'        => 'description',
    ];
    /**
     * @var array|string[]
     */
    private array $mappings = [
        'name'          => 'name',
        'vat_number'    => 'vat_number',
        'currency code' => 'currency_id',
        'website'       => 'website',
        'terms'         => 'terms',
        'industry'      => 'industry_id',
        'public notes'  => 'customer_note',
        'private notes' => 'internal_note',
        'custom value1' => 'custom_value1',
        'custom value2' => 'custom_value2',
        'custom value3' => 'custom_value3',
        'custom value4' => 'custom_value4',
        'contacts'      => [
            'first_name' => 'first_name',
            'last_name'  => 'last_name',
            'email'      => 'email',
            'phone'      => 'phone'
        ],
        'billing'       => [
            'billing address 1' => 'address_1',
            'billing address 2' => 'address_2',
            'billing zip'       => 'zip',
            'billing city'      => 'city',
            'billing country'   => 'country_id'
        ],
        'shipping'      => [
            'shipping address 1' => 'address_1',
            'shipping address 2' => 'address_2',
            'shipping zip'       => 'zip',
            'shipping city'      => 'city',
            'shipping country'   => 'country_id'
        ]
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
        parent::__construct('Customer');
        $this->entity = 'Customer';

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
                'first_name'    => ['validation' => 'required', 'cast' => 'string'],
                'last_name'     => ['validation' => 'required', 'cast' => 'string'],
                'email'         => ['validation' => 'email|required', 'cast' => 'string'],
                'phone'         => ['cast' => 'string'],
                'name'          => ['validation' => 'required', 'cast' => 'string'],
                'vat_number'    => ['required', 'cast' => 'string'],
                'custom value1' => ['cast' => 'string'],
                'custom value2' => ['cast' => 'string'],
                'custom value3' => ['cast' => 'string'],
                'custom value4' => ['cast' => 'string'],
                //'due date'      => ['cast' => 'date'],
                //'customer_id' => ['required', 'cast' => 'int'],
            ],
            'config'   => [
                'csv_date_format' => 'Y-m-d'
            ]
        ];
    }

    /**
     * @param array $params
     * @return Customer
     */
    public function factory(array $params): ?Customer
    {
        return CustomerFactory::create($this->account, $this->user);
    }

    /**
     * @return CustomerRepository
     */
    public function repository(): CustomerRepository
    {
        return new CustomerRepository(new Customer(), new CustomerContactRepository(new CustomerContact()));
    }

    /**
     * @param Customer $customer
     * @param array $data
     * @return Customer|null
     * @return Customer|null
     */
    public function saveCallback(Customer $customer, array $data)
    {

        if (!empty($data['contacts'])) {
            foreach ($data['contacts'] as $contact) {

                $customer_contact = CustomerContactFactory::create($this->account, $this->user, $customer);

                (new CustomerContactRepository(new CustomerContact()))->createContact($contact, $customer_contact);

            }
        }

        $addresses[0] = [];

        if (!empty($data['billing'])) {
            $billing = array_values($data['billing']);

            $addresses[0]['billing'] = $billing[0];
        }

        if (!empty($data['shipping'])) {
            $shipping = array_values($data['shipping']);

            $addresses[0]['shipping'] = $shipping[0];
        }

        if (!empty($addresses[0])) {
            $customer = StoreCustomerAddress::dispatchNow($customer, ['addresses' => $addresses]);
        }

        return $customer->fresh();
    }

    public function export()
    {
        $export_columns = $this->getExportColumns();

        $search_request = new SearchRequest();
        $search_request->replace(['column' => 'created_at', 'order' => 'desc']);

        $customers = (new CustomerSearch(new CustomerRepository(new Customer())))->filter($search_request, $this->account);

        foreach ($customers as $key => $formatted_customer) {

            if (!empty($formatted_customer['billing']['address_1'])) {

                $customers[$key] = array_merge($customers[$key], $formatted_customer['billing']);
            }

            if (!empty($formatted_customer['shipping'])) {

                $shipping = [
                    'shipping_address_1' => $formatted_customer['shipping']['address_1'],
                    'shipping_address_2' => $formatted_customer['shipping']['address_2'],
                    'shipping_zip'       => $formatted_customer['shipping']['zip'],
                    'shipping_city'      => $formatted_customer['shipping']['city'],
                ];

                $customers[$key] = array_merge($customers[$key], $shipping);
            }

            if (count($formatted_customer['contacts']) > 0) {
                foreach ($formatted_customer['contacts'] as $contact) {
                    $customers[$key] = array_merge($customers[$key], $contact);
                }
            }
        }

        $this->export->build(collect($customers), $export_columns);

        $this->export->notifyUser('customer');

        return true;
    }

    public function getExportColumns()
    {
        return $this->export_columns;
    }

    public function transformObject($object)
    {
        return $this->transformCustomer($object);
    }

    public function getContent()
    {
        return $this->export->getContent();
    }

    public function getTemplate()
    {
        return asset('storage/templates/customer.csv');
    }
}

<?php


namespace App\Components\Import;


use App\Factory\CustomerContactFactory;
use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\User;
use App\Repositories\CustomerContactRepository;
use App\Repositories\CustomerRepository;
use App\Transformations\ContactTransformable;

class ContactImporter extends BaseCsvImporter
{
    use ImportMapper;

    private array $export_columns = [
        'first_name' => 'first name',
        'last_name'  => 'last name',
        'email'      => 'email',
        'phone'      => 'phone'
    ];

    /**
     * @var array|string[]
     */
    private array $mappings = [
        'first_name' => 'first_name',
        'last_name'  => 'last_name',
        'email'      => 'email',
        'phone'      => 'phone'
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
        parent::__construct();

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
                'first_name' => ['required', 'cast' => 'string'],
                'last_name'  => ['cast' => 'string'],
                'email'      => ['cast' => 'string'],
                'phone'      => ['cast' => 'string']
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
        if (empty($this->customer)) {
            return null;
        }

        return CustomerContactFactory::create($this->account, $this->user, $this->customer);
    }

    /**
     * @return CustomerRepository
     */
    public function repository(): CustomerRepository
    {
        return new CustomerContactRepository(new CustomerContact());
    }

    /**
     * @param Customer $customer
     * @param array $data
     * @return Customer|null
     * @return Customer|null
     */
    public function saveCallback(Customer $customer, array $data)
    {
        //TODO
    }

    public function export()
    {
        $export_columns = $this->getExportColumns();
        $list = CustomerContact::where('account_id', '=', $this->account->id)->get();

        $contacts = [];

        foreach ($list as $contact) {
            $formatted_contact = $this->transformObject($contact);
            $contacts[] = $formatted_contact;
        }

        $this->export->build(collect($contacts), $export_columns);

        return true;
    }

    public function getExportColumns()
    {
        return $this->export_columns;
    }

    public function transformObject($object)
    {
        return (new ContactTransformable())->transformContact($object);
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

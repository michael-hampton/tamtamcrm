<?php


namespace App\Components\Import;


use App\Factory\CompanyFactory;
use App\Models\Account;
use App\Models\Company;
use App\Models\CompanyContact;
use App\Models\User;
use App\Repositories\CompanyContactRepository;
use App\Repositories\CompanyRepository;
use App\Transformations\CompanyContactTransformable;
use App\Transformations\CompanyTransformable;

class CompanyImporter extends BaseCsvImporter
{
    use ImportMapper;
    use CompanyTransformable;

    protected $entity;
    private array $export_columns = [
        'number'        => 'Number',
        'first_name'    => 'first name',
        'last_name'     => 'last name',
        'email'         => 'email',
        'phone'         => 'phone',
        'website'       => 'website',
        'terms'         => 'terms',
        'public notes'  => 'public notes',
        'private notes' => 'private notes',
        'job_title'     => 'job title',
        'address_1'     => 'address 1',
        'address_2'     => 'address 2',
        'postcode'      => 'postcode',
        'town'          => 'town',
        'city'          => 'city',
        'name'          => 'name',
        'description'   => 'description',
    ];
    /**
     * @var array|string[]
     */
    private array $mappings = [
        'number'               => 'Number',
        'email'                => 'email',
        'company phone number' => 'phone_number',
        'website'              => 'website',
        'terms'                => 'terms',
        'public notes'         => 'public_notes',
        'private notes'        => 'private_notes',
        'address 1'            => 'address_1',
        'address 2'            => 'address_2',
        'postcode'             => 'postcode',
        'town'                 => 'town',
        'city'                 => 'city',
        'name'                 => 'name',
        'vat number'           => 'vat_number',
        'contacts'             => [
            'first_name'    => 'first_name',
            'last_name'     => 'last_name',
            'email'         => 'email',
            'contact phone' => 'phone'
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
        parent::__construct('Company');
        $this->entity = 'Company';

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
                'website'              => ['cast' => 'string'],
                'company phone number' => ['cast' => 'string'],
                'contact phone'        => ['cast' => 'string'],
                'email'                => ['validation' => 'required', 'cast' => 'string'],
                'address 1'            => ['cast' => 'string'],
                'address 2'            => ['cast' => 'string'],
                'town'                 => ['cast' => 'string'],
                'city'                 => ['cast' => 'string'],
                'postcode'             => ['cast' => 'string'],
                'name'                 => ['validation' => 'required|unique:companies', 'cast' => 'string'],
                'vat number'           => ['cast' => 'string'],
            ],
            'config'   => [
                'csv_date_format' => 'Y-m-d'
            ]
        ];
    }

    /**
     * @param array $params
     * @return Company|null
     */
    public function factory(array $params): ?Company
    {
        return (new CompanyFactory())->create($this->user, $this->account);
    }

    /**
     * @return CompanyRepository
     */
    public function repository(): CompanyRepository
    {
        return new CompanyRepository(new Company(), new CompanyContactRepository(new CompanyContact()));
    }

    /**
     * @param Company $company
     * @param array $data
     * @return mixed
     */
    public function saveCallback(Company $company, array $data)
    {
        if (!empty($data['contacts'])) {
            (new CompanyContactRepository(new CompanyContact()))->save($data['contacts'], $company);
        }

        return $company->fresh();
    }

    public function export()
    {
        $export_columns = $this->getExportColumns();
        $list = Company::where('account_id', '=', $this->account->id)->get();

        $companies = [];

        foreach ($list as $company) {
            $formatted_company = $this->transformObject($company);

            foreach ($company->contacts as $contact) {
                $formatted_contact = (new CompanyContactTransformable())->transformCompanyContact($contact);

                $companies[] = array_merge($formatted_company, $formatted_contact);
            }
        }

        $this->export->build(collect($companies), $export_columns);

        return true;
    }

    public function getExportColumns()
    {
        return $this->export_columns;
    }

    public function transformObject($object)
    {
        return $this->transformCompany($object);
    }

    public function getContent()
    {
        return $this->export->getContent();
    }

    public function getTemplate()
    {
        return asset('storage/templates/companies.csv');
    }
}

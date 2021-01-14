<?php


namespace App\Components\Import;


use App\Factory\LeadFactory;
use App\Models\Account;
use App\Models\Lead;
use App\Models\User;
use App\Repositories\LeadRepository;
use App\Transformations\LeadTransformable;

class LeadImporter extends BaseCsvImporter
{
    use ImportMapper;
    use LeadTransformable;

    protected $entity;
    private array $export_columns = [
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
        'zip'           => 'zip',
        'city'          => 'city',
        'name'          => 'name',
        'description'   => 'description',
        'task status'   => 'task_status_id'
    ];
    /**
     * @var array|string[]
     */
    private array $mappings = [
        'first_name'    => 'first_name',
        'last_name'     => 'last_name',
        'email'         => 'email',
        'phone'         => 'phone',
        'website'       => 'website',
        'terms'         => 'terms',
        'public notes'  => 'public_notes',
        'private notes' => 'private_notes',
        'job_title'     => 'job_title',
        'address_1'     => 'address_1',
        'address_2'     => 'address_2',
        'zip'           => 'zip',
        'city'          => 'city',
        'name'          => 'name',
        'description'   => 'description',
        'task status'   => 'task_status_id'
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
        parent::__construct('Lead');
        $this->entity = 'Lead';

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
                'first_name'  => ['validation' => 'required', 'cast' => 'string'],
                'last_name'   => ['validation' => 'required', 'cast' => 'string'],
                'email'       => ['validation' => 'required:|unique:leads', 'cast' => 'string'],
                'phone'       => ['cast' => 'string'],
                'website'     => ['cast' => 'string'],
                'address_1'   => ['required', 'cast' => 'string'],
                'address_2'   => ['required', 'cast' => 'string'],
                'zip'         => ['required', 'cast' => 'string'],
                'city'        => ['required', 'cast' => 'string'],
                'name'        => ['required', 'cast' => 'string'],
                'description' => ['required', 'cast' => 'string'],
                'job_title'   => ['cast' => 'string'],
                'task status' => ['validation' => 'required', 'cast' => 'string'],
                //'customer_id' => ['required', 'cast' => 'int'],
            ],
            'config'   => [
                'csv_date_format' => 'Y-m-d'
            ]
        ];
    }

    /**
     * @param array $params
     * @return Lead
     */
    public function factory(array $params): ?Lead
    {
        return LeadFactory::create($this->account, $this->user);
    }

    /**
     * @return LeadRepository
     */
    public function repository(): LeadRepository
    {
        return new LeadRepository(new Lead());
    }

    public function export()
    {
        $export_columns = $this->getExportColumns();
        $list = Lead::where('account_id', '=', $this->account->id)->get();

        $leads = $list->map(
            function (Lead $lead) {
                return $this->transformObject($lead);
            }
        )->all();

        $this->export->build(collect($leads), $export_columns);

        return true;
    }

    public function getExportColumns()
    {
        return $this->export_columns;
    }

    public function transformObject($object)
    {
        return $this->transformLead($object);
    }

    public function getContent()
    {
        return $this->export->getContent();
    }

    public function getTemplate()
    {
        return asset('storage/templates/leads.csv');
    }
}

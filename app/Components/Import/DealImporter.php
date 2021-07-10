<?php


namespace App\Components\Import;


use App\Factory\DealFactory;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\User;
use App\Repositories\CustomerRepository;
use App\Repositories\DealRepository;
use App\Requests\SearchRequest;
use App\Search\CustomerSearch;
use App\Search\DealSearch;
use App\Transformations\DealTransformable;

class DealImporter extends BaseCsvImporter
{
    use ImportMapper;
    use DealTransformable;

    /**
     * @var string
     */
    protected string $json;

    protected $entity;
    private array $export_columns = [
        'name'          => 'name',
        'description'   => 'description',
        'valued_at'     => 'valued at',
        'due_date'      => 'due date',
        'terms'         => 'terms',
        'customer_note'  => 'public notes',
        'internal_note' => 'private notes'
    ];
    /**
     * @var array|string[]
     */
    private array $mappings = [
        'name'          => 'name',
        'description'   => 'description',
        'valued_at'     => 'valued_at',
        'due_date'      => 'due_date',
        'terms'         => 'terms',
        'public notes'  => 'customer_note',
        'private notes' => 'internal_note',
        'customer name' => 'customer_id',
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
        parent::__construct('Deal');
        $this->entity = 'Deal';

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
                'name'          => ['validation' => 'required|unique:deals', 'cast' => 'string'],
                'description'   => ['cast' => 'string'],
                'valued_at'     => ['cast' => 'float'],
                'due_date'      => ['cast' => 'date'],
                'customer name' => ['validation' => 'required', 'cast' => 'string'],
                'task status'   => ['validation' => 'required', 'cast' => 'string'],
            ],
            'config'   => [
                'csv_date_format' => 'Y-m-d'
            ]
        ];
    }

    /**
     * @param array $params
     * @return Deal
     */
    public function factory(array $params): ?Deal
    {
        return DealFactory::create($this->user, $this->account);
    }

    /**
     * @return DealRepository
     */
    public function repository(): DealRepository
    {
        return new DealRepository(new Deal());
    }

    public function export($is_json = false)
    {
        $export_columns = $this->getExportColumns();

        $search_request = new SearchRequest();
        $search_request->replace(['column' => 'created_at', 'order' => 'desc']);

        $deals = (new DealSearch(new DealRepository(new Deal())))->filter($search_request, $this->account);

        if ($is_json) {
            $this->export->sendJson('deal', $deals);
            $this->json = json_encode($deals);
            return true;
        }

        $this->export->build(collect($deals), $export_columns);

        $this->export->notifyUser('deal');

        return true;
    }

    public function getExportColumns()
    {
        return $this->export_columns;
    }

    public function transformObject($object)
    {
        return $this->transformDeal($object);
    }

    public function getContent()
    {
        return $this->export->getContent();
    }

    public function getTemplate()
    {
        return asset('storage/templates/deal.csv');
    }

    /**
     * @return string
     */
    public function getJson(): string
    {
        return $this->json;
    }
}

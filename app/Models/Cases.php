<?php


namespace App\Models;


use App\Services\Cases\CasesService;
use App\Traits\Archiveable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

class Cases extends Model
{
    use SoftDeletes;
    use PresentableTrait;
    use HasFactory;
    use Archiveable;

    const STATUS_DRAFT = 1;
    const STATUS_OPEN = 2;
    const STATUS_CLOSED = 3;
    const STATUS_MERGED = 4;
    const PRIORITY_LOW = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH = 3;
    const CASE_LINK_TYPE_PRODUCT = 1;
    const CASE_LINK_TYPE_PROJECT = 2;
    protected $fillable = [
        'status_id',
        'priority_id',
        'category_id',
        'due_date',
        'date_opened',
        'date_closed',
        'opened_by',
        'closed_by',
        'private_notes',
        'subject',
        'number',
        'message',
        'user_id',
        'account_id',
        'customer_id',
        'contact_id',
        'assigned_to',
        'parent_id',
        'link_project_value',
        'link_product_value',
        'custom_value1',
        'custom_value2',
        'custom_value3',
        'custom_value4'
    ];
    protected $presenter = 'App\Presenters\CasesPresenter';
    private $arrStatuses = [
        1 => 'Draft',
        2 => 'Open',
        3 => 'Closed'
    ];

    private $arrPriorities = [
        1 => 'Low',
        2 => 'Medium',
        3 => 'High'
    ];

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    /**
     * @return BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(CaseCategory::class)->withTrashed();
    }

    public function service(): CasesService
    {
        return new CasesService($this);
    }

    /**
     * @return mixed
     */
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer')->withTrashed();
    }

    public function emails()
    {
        return Email::whereEntity(get_class($this))->whereEntityId($this->id)->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function assignee()
    {
        return $this->hasOne(User::class, 'id', 'assigned_to');
    }

    /**
     * @return mixed
     */
    public function invitations()
    {
        return $this->morphMany(Invitation::class, 'inviteable')->orderBy('contact_id');
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer_id = $customer->id;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user_id = $user->id;
    }

    /**
     * @param Account $account
     */
    public function setAccount(Account $account)
    {
        $this->account_id = $account->id;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status)
    {
        $this->status_id = $status;
    }

    public function setNumber()
    {
        if (empty($this->number) || !isset($this->id)) {
            $this->number = (new NumberGenerator)->getNextNumberForEntity($this, $this->customer);
            return true;
        }

        return true;
    }

    public function getDesignId()
    {
        return !empty($this->design_id) ? $this->design_id : $this->customer->getSetting('case_design_id');
    }

    public function getPdfFilename()
    {
        return 'storage/' . $this->account->id . '/' . $this->customer->id . '/cases/' . $this->number . '.pdf';
    }

    public function getStatusName()
    {
        return $this->arrStatuses[$this->status_id];
    }

    public function getPriorityName()
    {
        return $this->arrPriorities[$this->priority_id];
    }
}

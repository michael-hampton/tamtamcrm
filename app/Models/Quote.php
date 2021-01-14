<?php

namespace App\Models;

use App\Services\Quote\QuoteService;
use App\Traits\Archiveable;
use App\Traits\Balancer;
use App\Traits\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

class Quote extends Model
{
    use SoftDeletes;
    use PresentableTrait;
    use Money;
    use Balancer;
    use HasFactory;
    use Archiveable;

    const STATUS_DRAFT = 1;
    const STATUS_SENT = 2;
    const STATUS_VIEWED = 3;
    const STATUS_CONVERTED = 5;
    const STATUS_APPROVED = 4;
    const STATUS_EXPIRED = -1;
    const STATUS_REJECTED = 6;
    const STATUS_CHANGE_REQUESTED = 7;

    const PRODUCT_TYPE = 1;
    const COMMISSION_TYPE = 2;
    const TASK_TYPE = 3;
    const LATE_FEE_TYPE = 4;
    const SUBSCRIPTION_TYPE = 5;
    const EXPENSE_TYPE = 6;
    const GATEWAY_FEE_TYPE = 7;

    protected $presenter = 'App\Presenters\QuotePresenter';

    protected $casts = [
        'customer_id' => 'integer',
        'account_id'  => 'integer',
        'user_id'     => 'integer',
        'line_items'  => 'object',
        'updated_at'  => 'timestamp',
        'deleted_at'  => 'timestamp',
        'is_deleted'  => 'boolean',
        'viewed'      => 'boolean'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'customer_id',
        'project_id',
        'assigned_to',
        'order_id',
        'total',
        'sub_total',
        'tax_total',
        'tax_rate',
        'tax_2',
        'tax_3',
        'tax_rate_name_2',
        'tax_rate_name_3',
        'tax_rate_name',
        'discount_total',
        'payment_type',
        'due_date',
        'status_id',
        'finance_type',
        'created_at',
        'public_notes',
        'private_notes',
        'terms',
        'footer',
        'partial',
        'partial_due_date',
        'date',
        'balance',
        'line_items',
        'company_id',
        'task_id',
        'custom_value1',
        'custom_value2',
        'custom_value3',
        'custom_value4',
        'transaction_fee',
        'shipping_cost',
        'transaction_fee_tax',
        'shipping_cost_tax',
        'number',
        'invoice_type_id',
        'is_amount_discount',
        'po_number',
        'design_id',
        'gateway_fee',
        'gateway_percentage',
    ];

    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function audits()
    {
        return $this->hasManyThrough(Audit::class, Notification::class, 'entity_id')->where(
            'entity_class',
            '=',
            get_class($this)
        )->orderBy('created_at', 'desc');
    }

    public function emails()
    {
        return Email::whereEntity(get_class($this))->whereEntityId($this->id)->get();
    }

    /**
     * @return mixed
     */
    public function invitations()
    {
        return $this->morphMany(Invitation::class, 'inviteable')->orderBy('contact_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function service(): QuoteService
    {
        return new QuoteService($this);
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function recurring_quote()
    {
        return $this->belongsTo(RecurringQuote::class, 'recurring_quote_id', 'id');
    }

    /********************** Getters and setters ***********************************
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user_id = (int)$user->id;
    }

    public function setDueDate()
    {
        $this->due_date = !empty($this->customer->getSetting('payment_terms')) ? Carbon::now()->addDays(
            $this->customer->getSetting('payment_terms')
        )->format('Y-m-d H:i:s') : null;
    }

    public function setAccount(Account $account)
    {
        $this->account_id = (int)$account->id;
    }

    public function setCustomer(Customer $customer)
    {
        $this->customer_id = (int)$customer->id;
    }

    public function setStatus(int $status)
    {
        $this->status_id = $status;
    }

    public function setInvoiceId($invoice_id)
    {
        $this->invoice_id = $invoice_id;
    }

    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;
    }

    public function setNumber()
    {
        if (empty($this->number)) {
            $this->number = (new NumberGenerator)->getNextNumberForEntity($this, $this->customer);
            return true;
        }

        return true;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getDesignId()
    {
        return !empty($this->design_id) ? $this->design_id : $this->customer->getSetting('quote_design_id');
    }

    public function getPdfFilename()
    {
        return 'storage/' . $this->account->id . '/' . $this->customer->id . '/quotes/' . $this->number . '.pdf';
    }

    public function canBeSent()
    {
        return $this->status_id === self::STATUS_DRAFT;
    }
}

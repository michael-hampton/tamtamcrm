<?php

namespace App\Models;

use App\Services\Credit\CreditService;
use App\Services\Transaction\TransactionService;
use App\Traits\Archiveable;
use App\Traits\Balancer;
use App\Traits\Money;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

class Credit extends Model
{
    use SoftDeletes;
    use PresentableTrait;
    use Money;
    use Balancer;
    use HasFactory;
    use Archiveable;

    const STATUS_DRAFT = 1;
    const STATUS_SENT = 2;
    const STATUS_PARTIAL = 3;
    const STATUS_APPLIED = 4;
    const STATUS_VIEWED = 5;

    const PRODUCT_TYPE = 1;
    const COMMISSION_TYPE = 2;
    const TASK_TYPE = 3;
    const LATE_FEE_TYPE = 4;
    const SUBSCRIPTION_TYPE = 5;
    const EXPENSE_TYPE = 6;
    const GATEWAY_FEE_TYPE = 7;
    const PAYMENT_TYPE = 12;

    protected $presenter = 'App\Presenters\CreditPresenter';
    /**
     * @var array
     */
    protected $fillable = [
        'number',
        'project_id',
        'customer_id',
        'assigned_to',
        'total',
        'balance',
        'sub_total',
        'tax_total',
        'tax_2',
        'tax_3',
        'tax_rate_name_2',
        'tax_rate_name_3',
        'tax_rate',
        'tax_rate_name',
        'discount_total',
        'is_amount_discount',
        'due_date',
        'status_id',
        'created_at',
        'line_items',
        'po_number',
        'public_notes',
        'private_notes',
        'terms',
        'footer',
        'partial',
        'partial_due_date',
        'date',
        'balance',
        'custom_value1',
        'custom_value2',
        'custom_value3',
        'custom_value4',
        'transaction_fee',
        'shipping_cost',
        'gateway_fee',
        'gateway_percentage',
        'transaction_fee_tax',
        'shipping_cost_tax',
        'design_id'
    ];
    protected $casts = [
        'account_id'  => 'integer',
        'user_id'     => 'integer',
        'customer_id' => 'integer',
        'line_items'  => 'object',
        'updated_at'  => 'timestamp',
        'deleted_at'  => 'timestamp',
        'viewed'      => 'boolean'
    ];

    /**
     * @return BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function transaction_service()
    {
        return new TransactionService($this);
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function service(): CreditService
    {
        return new CreditService($this);
    }

    public function invitations()
    {
        return $this->morphMany(Invitation::class, 'inviteable')->orderBy('contact_id');
    }

    public function payments()
    {
        return $this->morphToMany(Payment::class, 'paymentable');
    }

    /**
     * @return mixed
     */
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer')->withTrashed();
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

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function reversePaymentsForCredit($total_paid): ?bool
    {
        $paymentable = $this->paymentables()->first();
        $paymentable->amount = $total_paid;
        $paymentable->save();

        return true;
    }

    /************* Paymentables ******************************/
    public function paymentables()
    {
        $paymentables = Paymentable::wherePaymentableType(self::class)
                                   ->wherePaymentableId($this->id);

        return $paymentables;
    }

    /********************** Getters and setters ************************************/

    public function setDueDate()
    {
        $this->due_date = !empty($this->customer->getSetting('payment_terms')) ? Carbon::now()->addDays(
            $this->customer->getSetting('payment_terms')
        )->format('Y-m-d H:i:s') : null;
    }

    public function setStatus(int $status)
    {
        $this->status_id = $status;
    }

    public function setUser(User $user)
    {
        $this->user_id = (int)$user->id;
    }

    public function setAccount(Account $account)
    {
        $this->account_id = (int)$account->id;
    }

    /**
     * @param Invoice $invoice
     */
    public function setInvoiceId(Invoice $invoice)
    {
        $this->invoice_id = $invoice->id;
    }

    public function setCustomer(Customer $customer)
    {
        $this->customer_id = (int)$customer->id;
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
        return !empty($this->design_id) ? $this->design_id : $this->customer->getSetting('credit_design_id');
    }

    public function getPdfFilename()
    {
        return 'storage/' . $this->account->id . '/' . $this->customer->id . '/credits/' . $this->number . '.pdf';
    }

    public function canBeSent()
    {
        return $this->status_id === self::STATUS_DRAFT;
    }

}

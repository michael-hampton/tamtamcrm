<?php

namespace App\Models;

use App\Services\Invoice\CancelInvoice;
use App\Services\Transaction\TriggerTransaction;
use App\Models\Concerns\QueryScopes;
use App\Traits\Archiveable;
use App\Traits\Balancer;
use App\Traits\CalculateDates;
use App\Traits\Money;
use App\Traits\Taxable;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Invoice extends Model
{

    use SoftDeletes, Money, Balancer, HasFactory, Archiveable, Taxable, QueryCacheable, QueryScopes, CalculateDates;

    const STATUS_DRAFT = 1;
    const STATUS_SENT = 2;
    const STATUS_PAID = 3;
    const STATUS_PARTIAL = 4;
    const STATUS_CANCELLED = 5;
    const STATUS_REVERSED = 6;
    const STATUS_VIEWED = 7;

    const PRODUCT_TYPE = 1;
    const COMMISSION_TYPE = 2;
    const TASK_TYPE = 3;
    const LATE_FEE_TYPE = 4;
    const SUBSCRIPTION_TYPE = 5;
    const EXPENSE_TYPE = 6;
    const PROJECT_TYPE = 9;
    const GATEWAY_FEE_TYPE = 7;
    const PROMOCODE_TYPE = 8;

    protected static $flushCacheOnUpdate = true;

    protected $casts = [
        'customer_id' => 'integer',
        'account_id'  => 'integer',
        'user_id'     => 'integer',
        'line_items'  => 'object',
        'updated_at'  => 'timestamp',
        'deleted_at'  => 'timestamp',
        'hide'        => 'boolean',
        'viewed'      => 'boolean'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number',
        'customer_id',
        'assigned_to',
        'total',
        'order_id',
        'balance',
        'amount_paid',
        'sub_total',
        'tax_total',
        'tax_rate',
        'tax_2',
        'tax_3',
        'tax_rate_name_2',
        'tax_rate_name_3',
        'tax_rate_name',
        'discount_total',
        'is_amount_discount',
        'payment_type',
        'due_date',
        'status_id',
        'created_at',
        'start_date',
        'line_items',
        'po_number',
        'expiry_date',
        'frequency',
        'recurring_due_date',
        'customer_note',
        'internal_note',
        'terms',
        'footer',
        'partial',
        'partial_due_date',
        'date',
        'balance',
        'is_recurring',
        'task_id',
        'company_id',
        'custom_value1',
        'custom_value2',
        'custom_value3',
        'custom_value4',
        'transaction_fee',
        'gateway_fee',
        'shipping_cost',
        'transaction_fee_tax',
        'shipping_cost_tax',
        'design_id',
        'voucher_code',
        'commission_paid',
        'commission_paid_date',
        'gateway_fee',
        'gateway_percentage',
        'recurring_invoice_id',
        'late_fee_reminder',
        'project_id',
        'plan_subscription_id',
        'exchange_rate',
        'auto_billing_enabled'
    ];
    protected $dates = [
        'date_to_send',
    ];

    /**
     * When invalidating automatically on update, you can specify
     * which tags to invalidate.
     *
     * @return array
     */
    public function getCacheTagsToInvalidateOnUpdate(): array
    {
        return [
            'invoices',
            'dashboard_invoices'
        ];
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function deleteInvoice(): bool
    {
        (new CancelInvoice($this, true))->execute();
        $this->deleteEntity();

        return true;
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

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

    public function payments()
    {
        return $this->morphToMany(Payment::class, 'paymentable')->withPivot('amount', 'refunded')->withTimestamps();
    }

    public function emails()
    {
        return Email::whereEntity(get_class($this))->whereEntityId($this->id)->get();
    }

    public function recurring_invoice()
    {
        return $this->belongsTo(RecurringInvoice::class, 'recurring_invoice_id', 'id');
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    public function audits()
    {
        return $this->hasManyThrough(Audit::class, Notification::class, 'entity_id')->where(
            'entity_class',
            '=',
            get_class($this)
        )->orderBy('created_at', 'desc');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    /**
     * @return mixed
     */
    public function invitations()
    {
        return $this->morphMany(Invitation::class, 'inviteable')->orderBy('contact_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function isCancellable(): bool
    {
        return in_array(
                $this->status_id,
                [self::STATUS_SENT, self::STATUS_PARTIAL]
            ) && !$this->trashed();
    }

    public function isReversable(): bool
    {
        return in_array(
                $this->status_id,
                [self::STATUS_SENT, self::STATUS_PARTIAL, self::STATUS_PAID]
            ) && !$this->trashed();
    }

    public function isLocked()
    {
        return ($this->customer->getSetting(
                    'should_lock_invoice'
                ) === 'when_sent' && $this->status_id === self::STATUS_SENT) || ($this->customer->getSetting(
                    'should_lock_invoice'
                ) === 'when_paid' && $this->status_id === self::STATUS_PAID);
    }

    public function resetBalance($amount): bool
    {
        $this->increaseBalance($amount);

        $status = $this->total == $this->balance ? Invoice::STATUS_SENT : Invoice::STATUS_PARTIAL;

        $this->setStatus($status);
        $this->save();

        return true;
    }

    /********************** Getters and setters ***********************************
     * @param int $status
     */

    public function setStatus(int $status)
    {
        $this->status_id = $status;
    }

    public function reversePaymentsForInvoice()
    {
        $total_paid = $this->total - $this->balance;

        foreach ($this->paymentables() as $paymentable) {
            $reversable_amount = $paymentable->amount - $paymentable->refunded;

            $total_paid -= $reversable_amount;

            $paymentable->amount = $paymentable->refunded;
            $paymentable->save();
        }

        return $total_paid;
    }

    /************** Paymentables **************************/

    public function paymentables()
    {
        $paymentables = Paymentable::wherePaymentableType(self::class)
                                   ->wherePaymentableId($this->id);

        return $paymentables;
    }

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

    public function setDateCancelled()
    {
        $this->date_cancelled = Carbon::now();
    }

    public function setAccount(Account $account)
    {
        $this->account_id = (int)$account->id;
    }

    public function setCustomer(Customer $customer)
    {
        $this->customer_id = (int)$customer->id;
    }

    public function cacheData(): bool
    {
        $cached_data = [
            'balance'     => $this->balance,
            'status_id'   => $this->status_id,
            'amount_paid' => $this->amount_paid
        ];

        $this->cached_data = json_encode($cached_data);
        $this->save();

        return true;
    }

    public function rewindCache(): bool
    {
        $cached_data = json_decode($this->cached_data, true);

        if (!empty($cached_data['balance'])) {
            $this->updateCustomerBalance(floatval($cached_data['balance']));
            $this->setBalance(floatval($cached_data['balance']));
        }

        $this->setStatus($cached_data['status_id']);

        if (!empty($cached_data['amount_paid'])) {
            $this->setAmountPaid($cached_data['amount_paid']);
        }

        $this->cached_data = null;
        $this->save();

        return true;
    }

    /**
     * @param $amount
     * @return Customer
     */
    public function updateCustomerBalance($amount): Customer
    {
        $customer = $this->customer->fresh();
        $customer->increaseBalance($amount);
        $customer->save();

        if ($this->id) {
            (new TriggerTransaction($this))->execute(
                $amount,
                $customer->balance,
                "Customer Balance update for invoice {$this->getNumber()}"
            );
        }

        return $customer;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function setNumber()
    {
        if (empty($this->number)) {
            $this->number = (new NumberGenerator)->getNextNumberForEntity($this, $this->customer);
            return true;
        }

        return true;
    }

    public function setExchangeRateAttribute($value)
    {
        $this->attributes['exchange_rate'] = $value;
    }

    public function setCurrencyAttribute($value)
    {
        $this->attributes['currency_id'] = (int) $value;
    }

    public function getDesignIdAttribute()
    {
        return !empty($this->design_id) ? $this->design_id : $this->customer->getSetting('invoice_design_id');
    }

    public function getPdfFilenameAttribute()
    {
        return 'storage/' . $this->account->id . '/' . $this->customer->id . '/invoices/' . $this->number . '.pdf';
    }

    public function canBeSent()
    {
        return $this->status_id === self::STATUS_DRAFT;
    }


    public function scopeSubscriptions($query, PlanSubscription $plan_subscription)
    {
        $query->where('plan_subscription_id', '=', $plan_subscription->id);
    }

    public function scopeHasBalance($query)
    {
        $query->where('balance', '>', 0);
    }

    public function scopePaid($query)
    {
        $query->where('balance', '=', 0);
    }

    public function scopePermissions($query, User $user)
    {
        if ($user->isAdmin() || $user->isOwner() || $user->hasPermissionTo('invoicecontroller.index')) {
            return $query;
        }

        $query->where(
            function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('assigned_to', auth()->user($user)->id);
            }
        );
    }
}

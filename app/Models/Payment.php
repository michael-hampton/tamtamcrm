<?php

namespace App\Models;

use App\Actions\Transaction\TriggerTransaction;
use App\Events\Payment\PaymentWasDeleted;
use App\Models\Concerns\QueryScopes;
use App\Traits\Archiveable;
use App\Traits\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Payment extends Model
{
    use SoftDeletes;
    use Money;
    use HasFactory;
    use Archiveable;
    use QueryCacheable;
    use QueryScopes;

    const STATUS_PENDING = 1;
    const STATUS_VOIDED = 2;
    const STATUS_FAILED = 3;
    const STATUS_COMPLETED = 4;
    const STATUS_PARTIALLY_REFUNDED = 5;
    const STATUS_REFUNDED = 6;

    const TYPE_CUSTOMER_CREDIT = 2;
    protected static $flushCacheOnUpdate = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_method_id',
        'date',
        'number',
        'amount',
        'customer_id',
        'assigned_to',
        'status_id',
        'company_gateway_id',
        'refunded',
        'reference_number',
        'is_manual',
        'private_notes',
        'custom_value1',
        'custom_value2',
        'custom_value3',
        'custom_value4'
    ];
    protected $casts = [
        'exchange_rate' => 'float',
        'updated_at'    => 'timestamp',
        'deleted_at'    => 'timestamp',
        'hide'          => 'boolean',
    ];
    protected $with = [
        'paymentables',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * When invalidating automatically on update, you can specify
     * which tags to invalidate.
     *
     * @return array
     */
    public function getCacheTagsToInvalidateOnUpdate(): array
    {
        return [
            'payments',
            'dashboard_payments'
        ];
    }

    /**
     * @return BelongsTo
     */
    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function paymentables()
    {
        return $this->hasMany(Paymentable::class);
    }

    /**
     * @return BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function gateway()
    {
        return $this->belongsTo(CompanyGateway::class, 'company_gateway_id');
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * @param Invoice $invoice
     * @param float|null $amount
     * @param bool $send_transaction
     * @return $this
     */
    public function attachInvoice(Invoice $invoice, float $amount = null, $send_transaction = false): Payment
    {
        $this->invoices()->attach(
            $invoice->id,
            [
                'amount' => $amount === null ? $this->amount : $amount
            ]
        );

        if ($send_transaction && $amount !== null) {
            (new TriggerTransaction($invoice))->execute($amount * -1, $invoice->customer->balance);
        }

        return $this;
    }

    public function invoices()
    {
        return $this->morphedByMany(Invoice::class, 'paymentable')->withPivot('amount')->withTrashed();
    }

    /**
     * @param Credit $credit
     * @param $amount
     * @return $this
     */
    public function attachCredit(Credit $credit, $amount): Payment
    {
        $this->credits()->attach(
            $credit->id,
            [
                'amount' => $amount
            ]
        );

        return $this;
    }

    public function credits()
    {
        return $this->morphedByMany(Credit::class, 'paymentable')->withPivot('amount', 'refunded')->withTimestamps();
    }

    public function deletePayment(): bool
    {
        $this->hide = true;
        $this->save();

        $this->delete();

        event(new PaymentWasDeleted($this));

        return true;
    }

    /********************** Getters and setters ***********************************
     * @param int $status
     */

    public function setStatus(int $status)
    {
        $this->status_id = $status;
    }

    public function setNumber()
    {
        if (empty($this->number)) {
            $this->number = (new NumberGenerator)->getNextNumberForEntity($this, $this->customer);
            return true;
        }

        return true;
    }

    public function getFormattedTotal()
    {
        return $this->formatCurrency($this->amount, $this->customer);
    }

    public function setExchangeRateAttribute($value)
    {
        $this->attributes['exchange_rate'] = $value;
    }

    public function setCurrencyAttribute($value)
    {
        $this->attributes['currency_id'] = (int) $value;
    }

    public function getFormattedInvoices()
    {
        $invoice_texts = trans('texts.invoice_number_abbreviated');

        foreach ($this->invoices as $invoice) {
            $invoice_texts .= $invoice->number . '<br>';
        }

        return $invoice_texts;
    }

    public function reduceAmount($amount)
    {
        $this->amount -= $amount;
        $this->applied -= $amount;
        $this->save();
    }

    public function getUrl()
    {
        $url = $this->account->portal_domain;

        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }

        $url = rtrim($url, '/') . '/portal/payments/' . $this->id;

        return $url;
    }

    public function scopePermissions($query, User $user)
    {
        if ($user->isAdmin() || $user->isOwner() || $user->hasPermissionTo('paymentcontroller.index')) {
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

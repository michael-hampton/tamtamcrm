<?php

namespace App\Models;

use App\Events\Payment\PaymentWasDeleted;
use App\Services\Payment\PaymentService;
use App\Services\Transaction\TransactionService;
use App\Traits\Archiveable;
use App\Traits\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

class Payment extends Model
{
    use PresentableTrait;
    use SoftDeletes;
    use Money;
    use HasFactory;
    use Archiveable;

    const STATUS_PENDING = 1;
    const STATUS_VOIDED = 2;
    const STATUS_FAILED = 3;
    const STATUS_COMPLETED = 4;
    const STATUS_PARTIALLY_REFUNDED = 5;
    const STATUS_REFUNDED = 6;

    const TYPE_CUSTOMER_CREDIT = 2;

    protected $presenter = 'App\Presenters\OrderPresenter';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type_id',
        'date',
        'number',
        'type_id',
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
        'is_deleted'    => 'boolean',
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
     * @return BelongsTo
     */
    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, 'type_id');
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

    public function service(): PaymentService
    {
        return new PaymentService($this);
    }

    public function transaction_service()
    {
        return new TransactionService($this);
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
            $invoice->transaction_service()->createTransaction($amount * -1, $invoice->customer->balance);
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
        $this->is_deleted = true;
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

    public function getFormattedInvoices()
    {
        $invoice_texts = trans('texts.invoice_number_abbreviated');

        foreach ($this->invoices as $invoice) {
            $invoice_texts .= $invoice->number . ',';
        }

        return substr($invoice_texts, 0, -1);
    }

    public function reduceAmount($amount)
    {
        $this->amount -= $amount;
        $this->applied -= $amount;
        $this->save();
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: michael.hampton
 * Date: 22/12/2019
 * Time: 13:02
 */

namespace App\Models;


use App\Services\Order\OrderService;
use App\Traits\Archiveable;
use App\Traits\Balancer;
use App\Traits\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Order
 * @package App
 */
class Order extends Model
{
    use PresentableTrait;
    use SoftDeletes;
    use Money;
    use Balancer;
    use HasFactory;
    use Archiveable;

    const STATUS_DRAFT = 1;
    const STATUS_SENT = 2;
    const STATUS_COMPLETE = 3;
    const STATUS_APPROVED = 4;
    const STATUS_HELD = 5;
    const STATUS_BACKORDERED = 6;
    const STATUS_PARTIAL = 7;
    const STATUS_CANCELLED = 8;
    const STATUS_ORDER_FAILED = 9;
    const STATUS_PAID = 10;
    const STATUS_VIEWED = 11;

    const STATUS_EXPIRED = -1;

    protected $presenter = 'App\Presenters\OrderPresenter';
    protected $casts = [
        'account_id'    => 'integer',
        'user_id'       => 'integer',
        'customer_id'   => 'integer',
        'line_items'    => 'object',
        'updated_at'    => 'timestamp',
        'deleted_at'    => 'timestamp',
        'is_deleted'    => 'boolean',
        'payment_taken' => 'boolean',
        'viewed'        => 'boolean'
    ];

    protected $fillable = [
        'number',
        'customer_id',
        'project_id',
        'total',
        'balance',
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
        'due_date',
        'status_id',
        'created_at',
        'line_items',
        'public_notes',
        'private_notes',
        'terms',
        'footer',
        'partial',
        'partial_due_date',
        'date',
        'balance',
        'task_id',
        'custom_value1',
        'custom_value2',
        'custom_value3',
        'custom_value4',
        'transaction_fee',
        'gateway_fee',
        'gateway_percentage',
        'shipping_cost',
        'transaction_fee_tax',
        'shipping_cost_tax',
        'design_id',
        'shipping_id',
        'shipping_label_url',
        'previous_status',
        'voucher_code',
        'assigned_to'
    ];

    protected $table = 'product_task';

    public function service(): OrderService
    {
        return new OrderService($this);
    }

    public function task()
    {
        return $this->belongsTo('App\Models\Task');
    }

    public function emails()
    {
        return Email::whereEntity(get_class($this))->whereEntityId($this->id)->get();
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function audits()
    {
        return $this->hasManyThrough(Audit::class, Notification::class, 'entity_id')->where(
            'entity_class',
            '=',
            get_class($this)
        )->orderBy('created_at', 'desc');
    }

    /**
     * @return mixed
     */
    public function invitations()
    {
        return $this->morphMany(Invitation::class, 'inviteable')->orderBy('contact_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /********************** Getters and setters ************************************/
    public function setDueDate()
    {
        $this->due_date = !empty($this->customer->getSetting('payment_terms')) ? Carbon::now()->addDays(
            $this->customer->getSetting('payment_terms')
        )->format('Y-m-d H:i:s') : null;
    }

    public function setUser(User $user)
    {
        $this->user_id = (int)$user->id;
    }

    public function setAccount(Account $account)
    {
        $this->account_id = (int)$account->id;
    }

    public function setCustomer(Customer $customer)
    {
        $this->customer_id = (int)$customer->id;
    }

    /**
     * @param int $status
     */
    public function setPreviousStatus(int $status)
    {
        $this->previous_status = (int)$status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status)
    {
        $this->status_id = (int)$status;
    }

    public function setDateCancelled()
    {
        $this->date_cancelled = Carbon::now();
    }

    /**
     * @param $invoice_id
     */
    public function setInvoiceId($invoice_id)
    {
        $this->invoice_id = (int)$invoice_id;
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
        return !empty($this->design_id) ? $this->design_id : $this->customer->getSetting('order_design_id');
    }

    public function getPdfFilename()
    {
        return 'storage/' . $this->account->id . '/' . $this->customer->id . '/orders/' . $this->number . '.pdf';
    }

    public function canBeSent()
    {
        return in_array($this->status_id, [self::STATUS_DRAFT, self::STATUS_PARTIAL, self::STATUS_COMPLETE]);
    }
}

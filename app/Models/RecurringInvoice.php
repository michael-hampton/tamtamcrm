<?php

namespace App\Models;

use App\Models;
use App\Services\RecurringInvoice\RecurringInvoiceService;
use App\Traits\Archiveable;
use App\Traits\Balancer;
use App\Traits\CalculateRecurringDateRanges;
use App\Traits\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class for Recurring Invoices.
 */
class RecurringInvoice extends Model
{
    use SoftDeletes;
    use CalculateRecurringDateRanges;
    use PresentableTrait;
    use Balancer;
    use Money;
    use HasFactory;
    use Archiveable;

    const STATUS_DRAFT = 1;
    const STATUS_PENDING = 2;
    const STATUS_ACTIVE = 3;
    const STATUS_STOPPED = 4;
    const STATUS_COMPLETED = 5;
    const STATUS_VIEWED = 6;

    protected $presenter = 'App\Presenters\InvoicePresenter';
    protected $fillable = [
        'is_never_ending',
        'status_id',
        'account_id',
        'customer_id',
        'project_id',
        'number',
        'total',
        'sub_total',
        'tax_total',
        'tax_2',
        'tax_3',
        'tax_rate_name_2',
        'tax_rate_name_3',
        'discount_total',
        'partial_due_date',
        'is_amount_discount',
        'po_number',
        'date',
        'due_date',
        'line_items',
        'footer',
        'public_notes',
        'private_notes',
        'terms',
        'total',
        'partial',
        'frequency',
        'start_date',
        'expiry_date',
        'due_date',
        'grace_period',
        'auto_billing_enabled',
        'number_of_occurrances',
        'custom_value1',
        'custom_value2',
        'custom_value3',
        'custom_value4',
        'tax_rate_name',
        'tax_rate',
        'settings',
    ];

    protected $casts = [
        'line_items' => 'object',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
        'viewed'     => 'boolean',
        'is_deleted' => 'boolean',
    ];

    protected $dates = [
        'date_to_send',
        'last_sent_date',
        'start_date',
        'expiry_date'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assigned_user()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id')->withTrashed();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, "recurring_invoice_id", "id")->withTrashed();
    }

    public function invitations()
    {
        return $this->morphMany(Invitation::class, 'inviteable')->orderBy('contact_id');
    }

    public function service(): RecurringInvoiceService
    {
        return new RecurringInvoiceService($this);
    }

    public function account()
    {
        return $this->belongsTo(Models\Account::class);
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

    public function setNumber()
    {
        if (!empty($this->number)) {
            return true;
        }

        $this->number = (new NumberGenerator)->getNextNumberForEntity($this, $this->customer);
        return true;
    }

    public function setDueDate()
    {
        if (!empty($this->grace_period)) {
            $this->due_date = Carbon::now()->addDays($this->grace_period)->format('Y-m-d H:i:s');
            return true;
        }

        $this->due_date = !empty($this->customer->getSetting('payment_terms')) ? Carbon::now()->addDays(
            $this->customer->getSetting('payment_terms')
        )->format('Y-m-d H:i:s') : null;

        return true;
    }

    public function getPdfFilename()
    {
        return 'storage/' . $this->account->id . '/' . $this->customer->id . '/recurring_invoices/' . $this->number . '.pdf';
    }

    public function getDesignId()
    {
        return !empty($this->design_id) ? $this->design_id : $this->customer->getSetting('invoice_design_id');
    }
}

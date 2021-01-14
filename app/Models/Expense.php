<?php

namespace App\Models;

use App\Models;
use App\Services\Expense\ExpenseService;
use App\Traits\Archiveable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes, HasFactory, Archiveable;

    const STATUS_LOGGED = 1;
    const STATUS_PENDING = 2;
    const STATUS_INVOICED = 3;
    const STATUS_APPROVED = 4;

    protected $fillable = [
        'assigned_to',
        'number',
        'customer_id',
        'status_id',
        'company_id',
        'currency_id',
        'project_id',
        'invoice_id',
        'date',
        'invoice_currency_id',
        'amount',
        'converted_amount',
        'exchange_rate',
        'public_notes',
        'private_notes',
        'bank_id',
        'transaction_id',
        'expense_category_id',
        'tax_rate',
        'tax_rate_name',
        'tax_2',
        'tax_3',
        'tax_rate_name_2',
        'tax_rate_name_3',
        'payment_date',
        'payment_type_id',
        'reference_number',
        'include_documents',
        'create_invoice',
        'custom_value1',
        'custom_value2',
        'custom_value3',
        'custom_value4',
        'is_recurring',
        'recurring_start_date',
        'recurring_end_date',
        'recurring_due_date',
        'last_sent_date',
        'next_send_date',
        'recurring_frequency',
        'invoice_id',
        'tax_is_amount',
        'amount_includes_tax'
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];

    public function service(): ExpenseService
    {
        return new ExpenseService($this);
    }


    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function assigned_user()
    {
        return $this->belongsTo(Models\User::class, 'assigned_to', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    /**
     * @return mixed
     */
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer')->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class)->withTrashed();
    }

    public function setNumber()
    {
        if (empty($this->number) || !isset($this->id)) {
            $this->number = (new NumberGenerator)->getNextNumberForEntity($this, $this->customer);
            return true;
        }

        return true;
    }

    /**
     * @param int $status_id
     * @return bool
     */
    public function setStatus(int $status_id)
    {
        $this->status_id = $status_id;
        return true;
    }
}

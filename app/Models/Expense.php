<?php

namespace App\Models;

use App\Models;
use App\Models\Concerns\QueryScopes;
use App\Traits\Archiveable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Expense extends Model
{
    use SoftDeletes, HasFactory, Archiveable, QueryCacheable, QueryScopes;

    const STATUS_LOGGED = 1;
    const STATUS_PENDING = 2;
    const STATUS_INVOICED = 3;
    const STATUS_APPROVED = 4;
    protected static $flushCacheOnUpdate = true;
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
        'customer_note',
        'internal_note',
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
        'payment_method_id',
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
        'hide'       => 'boolean',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
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
            'expenses',
            'dashboard_expenses'
        ];
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

    public function scopePermissions($query, User $user)
    {
        if ($user->isAdmin() || $user->isOwner() || $user->hasPermissionTo('expensecontroller.index')) {
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

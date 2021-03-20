<?php

namespace App\Models;

use App\Traits\Archiveable;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Transaction extends Model
{
    use Archiveable;
    use QueryCacheable;

    protected $fillable = [
        'customer_id',
        'updated_balance',
        'amount',
        'notes',
        'account_id',
        'user_id'
    ];

    protected static $flushCacheOnUpdate = true;

    /**
     * When invalidating automatically on update, you can specify
     * which tags to invalidate.
     *
     * @return array
     */
    public function getCacheTagsToInvalidateOnUpdate(): array
    {
        return [
            'transactions',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function setUser(User $user)
    {
        $this->user_id = $user->id;
    }

    public function setAccount(Account $account)
    {
        $this->account_id = $account->id;
    }

    public function setCustomer(Customer $customer)
    {
        $this->customer_id = $customer->id;
    }

    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    public function setUpdatedBalance($updated_balance)
    {
        $this->updated_balance = $updated_balance;
    }

    public function setOriginalBalance()
    {
        $last = Transaction::where('customer_id', $this->customer_id)->orderBy('created_at', 'desc')->first();

        if (!empty($last)) {
            $this->original_customer_balance = $last->updated_balance;
        }
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getEntity()
    {
        $class_name = $this->transactionable_type;
        return $class_name::where('id', $this->transactionable_id)->cacheFor(now()->addMonthNoOverflow())->cacheTags(
            ['transactions']
        )->withTrashed()->first();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Domain extends Model
{
    use SoftDeletes;

    const SUBSCRIPTION_STANDARD = 1;
    const SUBSCRIPTION_ADVANCED = 2;
    const SUBSCRIPTION_FREE = 3;

    const SUBSCRIPTION_PERIOD_YEAR = 2;
    const SUBSCRIPTION_PERIOD_MONTH = 1;

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'customer_id',
        'default_account_id'
    ];

    /**
     * @return HasOne
     */
    public function default_company()
    {
        return $this->hasOne(Account::class, 'id', 'default_account_id');
    }

    /**
     * @return BelongsTo
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class)->withTrashed();
    }

    public function companies()
    {
        return $this->hasMany(Account::class);
    }

    public function company_users()
    {
        return $this->hasMany(AccountUser::class);
    }

    public function users()
    {
        return $this->hasMany(User::class)->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}

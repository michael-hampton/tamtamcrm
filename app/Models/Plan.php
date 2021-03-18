<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{

    const PLAN_STANDARD = 'STANDARD';
    const PLAN_ADVANCED = 'ADVANCED';
    const PLAN_TRIAL = 'TRIAL';
    const SUBSCRIPTION_FREE = 3;

    const PLAN_PERIOD_YEAR = 'YEARLY';
    const PLAN_PERIOD_MONTH = 'MONTHLY';

    protected $casts = [
        //'subscription_expiry_date' => 'date'
    ];

    protected $fillable = [
        'user_id',
        'customer_id',
        'domain_id',
        'expiry_date',
        'plan',
        'plan_period',
        'number_of_licences',
        'promocode',
        'promocode_applied',
        'due_date',
        'plan_started',
        'plan_ended',
        'is_active',
        'price_paid'
    ];

    protected $dates = [
        //'plan_started',
        //'expiry_date'
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function calculateCost()
    {
        if ($this->plan === self::PLAN_STANDARD) {
            $cost = $this->plan_period === self::PLAN_PERIOD_YEAR ? env(
                'STANDARD_YEARLY_ACCOUNT_PRICE'
            ) : env('STANDARD_MONTHLY_ACCOUNT_PRICE');
        } else {
            $cost = $this->plan_period === self::PLAN_PERIOD_YEAR ? env(
                'ADVANCED_YEARLY_ACCOUNT_PRICE'
            ) : env('ADVANCED_MONTHLY_ACCOUNT_PRICE');
        }

        return $cost;
    }
}

<?php

namespace App\Models;

use App\Models;
use App\Traits\Archiveable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laracasts\Presenter\PresentableTrait;

class CustomerContact extends Model implements ContactInterface
{
    use PresentableTrait;
    use SoftDeletes;
    use Notifiable;
    use HasFactory;
    use Archiveable;

    protected $presenter = 'App\Presenters\ClientContactPresenter';

    protected $dates = [
        'deleted_at'
    ];

    protected $casts = [
        'updated_at' => 'timestamp',
        'created_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];

    protected $hidden = [
        'user_id',
        'account_id',
        'customer_id',
        'token',
        'password',
    ];

    protected $fillable = [
        'contact_key',
        'first_name',
        'last_name',
        'phone',
        'custom_value1',
        'custom_value2',
        'custom_value3',
        'custom_value4',
        'email',
        'is_primary',
        'password'
    ];

    /**/
    public function getRouteKeyName()
    {
        return 'contact_id';
    }

    public function routeNotificationForMail($notification)
    {
        return $this->email;
    }

    public function preferredLocale()
    {
        return !empty($this->customer) ? $this->customer->locale() : 'en';
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function primary_contact()
    {
        return $this->where('is_primary', true);
    }

    public function account()
    {
        return $this->belongsTo(Models\Account::class);
    }

    public function user()
    {
        return $this->belongsTo(Models\User::class)->withTrashed();
    }
}

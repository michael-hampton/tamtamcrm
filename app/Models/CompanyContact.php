<?php

namespace App\Models;

use App\Models;
use App\Traits\Archiveable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laracasts\Presenter\PresentableTrait;

//use App\Notifications\ClientContactResetPassword as ResetPasswordNotification;
//use App\Notifications\ClientContactResetPassword;

class CompanyContact extends Model implements ContactInterface
{
    use Notifiable;
    use PresentableTrait;
    use SoftDeletes;
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

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'custom_value1',
        'custom_value2',
        'custom_value3',
        'custom_value4',
        'email',
        'is_primary',
    ];

    public function company()
    {
        return $this->belongsTo(Models\Company::class)->withTrashed();
    }

    public function primary_contact()
    {
        return $this->where('is_primary', true);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function user()
    {
        return $this->belongsTo(Models\User::class)->withTrashed();
    }

    public function preferredLocale()
    {
        return $this->company->locale();
    }

//    public function sendPasswordResetNotification($token)
//    {
//        $this->notify(new ClientContactResetPassword($token));
//    }
}

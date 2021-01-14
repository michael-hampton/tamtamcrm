<?php


namespace App\Models;

use App\Models;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    const CREDIT = 1;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'gateway_type_id'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Models\Payment::class);
    }

}

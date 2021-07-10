<?php
/**
 * Created by PhpStorm.
 * User: michael.hampton
 * Date: 08/12/2019
 * Time: 17:10
 */

namespace App\Models;

use App\Models\Concerns\QueryScopes;
use App\Traits\Archiveable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;

class BankAccount extends Model
{
    use SoftDeletes;
    use Archiveable;
    use HasFactory;
    use QueryScopes;

    protected $fillable = [
        'name',
        'assigned_to',
        'customer_note',
        'internal_note',
        'account_id',
        'username',
        'password',
        'parent_id',
        'user_id',
        'bank_id'
    ];

    public function children()
    {
        return $this->hasMany('App\Models\BankAccount', 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->hasOne('App\Models\BankAccount', 'id', 'parent_id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function setPasswordAttribute(string $value)
    {
        if (empty($value)) {
            return null;
        }

        $this->attributes['password'] = Hash::make($value);
    }
}

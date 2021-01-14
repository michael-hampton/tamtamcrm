<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyToken extends Model
{

    protected $fillable = [
        'account_id',
        'user_id',
        'domain_id',
        'user_id',
        'token',
        'name'
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}

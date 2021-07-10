<?php

namespace App\Models;

use App\Models\Concerns\QueryScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyToken extends Model
{
    use SoftDeletes;
    use QueryScopes;

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

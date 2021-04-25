<?php

namespace App\Models;

use App\Models\Concerns\QueryScopes;
use App\Traits\Archiveable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyGateway extends Model
{
    use HasFactory;
    use Archiveable;
    use QueryScopes;

    protected $casts = [
        //'fields'          => 'object',
        'charges'    => 'object',
        'settings'   => 'object',
        'updated_at' => 'timestamp',
        'created_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];
    protected $fillable = [
        'name',
        'description',
        'gateway_key',
        'accepted_credit_cards',
        'require_cvv',
        'fields',
        'settings',
        'charges',
        'custom_value1',
        'custom_value2',
        'custom_value3',
        'custom_value4',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function gateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'gateway_key', 'key');
    }

    public function getMode()
    {
        return isset($this->config->mode) ? $this->config->mode : 'Production';
    }

    public function error_logs()
    {
        return ErrorLog::where('entity', '=', $this->gateway_key)->get();
    }

    public function scopeByGatewayKey($query, string $gateway_key, Account $account)
    {
        return $query->where('gateway_key', '=', $gateway_key)->where('account_id', '=', $account->id);
    }
}

<?php

namespace App\Models;

use App\Traits\Archiveable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyGateway extends Model
{
    use HasFactory;
    use Archiveable;

    protected $casts = [
        //'fields'          => 'object',
        'fees_and_limits' => 'object',
        'config'          => 'object',
        'updated_at'      => 'timestamp',
        'created_at'      => 'timestamp',
        'deleted_at'      => 'timestamp',
    ];
    protected $fillable = [
        'name',
        'description',
        'gateway_key',
        'accepted_credit_cards',
        'require_cvv',
        'fields',
        'config',
        'fees_and_limits',
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

//    public function resolveRouteBinding($value)
//    {
//        return $this->where('id', $value)->firstOrFail();
//    }
}

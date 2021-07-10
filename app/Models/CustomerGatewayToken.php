<?php

namespace App\Models;

use App\Traits\Archiveable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerGatewayToken extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Archiveable;

    protected $fillable = [
        'customer_id',
        'token',
        'data',
        'gateway_type_id',
        'company_gateway_id',
        'customer_reference'
    ];

    public function setCustomer(Customer $customer)
    {
        $this->customer_id = $customer->id;
    }

    public function setUser(User $user)
    {
        $this->user_id = $user->id;
    }

    public function setAccount(Account $account)
    {
        $this->account_id = $account->id;
    }
}

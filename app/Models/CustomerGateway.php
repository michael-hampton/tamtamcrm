<?php


namespace App\Models;


use App\Traits\Archiveable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rennokki\QueryCache\Traits\QueryCacheable;

class CustomerGateway extends Model
{
    use SoftDeletes;
    use HasFactory;
    use Archiveable;
    use QueryCacheable;

    protected static $flushCacheOnUpdate = true;

    /**
     * When invalidating automatically on update, you can specify
     * which tags to invalidate.
     *
     * @return array
     */
    public function getCacheTagsToInvalidateOnUpdate(): array
    {
        return [
            'customer_gateway',
        ];
    }

    public function company_gateway()
    {
        return $this->hasOne(CompanyGateway::class, 'id', 'company_gateway_id');
    }
}

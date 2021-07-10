<?php

namespace App\Models;

use App\Models\Concerns\QueryScopes;
use App\Traits\Archiveable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rennokki\QueryCache\Traits\QueryCacheable;

/**
 * Class PaymentTerm.
 */
class PaymentTerms extends Model
{
    use SoftDeletes;
    use HasFactory;
    use Archiveable;
    use QueryCacheable;
    use QueryScopes;

    protected static $flushCacheOnUpdate = true;
    protected $fillable = ['name', 'number_of_days'];

    /**
     * When invalidating automatically on update, you can specify
     * which tags to invalidate.
     *
     * @return array
     */
    public function getCacheTagsToInvalidateOnUpdate(): array
    {
        return [
            'payment_terms',
        ];
    }
}

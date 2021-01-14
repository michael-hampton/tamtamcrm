<?php

namespace App\Models;

use App\Models;
use App\Traits\Inviteable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use ReflectionClass;

/**
 * Class Invitation.
 */
class Invitation extends Model
{

    use SoftDeletes;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'key',
        'contact_id'
    ];

    /**
     * @return mixed
     */
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer')->withTrashed();
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    /**
     * @return mixed
     */
    public function contact()
    {
        return $this->belongsTo(Models\CustomerContact::class, 'contact_id', 'id')->withTrashed();
    }

    public function company_contact()
    {
        return $this->belongsTo(Models\CompanyContact::class, 'contact_id', 'id')->withTrashed();
    }

    public function inviteable()
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function getLink()
    {
        return $this->account->subdomain . 'portal/' . $this->getSection() . '/' . $this->key;
    }

    public function getSection($plural = false)
    {
        $entity = strtolower((new ReflectionClass($this->inviteable))->getShortName());

        if ($plural) {
            return $this->pluralize(2, $entity);
        }

        return $entity;
    }

    public function pluralize($quantity, $singular, $plural = null)
    {
        if ($quantity == 1 || !strlen($singular)) {
            return $singular;
        }
        if ($plural !== null) {
            return $plural;
        }

        $last_letter = strtolower($singular[strlen($singular) - 1]);
        switch ($last_letter) {
            case 'y':
                return substr($singular, 0, -1) . 'ies';
            case 's':
                return $singular . 'es';
            default:
                return $singular . 's';
        }
    }

    public function markViewed()
    {
        $this->viewed_date = Carbon::now();
        $this->save();

        //update viewed flag
        $this->inviteable->viewed = true;
        $this->inviteable->save();

        return true;
    }

}

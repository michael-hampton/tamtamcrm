<?php

namespace App\Models;

use App\Models;
use App\Traits\Archiveable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Event extends Authenticatable
{
    use Notifiable, SoftDeletes, HasFactory, Archiveable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'beginDate',
        'endDate',
        'customer_id',
        'location',
        'event_type',
        'description',
        'created_by'
    ];

    /**
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(Models\User::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Models\Task::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Models\User::class, 'created_by');
    }
}

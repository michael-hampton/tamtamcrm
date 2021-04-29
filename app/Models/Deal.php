<?php

namespace App\Models;

use App\Models\Concerns\QueryScopes;
use App\Traits\Archiveable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deal extends Model
{

    use SoftDeletes;
    use HasFactory;
    use Archiveable;
    use QueryScopes;

    protected $fillable = [
        'order_id',
        'design_id',
        'name',
        'description',
        'is_completed',
        'assigned_to',
        'due_date',
        'project_id',
        'task_status_id',
        'created_by',
        'task_type',
        'customer_id',
        'rating',
        'valued_at',
        'parent_id',
        'source_type',
        'time_log',
        'is_running',
        'account_id',
        'custom_value1',
        'custom_value2',
        'custom_value3',
        'custom_value4',
        'public_notes',
        'private_notes',
        'column_color'
    ];

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function taskStatus()
    {
        return $this->belongsTo(TaskStatus::class);
    }

    public function getDesignIdAttribute()
    {
        return !empty($this->design_id) ? $this->design_id : $this->customer->getSetting('deal_design_id');
    }

    public function getPdfFilenameAttribute()
    {
        return 'storage/' . $this->account->id . '/' . $this->customer->id . '/deals/' . $this->number . '.pdf';
    }

    public function emails()
    {
        return Email::whereEntity(get_class($this))->whereEntityId($this->id)->get();
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function setNumber()
    {
        if (empty($this->number)) {
            $this->number = (new NumberGenerator)->getNextNumberForEntity($this, $this->customer);
            return true;
        }

        return true;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function scopePermissions($query, User $user)
    {
        if ($user->isAdmin() || $user->isOwner() || $user->hasPermissionTo('dealcontroller.index')) {
            return $query;
        }

        $query->where(
            function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('assigned_to', auth()->user($user)->id);
            }
        );
    }
}

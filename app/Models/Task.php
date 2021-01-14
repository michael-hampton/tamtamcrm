<?php

namespace App\Models;

use App\Models;
use App\Services\Task\TaskService;
use App\Traits\Archiveable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

class Task extends Model
{

    use SoftDeletes;
    use PresentableTrait;
    use HasFactory;
    use Archiveable;

    const TASK_TYPE_DEAL = 3;
    const STATUS_IN_PROGRESS = 7;
    const STATUS_INVOICED = 2000;

    protected $fillable = [
        'task_sort_order',
        'design_id',
        'name',
        'description',
        'assigned_to',
        'is_completed',
        'due_date',
        'start_date',
        'project_id',
        'task_status_id',
        'created_by',
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
        'is_recurring',
        'recurring_start_date',
        'recurring_end_date',
        'recurring_due_date',
        'last_sent_date',
        'next_send_date',
        'recurring_frequency',
        'include_documents',
        'task_rate',
        'column_color'
    ];


    protected $presenter = 'App\Presenters\TaskPresenter';

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function users()
    {
        return $this->belongsToMany(Models\User::class);
    }

    public function user()
    {
        return $this->belongsTo(Models\User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function emails()
    {
        return Email::whereEntity(get_class($this))->whereEntityId($this->id)->get();
    }

    public function taskStatus()
    {
        return $this->belongsTo(Models\TaskStatus::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get associated timers.
     *
     * @return HasMany
     */
    public function timers()
    {
        return $this->hasMany(Models\Timer::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function service(): TaskService
    {
        return new TaskService($this);
    }

    public function getDesignId()
    {
        return !empty($this->design_id) ? $this->design_id : $this->customer->getSetting('task_design_id');
    }

    public function getPdfFilename()
    {
        return 'storage/' . $this->account->id . '/' . $this->customer->id . '/tasks/' . $this->number . '.pdf';
    }

    public function setStatus(int $status_id)
    {
        $this->task_status_id = $status_id;
        return true;
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

    public function getTaskRate()
    {
        if (!empty($this->task_rate)) {
            return (float)$this->task_rate;
        }

        if (!empty($this->project) && !empty($this->project->task_rate)) {
            return (float)$this->project->task_rate;
        }

        return 0;
    }
}

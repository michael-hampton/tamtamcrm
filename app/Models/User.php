<?php

namespace App\Models;

use App\Models;
use App\Models\Concerns\QueryScopes;
use App\Notifications\User\ForgotPasswordNotification;
use App\Traits\Archiveable;
use App\Traits\HasPermissionsTrait;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use stdClass;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail, CanResetPassword
{

    use Notifiable, SoftDeletes, HasPermissionsTrait, HasFactory;
    use HasRelationships;
    use Archiveable;
    use QueryCacheable;
    use QueryScopes;

    protected static $flushCacheOnUpdate = true;

    public $account;

    protected $with = ['accounts'];

    protected $casts = [
        'two_factor_authentication_enabled' => 'boolean'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'profile_photo',
        'username',
        'email',
        'password',
        'role_id',
        'job_description',
        'dob',
        'phone_number',
        'gender',
        'auth_token',
        'account',
        'custom_value1',
        'custom_value2',
        'custom_value3',
        'custom_value4',
        'google2fa_secret',
        'google_id',
        'domain_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'is_active'
    ];

    /**
     * When invalidating automatically on update, you can specify
     * which tags to invalidate.
     *
     * @return array
     */
    public function getCacheTagsToInvalidateOnUpdate(): array
    {
        return [
            'users',
        ];
    }

    public function events()
    {
        return $this->belongsTo(Event::class);
    }

    public function timers()
    {
        return $this->hasMany(Timer::class);
    }

    /**
     * @return BelongsToMany
     */
    public function messages()
    {
        return $this->belongsToMany(Message::class);
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class);
    }

    public function department_manager()
    {
        return $this->morphTo();
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function permissions(Account $account = null)
    {
        if (empty($account)) {
            $account_user = $this->account_user();

            if (empty($account_user)) {
                return new \Illuminate\Support\Collection();
            }

            $account = $account_user->account;
        }

        return $this->belongsToMany(Permission::class, 'permission_user', 'user_id', 'permission_id')->where(
            'account_id',
            $account->id
        );
    }

    /**
     * Returns the current company
     *
     * @return Collection
     */
    /*public function account()
    {
        return $this->getAccount();
    }*/
    public function account_user()
    {
        if (!$this->id) {
            $this->id = auth()->user()->id;
        }

        return $this->account_users()->join(
            'company_tokens',
            'company_tokens.account_id',
            '=',
            'account_user.account_id'
        )
            ->where('company_tokens.user_id', '=', $this->id)
            ->where('company_tokens.is_web', '=', true)
            ->whereNull('company_tokens.deleted_at')
            ->where('company_tokens.token', '=', $this->auth_token)->select(
                'account_user.*'
            )->first();
    }


    public function account_users()
    {
        return $this->hasMany(Models\AccountUser::class, 'user_id', 'id');
    }

    public function domain()
    {
        return $this->hasOne(Domain::class, 'id', 'domain_id');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    /**
     * Returns a boolean of the administrator status of the user
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->account_user()->is_admin;
    }

    public function isOwner(): bool
    {
        return $this->account_user()->is_owner;
    }

    public function uploads()
    {
        return $this->hasMany(Upload::class);
    }

    /**
     * @param Account $account
     * @param bool $is_admin
     * @param bool $is_owner
     * @param array $notifications
     * @return bool
     */
    public function attachUserToAccount(Account $account, bool $is_admin = false, bool $is_owner = false, array $notifications = [])
    {
        $this->accounts()->attach(
            $account->id,
            [
                'account_id'    => $account->id,
                'is_owner'      => $is_owner,
                'is_admin'      => $is_admin,
                'notifications' => !empty($notifications) ? $notifications : $this->notificationDefaults()
            ]
        );
        return true;
    }

    /**
     * @return BelongsToMany
     */
    public function accounts()
    {
        return $this->belongsToMany(Account::class)->using(Models\AccountUser::class)
            ->withPivot('is_admin', 'is_owner', 'is_locked');
    }

    public static function notificationDefaults()
    {
        $notification = new stdClass;
        $notification->email = [];

        return $notification;
    }

    /**
     * Send a password reset notification to the user.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $url = url('reset-password/' . $token);

        $this->notify(new ForgotPasswordNotification($this, $url));
    }

    public function personalDataExportName(): string
    {
        $usernameSlug = Str::slug($this->username);

        return "personal-data-{$usernameSlug}.zip";
    }
}

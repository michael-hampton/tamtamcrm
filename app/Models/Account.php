<?php
/**
 * Created by PhpStorm.
 * User: michael.hampton
 * Date: 08/12/2019
 * Time: 17:10
 */

namespace App\Models;


use App\Events\Account\AccountWasDeleted;
use App\Models\Concerns\QueryScopes;
use App\ViewModels\AccountViewModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes, HasFactory, QueryScopes;

    protected $dispatchesEvents = [
        'deleted' => AccountWasDeleted::class,
    ];

    protected $fillable = [
        'subdomain',
        'custom_fields',
        'portal_domain',
        'support_email',
        'settings'
    ];
    protected $casts = [
        'country_id'    => 'string',
        'custom_fields' => 'object',
        'settings'      => 'object',
        'updated_at'    => 'timestamp',
        'created_at'    => 'timestamp',
        'deleted_at'    => 'timestamp',
    ];

    public function locale()
    {
        return $this->getLocale();
    }

    public function getLocale()
    {
        return isset($this->settings->language_id) && $this->language()
            ? $this->language()->locale
            : config(
                'taskmanager.locale'
            );
    }

    /**
     * @return BelongsTo
     */
    public function language()
    {
        return Language::find($this->settings->language_id);
    }

    public function getSettings()
    {
        return $this->settings;
    }

    public function getSetting($setting)
    {
        if (property_exists($this->settings, $setting) != false) {
            return $this->settings->{$setting};
        }

        return null;
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, AccountUser::class, 'account_id', 'id', 'id', 'user_id');
    }

    public function designs()
    {
        return $this->hasMany(Design::class)->whereAccountId($this->id)->orWhere('account_id', null);
    }

    /**
     * @return HasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class)->withTrashed();
    }

    /**
     * @return HasMany
     */
    public function quotes()
    {
        return $this->hasMany(Quote::class)->withTrashed();
    }


    /**
     * @return HasMany
     */
    public function credits()
    {
        return $this->hasMany(Credit::class)->withTrashed();
    }

    /**
     * @return HasMany
     */
    public function customers()
    {
        return $this->hasMany(Customer::class)->withTrashed();
    }

    public function customer_contacts()
    {
        return $this->hasMany(CustomerContact::class)->withTrashed();
    }

    public function domains()
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }


    /**
     * @return mixed
     */
    public function payments()
    {
        return $this->hasMany(Payment::class)->withTrashed();
    }


    /**
     * @return HasMany
     */
    public function tax_rates()
    {
        return $this->hasMany(TaxRate::class);
    }

    /**
     * @return HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function purchase_orders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function expense_categories()
    {
        return $this->hasMany(ExpenseCategory::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function task_statuses()
    {
        return $this->hasMany(TaskStatus::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    /**
     * @return BelongsTo
     */
    public function country()
    {
        //return $this->belongsTo(Country::class);
        return Country::find($this->settings->country_id);
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    /**
     * @return BelongsTo
     */
    public function getCurrency()
    {
        if (!empty($this->settings->currency_id)) {
            return Currency::whereId($this->settings->currency_id)->first();
        }

        return false;
    }

    public function getLogo()
    {
        return $this->settings->company_logo ?: null;
    }

    public function domain()
    {
        return 'https://' . $this->subdomain . config('taskmanager.app_domain');
    }

    /**
     * @return HasMany
     */
    public function companies()
    {
        return $this->hasMany(Company::class)->withTrashed();
    }

    public function company_contacts()
    {
        return $this->hasMany(CompanyContact::class)->withTrashed();
    }

    public function customer_gateways()
    {
        return $this->hasMany(CustomerGateway::class)->withTrashed();
    }

    public function company_gateways()
    {
        return $this->hasMany(CompanyGateway::class)->withTrashed();
    }

    public function routeNotificationForSlack($notification)
    {
        return $this->slack_webhook_url;
    }

    public function account_users()
    {
        return $this->hasMany(AccountUser::class);
    }

    public function owner()
    {
        $c = $this->account_users->where('is_owner', true)->first();

        return User::find($c->user_id);
    }

    public function getNumberOfAllowedUsers()
    {
        $plan = $this->getActiveSubscription();

        if (empty($plan)) {
            return 99999;
        }

        return $plan->number_of_licences;
    }

    public function getActiveSubscription()
    {
        return $this->plans()->where('ends_at', '>', now())->where('plan_id', $this->domains->plan_id)->first();
    }

    public function plans()
    {
        return $this->hasMany(PlanSubscription::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function tokens()
    {
        return $this->hasMany(CompanyToken::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function payment_terms()
    {
        return $this->hasMany(PaymentTerms::class);
    }

    public function recurring_invoices()
    {
        return $this->hasMany(RecurringInvoice::class);
    }

    public function recurring_quotes()
    {
        return $this->hasMany(RecurringQuote::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function plan_subscriptions()
    {
        return $this->hasMany(PlanSubscription::class);
    }

    public function getNumberOfAllowedDocuments()
    {
        if (empty($this->domains->plan)) {
            return 99999;
        }

        $plan_feature = $this->domains->plan->features->where('slug', '=', 'DOCUMENT')->first();

        return $plan_feature->value;
    }

    public function getNumberOfAllowedCustomers()
    {
        if (empty($this->domains->plan)) {
            return 99999;
        }

        $plan_feature = $this->domains->plan->features->where('slug', '=', 'CUSTOMER')->first();

        return $plan_feature->value;
    }

    public function getNumberOfAllowedEmails()
    {
        if (empty($this->domains->plan)) {
            return 99999;
        }

        $plan_feature = $this->domains->plan->features->where('slug', '=', 'EMAIL')->first();

        return $plan_feature->value;
    }

    public function selectPersonalData($personal_data = null, $return_array = false)
    {

        $export['customers'] = $this->customers->makeHidden('settings')->toArray();
        $export['customer_contacts'] = $this->customer_contacts->toArray();
        $export['customer_gateways'] = $this->customer_gateways->toArray();
        $export['company_gateways'] = $this->company_gateways->toArray();
        $export['transactions'] = $this->transactions->toArray();
        $export['credits'] = $this->credits->toArray();
        $export['designs'] = $this->designs->toArray();
        $export['expenses'] = $this->expenses->toArray();
        $export['expense_categories'] = $this->expense_categories->toArray();
        $export['groups'] = $this->groups->toArray();
        $export['invoices'] = $this->invoices->makeHidden('account')->makeHidden('customer')->toArray();
        $export['payment_terms'] = $this->payment_terms->toArray();
        $export['payments'] = $this->payments->toArray();
        $export['projects'] = $this->projects->toArray();
        $export['quotes'] = $this->quotes->makeHidden('account')->makeHidden('customer')->toArray();
        $export['recurring_invoices'] = $this->recurring_invoices->makeHidden('account')->makeHidden('customer')->toArray();
        $export['recurring_quotes'] = $this->recurring_quotes->makeHidden('account')->makeHidden('customer')->toArray();
        $export['webhooks'] = $this->subscriptions->toArray();
        $export['plans'] = Plan::all()->toArray();
        $export['subscriptions'] = $this->plan_subscriptions->makeHidden('plan')->toArray();
        $export['tasks'] = $this->tasks->makeHidden('account')->makeHidden('customer')->toArray();
        $export['task_statuses'] = $this->task_statuses->toArray();
        $export['tax_rates'] = $this->tax_rates->toArray();
        $export['companies'] = $this->companies->makeHidden('contacts')->toArray();
        $export['company_contacts'] = $this->company_contacts->toArray();
        $export['deals'] = $this->deals->toArray();
        $export['leads'] = $this->leads->toArray();
        $export['products'] = $this->products->toArray();
        $export['orders'] = $this->orders->makeHidden('account')->makeHidden('customer')->toArray();
        $export['purchase_orders'] = $this->purchase_orders->makeHidden('account')->makeHidden('company')->toArray();
        $export['users'] = $this->users->makeHidden('auth_token')->makeHidden('password')->makeHidden('accounts')->toArray();
        $export['company_tokens'] = $this->tokens()->where('is_web', false)->get()->toArray();
        $export['account_users'] = $this->account_users->toArray();

        if ($return_array) {
            return $export;
        }

        $personal_data->add('attributes.json', $export);

        return true;
    }
}

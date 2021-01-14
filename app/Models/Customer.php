<?php

namespace App\Models;

use App\Traits\Archiveable;
use App\Traits\Balancer;
use App\Traits\Money;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

class Customer extends Model implements HasLocalePreference
{

    use SoftDeletes, PresentableTrait, Balancer, Money, HasFactory, Archiveable;

    const CUSTOMER_TYPE_WON = 1;
    protected $presenter = 'App\Presenters\CustomerPresenter';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number',
        'balance',
        'name',
        'credits',
        'last_name',
        'status',
        'company_id',
        'currency_id',
        'phone',
        'customer_type',
        'default_payment_method',
        'settings',
        'assigned_to',
        'custom_value1',
        'custom_value2',
        'custom_value3',
        'custom_value4',
        'group_settings_id',
        'public_notes',
        'private_notes',
        'website',
        'size_id',
        'industry_id',
        'vat_number'
    ];
    protected $casts = [
        'settings'   => 'object',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
        'is_deleted' => 'boolean',
    ];
    private $merged_settings;

    /**
     * @return HasMany
     */
    public function addresses()
    {
        return $this->hasMany(Address::class)->whereStatus(true);
    }

    public function credits()
    {
        return $this->hasMany(Credit::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'default_payment_method');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    /**
     * @return BelongsToMany
     */
    public function messages()
    {
        return $this->belongsToMany(Message::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function error_logs()
    {
        return $this->hasMany(ErrorLog::class)->orderBy('created_at', 'desc');
    }

    public function contacts()
    {
        return $this->hasMany(CustomerContact::class)->orderBy('is_primary', 'desc');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id', 'desc');
    }

    public function primary_contact()
    {
        return $this->hasMany(CustomerContact::class)->whereIsPrimary(true);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group_settings()
    {
        return $this->belongsTo(Group::class);
    }

    public function getActiveCredits()
    {
        return $this->credits->where('balance', '>', 0)->whereIn(
            'status_id',
            [Credit::STATUS_SENT, Credit::STATUS_PARTIAL]
        )->where(
            'is_deleted',
            false
        );
    }

    public function preferredLocale()
    {
        return $this->locale();
    }

    public function locale()
    {
        $language = $this->language();

        return !empty($language) ? $this->language()->locale : 'en';
    }

    public function language()
    {
        return Language::find($this->getSetting('language_id'));
    }

    /**
     * @param $setting
     * @return bool
     */
    public function getSetting($setting)
    {
        if (empty($this->merged_settings)) {
            $account_settings = $this->account->settings;
            $customer_settings = $this->settings;
            unset($account_settings->pdf_variables, $customer_settings->pdf_variables);

            $this->merged_settings = (object)array_merge(
                array_filter((array)$account_settings, 'strval'),
                array_filter((array)$this->group_settings, 'strval'),
                array_filter((array)$customer_settings, 'strval')
            );
        }

        return !empty($this->merged_settings->{$setting}) ? $this->merged_settings->{$setting} : false;
    }

    public function gateways()
    {
        return $this->hasMany(CustomerGateway::class);
    }

    public function getPdfFilename()
    {
        return 'storage/' . $this->account->id . '/' . $this->id . '/statements/' . $this->number . '.pdf';
    }

    public function getDesignId()
    {
        return !empty($this->design_id) ? $this->design_id : $this->getSetting('invoice_design_id');
    }

    /**
     * @param float $amount
     */
    public function reducePaidToDateAmount(float $amount)
    {
        $this->paid_to_date -= $amount;
    }

    /**
     * @param float $amount
     */
    public function increasePaidToDateAmount(float $amount)
    {
        $this->paid_to_date += $amount;
    }

    public function getFormattedCustomerBalance()
    {
        return $this->formatCurrency($this->balance, $this);
    }

    public function getFormattedPaidToDate()
    {
        return $this->formatCurrency($this->paid_to_date, $this);
    }

    private function checkObjectEmpty($var)
    {
        return is_object($var) && empty((array)$var);
    }
}

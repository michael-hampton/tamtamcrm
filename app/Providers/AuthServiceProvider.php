<?php

namespace App\Providers;

use App\Mail\User\UserCreated;
use App\Mail\User\UserEmailChangedNotification;
use App\Models\Cases;
use App\Models\Company;
use App\Models\CompanyGateway;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\CustomerGatewayToken;
use App\Models\Deal;
use App\Models\Event;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\File;
use App\Models\Group;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentTerms;
use App\Models\Plan;
use App\Models\PlanSubscription;
use App\Models\Product;
use App\Models\Project;
use App\Models\Promocode;
use App\Models\PurchaseOrder;
use App\Models\Quote;
use App\Models\RecurringInvoice;
use App\Models\RecurringQuote;
use App\Models\Subscription;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaxRate;
use App\Models\User;
use App\Policies\CasePolicy;
use App\Policies\CompanyGatewayPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\CreditPolicy;
use App\Policies\CustomerGatewayTokenPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\DealPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\EventPolicy;
use App\Policies\ExpenseCategoryPolicy;
use App\Policies\ExpensePolicy;
use App\Policies\GroupPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\LeadPolicy;
use App\Policies\OrderPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\PaymentTermPolicy;
use App\Policies\PlanPolicy;
use App\Policies\PlanSubscriptionPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\PromocodePolicy;
use App\Policies\PurchaseOrderPolicy;
use App\Policies\QuotePolicy;
use App\Policies\RecurringInvoicePolicy;
use App\Policies\RecurringQuotePolicy;
use App\Policies\SubscriptionPolicy;
use App\Policies\TaskPolicy;
use App\Policies\TaskStatusPolicy;
use App\Policies\TaxRatePolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Log;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        //'App\Model'             => 'App\Policies\ModelPolicy',
        Customer::class             => CustomerPolicy::class,
        CompanyGateway::class       => CompanyGatewayPolicy::class,
        Lead::class                 => LeadPolicy::class,
        Deal::class                 => DealPolicy::class,
        Credit::class               => CreditPolicy::class,
        Expense::class              => ExpensePolicy::class,
        ExpenseCategory::class      => ExpenseCategoryPolicy::class,
        Group::class                => GroupPolicy::class,
        Invoice::class              => InvoicePolicy::class,
        Payment::class              => PaymentPolicy::class,
        PaymentTerms::class         => PaymentTermPolicy::class,
        Product::class              => ProductPolicy::class,
        Project::class              => ProjectPolicy::class,
        Quote::class                => QuotePolicy::class,
        RecurringInvoice::class     => RecurringInvoicePolicy::class,
        RecurringQuote::class       => RecurringQuotePolicy::class,
        Subscription::class         => SubscriptionPolicy::class,
        Task::class                 => TaskPolicy::class,
        TaskStatus::class           => TaskStatusPolicy::class,
        TaxRate::class              => TaxRatePolicy::class,
        User::class                 => UserPolicy::class,
        Company::class              => CompanyPolicy::class,
        Promocode::class            => PromocodePolicy::class,
        PurchaseOrder::class        => PurchaseOrderPolicy::class,
        Cases::class                => CasePolicy::class,
        Event::class                => EventPolicy::class,
        Order::class                => OrderPolicy::class,
        File::class                 => DocumentPolicy::class,
        PlanSubscription::class     => PlanSubscriptionPolicy::class,
        Plan::class                 => PlanPolicy::class,
        CustomerGatewayToken::class => CustomerGatewayTokenPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        VerifyEmail::toMailUsing(
            function ($notifiable, $url) {

                if(!empty($notifiable)) {
                    if (!empty($notifiable->previous_email_address)) {
                        return (new UserEmailChangedNotification($notifiable, $url));
                    }

                    return (new UserCreated($notifiable, $url));
                } else {
                    Log::emergency('empty user authserviceprovider');
                }
            }
        );
    }
}
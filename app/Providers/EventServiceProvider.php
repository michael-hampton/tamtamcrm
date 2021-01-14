<?php

namespace App\Providers;

use App\Events\Cases\CaseWasArchived;
use App\Events\Cases\CaseWasCreated;
use App\Events\Cases\CaseWasDeleted;
use App\Events\Cases\CaseWasRestored;
use App\Events\Cases\CaseWasUpdated;
use App\Events\Cases\RecurringInvoiceWasArchived;
use App\Events\Cases\RecurringInvoiceWasDeleted;
use App\Events\Cases\RecurringInvoiceWasRestored;
use App\Events\Company\CompanyWasArchived;
use App\Events\Company\CompanyWasCreated;
use App\Events\Company\CompanyWasDeleted;
use App\Events\Company\CompanyWasRestored;
use App\Events\Company\CompanyWasUpdated;
use App\Events\Credit\CreditWasArchived;
use App\Events\Credit\CreditWasCreated;
use App\Events\Credit\CreditWasDeleted;
use App\Events\Credit\CreditWasEmailed;
use App\Events\Credit\CreditWasMarkedSent;
use App\Events\Credit\CreditWasRestored;
use App\Events\Credit\CreditWasUpdated;
use App\Events\Customer\CustomerWasArchived;
use App\Events\Customer\CustomerWasCreated;
use App\Events\Customer\CustomerWasDeleted;
use App\Events\Customer\CustomerWasRestored;
use App\Events\Customer\CustomerWasUpdated;
use App\Events\Deal\DealWasArchived;
use App\Events\Deal\DealWasCreated;
use App\Events\Deal\DealWasDeleted;
use App\Events\Deal\DealWasEmailed;
use App\Events\Deal\DealWasRestored;
use App\Events\Deal\DealWasUpdated;
use App\Events\EmailFailedToSend;
use App\Events\Expense\ExpenseWasApproved;
use App\Events\Expense\ExpenseWasArchived;
use App\Events\Expense\ExpenseWasCreated;
use App\Events\Expense\ExpenseWasDeleted;
use App\Events\Expense\ExpenseWasRestored;
use App\Events\Expense\ExpenseWasUpdated;
use App\Events\Invoice\InvoiceWasArchived;
use App\Events\Invoice\InvoiceWasCancelled;
use App\Events\Invoice\InvoiceWasCreated;
use App\Events\Invoice\InvoiceWasDeleted;
use App\Events\Invoice\InvoiceWasEmailed;
use App\Events\Invoice\InvoiceWasMarkedSent;
use App\Events\Invoice\InvoiceWasPaid;
use App\Events\Invoice\InvoiceWasRestored;
use App\Events\Invoice\InvoiceWasReversed;
use App\Events\Invoice\InvoiceWasUpdated;
use App\Events\Lead\LeadWasArchived;
use App\Events\Lead\LeadWasCreated;
use App\Events\Lead\LeadWasDeleted;
use App\Events\Lead\LeadWasEmailed;
use App\Events\Lead\LeadWasRestored;
use App\Events\Lead\LeadWasUpdated;
use App\Events\Misc\InvitationWasViewed;
use App\Events\Order\OrderWasArchived;
use App\Events\Order\OrderWasBackordered;
use App\Events\Order\OrderWasCreated;
use App\Events\Order\OrderWasDeleted;
use App\Events\Order\OrderWasDispatched;
use App\Events\Order\OrderWasEmailed;
use App\Events\Order\OrderWasHeld;
use App\Events\Order\OrderWasMarkedSent;
use App\Events\Order\OrderWasRestored;
use App\Events\Order\OrderWasUpdated;
use App\Events\Payment\PaymentFailed;
use App\Events\Payment\PaymentWasArchived;
use App\Events\Payment\PaymentWasCreated;
use App\Events\Payment\PaymentWasDeleted;
use App\Events\Payment\PaymentWasRefunded;
use App\Events\Payment\PaymentWasRestored;
use App\Events\Payment\PaymentWasUpdated;
use App\Events\Project\ProjectWasArchived;
use App\Events\Project\ProjectWasCreated;
use App\Events\Project\ProjectWasDeleted;
use App\Events\Project\ProjectWasRestored;
use App\Events\Project\ProjectWasUpdated;
use App\Events\PurchaseOrder\PurchaseOrderChangeWasRequested;
use App\Events\PurchaseOrder\PurchaseOrderWasApproved;
use App\Events\PurchaseOrder\PurchaseOrderWasArchived;
use App\Events\PurchaseOrder\PurchaseOrderWasCreated;
use App\Events\PurchaseOrder\PurchaseOrderWasDeleted;
use App\Events\PurchaseOrder\PurchaseOrderWasEmailed;
use App\Events\PurchaseOrder\PurchaseOrderWasMarkedSent;
use App\Events\PurchaseOrder\PurchaseOrderWasRejected;
use App\Events\PurchaseOrder\PurchaseOrderWasRestored;
use App\Events\PurchaseOrder\PurchaseOrderWasUpdated;
use App\Events\Quote\QuoteChangeWasRequested;
use App\Events\Quote\QuoteWasApproved;
use App\Events\Quote\QuoteWasArchived;
use App\Events\Quote\QuoteWasCreated;
use App\Events\Quote\QuoteWasDeleted;
use App\Events\Quote\QuoteWasEmailed;
use App\Events\Quote\QuoteWasMarkedSent;
use App\Events\Quote\QuoteWasRejected;
use App\Events\Quote\QuoteWasRestored;
use App\Events\Quote\QuoteWasUpdated;
use App\Events\RecurringInvoice\RecurringInvoiceWasCreated;
use App\Events\RecurringInvoice\RecurringInvoiceWasUpdated;
use App\Events\RecurringQuote\RecurringQuoteWasArchived;
use App\Events\RecurringQuote\RecurringQuoteWasCreated;
use App\Events\RecurringQuote\RecurringQuoteWasDeleted;
use App\Events\RecurringQuote\RecurringQuoteWasRestored;
use App\Events\RecurringQuote\RecurringQuoteWasUpdated;
use App\Events\Task\TaskWasArchived;
use App\Events\Task\TaskWasCreated;
use App\Events\Task\TaskWasDeleted;
use App\Events\Task\TaskWasRestored;
use App\Events\Task\TaskWasUpdated;
use App\Events\Uploads\FileWasDeleted;
use App\Events\Uploads\FileWasUploaded;
use App\Events\User\UserEmailChanged;
use App\Events\User\UserWasCreated;
use App\Events\User\UserWasDeleted;
use App\Listeners\Cases\CaseArchived;
use App\Listeners\Cases\CaseCreated;
use App\Listeners\Cases\CaseDeleted;
use App\Listeners\Cases\CaseRestored;
use App\Listeners\Cases\CaseUpdated;
use App\Listeners\Company\CompanyArchived;
use App\Listeners\Company\CompanyCreated;
use App\Listeners\Company\CompanyDeleted;
use App\Listeners\Company\CompanyRestored;
use App\Listeners\Company\CompanyUpdated;
use App\Listeners\Credit\CreditArchived;
use App\Listeners\Credit\CreditCreated;
use App\Listeners\Credit\CreditDeleted;
use App\Listeners\Credit\CreditEmail;
use App\Listeners\Credit\CreditEmailedNotification;
use App\Listeners\Credit\CreditMarkedSent;
use App\Listeners\Credit\CreditRestored;
use App\Listeners\Credit\CreditUpdated;
use App\Listeners\Customer\CustomerArchived;
use App\Listeners\Customer\CustomerCreated;
use App\Listeners\Customer\CustomerDeleted;
use App\Listeners\Customer\CustomerRestored;
use App\Listeners\Customer\CustomerUpdated;
use App\Listeners\Deal\DealArchived;
use App\Listeners\Deal\DealCreated;
use App\Listeners\Deal\DealDeleted;
use App\Listeners\Deal\DealEmailed;
use App\Listeners\Deal\DealNotification;
use App\Listeners\Deal\DealRestored;
use App\Listeners\Deal\DealUpdated;
use App\Listeners\Entity\EntityEmailFailedToSend;
use App\Listeners\Entity\EntityViewedListener;
use App\Listeners\Expense\ExpenseApproved;
use App\Listeners\Expense\ExpenseArchived;
use App\Listeners\Expense\ExpenseCreated;
use App\Listeners\Expense\ExpenseDeleted;
use App\Listeners\Expense\ExpenseRestored;
use App\Listeners\Expense\ExpenseUpdated;
use App\Listeners\Expense\SendExpenseApprovedNotification;
use App\Listeners\Invoice\InvoiceArchived;
use App\Listeners\Invoice\InvoiceCancelled;
use App\Listeners\Invoice\InvoiceCreated;
use App\Listeners\Invoice\InvoiceDeleted;
use App\Listeners\Invoice\InvoiceEmail;
use App\Listeners\Invoice\InvoiceEmailedNotification;
use App\Listeners\Invoice\InvoiceMarkedSent;
use App\Listeners\Invoice\InvoicePaid;
use App\Listeners\Invoice\InvoiceRestored;
use App\Listeners\Invoice\InvoiceReversed;
use App\Listeners\Invoice\InvoiceUpdated;
use App\Listeners\Lead\LeadArchived;
use App\Listeners\Lead\LeadCreated;
use App\Listeners\Lead\LeadDeleted;
use App\Listeners\Lead\LeadEmailed;
use App\Listeners\Lead\LeadNotification;
use App\Listeners\Lead\LeadRestored;
use App\Listeners\Lead\LeadUpdated;
use App\Listeners\NewUserNotification;
use App\Listeners\Order\OrderArchived;
use App\Listeners\Order\OrderBackordered;
use App\Listeners\Order\OrderBackorderedNotification;
use App\Listeners\Order\OrderCreated;
use App\Listeners\Order\OrderDeleted;
use App\Listeners\Order\OrderDispatched;
use App\Listeners\Order\OrderEmailed;
use App\Listeners\Order\OrderEmailedNotification;
use App\Listeners\Order\OrderHeld;
use App\Listeners\Order\OrderHeldNotification;
use App\Listeners\Order\OrderMarkedSent;
use App\Listeners\Order\OrderNotification;
use App\Listeners\Order\OrderRestored;
use App\Listeners\Order\OrderUpdated;
use App\Listeners\Payment\PaymentArchived;
use App\Listeners\Payment\PaymentCreated;
use App\Listeners\Payment\PaymentDeleted;
use App\Listeners\Payment\PaymentFailedNotification;
use App\Listeners\Payment\PaymentNotification;
use App\Listeners\Payment\PaymentRefunded;
use App\Listeners\Payment\PaymentRefundedNotification;
use App\Listeners\Payment\PaymentRestored;
use App\Listeners\Payment\PaymentUpdated;
use App\Listeners\Project\ProjectArchived;
use App\Listeners\Project\ProjectCreated;
use App\Listeners\Project\ProjectDeleted;
use App\Listeners\Project\ProjectRestored;
use App\Listeners\Project\ProjectUpdated;
use App\Listeners\PurchaseOrder\PurchaseOrderApproved;
use App\Listeners\PurchaseOrder\PurchaseOrderArchived;
use App\Listeners\PurchaseOrder\PurchaseOrderChangeRequested;
use App\Listeners\PurchaseOrder\PurchaseOrderCreated;
use App\Listeners\PurchaseOrder\PurchaseOrderDeleted;
use App\Listeners\PurchaseOrder\PurchaseOrderEmailed;
use App\Listeners\PurchaseOrder\PurchaseOrderEmailedNotification;
use App\Listeners\PurchaseOrder\PurchaseOrderMarkedSent;
use App\Listeners\PurchaseOrder\PurchaseOrderRejected;
use App\Listeners\PurchaseOrder\PurchaseOrderRestored;
use App\Listeners\PurchaseOrder\PurchaseOrderUpdated;
use App\Listeners\PurchaseOrder\SendPurchaseOrderApprovedNotification;
use App\Listeners\PurchaseOrder\SendPurchaseOrderChangeRequestedNotification;
use App\Listeners\PurchaseOrder\SendPurchaseOrderRejectedNotification;
use App\Listeners\Quote\QuoteApproved;
use App\Listeners\Quote\QuoteArchived;
use App\Listeners\Quote\QuoteChangeRequested;
use App\Listeners\Quote\QuoteCreated;
use App\Listeners\Quote\QuoteDeleted;
use App\Listeners\quote\QuoteEmailed;
use App\Listeners\Quote\QuoteEmailedNotification;
use App\Listeners\Quote\QuoteMarkedSent;
use App\Listeners\Quote\QuoteRejected;
use App\Listeners\Quote\QuoteRestored;
use App\Listeners\Quote\QuoteUpdated;
use App\Listeners\Quote\SendQuoteApprovedNotification;
use App\Listeners\Quote\SendQuoteChangeRequestedNotification;
use App\Listeners\Quote\SendQuoteRejectedNotification;
use App\Listeners\RecurringInvoice\RecurringInvoiceArchived;
use App\Listeners\RecurringInvoice\RecurringInvoiceCreated;
use App\Listeners\RecurringInvoice\RecurringInvoiceDeleted;
use App\Listeners\RecurringInvoice\RecurringInvoiceRestored;
use App\Listeners\RecurringInvoice\RecurringInvoiceUpdated;
use App\Listeners\RecurringQuote\RecurringQuoteArchived;
use App\Listeners\RecurringQuote\RecurringQuoteCreated;
use App\Listeners\RecurringQuote\RecurringQuoteDeleted;
use App\Listeners\RecurringQuote\RecurringQuoteRestored;
use App\Listeners\RecurringQuote\RecurringQuoteUpdated;
use App\Listeners\Task\TaskArchived;
use App\Listeners\Task\TaskCreated;
use App\Listeners\Task\TaskDeleted;
use App\Listeners\Task\TaskRestored;
use App\Listeners\Task\TaskUpdated;
use App\Listeners\User\DeletedUser;
use App\Listeners\User\SendUserEmailChangedEmail;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserWasCreated::class                  => [
            NewUserNotification::class,
        ],
        UserWasDeleted::class                  => [
            DeletedUser::class,
        ],
        UserEmailChanged::class                => [
            SendUserEmailChangedEmail::class
        ],
        // Customers
        CustomerWasCreated::class              => [
            CustomerCreated::class
        ],
        CustomerWasArchived::class             => [
            CustomerArchived::class
        ],
        CustomerWasRestored::class             => [
            CustomerRestored::class
        ],
        CustomerWasDeleted::class              => [
            CustomerDeleted::class
        ],
        CustomerWasUpdated::class              => [
            CustomerUpdated::class
        ],
        //payments
        PaymentWasCreated::class               => [
            PaymentCreated::class,
            PaymentNotification::class,
        ],
        PaymentWasUpdated::class               => [
            PaymentUpdated::class
        ],
        PaymentWasArchived::class              => [
            PaymentArchived::class,
        ],
        PaymentWasRestored::class              => [
            PaymentRestored::class,
        ],
        PaymentWasDeleted::class               => [
            PaymentDeleted::class,
        ],
        PaymentWasRefunded::class              => [
            PaymentRefunded::class,
            PaymentRefundedNotification::class
        ],
        PaymentFailed::class                   => [
            \App\Listeners\Payment\PaymentFailed::class,
            PaymentFailedNotification::class
        ],
        //Invoices
        InvoiceWasMarkedSent::class            => [
            InvoiceMarkedSent::class,
        ],
        InvoiceWasArchived::class              => [
            InvoiceArchived::class
        ],
        InvoiceWasRestored::class              => [
            InvoiceRestored::class
        ],
        InvoiceWasUpdated::class               => [
            InvoiceUpdated::class
        ],
        InvoiceWasCreated::class               => [
            InvoiceCreated::class
        ],
        InvoiceWasPaid::class                  => [
            InvoicePaid::class,
        ],
        InvoiceWasEmailed::class               => [
            InvoiceEmail::class,
            InvoiceEmailedNotification::class,
        ],
        InvoiceWasDeleted::class               => [
            InvoiceDeleted::class,
        ],
        InvoiceWasReversed::class              => [
            InvoiceReversed::class
        ],
        InvoiceWasCancelled::class             => [
            InvoiceCancelled::class
        ],
        InvitationWasViewed::class             => [
            EntityViewedListener::class
        ],
        // quotes
        QuoteWasRejected::class                => [
            QuoteRejected::class,
            SendQuoteRejectedNotification::class
        ],
        QuoteChangeWasRequested::class         => [
            QuoteChangeRequested::class,
            SendQuoteChangeRequestedNotification::class
        ],
        QuoteWasApproved::class                => [
            QuoteApproved::class,
            SendQuoteApprovedNotification::class
        ],
        QuoteWasCreated::class                 => [
            QuoteCreated::class
        ],
        QuoteWasEmailed::class                 => [
            QuoteEmailed::class,
            QuoteEmailedNotification::class
        ],
        QuoteWasUpdated::class                 => [
            QuoteUpdated::class
        ],
        QuoteWasDeleted::class                 => [
            QuoteDeleted::class
        ],
        QuoteWasArchived::class                => [
            QuoteArchived::class
        ],
        QuoteWasRestored::class                => [
            QuoteRestored::class
        ],
        QuoteWasMarkedSent::class              => [
            QuoteMarkedSent::class
        ],
        //companies
        CompanyWasCreated::class               => [
            CompanyCreated::class
        ],
        CompanyWasUpdated::class               => [
            CompanyUpdated::class
        ],
        CompanyWasDeleted::class               => [
            CompanyDeleted::class
        ],
        CompanyWasArchived::class              => [
            CompanyArchived::class
        ],
        CompanyWasRestored::class              => [
            CompanyRestored::class
        ],
        //expenses
        ExpenseWasCreated::class               => [
            ExpenseCreated::class
        ],
        ExpenseWasUpdated::class               => [
            ExpenseUpdated::class
        ],
        ExpenseWasDeleted::class               => [
            ExpenseDeleted::class
        ],
        ExpenseWasArchived::class              => [
            ExpenseArchived::class
        ],
        ExpenseWasRestored::class              => [
            ExpenseRestored::class
        ],
        ExpenseWasApproved::class              => [
            ExpenseApproved::class,
            SendExpenseApprovedNotification::class
        ],
        //recurring invoice
        RecurringInvoiceWasCreated::class      => [
            RecurringInvoiceCreated::class
        ],
        RecurringInvoiceWasUpdated::class      => [
            RecurringInvoiceUpdated::class
        ],
        RecurringInvoiceWasDeleted::class      => [
            RecurringInvoiceDeleted::class
        ],
        RecurringInvoiceWasArchived::class     => [
            RecurringInvoiceArchived::class
        ],
        RecurringInvoiceWasRestored::class     => [
            RecurringInvoiceRestored::class
        ],
        //recurring quote
        RecurringQuoteWasCreated::class        => [
            RecurringQuoteCreated::class
        ],
        RecurringQuoteWasUpdated::class        => [
            RecurringQuoteUpdated::class
        ],
        RecurringQuoteWasDeleted::class        => [
            RecurringQuoteDeleted::class
        ],
        RecurringQuoteWasArchived::class       => [
            RecurringQuoteArchived::class
        ],
        RecurringQuoteWasRestored::class       => [
            RecurringQuoteRestored::class
        ],
        //cases
        CaseWasCreated::class                  => [
            CaseCreated::class
        ],
        CaseWasUpdated::class                  => [
            CaseUpdated::class
        ],
        CaseWasDeleted::class                  => [
            CaseDeleted::class
        ],
        CaseWasArchived::class                 => [
            CaseArchived::class
        ],
        CaseWasRestored::class                 => [
            CaseRestored::class
        ],
        //projects
        ProjectWasCreated::class               => [
            ProjectCreated::class
        ],
        ProjectWasUpdated::class               => [
            ProjectUpdated::class
        ],
        ProjectWasDeleted::class               => [
            ProjectDeleted::class
        ],
        ProjectWasArchived::class              => [
            ProjectArchived::class
        ],
        ProjectWasRestored::class              => [
            ProjectRestored::class
        ],
        //orders
        OrderWasDispatched::class              => [
            OrderDispatched::class
        ],
        OrderWasDeleted::class                 => [
            OrderDeleted::class
        ],
        OrderWasBackordered::class             => [
            OrderBackordered::class,
            OrderBackorderedNotification::class
        ],
        OrderWasHeld::class                    => [
            OrderHeld::class,
            OrderHeldNotification::class
        ],
        OrderWasArchived::class                => [
            OrderArchived::class
        ],
        OrderWasRestored::class                => [
            OrderRestored::class
        ],
        OrderWasUpdated::class                 => [
            OrderUpdated::class
        ],
        OrderWasMarkedSent::class              => [
            OrderMarkedSent::class
        ],
        // credits
        CreditWasCreated::class                => [
            CreditCreated::class
        ],
        CreditWasDeleted::class                => [
            CreditDeleted::class
        ],
        CreditWasArchived::class               => [
            CreditArchived::class
        ],
        CreditWasRestored::class               => [
            CreditRestored::class
        ],
        CreditWasUpdated::class                => [
            CreditUpdated::class
        ],
        CreditWasMarkedSent::class             => [
            CreditMarkedSent::class
        ],
        CreditWasEmailed::class                => [
            CreditEmail::class,
            CreditEmailedNotification::class
        ],
        LeadWasCreated::class                  => [
            LeadCreated::class,
            LeadNotification::class
        ],
        LeadWasArchived::class                 => [
            LeadArchived::class
        ],
        LeadWasDeleted::class                  => [
            LeadDeleted::class
        ],
        LeadWasEmailed::class                  => [
            LeadEmailed::class
        ],
        LeadWasRestored::class                 => [
            LeadRestored::class
        ],
        LeadWasUpdated::class                  => [
            LeadUpdated::class
        ],
        OrderWasCreated::class                 => [
            OrderCreated::class,
            OrderNotification::class
        ],
        OrderWasEmailed::class                 => [
            OrderEmailed::class,
            OrderEmailedNotification::class
        ],
        FileWasUploaded::class                 => [
        ],
        FileWasDeleted::class                  => [
        ],
        EmailFailedToSend::class               => [
            EntityEmailFailedToSend::class
        ],
        // purchase orders
        PurchaseOrderWasRejected::class        => [
            PurchaseOrderRejected::class,
            SendPurchaseOrderRejectedNotification::class,
        ],
        PurchaseOrderChangeWasRequested::class => [
            PurchaseOrderChangeRequested::class,
            SendPurchaseOrderChangeRequestedNotification::class
        ],
        PurchaseOrderWasCreated::class         => [
            PurchaseOrderCreated::class
        ],
        PurchaseOrderWasApproved::class        => [
            PurchaseOrderApproved::class,
            SendPurchaseOrderApprovedNotification::class
        ],
        PurchaseOrderWasArchived::class        => [
            PurchaseOrderArchived::class
        ],
        PurchaseOrderWasDeleted::class         => [
            PurchaseOrderDeleted::class
        ],
        PurchaseOrderWasEmailed::class         => [
            PurchaseOrderEmailed::class,
            PurchaseOrderEmailedNotification::class
        ],
        PurchaseOrderWasMarkedSent::class      => [
            PurchaseOrderMarkedSent::class
        ],
        PurchaseOrderWasRestored::class        => [
            PurchaseOrderRestored::class
        ],
        PurchaseOrderWasUpdated::class         => [
            PurchaseOrderUpdated::class
        ],
        DealWasCreated::class                  => [
            DealCreated::class,
            DealNotification::class
        ],
        DealWasArchived::class                 => [
            DealArchived::class
        ],
        DealWasDeleted::class                  => [
            DealDeleted::class
        ],
        DealWasEmailed::class                  => [
            DealEmailed::class
        ],
        DealWasRestored::class                 => [
            DealRestored::class
        ],
        DealWasUpdated::class                  => [
            DealUpdated::class
        ],
        TaskWasArchived::class                 => [
            TaskArchived::class
        ],
        TaskWasCreated::class                  => [
            TaskCreated::class
        ],
        TaskWasDeleted::class                  => [
            TaskDeleted::class
        ],
        //        TaskWasEmailed::class              => [
        //            RecurringInvoiceEmailed::class
        //        ],
        TaskWasRestored::class                 => [
            TaskRestored::class
        ],
        TaskWasUpdated::class                  => [
            TaskUpdated::class
        ]
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}

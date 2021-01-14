<?php

namespace App\Console\Commands;

use App\Jobs\Expense\SendRecurringExpense;
use App\Jobs\Invoice\SendRecurringInvoice;
use App\Jobs\Quote\SendRecurringQuote;
use App\Jobs\Task\SendRecurringTask;
use App\Libraries\Utils;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Quote;
use App\Models\Task;
use App\Repositories\ExpenseRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\QuoteRepository;
use App\Repositories\TaskRepository;
use App\Services\InvoiceService;
use Auth;
use Illuminate\Console\Command;

/**
 * Class SendRecurring
 * @package App\Console\Commands
 */
class SendRecurring extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-recurring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for any invoices that need to be recurred and manages them accordingly.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        SendRecurringInvoice::dispatchNow((new InvoiceRepository(new Invoice())));
        SendRecurringQuote::dispatchNow(new QuoteRepository(new Quote()));
        SendRecurringTask::dispatchNow(new TaskRepository(new Task, new ProjectRepository(new Project())));
        SendRecurringExpense::dispatchNow(new ExpenseRepository(new Expense()));
    }
}

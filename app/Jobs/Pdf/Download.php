<?php
//https://www.itsolutionstuff.com/post/how-to-create-zip-file-and-download-in-laravel-6example.html

namespace App\Jobs\Pdf;

use App\Mail\InvoiceWithAttachment;
use App\Models\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use ZipArchive;

class Download implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $invoices;

    private $account;

    private $email;

    /**
     * @param $invoices
     * @param Account $account
     * @param $email
     * @deprecated confirm to be deleted
     * Create a new job instance.
     */
    public function __construct($invoices, Account $account, $email)
    {
        $this->invoices = $invoices;
        $this->account = $account;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     *
     * @return void
     */
    public function handle()
    {
        $zip = new ZipArchive;
        $first_invoice = $this->invoices->first();
        $fileName = str_replace(' ', '_', $first_invoice->customer->present()->name) . '_' . date('Y-m-d') . '.zip';

        $class = strtolower(class_basename($first_invoice)) . 's';

        $path = public_path("storage/{$first_invoice->account_id}/$first_invoice->customer_id/{$class}/{$fileName}");

        if ($zip->open($path, ZipArchive::CREATE) === true) {
            foreach ($this->invoices as $invoice) {
                $file = $invoice->service()->generatePdf();

                $relativeNameInZipFile = basename($file);

                $zip->addFile(public_path($file), $relativeNameInZipFile);
            }

            $zip->close();
        }

        Mail::to($this->email)->send(
            new InvoiceWithAttachment('Please find your invoice attached', $path, $first_invoice)
        );
    }
}

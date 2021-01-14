<?php

namespace App\Jobs\Pdf;

use App\Designs\PdfColumns;
use App\Models\Design;
use App\Traits\MakesInvoiceHtml;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use ReflectionClass;

class CreatePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, MakesInvoiceHtml;

    public $entity;

    private $disk;

    private $contact;

    private $file_path;

    private $designer;

    private $objPdf;

    private $update = false;

    private $entity_string = '';

    /**
     * Create a new job instance.
     *
     * @param $objPdf
     * @param $entity
     * @param null $contact
     * @param bool $update
     * @param string $entity_string
     * @param string $disk
     */
    public function __construct(
        $objPdf,
        $entity,
        $contact = null,
        $update = false,
        $entity_string = '',
        $disk = 'public'
    ) {
        $this->entity = $entity;
        $this->objPdf = $objPdf;
        $this->contact = $contact;
        $this->disk = $disk ?? config('filesystems.default');
        $this->update = $update;
        $this->entity_string = $entity_string;
    }

    public function handle()
    {
        if (!empty($this->contact)) {
            App::setLocale($this->contact->preferredLocale());
        }

        $this->file_path = $this->entity->getPdfFilename();

        if ($this->entity_string === 'dispatch_note') {
            $this->file_path = str_replace(['invoices', 'orders'], 'dispatch_note', $this->file_path);
        }

        if ($this->checkIfExists()) {
            return $this->file_path;
        }

        $design = Design::find($this->entity->getDesignId());

        $entity = empty($this->entity_string) ? strtolower(
            (new ReflectionClass($this->entity))->getShortName()
        ) : $this->entity_string;

        $this->designer =
            new PdfColumns(
                $this->objPdf, $this->entity, $design, $this->entity->account->settings->pdf_variables, $entity
            );

        $this->build();

        return $this->file_path;
    }

    private function checkIfExists()
    {
        $disk = config('filesystems.default');
        $file = Storage::disk($disk)->exists($this->file_path);

        if ($file && $this->update === false) {
            return true;
        }

        return false;
    }

    private function build()
    {
        //get invoice design
        $html = $this->generateEntityHtml(
            $this->objPdf,
            $this->designer,
            $this->entity,
            $this->contact,
            $this->entity_string
        );

        //todo - move this to the client creation stage so we don't keep hitting this unnecessarily
        Storage::makeDirectory(dirname($this->file_path), 0755);

        //\Log::error($html);
        $pdf = $this->makePdf(null, null, $html);

        Storage::disk($this->disk)->put($this->file_path, $pdf);
    }

    private function makePdf($header, $footer, $html)
    {
        $pdf = App::make('dompdf.wrapper');
        //$pdf->setOptions(['isJavascriptEnabled' => true]);
        $pdf->loadHTML($html);
        return $pdf->stream();
    }
}

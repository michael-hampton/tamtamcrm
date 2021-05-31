<?php

namespace App\Jobs\Pdf;

use App\Components\Pdf\GenerateHtml;
use App\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class CreatePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $entity;

    private string $disk;

    private $contact;

    private $file_path;

    private $designer;

    private $objPdf;

    private bool $update = false;

    private string $entity_string = '';

    private bool $html_version;

    private ?int $design_id = null;

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
        bool $update = false,
        bool $html_version = false,
        string $entity_string = '',
        string $disk = 'public',
        int $design_id = null
    )
    {
        $this->entity = $entity;
        $this->objPdf = $objPdf;
        $this->contact = $contact;
        $this->disk = $disk ?? config('filesystems.default');
        $this->update = $update;
        $this->entity_string = !empty($entity_string) ? $entity_string : $objPdf->getEntityString();
        $this->html_version = $html_version;
        $this->design_id = $design_id;
    }

    public function handle()
    {
        if (!empty($this->contact)) {
            App::setLocale($this->contact->preferredLocale());
        }

        $this->file_path = $this->entity->pdf_filename;

        if ($this->entity_string === 'dispatch_note') {
            $this->file_path = str_replace(['invoices', 'orders'], 'dispatch_note', $this->file_path);
        }

        if ($this->checkIfExists()) {
            return $this->file_path;
        }

        $design = Design::find(!empty($this->design_id) ? $this->design_id : $this->entity->design_id);

        $html = $this->build($design);

        return $this->html_version === true ? $html : $this->file_path;
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

    private function build($design)
    {
        //get invoice design
        $html = (new GenerateHtml())->generateEntityHtml(
            $this->objPdf,
            $design,
            $this->entity,
            $this->contact,
            $this->entity_string
        );

        if ($this->html_version) {
            return $html;
        }

        Storage::makeDirectory(dirname($this->file_path), 0755);

        //\Log::error($html);
        $pdf = $this->makePdf(null, null, $html);

        Storage::disk($this->disk)->put($this->file_path, $pdf);

        return true;
    }

    private function makePdf($header, $footer, $html)
    {
        $pdf = App::make('dompdf.wrapper');
        //$pdf->setOptions(['isJavascriptEnabled' => true]);
        $pdf->loadHTML($html);
        return $pdf->stream();
    }
}

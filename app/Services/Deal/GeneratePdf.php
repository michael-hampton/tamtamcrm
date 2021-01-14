<?php

namespace App\Services\Deal;

use App\Components\Pdf\TaskPdf;
use App\Designs\PdfColumns;
use App\Jobs\Pdf\CreatePdf;
use App\Models\CustomerContact;
use App\Models\Deal;
use App\Models\Design;
use Illuminate\Support\Facades\Storage;

class GeneratePdf
{
    private $contact;

    /**
     * @var Deal
     */
    private Deal $deal;

    /**
     * @var bool
     */
    private bool $update;

    /**
     * GeneratePdf constructor.
     * @param Deal $deal
     * @param CustomerContact|null $contact
     * @param bool $update
     */
    public function __construct(Deal $deal, CustomerContact $contact = null, $update = false)
    {
        $this->contact = $contact;
        $this->deal = $deal;
        $this->update = $update;
    }

    public function execute()
    {
        if (!$this->contact) {
            $this->contact = $this->deal->customer->primary_contact()->first();
        }

        $file_path = $this->deal->getPdfFilename();

        $disk = config('filesystems.default');
        $file = Storage::disk($disk)->exists($file_path);

        if ($file && $this->update === false) {
            return $file_path;
        }

        $design = Design::find($this->deal->getDesignId());

        $objPdf = new TaskPdf($this->deal);

        $designer =
            new PdfColumns(
                $objPdf, $this->deal, $design, $this->deal->account->settings->pdf_variables, 'deal'
            );

        return CreatePdf::dispatchNow($objPdf, $this->deal, $file_path, $designer, $this->contact);
    }

}

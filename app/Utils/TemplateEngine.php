<?php

namespace App\Utils;

use App\Traits\BuildVariables;
use App\Traits\GenerateHtml;
use League\CommonMark\CommonMarkConverter;

class TemplateEngine
{
    use BuildVariables;

    public $body;

    public $subject;

    public $template;

    /**
     * @var
     */
    private $objPdf;


    /**
     * TemplateEngine constructor.
     * @param $objPdf
     * @param $body
     * @param $subject
     * @param $entity
     * @param $entity_id
     * @param $template
     */
    public function __construct($objPdf, $body, $subject, $entity, $entity_id, $template)
    {
        $this->body = $body;

        $this->subject = $subject;

        $this->template = $template;

        $this->objPdf = $objPdf;
    }
}

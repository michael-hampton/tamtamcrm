<?php

namespace App\Designs;

class Custom
{
    public $includes;

    public $header;

    public $body;

    public $product;

    public $task;

    public $footer;

    public $name;

    public $design;

    public function __construct($design)
    {
        $this->name = $design->name;

        $this->includes = $design->design->includes;

        $this->header = $design->design->header;

        $this->body = $design->design->body;

        $this->design = $design->design;

        $this->product = $design->design->product ?: '';

        $this->task = $design->design->task ?: '';

        $this->footer = $design->design->footer;
    }

}

<?php

namespace App\Presenters;

class InvoicePresenter extends EntityPresenter
{
    public function customerName()
    {
        return $this->customer->present()->name();
    }
}

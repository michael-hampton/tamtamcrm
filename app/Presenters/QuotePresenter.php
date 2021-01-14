<?php

namespace App\Presenters;

class QuotePresenter extends EntityPresenter
{
    public function customerName()
    {
        return $this->customer->present()->name();
    }
}

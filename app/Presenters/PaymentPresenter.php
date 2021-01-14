<?php

namespace App\Presenters;

class PaymentPresenter extends EntityPresenter
{
    public function customerName()
    {
        return $this->customer->present()->name();
    }
}

<?php

namespace App\Presenters;

class OrderPresenter extends EntityPresenter
{
    public function customerName()
    {
        return $this->customer->present()->name();
    }
}

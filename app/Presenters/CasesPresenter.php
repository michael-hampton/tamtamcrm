<?php

namespace App\Presenters;

class CasesPresenter extends EntityPresenter
{
    public function customerName()
    {
        return $this->customer->present()->name();
    }
}

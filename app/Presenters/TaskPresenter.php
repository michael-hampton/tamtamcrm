<?php

namespace App\Presenters;

class TaskPresenter extends EntityPresenter
{
    public function customerName()
    {
        return $this->customer->present()->name();
    }
}

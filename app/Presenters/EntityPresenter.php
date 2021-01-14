<?php

namespace App\Presenters;

use Laracasts\Presenter\Presenter;

/**
 * Class EntityPresenter
 * @package App\Models\Presenters
 */
class EntityPresenter extends Presenter
{

    public function cityStateZip($city, $state, $postalCode, $swap)
    {
        $str = $city;

        if ($state) {
            if ($str) {
                $str .= ', ';
            }
            $str .= $state;
        }

        if ($swap) {
            return $postalCode . ' ' . $str;
        } else {
            return $str . ' ' . $postalCode;
        }
    }

    public function clientName()
    {
        return $this->customer->present()->name();
    }
}

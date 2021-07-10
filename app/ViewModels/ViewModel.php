<?php


namespace App\ViewModels;


class ViewModel
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
}
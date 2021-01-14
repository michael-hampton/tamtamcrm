<?php

namespace App\Factory;

use App\Models\Design;

class DesignFactory
{
    public static function create(int $account_id, int $user_id): Design
    {
        $design = new Design();
        $design->user_id = $user_id;
        $design->account_id = $account_id;
        $design->is_deleted = false;
        $design->is_active = true;
        $design->is_custom = true;

        return $design;
    }
}

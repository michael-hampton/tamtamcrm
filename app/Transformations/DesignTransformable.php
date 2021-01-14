<?php

namespace App\Transformations;

use App\Models\Design;

trait DesignTransformable
{

    /**
     * @param Design $design
     * @return array
     */
    public function transformDesign(Design $design)
    {
        return [
            'id'        => (int)$design->id,
            'name'      => (string)$design->name,
            'is_custom' => (bool)$design->is_custom,
            'is_active' => (bool)$design->is_active,
            'design'    => $design->design,
        ];
    }

}

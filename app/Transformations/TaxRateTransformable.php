<?php
/**
 * Created by PhpStorm.
 * User: michael.hampton
 * Date: 11/12/2019
 * Time: 20:10
 */

namespace App\Transformations;


use App\Models\TaxRate;

trait TaxRateTransformable
{

    /**
     * @param TaxRate $tax_rate
     * @return array
     */
    protected function transformTaxRate(TaxRate $tax_rate)
    {
        return [
            'id'         => (int)$tax_rate->id,
            'name'       => (string)$tax_rate->name,
            'rate'       => (float)$tax_rate->rate,
            'updated_at' => $tax_rate->updated_at,
            'deleted_at' => $tax_rate->deleted_at,
            'created_at' => $tax_rate->created_at,
            'is_deleted' => (bool)$tax_rate->is_deleted,
        ];
    }
}

<?php


namespace App\Traits;


trait ManageStock
{

    /**
     * @param $quantity
     */
    public function reduceQuantityAvailiable($quantity)
    {
        $this->quantity -= $quantity;
        $this->save();
    }

    /**
     * @param $quantity
     */
    public function increaseQuantityAvailiable($quantity)
    {
        $this->quantity += $quantity;
        $this->save();
    }

    /**
     * @param int $quantity
     */
    public function reduceQuantityReserved(int $quantity)
    {
        $this->reserved_stock -= $quantity;
        $this->save();
    }

    /**
     * @param int $quantity
     */
    public function increaseQuantityReserved(int $quantity)
    {
        $this->reserved_stock += $quantity;
        $this->save();
    }
}
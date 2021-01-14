<?php


namespace App\Components\Promocodes\Scopes;


interface ScopeInterface
{

    /**
     * @param $scope_value
     * @return mixed
     */
    public function setScopeValue($scope_value);

    /**
     * @param \App\Models\Order $order
     * @return mixed
     */
    public function setOrder(\App\Models\Order $order);

    /**
     * @return bool
     */
    public function validate(): bool;
}
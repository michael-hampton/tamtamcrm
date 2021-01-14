<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Repositories\Interfaces;

use App\Models\Address;
use App\Models\Customer;
use App\Repositories\Base\BaseRepositoryInterface;
use Illuminate\Support\Collection;

/**
 *
 * @author michael.hampton
 */
interface AddressRepositoryInterface extends BaseRepositoryInterface
{

    /**
     *
     * @param array $update
     * @return bool
     * @return bool
     */
    public function updateAddress(array $update): bool;

    /**
     *
     */
    public function deleteAddress();

    /**
     *
     * @param string $order
     * @param string $sort
     * @param array $columns
     * @return Collection
     * @return Collection
     */
    public function listAddress(string $order = 'id', string $sort = 'desc', array $columns = ['*']): Collection;

    /**
     *
     */
    public function findCustomer(): Customer;

    /**
     *
     * @param Address $address
     * @param Customer $customer
     */
    public function attachToCustomer(Address $address, Customer $customer);

    /**
     *
     * @param string $text
     * @return Collection
     * @return Collection
     */
    public function searchAddress(string $text): Collection;
}

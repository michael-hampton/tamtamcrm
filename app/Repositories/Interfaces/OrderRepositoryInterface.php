<?php
/**
 * Created by PhpStorm.
 * User: michael.hampton
 * Date: 15/11/2019
 * Time: 21:17
 */

namespace App\Repositories\Interfaces;


use App\Models\Account;
use App\Models\Order;
use App\Requests\SearchRequest;

interface OrderRepositoryInterface
{
    /**
     * @param int $id
     * @return Order
     */
    public function findOrderById(int $id): Order;

    /**
     * @param SearchRequest $search_request
     * @param Account $account
     * @return mixed
     */
    public function getAll(SearchRequest $search_request, Account $account);

    /**
     * @param array $data
     * @param Order $order
     * @return Order|null
     */
    public function createOrder(array $data, Order $order): ?Order;

    /**
     * @param array $data
     * @param Order $credit
     * @return Order|null
     */
    public function updateOrder(array $data, Order $credit): ?Order;

    /**
     * @param array $data
     * @param Order $credit
     * @return Order|null
     */
    public function save(array $data, Order $credit): ?Order;

}
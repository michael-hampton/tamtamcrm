<?php

namespace App\Repositories\Interfaces;

use App\Models\Account;
use App\Models\PurchaseOrder;
use App\Repositories\Base\BaseRepositoryInterface;
use App\Requests\SearchRequest;

interface PurchaseOrderRepositoryInterface extends BaseRepositoryInterface
{

    /**
     * @param int $id
     * @return PurchaseOrder
     */
    public function findPurchaseOrderById(int $id): PurchaseOrder;

    /**
     * @param SearchRequest $search_request
     * @param Account $account
     * @return mixed
     */
    public function getAll(SearchRequest $search_request, Account $account);

    /**
     * @param array $data
     * @param PurchaseOrder $po
     * @return PurchaseOrder|null
     */
    public function createPurchaseOrder(array $data, PurchaseOrder $po): ?PurchaseOrder;

    /**
     * @param array $data
     * @param PurchaseOrder $po
     * @return PurchaseOrder|null
     */
    public function updatePurchaseOrder(array $data, PurchaseOrder $po): ?PurchaseOrder;

    /**
     * @param array $data
     * @param PurchaseOrder $po
     * @return PurchaseOrder|null
     */
    public function save(array $data, PurchaseOrder $po): ?PurchaseOrder;
}

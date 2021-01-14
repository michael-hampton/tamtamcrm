<?php

namespace App\Repositories;

use App\Components\Invitations;
use App\Events\PurchaseOrder\PurchaseOrderWasCreated;
use App\Events\PurchaseOrder\PurchaseOrderWasUpdated;
use App\Models\Account;
use App\Models\PurchaseOrder;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\PurchaseOrderRepositoryInterface;
use App\Requests\SearchRequest;
use App\Search\PurchaseOrderSearch;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class PurchaseOrderRepository
 * @package App\Repositories
 */
class PurchaseOrderRepository extends BaseRepository implements PurchaseOrderRepositoryInterface
{

    /**
     * PurchaseOrderRepository constructor.
     *
     * @param PurchaseOrder $purchase_order
     */
    public function __construct(PurchaseOrder $purchase_order)
    {
        parent::__construct($purchase_order);
        $this->model = $purchase_order;
    }

    /**
     * @param int $id
     * @return PurchaseOrder
     */
    public function findPurchaseOrderById(int $id): PurchaseOrder
    {
        return $this->findOneOrFail($id);
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param array $data
     * @param PurchaseOrder $purchase_order
     * @return PurchaseOrder|null
     */
    public function createPurchaseOrder(array $data, PurchaseOrder $purchase_order): ?PurchaseOrder
    {
        $purchase_order = $this->save($data, $purchase_order);

        if (!empty($data['recurring'])) {
            $recurring = json_decode($data['recurring'], true);
            $purchase_order->service()->createRecurringPurchaseOrder($recurring);
        }


        event(new PurchaseOrderWasCreated($purchase_order));

        return $purchase_order;
    }

    /**
     * @param array $data
     * @param PurchaseOrder $purchase_order
     * @return PurchaseOrder|null
     */
    public function save(array $data, PurchaseOrder $purchase_order): ?PurchaseOrder
    {
        $purchase_order->fill($data);

        $purchase_order = $purchase_order->service()->calculateInvoiceTotals();

        $purchase_order->setNumber();

        $purchase_order->save();

        $this->saveInvitationsForPurchaseOrder($purchase_order, 'purchaseOrder', $data);

        return $purchase_order->fresh();
    }

    private function saveInvitationsForPurchaseOrder($entity, $key, array $data)
    {
        if (empty($data['invitations']) && $entity->invitations->count() === 0) {
            $created = $entity->company->contacts->pluck(
                'id'
            )->toArray();

            (new Invitations())->createNewInvitation($created, $entity);

            return true;
        }

        return (new Invitations())->generateInvitations($entity, $data);
    }

    /**
     * @param array $data
     * @param PurchaseOrder $purchase_order
     * @return Quote|null
     */
    public function updatePurchaseOrder(array $data, PurchaseOrder $purchase_order): ?PurchaseOrder
    {
        $purchase_order = $this->save($data, $purchase_order);

        event(new PurchaseOrderWasUpdated($purchase_order));

        return $purchase_order;
    }

    /**
     * @param SearchRequest $search_request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function getAll(SearchRequest $search_request, Account $account)
    {
        return (new PurchaseOrderSearch($this))->filter($search_request, $account);
    }
}

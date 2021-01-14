<?php
/**
 * Created by PhpStorm.
 * User: michael.hampton
 * Date: 22/12/2019
 * Time: 13:05
 */

namespace App\Repositories;

use App\Events\Order\OrderWasBackordered;
use App\Events\Order\OrderWasCreated;
use App\Events\Order\OrderWasUpdated;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\Task;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Requests\SearchRequest;
use App\Search\LengthAwarePaginator;
use App\Search\OrderSearch;
use App\Traits\BuildVariables;
use Exception;
use Illuminate\Support\Collection;

/**
 * Class OrderRepository
 * @package App\Repositories
 */
class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    use BuildVariables;

    /**
     * OrderRepository constructor.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        parent::__construct($order);
        $this->model = $order;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param int $id
     *
     * @return Order
     * @throws Exception
     */
    public function findOrderById(int $id): Order
    {
        return $this->findOneOrFail($id);
    }

    /**
     * @param SearchRequest $search_request
     * @param Account $account
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAll(SearchRequest $search_request, Account $account)
    {
        return (new OrderSearch($this))->filter($search_request, $account);
    }

    /**
     * @param array $data
     * @param Order $order
     * @return Order|null
     * @return Order|null
     */
    public function updateOrder(array $data, Order $order): ?Order
    {
        $original_order = $order->line_items;

        $order->fill($data);
        $order = $this->save($data, $order);

        event(new OrderWasUpdated($order));

        $this->updateInventory($original_order, $data);

        return $order;
    }

    /**
     * @param array $data
     * @param Order $order
     * @return Order
     */
    public function save(array $data, Order $order): Order
    {
        $order->fill($data);
        $order = $this->populateDefaults($order);
        $order = $this->formatNotes($order);

        $order = $order->service()->calculateInvoiceTotals();
        $order->setNumber();

        $order->save();

        $this->saveInvitations($order, $data);

        return $order->fresh();
    }

    private function updateInventory($line_items, $data)
    {
        if (empty($data['line_items'])) {
            return true;
        }

        $new_lines = collect($data['line_items'])->keyBy('product_id')->toArray();

        foreach ($line_items as $line_item) {
            if ($line_item->type_id !== Invoice::PRODUCT_TYPE) {
                continue;
            }

            if (empty($new_lines[$line_item->product_id])) {
                $difference = $line_item->quantity;
                $product->increment('quantity', $difference);
                $product->save();
                continue;
            }

            $new_line = $new_lines[$line_item->product_id];

            $product = Product::where('id', '=', $line_item->product_id)->first();

            if ($new_line['quantity'] > $line_item->quantity) {
                $difference = $new_line['quantity'] - $line_item->quantity;
                $product->increment('quantity', $difference);
                $product->save();
            }

            if ($new_line['quantity'] < $line_item->quantity) {
                $difference = $line_item->quantity - $new_line['quantity'];
                $product->decrement('quantity', $difference);
                $product->save();
            }
        }

        return true;
    }

    /**
     * @param array $data
     * @param Order $order
     * @return Order|null
     */
    public function createOrder(array $data, Order $order): ?Order
    {
        $order->fill($data);

        if ($order->customer->getSetting('inventory_enabled') === true) {
            $order = $order->service()->fulfillOrder($this);

            /************** hold stock ***************************/
            // if the order hasnt been failed at this point then reserve stock
            if ($order->status_id !== Order::STATUS_ORDER_FAILED) {
                $order->service()->holdStock();
            }
        }

        // save the order
        $order = $this->save($data, $order);

        // send backorder notification if order has been backordered
        if ($order->status_id === Order::STATUS_BACKORDERED) {
            event(new OrderWasBackordered($order));
        }

        event(new OrderWasCreated($order));

        return $order;
    }

    public function getOrdersForTask(Task $task): Collection
    {
        return $this->model->where('task_id', '=', $task->id)->get();
    }

}

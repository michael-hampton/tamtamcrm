<?php

namespace App\Repositories\Base;

use App\Services\Order\SendOrder;
use App\Components\Currency\CurrencyConverter;
use App\Components\Invitations;
use App\Components\InvoiceCalculator\InvoiceCalculator;
use App\Models\Invitation;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Input;
use ReflectionClass;
use ReflectionException;

class BaseRepository implements BaseRepositoryInterface
{

    protected $model;

    /**
     * BaseRepository constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $columns
     * @param string $orderBy
     * @param string $sortBy
     * @return mixed
     */
    public function all($columns = ['*'], string $orderBy = 'id', string $sortBy = 'asc')
    {
        return $this->model->orderBy($orderBy, $sortBy)->get($columns);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * @param  $id
     * @return mixed
     * @throws ModelNotFoundException
     */
    public function findOneOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return Collection
     */
    public function findBy(array $data)
    {
        return $this->model->where($data)->get();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function findOneBy(array $data)
    {
        return $this->model->where($data)->first();
    }

    /**
     * @param array $data
     * @return mixed
     * @throws ModelNotFoundException
     */
    public function findOneByOrFail(array $data)
    {
        return $this->model->where($data)->firstOrFail();
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function delete(): bool
    {
        return $this->model->delete();
    }

    /**
     * @param $entity
     * @return |null |null |null |null
     * @throws ReflectionException
     */
    public function markSent($entity)
    {
        if (get_class($entity) === 'App\Models\Order') {
            if (!$entity->invoice_id) {
                return null;
            }
        }

        if (!$entity->canBeSent()) {
            return $entity;
        }

        $entity->invitations()->where('sent_date', '=', null)->update(['sent_date' => Carbon::now()]);

        $entity->setStatus($entity::STATUS_SENT);
        $entity->save();

        if (get_class($entity) === 'App\Models\Order') {
            (new SendOrder($entity))->execute();
        }

        if (get_class($entity) === 'App\Models\Invoice') {
            $entity->updateCustomerBalance($entity->fresh()->balance);
        }

        $class = (new ReflectionClass($entity))->getShortName();
        $event_class = "App\Events\\" . $class . "\\" . $class . "WasMarkedSent";

        if (class_exists($event_class)) {
            event(new $event_class($entity));
        }

        return $entity;
    }

    /**
     * Paginate arrays
     *
     * @param array $data
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginateArrayResults(array $data, int $perPage = 50)
    {
        $page = request()->input('page', 1);
        $offset = ($page * $perPage) - $perPage;
        return new LengthAwarePaginator(
            array_values(array_slice($data, $offset, $perPage, true)), count($data),
            $perPage, $page, [
                'path'  => app('request')->url(),
                'query' => app('request')->query()
            ]
        );
    }

    public function paginateCollection($items, $perPage = 15, $options = [])
    {
        $page = Input::get('page', 1);
        //$items = $items->forPage($page, $perPage); //Filter the page var


        return new LengthAwarePaginator(
            $items->forPage($page, $perPage), count($items) ?: $this->count(), $perPage,
            $page, [
                'path'  => app('request')->url(),
                'query' => app('request')->query(),
            ]
        );
    }

    public function getInvitation($invitation, $resource)
    {
        if (!isset($invitation['key'])) {
            return false;
        }

        $invitation = Invitation::whereRaw("BINARY `key`= ?", [$invitation['key']])->first();

        return $invitation;
    }

    public function calculateTotals($entity)
    {
        if (empty($entity->line_items)) {
            return $entity;
        }

        $objInvoice = (new InvoiceCalculator($entity))->build();

        return $objInvoice->rebuildEntity();
    }

    /**
     * @param $entity
     * @param array $data
     * @return bool
     */
    protected function saveInvitations($entity, array $data): bool
    {
        if (empty($data['invitations']) && $entity->invitations->count() === 0) {
            $created = $entity->customer->contacts->pluck(
                'id'
            )->toArray();
            (new Invitations())->createNewInvitation($created, $entity);

            return true;
        }

        return (new Invitations())->generateInvitations($entity, $data);
    }

}

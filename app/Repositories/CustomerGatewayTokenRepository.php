<?php


namespace App\Repositories;


use App\Events\Deal\DealWasCreated;
use App\Events\Deal\DealWasUpdated;
use App\Models\Customer;
use App\Models\CustomerGatewayToken;
use App\Models\Deal;
use App\Repositories\Base\BaseRepository;

class CustomerGatewayTokenRepository extends BaseRepository
{
    /**
     * CustomerGatewayTokenRepository constructor.
     * @param CustomerGatewayToken $customer_gateway_token
     */
    public function __construct(CustomerGatewayToken $customer_gateway_token)
    {
        parent::__construct($customer_gateway_token);
        $this->model = $customer_gateway_token;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param int $id
     * @return CustomerGatewayToken
     */
    public function findCustomerGatewayTokenById(int $id): CustomerGatewayToken
    {
        return $this->findOneOrFail($id);
    }

    /**
     * @param array $data
     * @param CustomerGatewayToken $customer_gateway_token
     * @return CustomerGatewayToken|null
     */
    public function create(array $data, CustomerGatewayToken $customer_gateway_token): ?CustomerGatewayToken
    {
        $customer_gateway_token->fill($data);
        $customer_gateway_token->save();


        return $customer_gateway_token->fresh();
    }

    /**
     * @param array $data
     * @param CustomerGatewayToken $customer_gateway_token
     * @return CustomerGatewayToken|null
     */
    public function update(array $data, CustomerGatewayToken $customer_gateway_token): ?CustomerGatewayToken
    {
        $customer_gateway_token->update($data);

        return $customer_gateway_token->fresh();
    }
}
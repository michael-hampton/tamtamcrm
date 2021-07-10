<?php

namespace App\Http\Controllers;

use App\Factory\CustomerGatewayTokenFactory;
use App\Factory\InvoiceFactory;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\CustomerGatewayToken;
use App\Models\Invoice;
use App\Repositories\CustomerGatewayTokenRepository;
use App\Requests\CustomerGatewayToken\CreateCustomerGatewayTokenRequest;
use App\Requests\CustomerGatewayToken\UpdateCustomerGatewayTokenRequest;
use App\Requests\SearchRequest;
use App\Search\CustomerGatewayTokenSearch;
use App\Search\InvoiceSearch;
use App\Transformations\CustomerGatewayTokenTransformable;
use App\Transformations\InvoiceTransformable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class CustomerGatewayTokenController extends Controller
{
    use CustomerGatewayTokenTransformable;

    private CustomerGatewayTokenRepository $customer_gateway_token_repository;

    public function __construct(CustomerGatewayTokenRepository $customer_gateway_token_repository)
    {
        $this->customer_gateway_token_repository = $customer_gateway_token_repository;
    }

    /**
     * @param SearchRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $tokens =
            (new CustomerGatewayTokenSearch($this->customer_gateway_token_repository))->filter($request, auth()->user()->account_user()->account);

        return response()->json($tokens);
    }

    /**
     * @param CreateCustomerGatewayTokenRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateCustomerGatewayTokenRequest $request)
    {
        $customer = Customer::find($request->input('customer_id'));

        $token = $this->customer_gateway_token_repository->create(
            $request->all(),
            CustomerGatewayTokenFactory::create(auth()->user()->account_user()->account, auth()->user(), $customer)
        );

        return response()->json($this->transformCustomerGatewayToken($token));
    }

    /**
     * @param CustomerGatewayToken $customer_gateway_token
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(CustomerGatewayToken $customer_gateway_token)
    {
        return response()->json($this->transformCustomerGatewayToken($customer_gateway_token));

    }

    /**
     * @param UpdateCustomerGatewayTokenRequest $request
     * @param CustomerGatewayToken $customer_gateway_token
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCustomerGatewayTokenRequest $request, CustomerGatewayToken $customer_gateway_token)
    {
        $token = $this->customer_gateway_token_repository->update($request->all(), $customer_gateway_token);
        return response()->json($this->transformCustomerGatewayToken($token));
    }

    /**
     * @param int $id
     * @return mixed
     * @throws Exception
     */
    public function archive(CustomerGatewayToken $customer_gateway_token)
    {
        $customer_gateway_token->archive();
        return response()->json([], 200);
    }

    /**
     * @param CustomerGatewayToken $customer_gateway_token
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(CustomerGatewayToken $customer_gateway_token)
    {
        $this->authorize('delete', $customer_gateway_token);

        $customer_gateway_token->deleteEntity();
        return response()->json([], 200);
    }

    /**
     * @param CustomerGatewayToken $customer_gateway_token
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(CustomerGatewayToken $customer_gateway_token)
    {
        $customer_gateway_token->restoreEntity();
        return response()->json([], 200);
    }
}

<?php


namespace App\Http\Controllers;


use App\Components\Promocodes\Promocodes;
use App\Exceptions\InvalidPromocodeException;
use App\Models\CompanyToken;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Promocode;
use App\Repositories\PromocodeRepository;
use App\Requests\Promocode\CreatePromocode;
use App\Requests\Promocode\UpdatePromocode;
use App\Requests\SearchRequest;
use App\Search\PromocodeSearch;
use App\Transformations\PromocodeTransformable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class PromocodeController
 * @package App\Http\Controllers
 */
class PromocodeController extends Controller
{
    use PromocodeTransformable;

    /**
     * @var PromocodeRepository
     */
    private PromocodeRepository $promocode_repo;

    /**
     * PromocodeController constructor.
     * @param PromocodeRepository $promocode_repo
     */
    public function __construct(PromocodeRepository $promocode_repo)
    {
        $this->promocode_repo = $promocode_repo;
    }

    /**
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $group_settings = (new PromocodeSearch($this->promocode_repo))->filter(
            $request,
            auth()->user()->account_user()->account
        );

        return response()->json($group_settings);
    }


    public function show(string $code)
    {
        $token_sent = \request()->bearerToken();
        $token = CompanyToken::whereToken($token_sent)->first();
        $account = $token->account;

        return (new Promocodes)->check($account, $code);
    }

    public function store(CreatePromocode $request)
    {
        $data = [
            'scope'       => $request->input('scope'),
            'scope_value' => $request->input('scope_value')
        ];

        $promocodes = (new Promocodes)->create(
            auth()->user()->account_user()->account,
            $request->input('amount'),
            $request->input('reward'),
            $data,
            $request->input('expires_at'),
            $request->input('quantity'),
            false,
            $request->input('description'),
            $request->input('amount_type'),
        );

        $created = [];

        foreach ($promocodes as $promocode) {
            $promocode = (new Promocode)->fill($promocode);

            $created[] = $this->transformPromocodes($promocode);
        }

        return response()->json($created);
    }

    /**
     * @param UpdatePromocode $request
     * @param int $id
     * @return JsonResponse
     * @return JsonResponse
     */
    public function update(UpdatePromocode $request, int $id)
    {
        $promocode = $this->promocode_repo->findPromocodeById($id);

        $data = [
            'scope'       => $request->input('scope'),
            'scope_value' => $request->input('scope_value')
        ];

        $promocode->update(
            [
                'amount_type' => $request->input('amount_type'),
                'reward'      => $request->input('reward'),
                'data'        => $data,
                'expires_at'  => $request->input('expires_at'),
                'quantity'    => $request->input('quantity'),
                'description' => $request->input('description')
            ]
        );

        return response()->json($this->transformPromocodes($promocode->fresh()));
    }

    public function validateCode(Request $request)
    {
        $order = new Order;
        $order->fill($request->all());

        $token_sent = \request()->bearerToken();
        $token = CompanyToken::whereToken($token_sent)->first();
        $account = $token->account;
        $customer = Customer::find($request->customer_id);

        $response = (new Promocodes)->check($account, $request->voucher_code, $order, $customer);

        if (!$response) {
            return response()->json('Invalid code used', 400);
        }

        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return Promocode|bool
     */
    public function apply(Request $request)
    {
        $order = Order::find($request->order_id);
        $customer = Customer::find($request->customer_id);
        $token_sent = \request()->bearerToken();
        $token = CompanyToken::whereToken($token_sent)->first();
        $account = $token->account;
        return (new Promocodes)->apply($order, $account, $request->code, $customer);
    }

    /**
     * @param int $id
     * @throws InvalidPromocodeException
     */
    public function destroy(int $id)
    {
        $promocode = $this->promocode_repo->findPromocodeById($id);
        (new Promocodes)->disable($promocode->code);
    }
}
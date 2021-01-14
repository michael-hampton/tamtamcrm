<?php

namespace App\Http\Controllers;

use App\Factory\CompanyTokenFactory;
use App\Models\CompanyToken;
use App\Repositories\TokenRepository;
use App\Requests\SearchRequest;
use App\Requests\Token\CreateTokenRequest;
use App\Requests\Token\UpdateTokenRequest;
use App\Search\TokenSearch;
use App\Transformations\TokenTransformable;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Class TokenController
 * @package App\Http\Controllers
 */
class TokenController extends Controller
{
    use TokenTransformable;

    public $token_repo;

    /**
     * TokenController constructor.
     * @param TokenRepository $token_repo
     */
    public function __construct(TokenRepository $token_repo)
    {
        $this->token_repo = $token_repo;
    }

    /**
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $invoices = (new TokenSearch($this->token_repo))->filter($request, auth()->user()->account_user()->account);
        return response()->json($invoices);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id)
    {
        $token = $this->token_repo->findTokenById($id);
        return response()->json($this->transform($token));
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function edit(int $id)
    {
        $token = $this->token_repo->findTokenById($id);
        return response()->json($this->transform($token));
    }

    /**
     * @param int $id
     * @param UpdateTokenRequest $request
     * @return JsonResponse
     */
    public function update(int $id, UpdateTokenRequest $request)
    {
        $token = $this->token_repo->findTokenById($id);

        $token = $this->token_repo->save($request->all(), $token);

        return response()->json($this->transform($token->fresh()));
    }

    /**
     * @return JsonResponse
     */
    public function create()
    {
        $token = CompanyTokenFactory::create(
            auth()->user()->account_user()->account_id,
            auth()->user()->id,
            auth()->user()->account_user()->account->domain_id
        );

        return response()->json($this->transform($token));
    }

    /**
     * @param CreateTokenRequest $request
     * @return JsonResponse
     */
    public function store(CreateTokenRequest $request)
    {
        $company_token = CompanyTokenFactory::create(
            auth()->user()->account_user()->account_id,
            auth()->user()->id,
            auth()->user()->account_user()->account->domain_id
        );
        $token = $this->token_repo->save($request->all(), $company_token);
        return response()->json($this->transform($token));
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(int $id)
    {
        $token = $this->token_repo->findTokenById($id);

        //may not need these destroy routes as we are using actions to 'archive/delete'
        $token->delete();

        return response()->json($this->transform($token));
    }

    /**
     * @return JsonResponse
     */
    public function bulk()
    {
        $action = request()->input('action');

        $ids = request()->input('ids');
        $tokens = CompanyToken::withTrashed()->find($ids);

        return response()->json($tokens);
    }

}

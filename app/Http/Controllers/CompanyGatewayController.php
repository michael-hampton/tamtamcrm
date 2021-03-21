<?php

namespace App\Http\Controllers;

use App\Factory\CompanyGatewayFactory;
use App\Models\CompanyGateway;
use App\Models\ErrorLog;
use App\Repositories\AccountRepository;
use App\Repositories\CompanyGatewayRepository;
use App\Requests\CompanyGateway\StoreCompanyGatewayRequest;
use App\Requests\CompanyGateway\UpdateCompanyGatewayRequest;
use App\Requests\SearchRequest;
use App\Search\CompanyGatewaySearch;
use App\Transformations\CompanyGatewayTransformable;
use App\Transformations\ErrorLogTransformable;

/**
 * Class CompanyGatewayController
 * @package App\Http\Controllers
 */
class CompanyGatewayController extends Controller
{
    use CompanyGatewayTransformable;

    private $account_repo;
    private $company_gateway_repo;

    /**
     * CompanyGatewayController constructor.
     * @param AccountRepository $account_repo
     * @param CompanyGatewayRepository $company_gateway_repo
     */
    public function __construct(AccountRepository $account_repo, CompanyGatewayRepository $company_gateway_repo)
    {
        $this->account_repo = $account_repo;
        $this->company_gateway_repo = $company_gateway_repo;
    }

    public function index(SearchRequest $request)
    {
        $invoices =
            (new CompanyGatewaySearch($this->company_gateway_repo))->filter(
                $request,
                auth()->user()->account_user()->account
            );

        return response()->json($invoices);
    }

    public function store(StoreCompanyGatewayRequest $request)
    {
        $company_gateway = $this->company_gateway_repo->create(
            $request->all(),
            CompanyGatewayFactory::create(auth()->user()->account_user()->account_id, auth()->user()->id)
        );

        return response()->json($this->transformCompanyGateway($company_gateway));
    }

    /**
     * @param UpdateCompanyGatewayRequest $request
     * @param int $id
     * @return mixed
     */
    public function update(UpdateCompanyGatewayRequest $request, CompanyGateway $company_gateway)
    {
        $company_gateway = $this->company_gateway_repo->update($request->all(), $company_gateway);

        return response()->json($this->transformCompanyGateway($company_gateway));
    }

    /**
     * @param string $gateway_key
     * @return mixed
     */
    public function show(string $gateway_key)
    {
        $company_gateway = $this->company_gateway_repo->getCompanyGatewayByGatewayKey($gateway_key);

        if (!$company_gateway) {
            return response()->json([]);
        }

        return response()->json($this->transformCompanyGateway($company_gateway));
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function restore(int $id)
    {
        $company_gateway = CompanyGateway:: withTrashed()->where('id', '=', $id)->first();
        $company_gateway->restore();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function archive(CompanyGateway $company_gateway)
    {
        $company_gateway->archive();
    }

    public function destroy(CompanyGateway $company_gateway)
    {
        $this->authorize('delete', $company_gateway);

        $company_gateway->deleteEntity();
        return response()->json([], 200);
    }

    public function getErrorLogs(CompanyGateway $company_gateway)
    {
        $error_logs = $company_gateway->error_logs();

        $error_logs = $error_logs->map(
            function (ErrorLog $error_log) {
                return (new ErrorLogTransformable())->transformErrorLog($error_log);
            }
        )->all();

        return response()->json($error_logs);
    }
}

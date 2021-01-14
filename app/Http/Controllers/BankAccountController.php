<?php

namespace App\Http\Controllers;

use App\Components\OFX\OFXImport;
use App\Factory\BankAccountFactory;
use App\Models\Company;
use App\Models\CompanyContact;
use App\Models\Expense;
use App\Repositories\BankAccountRepository;
use App\Repositories\CompanyContactRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\ExpenseRepository;
use App\Requests\BankAccount\CreateBankAccountRequest;
use App\Requests\BankAccount\UpdateBankAccountRequest;
use App\Requests\SearchRequest;
use App\Search\BankAccountSearch;
use App\Transformations\BankAccountTransformable;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class BankAccountController
 * @package App\Http\Controllers
 */
class BankAccountController extends Controller
{
    use BankAccountTransformable;

    /**
     * @var BankAccountRepository
     */
    private BankAccountRepository $bank_account_repo;

    /**
     * BankAccountController constructor.
     * @param BankAccountRepository $bank_account_repository
     */
    public function __construct(BankAccountRepository $bank_account_repository)
    {
        $this->bank_account_repo = $bank_account_repository;
    }

    /**
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $bank_accounts = (new BankAccountSearch($this->bank_account_repo))->filter(
            $request,
            auth()->user()->account_user()->account
        );
        return response()->json($bank_accounts);
    }

    /**
     * @param CreateBankAccountRequest $request
     * @return JsonResponse
     */
    public function store(CreateBankAccountRequest $request)
    {
        $bank_account = $this->bank_account_repo->save(
            $request->all(),
            BankAccountFactory::create(auth()->user()->account_user()->account, auth()->user())
        );
        return response()->json($this->transformBankAccount($bank_account));
    }

    /**
     * @param UpdateBankAccountRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(UpdateBankAccountRequest $request, $id)
    {
        $bank_account = $this->bank_account_repo->findBankAccountById($id);

        $bank_account_repo = new BankAccountRepository($bank_account);
        $bank_account = $bank_account_repo->save($request->all(), $bank_account);

        return response()->json($this->transformBankAccount($bank_account));
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id)
    {
        $bank_account = $this->bank_account_repo->findBankAccountById($id);
        $bank_account->deleteEntity();

        return response()->json('deleted');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function preview(Request $request)
    {
        $file = $request->file('file');

        $transactions = (new OFXImport())->preview($file);

        return response()->json($transactions);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function import(Request $request)
    {
        $result = (new OFXImport())->import(
            auth()->user(),
            auth()->user()->account_user()->account,
            new ExpenseRepository(new Expense),
            new CompanyRepository(
                new Company, new CompanyContactRepository(new CompanyContact())
            ),
            $request->input('data'),
            $request->input('checked'),
            $request->input('bank_id') ?? null
        );

        if (empty($result)) {
            return response()->json([], 422);
        }

        return response()->json($result);
    }
}

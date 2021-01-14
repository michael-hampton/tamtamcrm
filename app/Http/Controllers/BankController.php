<?php

namespace App\Http\Controllers;

use App\Repositories\BankRepository;
use App\Requests\Brand\CreateBankAccountRequest;
use App\Requests\Brand\UpdateBankAccountRequest;
use App\Requests\SearchRequest;
use App\Search\BankSearch;
use App\Transformations\BankTransformable;
use Illuminate\Http\JsonResponse;

/**
 * Class BankController
 * @package App\Http\Controllers
 */
class BankController extends Controller
{
    use BankTransformable;

    /**
     * @var BankRepository
     */
    private BankRepository $bank_repo;

    /**
     * BankAccountController constructor.
     * @param BankRepository $bank_repository
     */
    public function __construct(BankRepository $bank_repository)
    {
        $this->bank_repo = $bank_repository;
    }

    /**
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $banks = (new BankSearch($this->bank_repo))->filter(
            $request,
            auth()->user()->account_user()->account
        );
        return response()->json($banks);
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: michael.hampton
 * Date: 23/11/2019
 * Time: 13:46
 */

namespace App\Http\Controllers;

use App\Repositories\Interfaces\CurrencyRepositoryInterface;
use Illuminate\Http\Response;

class CurrencyController extends Controller
{
    private $currencyRepo;

    public function __construct(CurrencyRepositoryInterface $currencyRepository)
    {
        $this->currencyRepo = $currencyRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $list = $this->currencyRepo->listCurrencies('created_at', 'desc');
        return response()->json($list);
    }
}

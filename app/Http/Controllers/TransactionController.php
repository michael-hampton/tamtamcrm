<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Response;

class TransactionController extends Controller
{

    public function __construct()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     *
     */
    public function index()
    {
        $transaction = Transaction::whereAccountId(auth()->user()->account_user()->account_id)->orderBy(
            'id',
            'ASC'
        );

        return response()->json($transaction);
    }


}

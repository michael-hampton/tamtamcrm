<?php

namespace App\Http\Controllers;

use App\Mail\SupportMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{

    /**
     * Send a support message.
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request)
    {
        $request->validate(
            [
                'message' => ['required'],
            ]
        );

        Mail::to(config('taskmanager.support_email'))->send(new SupportMessage($request->message));

        return response()->json(
            [
                'success' => true
            ]
        );
    }
}

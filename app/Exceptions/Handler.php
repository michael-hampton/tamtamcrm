<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [//
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $exception
     * @return void
     * @throws Throwable
     */
    public function report(Throwable $exception)
    {
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
//        if (($request->is('api/*') || $request->wantsJson()) && ! $exception instanceof \Illuminate\Validation\ValidationException) {
//            $json = $exception->getMessage();
//
//            // Default response of 400
//            $status = 400;
//
//            // If this exception is an instance of HttpException
//            if ($this->isHttpException($exception)) {
//                // Grab the HTTP status code from the Exception
//                $status = $exception->getStatusCode();
//            }
//
//            return response()->json($json, $status);
//        }
        return parent::render($request, $exception);
    }

}

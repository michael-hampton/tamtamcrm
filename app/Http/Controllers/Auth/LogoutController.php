<?php


namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Models\CompanyToken;
use Illuminate\Support\Facades\Auth;
use Namshi\JOSE\JWT;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LogoutController extends Controller
{
    public function __invoke()
    {
        $token_sent = request()->bearerToken();

        Auth::logout();

        $token = CompanyToken::whereToken($token_sent)->delete();

        try {
            JWTAuth::parseToken()->invalidate($token_sent);
            return response()->json(['error' => true, 'message' => trans('auth.logged_out')]);
        } catch (TokenExpiredException $exception) {
            return response()->json(['error' => true, 'message' => trans('auth.token.expired')], 401);
        } catch (TokenInvalidException $exception) {
            return response()->json(['error' => true, 'message' => trans('auth.token.invalid'), 401]);
        } catch (JWTException $exception) {
            return response()->json(['error' => true, 'message' => trans('auth.token.missing'), 500]);
        }
    }
}
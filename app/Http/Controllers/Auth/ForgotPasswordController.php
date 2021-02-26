<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{

    use SendsPasswordResetEmails;
    
    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        //https://muhammadatt.medium.com/building-api-based-password-recovery-in-laravel-7-for-vue-react-angular-single-page-applications-cb6f0f28ea55
        // Validate user input
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255' ],
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        // Attempt to send the password reset email to user.  
        $response = $this->broker()->sendResetLink(
            $this->credentials($request)
        );
        
        // After attempting to send the link, we can examine the response to see 
        // the message we need to show to the user and then send out a 
        // proper response.

        return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($request, $response)
                    : $this->sendResetLinkFailedResponse($request, $response);
    }
    /**
     * Send the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        
        // On success, a string $response is returned with value of RESET_LINK_SENT 
        // from the Password facade (the default is "passwords.sent") 
        // Laravel trans() function translates this response to the text  
        // designated in resources/lang/en/passwords.php

        return response()->json(['success' => ["message" => trans($response)] ], 200);   
    }
    /**
     * Send the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {        
        return response()->json(['error' => ["message" => trans($response)] ], 422);    
    }
}

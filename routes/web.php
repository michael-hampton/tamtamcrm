<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('two_factor_auth')->group(function () {
    Route::get('setup/final', 'SetupController@finish')->name('home');
});


Route::get("account/download/{zipFilename}", 'AccountController@export')
    ->name('account-data-exports');

Route::get('reset-password/{token}', 'Auth\ResetPasswordController@getPassword');
Route::post('/reset-password', 'Auth\ResetPasswordController@updatePassword')->name('password.request');
Route::get('/2fa/enable', 'TwoFactorController@enableTwoFactorAuthenticationForUser');
Route::get('/2fa', 'TwoFactorController@show2faForm');
Route::post('/2fa', 'TwoFactorController@verifyToken');
Route::get('setup', 'SetupController@welcome')->name('setup.welcome');
Route::get('setup/requirements', 'SetupController@requirements')->name('setup.requirements');
Route::get('setup/permissions', 'SetupController@permissions')->name('setup.permissions');
Route::get('setup/environment', 'SetupController@environmentMenu')->name('setup.environment');
Route::get('setup/database', 'SetupController@database')->name('setup.database');
Route::get('setup/user', 'SetupController@user')->name('setup.user');
Route::get('setup/final', 'SetupController@finish')->name('setup.final');
Route::get('setup/twofactor/{user}', 'SetupController@twoFactorSetup')->name('setup.2fa');
Route::get('setup/environmentWizard', 'SetupController@environmentWizard')->name('setup.environment-wizard');
Route::post('setup/user/save', 'SetupController@saveUser')->name('setup.saveUser');
Route::post('setup/environment/save', 'SetupController@saveWizard')->name('setup.environment-save-wizard');
Route::get('setup/environmentClassic', 'SetupController@environmentClassic')->name('setup.environment-classic');
Route::get('dashboard', 'DashboardController@index');
Route::get('buy_now', 'BuyNowController@buyNowTrigger');
Route::get('pay_now/process/{invoice_id}', 'PaymentController@buyNow');
Route::get('pay_now/success', 'PaymentController@buyNowSuccess');
Route::get('company_gateways/stripe/complete', 'CompanyGatewayController@completeStripeConnect');
Route::get('company_gateways/stripe/refresh', 'CompanyGatewayController@refreshStripeConnect');

Route::view('/{path?}', 'app');

Route::get('auth/google', [\App\Http\Controllers\LoginController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [\App\Http\Controllers\LoginController::class, 'handleGoogleCallback']);
Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');
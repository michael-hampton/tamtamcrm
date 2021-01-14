<?php

namespace App\Providers;

use App\Components\Mail\CaseMailHandler;
use App\Components\Mail\LeadMailHandler;
use BeyondCode\Mailbox\Facades\Mailbox;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//        DB::listen(function($query) {
//            Log::info(
//                $query->sql,
//                $query->bindings,
//                $query->time
//            );
//        });

        Mailbox::to('leads@tamtamcrm.com', LeadMailHandler::class);
        Mailbox::to('{hash}_cases+{number}@tamtamcrm.com', CaseMailHandler::class);
        Mailbox::to('{hash}_cases@tamtamcrm.com', CaseMailHandler::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

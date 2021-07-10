<?php

namespace App\Providers;

use App\Components\Mail\CaseMailHandler;
use App\Components\Mail\LeadMailHandler;
use BeyondCode\Mailbox\Facades\Mailbox;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $this->setupListener();

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

    private function setupListener()
    {

        if (!config('taskmanager.slow_query_log_enabled')) {
            return;
        }

        DB::listen(function (QueryExecuted $queryExecuted) {

            $sql = $queryExecuted->sql;
            $bindings = $queryExecuted->bindings;
            $time = $queryExecuted->time;

            $logSqlQueriesSlowerThan = (float)config('taskmanager.time-to-log', -1);
            if ($logSqlQueriesSlowerThan < 0 || $time < $logSqlQueriesSlowerThan) {
                return;
            }

            $level = config('taskmanager.log-level', 'debug');
            try {
                foreach ($bindings as $val) {
                    if ($val instanceof \DateTime) {
                        continue;
                    }
                    $sql = preg_replace('/\?/', "'{$val}'", $sql, 1);
                }

                Log::channel('single')->log($level, $time . '  ' . $sql);
            } catch (\Exception $e) {
                //  be quiet on error
            }
        });
    }
}

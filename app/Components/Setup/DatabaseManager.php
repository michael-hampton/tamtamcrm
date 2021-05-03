<?php

namespace App\Components\Setup;

use App\Actions\Account\CreateAccount;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Output\BufferedOutput;

class DatabaseManager
{
    /**
     * @return array
     */
    public function migrateAndSeed()
    {
        $outputLog = new BufferedOutput;

        $this->sqlite($outputLog);

        $result = $this->migrate($outputLog);

        $user = $this->createUser();

        $this->seed($outputLog);

        return [
            'user' => $user,
            'result' => $result
        ];
    }

    private function createUser()
    {
        $data = Cache::pull('user_data');

        return (new CreateAccount())->execute($data);
    }

    /**
     * Check database type. If SQLite, then create the database file.
     *
     * @param BufferedOutput $outputLog
     */
    private function sqlite(BufferedOutput $outputLog)
    {
        if (DB::connection() instanceof SQLiteConnection) {
            $database = DB::connection()->getDatabaseName();
            if (!file_exists($database)) {
                touch($database);
                DB::reconnect(Config::get('database.default'));
            }
            $outputLog->write('Using SqlLite database: ' . $database, 1);
        }
    }

    /**
     * Run the migration and call the seeder.
     *
     * @param BufferedOutput $outputLog
     * @return array
     */
    private function migrate(BufferedOutput $outputLog)
    {
        try {
            Artisan::call('migrate', ['--force' => true], $outputLog);
        } catch (Exception $e) {
            return $this->response($e->getMessage(), 'error', $outputLog);
        }
    }

    /**
     * Return a formatted error messages.
     *
     * @param string $message
     * @param string $status
     * @param BufferedOutput $outputLog
     * @return array
     */
    private function response($message, $status, BufferedOutput $outputLog)
    {
        return [
            'status' => $status,
            'message' => $message,
            'dbOutputLog' => $outputLog->fetch(),
        ];
    }

    /**
     * Seed the database.
     *
     * @param BufferedOutput $outputLog
     * @return array
     */
    private function seed(BufferedOutput $outputLog)
    {
        try {
            Artisan::call('db:seed', ['--force' => true], $outputLog);
        } catch (Exception $e) {
            return $this->response($e->getMessage(), 'error', $outputLog);
        }

        return $this->response(trans('texts.final.finished'), 'success', $outputLog);
    }
}

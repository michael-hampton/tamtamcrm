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
     * @var array
     */
    private array $error = [];

    /**
     * @return array
     */
    public function migrateAndSeed()
    {
        $outputLog = new BufferedOutput;

        $this->sqlite($outputLog);

        $result = $this->migrate($outputLog);

        if(!$result) {
            return [
                'result' => $this->error
            ];
        }

        $user = $this->createUser($outputLog);

        if(!$user) {
            return [
                'result' => $this->error
            ];
        }

        $result = $this->seed($outputLog);

        if(!$result) {
            return [
                'result' => $this->error
            ];
        }

        return [
            'user' => $user,
            'result' => $this->response(trans('texts.final.finished'), 'success', $outputLog)
        ];
    }

    /**
     * create user and account
     *
     * @param BufferedOutput $outputLog
     * @return bool
     */
    private function createUser(BufferedOutput $outputLog)
    {
        $data = Cache::pull('user_data');

        try {
            (new CreateAccount())->execute($data);
            return true;
        } catch (Exception $e) {
            $this->error = $this->response($e->getMessage(), 'error', $outputLog);
            return false;
        }
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
     * @return bool
     */
    private function migrate(BufferedOutput $outputLog)
    {
        try {
            Artisan::call('migrate', ['--force' => true], $outputLog);
            return true;
        } catch (Exception $e) {
            $this->error = $this->response($e->getMessage(), 'error', $outputLog);
            return false;
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
     * @return bool
     */
    private function seed(BufferedOutput $outputLog)
    {
        try {
            Artisan::call('db:seed', ['--force' => true], $outputLog);
            return false;
        } catch (Exception $e) {
            $this->error = $this->response($e->getMessage(), 'error', $outputLog);
        }

        return true;
    }
}

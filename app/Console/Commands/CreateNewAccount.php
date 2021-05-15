<?php

namespace App\Console\Commands;

use App\Services\Account\CreateAccount;
use Illuminate\Console\Command;
use ReflectionException;

class CreateNewAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-account {email?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws ReflectionException
     */
    public function handle()
    {
        (new CreateAccount())->execute(
            ['email' => $this->argument('email'), 'password' => $this->argument('password')]
        );
    }
}

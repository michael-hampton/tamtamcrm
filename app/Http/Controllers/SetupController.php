<?php

namespace App\Http\Controllers;

use App\Components\Setup\DatabaseManager;
use App\Components\Setup\EnvironmentManager;
use App\Components\Setup\FinalInstallManager;
use App\Components\Setup\InstalledFileManager;
use App\Components\Setup\PermissionsChecker;
use App\Components\Setup\RequirementsChecker;
use App\Events\EnvironmentSaved;
use App\Events\SetupFinished;
use App\Events\User\UserWasCreated;
use App\Factory\AccountFactory;
use App\Factory\UserFactory;
use App\Models\Account;
use App\Models\Domain;
use App\Models\User;
use App\Notifications\Account\NewAccount;
use App\Repositories\AccountRepository;
use App\Repositories\DomainRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Imagick;

class SetupController extends Controller
{
    /**
     * @var EnvironmentManager
     */
    protected $environmentManager;
    /**
     * @var PermissionsChecker
     */
    protected $permissions;
    /**
     * @var RequirementsChecker
     */
    protected $requirements;
    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    /**
     * SetupController constructor.
     * @param DatabaseManager $databaseManager
     * @param EnvironmentManager $environmentManager
     * @param PermissionsChecker $checker
     * @param RequirementsChecker $requirementsChecker
     */
    public function __construct(
        DatabaseManager $databaseManager,
        EnvironmentManager $environmentManager,
        PermissionsChecker $checker,
        RequirementsChecker $requirementsChecker
    ) {
        $this->databaseManager = $databaseManager;
        $this->environmentManager = $environmentManager;
        $this->permissions = $checker;
        $this->requirements = $requirementsChecker;
    }

    public function healthCheck()
    {
        $phpSupportInfo = $this->requirements->checkPHPversion(
            config('installer.core.minPhpVersion')
        );
        $requirements = $this->requirements->check(
            config('installer.requirements')
        );

        $can_connect = false;

        try {
            DB::connection()->getPdo();

            $can_connect = true;
        } catch (Exception $e) {
            $can_connect = false;
        }

        $requirements['requirements']['php']['db_connection'] = $can_connect;

        return response()->json($requirements['requirements']['php']);
    }

    /**
     * Display the requirements page.
     *
     * @return View
     */
    public function requirements()
    {
        $phpSupportInfo = $this->requirements->checkPHPversion(
            config('installer.core.minPhpVersion')
        );
        $requirements = $this->requirements->check(
            config('installer.requirements')
        );

        return view('setup.requirements', compact('requirements', 'phpSupportInfo'));
    }

    /**
     * Migrate and seed the database.
     *
     * @return View
     */
    public function database()
    {
        $response = $this->databaseManager->migrateAndSeed();

        return redirect()->route('setup.final')
                         ->with(['message' => $response]);
    }


    /**
     * Create the user and account.
     *
     * @return View
     */
    public function user()
    {
        return view('setup.user');
    }

    public function permissions()
    {
        $permissions = $this->permissions->check(
            config('installer.permissions')
        );

        return view('setup.permissions', compact('permissions'));
    }

    /**
     * Display the installer welcome page.
     *
     * @return Response
     */
    public function welcome()
    {
        return view('setup.welcome');
    }

    /**
     * Display the Environment menu page.
     *
     * @return View
     */
    public function environmentMenu()
    {
        return view('setup.environment');
    }

    /**
     * Display the Environment page.
     *
     * @return View
     */
    public function environmentWizard()
    {
        $envConfig = $this->environmentManager->getEnvContent();

        return view('setup.environment-wizard', compact('envConfig'));
    }

    /**
     * Display the Environment page.
     *
     * @return View
     */
    public function environmentClassic()
    {
        $envConfig = $this->environmentManager->getEnvContent();

        return view('setup.environment-classic', compact('envConfig'));
    }

    /**
     * Processes the newly saved environment configuration (Classic).
     *
     * @param Request $input
     * @param Redirector $redirect
     * @return RedirectResponse
     */
    public function saveClassic(Request $input, Redirector $redirect)
    {
        $message = $this->environmentManager->saveFileClassic($input);

        event(new EnvironmentSaved($input));

        return $redirect->route('setup.environmentClassic')
                        ->with(['message' => $message]);
    }

    /**
     * Processes the newly saved environment configuration (Form Wizard).
     *
     * @param Request $request
     * @param Redirector $redirect
     * @return RedirectResponse
     */
    public function saveWizard(Request $request, Redirector $redirect)
    {
        $rules = config('installer.environment.form.rules');
        $messages = [
            'environment_custom.required_if' => trans('texts.environment.wizard.form.name_required'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return $redirect->route('setup.environment-wizard')->withInput()->withErrors($validator->errors());
        }

        if (!$this->checkDatabaseConnection($request)) {
            return $redirect->route('setup.environment-wizard')->withInput()->withErrors(
                [
                    'database_connection' => trans('texts.environment.wizard.form.db_connection_failed'),
                ]
            );
        }

        $results = $this->environmentManager->saveFileWizard($request);

        event(new EnvironmentSaved($request));

        return $redirect->route('setup.database')
                        ->with(['results' => $results]);
    }

    /**
     * TODO: We can remove this code if PR will be merged: https://github.com/RachidLaasri/LaravelInstaller/pull/162
     * Validate database connection with user credentials (Form Wizard).
     *
     * @param Request $request
     * @return bool
     */
    private function checkDatabaseConnection(Request $request)
    {
        $connection = $request->input('database_connection');

        $settings = config("database.connections.$connection");

        config(
            [
                'database' => [
                    'default'     => $connection,
                    'connections' => [
                        $connection => array_merge(
                            $settings,
                            [
                                'driver'   => $connection,
                                'host'     => $request->input('database_hostname'),
                                'port'     => $request->input('database_port'),
                                'database' => $request->input('database_name'),
                                'username' => $request->input('database_username'),
                                'password' => $request->input('database_password'),
                            ]
                        ),
                    ],
                ],
            ]
        );

        try {
            DB::connection()->getPdo();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Processes the newly saved user and account
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function saveUser(Request $request)
    {
        $rules = config('installer.user.form.rules');
        $messages = [
            'environment_custom.required_if' => trans('texts.environment.wizard.form.name_required'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->route('setup.user')->withInput()->withErrors($validator->errors());
        }

        $data = $request->except('_token');

        // create domain
        $domain = (new DomainRepository(new Domain))->create($data);

        // create account
        $account = AccountFactory::create($domain->id);

        $account = (new AccountRepository(new Account))->save($data, $account);

        // set default account
        $domain->default_account_id = $account->id;
        $domain->allowed_number_of_users = 99999;
        $domain->save();

        $user_repo = new UserRepository(new User);

        $data['username'] = $data['email'];

        // create new user
        $user = $user_repo->save($data, UserFactory::create($domain->id));

        $user->two_factor_expiry = Carbon::now();
        $user->save();

        $user->attachUserToAccount($account, true);

        if ($user) {
            auth()->login($user, false);
            event(new UserWasCreated($user));
            $user->notify(new NewAccount($account));
        }

        Auth::login($user);

        //$account->service()->convertAccount();

        return redirect()->route('setup.environment');
    }

    /**
     * Update installed file and display finished view.
     *
     * @param InstalledFileManager $fileManager
     * @param FinalInstallManager $finalInstall
     * @param EnvironmentManager $environment
     * @return Factory|View
     */
    public function finish(
        InstalledFileManager $fileManager,
        FinalInstallManager $finalInstall,
        EnvironmentManager $environment
    ) {
        $finalMessages = $finalInstall->runFinal();
        $finalStatusMessage = $fileManager->update();
        $finalEnvFile = $environment->getEnvContent();

        event(new SetupFinished);

        return view('setup.finished', compact('finalMessages', 'finalStatusMessage', 'finalEnvFile'));
    }

    public function twoFactorSetup(User $user)
    {
        phpinfo();

        if (!class_exists(Imagick::class)) {
            die('here');
        }

        die('mike');

        $google2fa = app('pragmarx.google2fa');

        $QR_Image = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );

        var_dump($QR_Image);
        die;
    }
}

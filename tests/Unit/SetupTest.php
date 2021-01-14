<?php

namespace Tests\Unit;

use App\Components\Setup\DatabaseManager;
use App\Components\Setup\EnvironmentManager;
use App\Components\Setup\PermissionsChecker;
use App\Components\Setup\RequirementsChecker;
use App\Http\Controllers\SetupController;
use App\Transformations\UserTransformable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\UrlGenerator;
use Tests\TestCase;

class SetupTest extends TestCase
{

    use DatabaseTransactions, WithFaker, UserTransformable;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
    }

    /*public function test_user_created()
    {
        $request = Request::create('/store', 'POST',[
            'first_name'     =>     $this->faker->firstName,
            'last_name'     =>     $this->faker->lastName,
            'email' => $this->faker->safeEmail,
            'password' => $this->faker->word
        ]);

        $controller = new SetupController(new DatabaseManager(), new EnvironmentManager(), new PermissionsChecker(), new RequirementsChecker());
        $response = $controller->saveUser($request);

        echo '<pre>';
        print_r($response);
        die;

    }*/
}
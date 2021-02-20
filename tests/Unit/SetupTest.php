<?php

namespace Tests\Unit;

use App\Components\Setup\DatabaseManager;
use App\Components\Setup\EnvironmentManager;
use App\Components\Setup\PermissionsChecker;
use App\Components\Setup\RequirementsChecker;
use App\Http\Controllers\SetupController;
use App\Models\User;
use App\Transformations\UserTransformable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class SetupTest extends TestCase
{

    use DatabaseTransactions, WithFaker, UserTransformable;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
    }

    public function test_user_created()
    {
        $data = [
            'first_name' => $this->faker->firstName,
            'last_name'  => $this->faker->lastName,
            'email'      => $this->faker->safeEmail,
            'password'   => $this->faker->word
        ];

        $request = Request::create('/store', 'POST', $data);

        $controller = new SetupController(
            new DatabaseManager(),
            new EnvironmentManager(),
            new PermissionsChecker(),
            new RequirementsChecker()
        );
        $response = $controller->saveUser($request);

        $user = User::where('first_name', $data['first_name'])->where('last_name', $data['last_name'])->where(
            'email',
            $data['email']
        )->first();

        $this->assertNotEmpty($user);
        $this->assertEquals(1, $user->account_users()->count());
    }
}
<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        return [
            'assigned_to'          => null,
            'account_id'           => 1,
            'company_id'           => null,
            'is_active'            => 1,
            'fileable_id'          => $task->id,
            'fileable_type'        => 'App\Models\Task',
            'user_id'              => $user->id,
            'name'                 => $this->faker->text,
            'file_path'            => $this->faker->word,
            'preview'              => null,
            'type'                 => null,
            'size'                 => null,
            'width'                => null,
            'height'               => null,
            'is_default'           => 0,
            'deleted_at'           => null,
            'uploaded_by_customer' => false,
            'customer_can_view'    => false
        ];
    }
}

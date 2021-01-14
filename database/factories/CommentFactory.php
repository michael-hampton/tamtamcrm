<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create();

        return [
            'commentable_type' => '',
            'commentable_id'   => 0,
            'account_id'       => 1,
            'parent_id'        => 0,
            'is_active'        => 1,
            'user_id'          => $user->id,
            'parent_type'      => 1,
            'comment'          => $this->faker->text,
        ];
    }
}

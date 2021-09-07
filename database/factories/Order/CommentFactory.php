<?php

namespace Database\Factories\Order;

use App\Models\Manager;
use App\Models\Order\Comment;
use App\Models\Order\Order;
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
        $comments = Comment::all();
        $comment_id = null;
        if (count($comments) > 0 && rand(0, 1) == 1) {
            $comment_id = $comments->random()->id;
        }

        return [
            'user_id' => Manager::all()->random()->user_id,
            'order_id' => Order::all()->random()->id,
            'comment_id' => $comment_id,
            'text' => $this->faker->sentence($nbWords = 20, $variableNbWords = true),
            'seen_by' => json_encode(User::all()->random(rand(1, 5))->pluck('id'))
        ];
    }
}

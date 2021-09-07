<?php

namespace Database\Factories\Order;

use App\Models\Manager;
use App\Models\Order\Task;
use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->sentence($nbWords = 10, $variableNbWords = true),
            'user_id' => Manager::all()->random()->user_id,
            'order_id' => Order::all()->random()->id,
            'is_completed' => rand(1, 0) == 1
        ];
    }
}

<?php

namespace Database\Factories\Order;

use App\Models\Manager;
use App\Models\Order\Order;
use App\Models\Order\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Activity::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->sentence($nbWords = 5, $variableNbWords = true),
            'user_id' => Manager::all()->random()->user_id,
            'order_id' => Order::all()->random()->id,
        ];
    }
}

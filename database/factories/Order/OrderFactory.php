<?php

namespace Database\Factories\Order;

use App\Models\Order\Order;
use App\Models\Video\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'video_id' => Video::all()->random()->id,
            'deadline' => $this->faker->dateTimeThisMonth($max = '7 days', $timezone = null),
            'is_completed' => rand(0, 1) == 1
        ];
    }
}

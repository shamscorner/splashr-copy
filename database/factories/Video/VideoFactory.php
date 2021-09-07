<?php

namespace Database\Factories\Video;

use App\Models\Client;
use App\Models\Video\Campaign;
use App\Models\Video\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

class VideoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Video::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $client = Client::all()->random();

        return [
            'client_id' => $client->id,
            'campaign_id' => Campaign::all()->random()->id,
            'company_id' => $client->user()->first()->company_id,
            'name' => $this->faker->sentence($nbWords = 8, $variableNbWords = true),
            'service_offer' => $this->faker->sentence($nbWords = 10, $variableNbWords = true),
            'audience' => $this->faker->paragraph($nbSentences = 5, $variableNbSentences = true),
            'key_message' => $this->faker->sentence($nbWords = 6, $variableNbWords = true),
            'is_voice_over' => rand(0, 1) == 1,
            'other_requirements' => $this->faker->paragraph($nbSentences = 10, $variableNbSentences = true),
            'thumbnail' => $this->faker->imageUrl($width = 640, $height = 480, 'cats'),
            'status' => rand(1, 6),
            'is_visited' => rand(0, 1) == 1
        ];
    }
}

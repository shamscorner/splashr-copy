<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Video\Campaign;
use Illuminate\Database\Seeder;

class CampaignClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // get all the clients
        $clients = Client::all();

        // get all the campaigns
        $campaigns = Campaign::all();

        $clients->each(function ($client, $key) use ($campaigns) {
            $client->campaigns()->sync($campaigns->random(rand(1, count($campaigns)))->pluck('id'));
        });
    }
}

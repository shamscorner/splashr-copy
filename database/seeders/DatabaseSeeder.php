<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CompanySeeder::class,
            ManagerSeeder::class,
            ClientSeeder::class,
            FormatSeeder::class, // generic
            PurposeSeeder::class, // generic
            PlatformSeeder::class, // generic
            LanguageSeeder::class, // generic
            CampaignSeeder::class,
            CampaignClientSeeder::class,
            CreditSeeder::class, // generic
            VideoSeeder::class,
            OrderSeeder::class,
            ManagerOrderSeeder::class,
            CompanyManagerSeeder::class,
            CompanyCreditSeeder::class,
            CommentSeeder::class,
            ActivitySeeder::class
        ]);
    }
}

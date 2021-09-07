<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Manager;
use Illuminate\Database\Seeder;

class CompanyManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // get all managers
        $managers = Manager::all();

        // get all companies
        $companies = Company::all();

        $companies->each(function ($company, $key) use ($managers) {
            $company->managers()->sync($managers->random(rand(1, 3))->pluck('id'));
        });
    }
}

<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Credit;
use App\Models\Manager;
use Illuminate\Database\Seeder;

class CompanyCreditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // get all the companies
        $companies = Company::all();

        // get all the credits
        $credits = Credit::all();

        // get all the managers
        $managers = Manager::all();

        $companies->each(function ($company, $key) use ($credits, $managers) {
            $company->credits()->sync(
                $credits->random(rand(1, 3))->pluck('id')->map(function ($creditId) use ($managers) {
                    return [
                        $creditId => [
                            'reference_number' => 'ref' . rand(100, 10000),
                            'quantity' => rand(1, 10),
                            'manager_id' => $managers->random()->id
                        ]
                    ];
                })->flatMap(function ($values) {
                    return $values;
                })
            );
        });
    }
}

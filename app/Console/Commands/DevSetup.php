<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Client;
use App\Models\Company;
use App\Models\Manager;
use Illuminate\Console\Command;

class DevSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:setup {--seed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets up the development environment';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->newLine();
        $this->warn('Setting up development environment. . .');

        // if ($this->option('a')) {
        //     $this->info("Running composer install . . .");
        //     shell_exec('composer install');
        //     $this->newLine();
        //     $this->info("Running npm install . . .");
        //     shell_exec('npm install');
        //     $this->newLine();
        // }

        // run migration and seed
        $this->MigrateAndSeedDatabase();

        // finally show a cool message
        $this->newLine(2);
        $this->info("All done. Happy coding :)");
        $this->newLine();
    }

    /**
     * Run migration and necessary seeder
     * 
     * @return null
     */
    private function MigrateAndSeedDatabase()
    {
        // remove any caches
        $this->call('optimize');

        // run migration 
        $this->call('migrate:fresh');

        // create a demo user 
        $this->createSomeDummyUsers();

        // seed
        if ($this->option('seed')) {
            $this->call('db:seed');
        }
    }

    /**
     * Create users, clients, and manages
     * 
     * @return User
     */
    private function createSomeDummyUsers()
    {
        // create a company 
        $company = Company::factory()->create([
            'name' => 'Booker and Barton Inc'
        ]);

        // create a user
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'company_id' => $company->id,
            'email' => 'john@example.com',
            'password' => bcrypt('password')
        ]);

        $this->newLine();
        $this->info("John Doe user created");

        // show the user's email address, this will help to login later
        $this->warn('User email: ' . $user->email);
        $this->warn('User password: password');
        $this->warn('User company: ' . $company->name);

        // create a company for the manager
        $companyManager = Company::factory()->create([
            'name' => 'Splashr'
        ]);

        // create a manager
        $managerUser = User::factory()->create([
            'first_name' => 'Alex',
            'last_name' => 'Dane',
            'company_id' => $companyManager->id,
            'email' => 'alex@example.com',
            'password' => bcrypt('password')
        ]);
        $manager = Manager::create([
            'user_id' => $managerUser->id
        ]);
        $this->newLine();
        $this->info($managerUser->first_name . " " . $managerUser->last_name . " manager created");
        $this->warn('Manager email: ' . $managerUser->email);
        $this->warn('Manager password: password');
        $this->warn('Manager company: ' . $companyManager->name);

        // create an admin
        $adminUser = User::factory()->create([
            'first_name' => 'Ron',
            'last_name' => 'Dale',
            'company_id' => $companyManager->id,
            'email' => 'ron@example.com',
            'password' => bcrypt('password')
        ]);
        $manager = Manager::create([
            'user_id' => $adminUser->id,
            'role' => 1
        ]);
        $this->newLine();
        $this->info($adminUser->first_name . " " . $adminUser->last_name . " admin created");
        $this->warn('Admin email: ' . $adminUser->email);
        $this->warn('Admin password: password');
        $this->warn('Admin company: ' . $companyManager->name);

        // make this user as client
        Client::create([
            'user_id' => $user->id
        ]);
        $this->newLine();
        $this->info("John Doe client created");
        $this->warn('Client email: ' . $user->email);
        $this->warn('Client password: password');
        $this->warn('Client company: ' . $company->name);
        $this->newLine();

        return $user;
    }
}

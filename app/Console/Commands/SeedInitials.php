<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedInitials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set-initials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup initial database migrations and seeder';

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
        $this->warn('Setting up production environment. . .');

        // run migration and seed
        $this->MigrateAndSeedDatabase();

        // finally show a cool message
        $this->newLine(2);
        $this->info("All done :)");
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

        $this->call('db:seed', [
            '--class' => 'FormatSeeder'
        ]);
        $this->call('db:seed', [
            '--class' => 'PurposeSeeder'
        ]);
        $this->call('db:seed', [
            '--class' => 'PlatformSeeder'
        ]);
        $this->call('db:seed', [
            '--class' => 'CreditSeeder'
        ]);
        $this->call('db:seed', [
            '--class' => 'LanguageSeeder'
        ]);
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\RandomDataSeeder;

class SeedRandomData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:random-data {--fresh : Clear existing data before seeding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate random test data for the box_web system';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting random data generation for box_web system...');
        $this->line('================================================');
        
        if ($this->option('fresh')) {
            $this->warn('Fresh option selected - this will clear existing data!');
            if (!$this->confirm('Are you sure you want to continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
            
            $this->info('Clearing existing data...');
            // Add commands to clear existing data if needed
            // $this->call('migrate:fresh');
        }
        
        try {
            $seeder = new RandomDataSeeder();
            $seeder->run();
            
            $this->line('');
            $this->line('================================================');
            $this->info('Random data generation completed successfully!');
            $this->line('');
            $this->info('Generated data includes:');
            $this->line('- 15 Merchants with shops and delivery charges');
            $this->line('- 20 Delivery men');
            $this->line('- 100 Parcels with various statuses and events');
            $this->line('- Financial data (accounts, payments, statements)');
            $this->line('- Income and expense records');
            $this->line('');
            $this->info('You can now test the system with this random data.');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}

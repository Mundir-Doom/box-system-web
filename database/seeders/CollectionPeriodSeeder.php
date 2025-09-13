<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CollectionPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $periods = [
            [
                'name' => 'Morning Collection',
                'start_time' => '11:00:00',
                'end_time' => '15:00:00',
                'is_active' => true,
                'description' => 'Main collection period from 11:00 AM to 3:00 PM for regular parcels',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Evening Collection',
                'start_time' => '16:00:00',
                'end_time' => '18:00:00',
                'is_active' => true,
                'description' => 'Secondary collection period from 4:00 PM to 6:00 PM for express parcels',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($periods as $period) {
            \App\Models\Backend\CollectionPeriod::firstOrCreate(
                ['name' => $period['name']],
                $period
            );
        }

        $this->command->info('Collection periods seeded successfully!');
    }
}

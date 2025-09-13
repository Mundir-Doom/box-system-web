<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Backend\Hub;

class HubCoordinateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Libya coordinates for different cities
        $hubCoordinates = [
            'Tripoli Central' => ['lat' => '32.8872', 'long' => '13.1913'],
            'Benghazi' => ['lat' => '32.1167', 'long' => '20.0667'],
            'Misrata' => ['lat' => '32.3754', 'long' => '15.0927'],
            'Zawiya' => ['lat' => '32.7500', 'long' => '12.7167'],
            'Zliten' => ['lat' => '32.4667', 'long' => '14.5667'],
        ];

        foreach ($hubCoordinates as $hubName => $coordinates) {
            $hub = Hub::where('name', $hubName)->first();
            if ($hub && (empty($hub->hub_lat) || empty($hub->hub_long))) {
                $hub->update([
                    'hub_lat' => $coordinates['lat'],
                    'hub_long' => $coordinates['long']
                ]);
                $this->command->info("Updated coordinates for hub: {$hubName}");
            }
        }
    }
}

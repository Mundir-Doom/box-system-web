<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Backend\Hub;

class HubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $hubs = [
            [
                'name'            => 'Tripoli Central',
                'phone'           => '0212123456',
                'address'         => 'Tripoli, Libya',
                'current_balance' => '00'
            ],
            [
                'name'            => 'Benghazi',
                'phone'           => '0612123456',
                'address'         => 'Benghazi, Libya',
                'current_balance' => '00'
            ],
            [
                'name'            => 'Misrata',
                'phone'           => '0512123456',
                'address'         => 'Misrata, Libya',
                'current_balance' => '00'
            ],
            [
                'name'            => 'Sabha',
                'phone'           => '0712123456',
                'address'         => 'Sabha, Libya',
                'current_balance' => '00'
            ],
            [
                'name'            => 'Zawiya',
                'phone'           => '0232123456',
                'address'         => 'Zawiya, Libya',
                'current_balance' => '00'
            ],
            [
                'name'            => 'Zliten',
                'phone'           => '0252123456',
                'address'         => 'Zliten, Libya',
                'current_balance' => '00'
            ],
        ];

        for($n = 0; $n < sizeof($hubs); $n++)
        {
            $hub                  = new Hub();
            $hub->name            = $hubs[$n]['name'];
            $hub->phone           = $hubs[$n]['phone'];
            $hub->address         = $hubs[$n]['address'];
            $hub->current_balance = $hubs[$n]['current_balance'];
            $hub->save();
        }
    }
}

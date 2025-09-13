<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Safe seeding: avoid destructive truncation; upsert LYD currency
        if (app()->environment('production')) {
            // In production, only upsert the target currency
            DB::table('currencies')->updateOrInsert(
                ['code' => 'LYD'],
                [
                    'country'       => 'Libya',
                    'name'          => 'Libyan Dinar',
                    'symbol'        => 'LYD',
                    'exchange_rate' => 1,
                    'status'        => 1,
                    'position'      => null,
                    'updated_at'    => now(),
                    'created_at'    => now(),
                ]
            );
            return;
        }

        DB::table('currencies')->updateOrInsert(
            ['code' => 'LYD'],
            [
                'country'       => 'Libya',
                'name'          => 'Libyan Dinar',
                'symbol'        => 'LYD',
                'exchange_rate' => 1,
                'status'        => 1,
                'position'      => null,
                'updated_at'    => now(),
                'created_at'    => now(),
            ]
        );
    }
}

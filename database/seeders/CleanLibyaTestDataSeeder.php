<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CleanLibyaTestDataSeeder extends Seeder
{
    public function run()
    {
        // Never allow destructive cleanup in production
        if (app()->environment('production')) {
            throw new \RuntimeException('Refusing to truncate tables in production environment.');
        }

        // Disable foreign key checks for truncation safety (DB-agnostic)
        try { \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints(); } catch (\Throwable $e) {}

        // Detach user->hub relation to avoid FK issues
        try { DB::table('users')->update(['hub_id' => null]); } catch (\Throwable $e) {}

        $tables = [
            'parcel_events',
            'parcels',
            'merchant_delivery_charges',
            'merchant_shops',
            'merchants',
            'delivery_man',
            'accounts',
            'hubs',
            // Front web content
            'pages',
            'blogs',
            'faqs',
            'partners',
            'services',
            'sections',
            'social_links',
            // Leave users/roles/permissions intact per request
        ];

        foreach ($tables as $table) {
            try { DB::table($table)->truncate(); } catch (\Throwable $e) {}
        }

        // Re-enable foreign key checks
        try { \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints(); } catch (\Throwable $e) {}
    }
}

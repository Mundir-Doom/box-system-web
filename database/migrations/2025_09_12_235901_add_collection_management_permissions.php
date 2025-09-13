<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Avoid duplicate insert if it already exists
        $exists = DB::table('permissions')->where('attribute', 'collection_management')->exists();
        if (!$exists) {
            DB::table('permissions')->insert([
                'attribute'  => 'collection_management',
                'keywords'   => json_encode([
                    // Read permissions for collection management features
                    'collection_slots_read'       => 'collection_slots_read',
                    'collection_sessions_read'    => 'collection_sessions_read',
                    'collected_shipments_read'    => 'collected_shipments_read',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('permissions')->where('attribute', 'collection_management')->delete();
    }
};


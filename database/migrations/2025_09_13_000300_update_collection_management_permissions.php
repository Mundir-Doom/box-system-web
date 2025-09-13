<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $row = DB::table('permissions')->where('attribute', 'collection_management')->first();
        if (!$row) {
            return; // previous migration not applied yet
        }

        $keywords = json_decode($row->keywords ?? '[]', true) ?: [];

        // Ensure all expected permissions exist
        $expected = [
            // Time slots CRUD
            'collection_slots_read'    => 'collection_slots_read',
            'collection_slots_create'  => 'collection_slots_create',
            'collection_slots_update'  => 'collection_slots_update',
            'collection_slots_delete'  => 'collection_slots_delete',

            // Sessions actions
            'collection_sessions_read'       => 'collection_sessions_read',
            'collection_sessions_create'     => 'collection_sessions_create',
            'collection_sessions_update'     => 'collection_sessions_update',
            'collection_sessions_complete'   => 'collection_sessions_complete',
            'collection_sessions_historical' => 'collection_sessions_historical',

            // Collected shipments actions
            'collected_shipments_read'       => 'collected_shipments_read',
            'collected_shipments_assign'     => 'collected_shipments_assign',
            'collected_shipments_historical' => 'collected_shipments_historical',

            // Delivery assignments
            'delivery_assignments_read'      => 'delivery_assignments_read',
            'delivery_assignments_create'    => 'delivery_assignments_create',
            'delivery_assignments_update'    => 'delivery_assignments_update',

            // Collection periods (for backward compatibility)
            'collection_periods_read'        => 'collection_periods_read',
            'collection_periods_create'      => 'collection_periods_create',
            'collection_periods_update'      => 'collection_periods_update',
            'collection_periods_delete'      => 'collection_periods_delete',
        ];

        $keywords = array_merge($keywords, array_diff_key($expected, $keywords));

        DB::table('permissions')
            ->where('attribute', 'collection_management')
            ->update([
                'keywords'   => json_encode($keywords),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        $row = DB::table('permissions')->where('attribute', 'collection_management')->first();
        if (!$row) return;

        $keywords = json_decode($row->keywords ?? '[]', true) ?: [];
        $remove = [
            'collection_slots_create',
            'collection_slots_update',
            'collection_slots_delete',
            'collection_sessions_create',
            'collection_sessions_update',
            'collection_sessions_complete',
            'collection_sessions_historical',
            'collected_shipments_assign',
            'collected_shipments_historical',
            'delivery_assignments_read',
            'delivery_assignments_create',
            'delivery_assignments_update',
            'collection_periods_read',
            'collection_periods_create',
            'collection_periods_update',
            'collection_periods_delete',
        ];
        foreach ($remove as $k) unset($keywords[$k]);

        DB::table('permissions')
            ->where('attribute', 'collection_management')
            ->update([
                'keywords'   => json_encode($keywords),
                'updated_at' => now(),
            ]);
    }
};


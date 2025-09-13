<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop legacy foreign key and columns if they exist to prevent insert failures
        try { DB::statement('ALTER TABLE collection_sessions DROP FOREIGN KEY collection_sessions_collection_slot_id_foreign'); } catch (\Throwable $e) {}
        try { DB::statement('ALTER TABLE collection_sessions DROP INDEX collection_sessions_collection_slot_id_foreign'); } catch (\Throwable $e) {}

        // Drop legacy columns if present
        $columns = DB::getSchemaBuilder()->getColumnListing('collection_sessions');
        $toDrop = [];
        foreach (['collection_slot_id','session_date','start_time','end_time','total_shipments','assigned_shipments'] as $col) {
            if (in_array($col, $columns)) { $toDrop[] = $col; }
        }
        foreach ($toDrop as $col) {
            try { DB::statement("ALTER TABLE collection_sessions DROP COLUMN `$col`"); } catch (\Throwable $e) {}
        }
    }

    public function down(): void
    {
        // No-op: we won't recreate legacy columns/constraints
    }
};


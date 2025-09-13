<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('collection_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('collection_sessions', 'collection_period_id')) {
                // Add without immediate FK to avoid legacy data failures
                $table->unsignedBigInteger('collection_period_id')->nullable()->after('id');
                $table->index('collection_period_id', 'idx_collection_sessions_period');
            }
            if (!Schema::hasColumn('collection_sessions', 'collection_date')) {
                $table->date('collection_date')->nullable()->after('collection_period_id');
                $table->index('collection_date', 'idx_collection_sessions_date');
            }
            if (!Schema::hasColumn('collection_sessions', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('collection_sessions', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('started_at');
            }
            if (!Schema::hasColumn('collection_sessions', 'total_parcels')) {
                $table->integer('total_parcels')->default(0)->after('status');
            }
            if (!Schema::hasColumn('collection_sessions', 'assigned_parcels')) {
                $table->integer('assigned_parcels')->default(0)->after('total_parcels');
            }
            if (!Schema::hasColumn('collection_sessions', 'unassigned_parcels')) {
                $table->integer('unassigned_parcels')->default(0)->after('assigned_parcels');
            }
            // Ensure status index
            try {
                $table->index('status', 'idx_collection_sessions_status');
            } catch (\Throwable $e) {
                // ignore
            }
        });

        // Backfill from legacy columns if present
        if (Schema::hasColumn('collection_sessions', 'collection_slot_id')) {
            DB::statement('UPDATE collection_sessions SET collection_period_id = collection_slot_id WHERE collection_period_id IS NULL');
        }
        if (Schema::hasColumn('collection_sessions', 'session_date')) {
            DB::statement('UPDATE collection_sessions SET collection_date = session_date WHERE collection_date IS NULL');
        }
        if (Schema::hasColumn('collection_sessions', 'start_time')) {
            DB::statement('UPDATE collection_sessions SET started_at = start_time WHERE started_at IS NULL');
        }
        if (Schema::hasColumn('collection_sessions', 'end_time')) {
            DB::statement('UPDATE collection_sessions SET completed_at = end_time WHERE completed_at IS NULL');
        }
        if (Schema::hasColumn('collection_sessions', 'total_shipments')) {
            DB::statement('UPDATE collection_sessions SET total_parcels = total_shipments WHERE total_parcels = 0');
        }
        if (Schema::hasColumn('collection_sessions', 'assigned_shipments')) {
            DB::statement('UPDATE collection_sessions SET assigned_parcels = assigned_shipments WHERE assigned_parcels = 0');
        }
        // Compute unassigned
        DB::statement('UPDATE collection_sessions SET unassigned_parcels = GREATEST(COALESCE(total_parcels,0) - COALESCE(assigned_parcels,0), 0) WHERE unassigned_parcels = 0');

        // Add unique constraint if both columns exist
        try {
            DB::statement('ALTER TABLE collection_sessions ADD UNIQUE unique_period_date (collection_period_id, collection_date)');
        } catch (\Throwable $e) {
            // ignore if exists
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collection_sessions', function (Blueprint $table) {
            // Remove unique if we added it
            try { DB::statement('ALTER TABLE collection_sessions DROP INDEX unique_period_date'); } catch (\Throwable $e) {}

            // We will not drop newly added columns to avoid data loss in down.
        });
    }
};


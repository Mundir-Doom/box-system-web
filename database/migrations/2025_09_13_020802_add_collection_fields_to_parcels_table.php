<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('parcels', function (Blueprint $table) {
            if (!Schema::hasColumn('parcels', 'collection_session_id')) {
                $table->foreignId('collection_session_id')->nullable()->constrained('collection_sessions')->onDelete('set null');
            }
            if (!Schema::hasColumn('parcels', 'collected_at')) {
                $table->timestamp('collected_at')->nullable();
            }
            if (!Schema::hasColumn('parcels', 'is_priority_delivery')) {
                $table->boolean('is_priority_delivery')->default(false);
            }

            // Indexes - check if they don't exist before adding
            try {
                $table->index('collection_session_id', 'idx_parcels_collection_session');
            } catch (\Exception $e) {
                // Index already exists
            }
            try {
                $table->index('collected_at', 'idx_parcels_collected_at');
            } catch (\Exception $e) {
                // Index already exists
            }
            try {
                $table->index('is_priority_delivery', 'idx_parcels_priority');
            } catch (\Exception $e) {
                // Index already exists
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parcels', function (Blueprint $table) {
            $table->dropIndex('idx_parcels_collection_session');
            $table->dropIndex('idx_parcels_collected_at');
            $table->dropIndex('idx_parcels_priority');
            $table->dropForeign(['collection_session_id']);
            $table->dropColumn(['collection_session_id', 'collected_at', 'is_priority_delivery']);
        });
    }
};

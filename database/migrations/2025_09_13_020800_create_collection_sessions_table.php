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
        if (!Schema::hasTable('collection_sessions')) {
            Schema::create('collection_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_period_id')->constrained('collection_periods')->onDelete('cascade');
            $table->date('collection_date');
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->integer('total_parcels')->default(0);
            $table->integer('assigned_parcels')->default(0);
            $table->integer('unassigned_parcels')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('collection_date', 'idx_collection_sessions_date');
            $table->index('collection_period_id', 'idx_collection_sessions_period');
            $table->index('status', 'idx_collection_sessions_status');
            $table->unique(['collection_period_id', 'collection_date'], 'unique_period_date');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_sessions');
    }
};

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
        if (!Schema::hasTable('parcel_collection_assignments')) {
            Schema::create('parcel_collection_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcel_id')->constrained('parcels')->onDelete('cascade');
            $table->foreignId('collection_session_id')->constrained('collection_sessions')->onDelete('cascade');
            $table->timestamp('collected_at');
            $table->foreignId('delivery_man_id')->nullable()->constrained('delivery_man')->onDelete('set null');
            $table->timestamp('assigned_at')->nullable();
            $table->enum('assignment_status', ['collected', 'assigned', 'out_for_delivery', 'delivered'])->default('collected');
            $table->integer('priority')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('parcel_id', 'idx_parcel_collection_parcel');
            $table->index('collection_session_id', 'idx_parcel_collection_session');
            $table->index('delivery_man_id', 'idx_parcel_collection_delivery');
            $table->index('assignment_status', 'idx_parcel_collection_status');
            $table->unique(['parcel_id', 'collection_session_id'], 'unique_parcel_session');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcel_collection_assignments');
    }
};

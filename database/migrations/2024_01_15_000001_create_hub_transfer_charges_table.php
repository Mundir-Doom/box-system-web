<?php

use App\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hub_transfer_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_hub_id')->constrained('hubs')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('to_hub_id')->constrained('hubs')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('base_charge', 16, 2)->default(0.00)->comment('Base charge for transfer regardless of distance');
            $table->decimal('per_km_rate', 16, 2)->default(0.00)->comment('Rate per kilometer');
            $table->decimal('min_charge', 16, 2)->default(0.00)->comment('Minimum charge for this transfer');
            $table->decimal('max_charge', 16, 2)->nullable()->comment('Maximum charge for this transfer (null = no limit)');
            $table->decimal('weight_factor', 8, 4)->default(1.0000)->comment('Weight multiplier factor');
            $table->unsignedTinyInteger('status')->default(Status::ACTIVE)->comment(Status::ACTIVE.'='.trans('status.'.Status::ACTIVE).', ' .Status::INACTIVE.'='.trans('status.'.Status::INACTIVE));
            $table->timestamps();

            // Indexes for performance
            $table->index(['from_hub_id', 'to_hub_id']);
            $table->index('status');
            
            // Unique constraint to prevent duplicate hub pairs
            $table->unique(['from_hub_id', 'to_hub_id'], 'unique_hub_transfer_pair');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hub_transfer_charges');
    }
};

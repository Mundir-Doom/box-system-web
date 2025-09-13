<?php

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
        Schema::table('parcels', function (Blueprint $table) {
            $table->decimal('transfer_charge', 16, 2)->default(0.00)->after('delivery_charge')->comment('Charge for hub-to-hub transfer');
            $table->decimal('distance_km', 8, 2)->default(0.00)->after('transfer_charge')->comment('Distance in kilometers from pickup to delivery');
            $table->enum('pricing_method', ['fixed', 'distance'])->default('fixed')->after('distance_km')->comment('Pricing method used for this parcel');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parcels', function (Blueprint $table) {
            $table->dropColumn(['transfer_charge', 'distance_km', 'pricing_method']);
        });
    }
};

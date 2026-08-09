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
        Schema::create('buyback_prices', function (Blueprint $table) {
            $table->unsignedBigInteger('service_center_id');
            $table->unsignedBigInteger('equipment_type_id');
            $table->unsignedBigInteger('brand_id');
            $table->integer('price');
            $table->primary(['service_center_id', 'equipment_type_id', 'brand_id']);
            $table->foreign('service_center_id')->references('id')->on('service_centers')->onDelete('cascade');
            $table->foreign('equipment_type_id')->references('id')->on('equipment_types')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->index('service_center_id');
            $table->index('equipment_type_id');
            $table->index('brand_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('buyback_prices');
    }
};

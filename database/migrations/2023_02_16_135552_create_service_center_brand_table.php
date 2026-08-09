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
        Schema::create('service_center_brand', function (Blueprint $table) {
            $table->unsignedBigInteger('service_center_id');
            $table->unsignedBigInteger('brand_id');
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedInteger('position')->nullable();
            $table->primary(['service_center_id', 'brand_id']);
            $table->foreign('service_center_id')->references('id')->on('service_centers')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->index('service_center_id');
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
        Schema::dropIfExists('service_center_brand');
    }
};

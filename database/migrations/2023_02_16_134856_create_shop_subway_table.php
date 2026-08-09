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
        Schema::create('service_center_subway', function (Blueprint $table) {
            $table->unsignedBigInteger('service_center_id');
            $table->unsignedBigInteger('subway_id');
            $table->primary(['service_center_id', 'subway_id']);
            $table->foreign('service_center_id')->references('id')->on('service_centers')->onDelete('cascade');
            $table->foreign('subway_id')->references('id')->on('subways')->onDelete('cascade');
            $table->index('service_center_id');
            $table->index('subway_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_center_subway');
    }
};


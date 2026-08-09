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
        Schema::create('service_center_working_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_center_id');
            $table->unsignedTinyInteger('day_of_week');
            $table->boolean('is_open');
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->foreign('service_center_id')->references('id')->on('service_centers');
            $table->index('service_center_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_center_working_hours');
    }
};


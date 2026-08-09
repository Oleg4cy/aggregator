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
        Schema::create('service_center_review_source', function (Blueprint $table) {
            $table->unsignedBigInteger('service_center_id');
            $table->unsignedBigInteger('review_source_id');
            $table->text('external_id');
            $table->decimal('rating', 10, 2);
            $table->decimal('rating_count', 10, 0);
            $table->text('link');
            $table->text('comments');
            $table->primary(['service_center_id', 'review_source_id']);
            $table->foreign('service_center_id')->references('id')->on('service_centers')->onDelete('cascade');
            $table->foreign('review_source_id')->references('id')->on('review_sources')->onDelete('cascade');
            $table->index('service_center_id');
            $table->index('review_source_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_center_review_source');
    }
};


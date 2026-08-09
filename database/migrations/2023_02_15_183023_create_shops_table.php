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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('region_id');
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('area_id')->nullable();
            $table->unsignedBigInteger('municipality_id')->nullable();
            $table->unsignedBigInteger('chain_id')->nullable();
            $table->text('logo')->nullable();
            $table->text('photos')->nullable();
            $table->text('title')->nullable();
            $table->text('name')->nullable();
            $table->text('description')->nullable();
            $table->text('zip')->nullable();
            $table->text('coord')->nullable();
            $table->text('address')->nullable();
            $table->text('phone')->nullable();
            $table->text('additional_phones')->nullable();
            $table->text('whatsapp')->nullable();
            $table->text('telegram')->nullable();
            $table->text('vk')->nullable();
            $table->text('web')->nullable();
            $table->text('more_socials')->nullable();
            $table->text('emails')->nullable();
            $table->boolean('open_24_hours')->default(false);
            $table->boolean('online_estimate')->default(false);
            $table->boolean('warranty')->default(false);
            $table->boolean('onsite_repair')->default(false);
            $table->boolean('courier')->default(false);
            $table->boolean('original_parts')->default(false);
            $table->boolean('buyback')->default(false);
            $table->boolean('trade_in')->default(false);
            $table->boolean('buy_for_parts')->default(false);
            // $table->text('yandex_rating')->nullable();
            // $table->text('google_rating')->nullable();
            // $table->text('gis_rating')->nullable();
            // $table->text('avito_rating')->nullable();
            $table->decimal('average_rating', 10, 2)->nullable();
            // $table->text('yandex_comments')->nullable();
            // $table->text('google_comments')->nullable();
            // $table->text('gis_comments')->nullable();
            // $table->text('avito_comments')->nullable();
            $table->boolean('show')->default(true);
            $table->timestamps();
            $table->foreign('region_id')->references('id')->on('regions');
            $table->foreign('city_id')->references('id')->on('cities');
            $table->foreign('municipality_id')->references('id')->on('municipalities');
            $table->foreign('area_id')->references('id')->on('areas');
            $table->foreign('chain_id')->references('id')->on('chains');
            $table->index('region_id');
            $table->index('municipality_id');
            $table->index('city_id');
            $table->index('area_id');
            $table->index('chain_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shops');
    }
};


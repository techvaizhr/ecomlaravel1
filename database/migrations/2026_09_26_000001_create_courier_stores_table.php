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
        if (!Schema::hasTable('courier_stores')) {
            Schema::create('courier_stores', function (Blueprint $table) {
                $table->id();
                $table->string('courier_type', 50)->comment('carrybee, pathao, steadfast, redx, etc.');
                $table->string('store_id', 100)->comment('Store ID from courier API or custom identifier');
                $table->string('store_name', 191);
                $table->string('contact_person_name', 191)->nullable();
                $table->string('contact_person_number', 50)->nullable();
                $table->string('contact_person_secondary_number', 50)->nullable();
                $table->text('address')->nullable();
                $table->unsignedBigInteger('city_id')->nullable();
                $table->string('city_name', 100)->nullable();
                $table->unsignedBigInteger('zone_id')->nullable();
                $table->string('zone_name', 100)->nullable();
                $table->unsignedBigInteger('area_id')->nullable();
                $table->string('area_name', 100)->nullable();
                $table->decimal('lat', 10, 7)->nullable();
                $table->decimal('lng', 10, 7)->nullable();
                $table->boolean('is_default')->default(false)->comment('Default pickup store for this courier');
                $table->boolean('is_active')->default(true);
                $table->json('raw_data')->nullable()->comment('Full payload from courier API');
                $table->timestamps();

                $table->index(['courier_type', 'is_default']);
                $table->index(['courier_type', 'store_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courier_stores');
    }
};

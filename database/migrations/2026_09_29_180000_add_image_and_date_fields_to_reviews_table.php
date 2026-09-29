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
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'image')) {
                $table->string('image', 255)->nullable()->after('review');
            }
            if (!Schema::hasColumn('reviews', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('reviews', 'review_date')) {
                $table->dateTime('review_date')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'image')) {
                $table->dropColumn('image');
            }
            if (Schema::hasColumn('reviews', 'customer_id')) {
                $table->dropColumn('customer_id');
            }
            if (Schema::hasColumn('reviews', 'review_date')) {
                $table->dropColumn('review_date');
            }
        });
    }
};

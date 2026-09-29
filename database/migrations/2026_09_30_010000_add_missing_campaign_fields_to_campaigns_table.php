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
        Schema::table('campaigns', function (Blueprint $table) {
            if (!Schema::hasColumn('campaigns', 'banner')) {
                $table->string('banner')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('campaigns', 'banner_title')) {
                $table->string('banner_title')->nullable()->after('banner');
            }
            if (!Schema::hasColumn('campaigns', 'deadline')) {
                $table->dateTime('deadline')->nullable()->after('banner_title');
            }
            if (!Schema::hasColumn('campaigns', 'top_title_1')) {
                $table->text('top_title_1')->nullable()->after('deadline');
            }
            if (!Schema::hasColumn('campaigns', 'top_title_2')) {
                $table->text('top_title_2')->nullable()->after('top_title_1');
            }
            if (!Schema::hasColumn('campaigns', 'heading_1')) {
                $table->text('heading_1')->nullable()->after('top_title_2');
            }
            if (!Schema::hasColumn('campaigns', 'heading_2')) {
                $table->text('heading_2')->nullable()->after('heading_1');
            }
            if (!Schema::hasColumn('campaigns', 'heading_3')) {
                $table->text('heading_3')->nullable()->after('heading_2');
            }
            if (!Schema::hasColumn('campaigns', 'heading_4')) {
                $table->text('heading_4')->nullable()->after('heading_3');
            }
            if (!Schema::hasColumn('campaigns', 'feature_1')) {
                $table->text('feature_1')->nullable()->after('heading_4');
            }
            if (!Schema::hasColumn('campaigns', 'feature_2')) {
                $table->text('feature_2')->nullable()->after('feature_1');
            }
            if (!Schema::hasColumn('campaigns', 'note')) {
                $table->text('note')->nullable()->after('feature_2');
            }
            if (!Schema::hasColumn('campaigns', 'billing_details')) {
                $table->text('billing_details')->nullable()->after('note');
            }
            if (!Schema::hasColumn('campaigns', 'video')) {
                $table->string('video')->nullable()->after('billing_details');
            }
            if (!Schema::hasColumn('campaigns', 'product_id')) {
                $table->unsignedBigInteger('product_id')->nullable()->after('video');
            }
            if (!Schema::hasColumn('campaigns', 'image_one')) {
                $table->string('image_one')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('campaigns', 'image_two')) {
                $table->string('image_two')->nullable()->after('image_one');
            }
            if (!Schema::hasColumn('campaigns', 'image_three')) {
                $table->string('image_three')->nullable()->after('image_two');
            }
            if (!Schema::hasColumn('campaigns', 'review')) {
                $table->text('review')->nullable()->after('image_three');
            }
            if (!Schema::hasColumn('campaigns', 'short_description')) {
                $table->longText('short_description')->nullable()->after('review');
            }
            if (!Schema::hasColumn('campaigns', 'description')) {
                $table->longText('description')->nullable()->after('short_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe reverse
    }
};

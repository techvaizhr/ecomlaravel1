<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates high-performance indexed media table for instant library access.
     */
    public function up(): void
    {
        if (!Schema::hasTable('media')) {
            Schema::create('media', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('file_name', 255)->index();
                $table->string('file_path', 255)->unique();
                $table->string('folder', 50)->index();
                $table->string('subfolder', 50)->nullable()->index();
                $table->string('extension', 10)->index();
                $table->unsignedBigInteger('file_size')->default(0)->index();
                $table->string('dimensions', 50)->nullable();
                $table->string('mime_type', 100)->nullable();
                $table->timestamps();

                // Composite index for fast folder-wise chronological sorting
                $table->index(['folder', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};

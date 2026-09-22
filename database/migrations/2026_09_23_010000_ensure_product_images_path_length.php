<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ensures product image path column has adequate capacity (VARCHAR 255)
     * for Year/Month subfolders (e.g. public/uploads/product/YYYY/MM/filename.webp).
     */
    public function up(): void
    {
        if (Schema::hasTable('productimages')) {
            Schema::table('productimages', function (Blueprint $table) {
                // Ensure image column is at least string 255 (standard)
                $table->string('image', 255)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep string length
    }
};

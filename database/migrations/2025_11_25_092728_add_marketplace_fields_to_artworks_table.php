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
        Schema::table('artworks', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->nullable(); // Null = Display Only
            $table->integer('stock')->default(0);
            $table->integer('reserved_stock')->default(0);
            $table->timestamp('reserved_until')->nullable(); // For the 6-hour timer
            $table->boolean('is_promo')->default(false);
            $table->decimal('promo_price', 12, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            //
        });
    }
};

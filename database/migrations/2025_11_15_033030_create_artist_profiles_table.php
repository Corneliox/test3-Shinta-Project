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
        Schema::create('artist_profiles', function (Blueprint $table) {
            $table->id();
            // This links it one-to-one with your users table
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('about')->nullable(); // For the "About" section
            $table->string('profile_picture')->nullable(); // For the oval image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artist_profiles');
    }
};

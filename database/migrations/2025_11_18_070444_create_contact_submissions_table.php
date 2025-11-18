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
        Schema::create('contact_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // For logged-in users
            $table->string('name');
            $table->string('email');
            $table->string('subject');
            $table->text('feedback');
            $table->boolean('is_seen')->default(false);
            $table->timestamps();

            // Optional: Link to users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_submissions');
    }
};

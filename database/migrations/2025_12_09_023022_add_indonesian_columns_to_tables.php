<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. Artist Profiles
        Schema::table('artist_profiles', function (Blueprint $table) {
            $table->text('about_id')->nullable()->after('about'); 
        });

        // 2. Artworks
        Schema::table('artworks', function (Blueprint $table) {
            $table->string('title_id')->nullable()->after('title');
            $table->text('description_id')->nullable()->after('description');
        });

        // 3. Events
        Schema::table('events', function (Blueprint $table) {
            $table->string('title_id')->nullable()->after('title');
            $table->text('description_id')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
};

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
        Schema::create('room_amenity', function (Blueprint $table) {
            // $table->unsignedMediumInteger('room_id')->index();
            // $table->unsignedSmallInteger('amenity_id')->index();
            $table->foreignId('room_id')->constrained();
            $table->foreignId('amenity_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_amenity');
    }
};

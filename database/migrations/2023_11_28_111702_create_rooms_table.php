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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(1);
            $table->unsignedMediumInteger('user_id')->nullable()->index();
            $table->string('key')->index();
            $table->string('airbnb_id')->nullable();
            $table->string('airbnb_host_id')->nullable();
            $table->string('name')->index();
            $table->longText('description')->nullable();
            $table->unsignedTinyInteger('type_id')->index();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('slug')->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};

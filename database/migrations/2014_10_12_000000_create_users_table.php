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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(1);
            $table->string('key')->index();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('username')->unique();
            $table->unsignedSmallInteger('role_id')->index();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->ipAddress('registration_ip_address');
            $table->dateTime('login')->nullable();
            $table->dateTime('access')->nullable();
            $table->unsignedSmallInteger('country_id')->nullable()->index();
            $table->string('slug')->unique();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

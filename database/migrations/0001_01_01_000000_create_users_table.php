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
            $table->string('position', 25)->nullable();
            $table->string('nrp', 20)->unique();
            $table->string('full_name', 50);
            $table->string('nik', 16)->nullable();
            $table->string('bpjs_ketenagakerjaan', 13)->unique()->nullable();
            $table->string('bpjs_kesehatan', 13)->unique()->nullable();
            $table->unsignedInteger('payrate')->nullable();
            $table->string('npwp', 25)->unique()->nullable();
            $table->date('doh')->nullable();
            $table->string('birth_place', 20)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('religion', 10)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('city', 20)->nullable();
            $table->string('address', 200)->nullable();
            $table->string('status', 20)->nullable();
            $table->string('image', 255)->nullable();
            $table->string('password', 100);

            $table->rememberToken();

            $table->softDeletes();

            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

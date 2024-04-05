<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        // TODO Admin saat create karyawan, cuma kasih nrp dan pw doang biar bisa Login ?
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->timestamp('clock_in')->nullable();
            $table->timestamp('clock_out')->nullable();
            $table->string('description', 200)->nullable();
            $table->unsignedTinyInteger('status');
            $table->string('latitude', 50);
            $table->string('longitude', 50);
            $table->string('location_address', 200);

            $table->softDeletes();
            $table->timestamps();
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

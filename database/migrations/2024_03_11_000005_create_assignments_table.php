<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('work_id');
            $table->timestamp('date');
            $table->string('description', 200);
            $table->string('latitude', 50);
            $table->string('longitude', 50);
            $table->string('location_address', 200);

            $table->softDeletes();
            $table->timestamps();
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->foreign('work_id')
                ->references('id')
                ->on('works');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};

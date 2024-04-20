<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('assignment_image', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id');
            $table->string('name', 255);

            $table->softDeletes();
            $table->timestamps();
            $table->foreign('assignment_id')
                ->references('id')
                ->on('assignments');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_image');
    }
};

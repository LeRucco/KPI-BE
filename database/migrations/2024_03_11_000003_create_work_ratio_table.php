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
        Schema::create('work_ratio', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_id');
            $table->double('percentage');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('work_id')
                ->references('id')
                ->on('works');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_ratio');
    }
};

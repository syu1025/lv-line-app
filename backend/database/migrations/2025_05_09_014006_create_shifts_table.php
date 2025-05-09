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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->enum('type', ['time', 'lecture']);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->json('lectures')->nullable();
            $table->string('line_user_id');
            $table->timestamps();

            // 同じユーザーが同じ日に複数のシフトを登録できないようにする
            $table->unique(['date', 'line_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};

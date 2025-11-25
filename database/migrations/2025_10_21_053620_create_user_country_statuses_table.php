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
        Schema::create('user_country_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->enum('status', [
                'lived',      // 住んだことがある
                'stayed',     // 宿泊したことがある
                'visited',    // 日帰りで訪れたことがある
                'passed',     // 通ったことがある
                'not_visited' // 行ったことがない
            ])->default('not_visited');
            $table->text('notes')->nullable(); // メモ
            $table->timestamps();
            
            // ユーザーと国の組み合わせは一意
            $table->unique(['user_id', 'country_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_country_statuses');
    }
};

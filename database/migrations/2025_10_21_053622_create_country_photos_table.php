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
        Schema::create('country_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->string('image_path'); // 画像ファイルパス
            $table->string('caption')->nullable(); // キャプション
            $table->text('description')->nullable(); // 詳細説明
            $table->timestamp('taken_at')->nullable(); // 撮影日時
            $table->string('location')->nullable(); // 撮影場所
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_photos');
    }
};

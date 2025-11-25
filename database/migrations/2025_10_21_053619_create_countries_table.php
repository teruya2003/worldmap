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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 国名（日本語）
            $table->string('name_en'); // 国名（英語）
            $table->string('code', 3); // 国コード（ISO 3166-1 alpha-3）
            $table->string('continent'); // 大陸名
            $table->bigInteger('population')->nullable(); // 人口
            $table->string('capital')->nullable(); // 首都
            $table->text('languages')->nullable(); // 公用語（JSON形式）
            $table->string('currency')->nullable(); // 通貨
            $table->text('description')->nullable(); // 国の説明
            $table->string('background_image')->nullable(); // 背景画像パス
            $table->decimal('latitude', 10, 8)->nullable(); // 緯度
            $table->decimal('longitude', 11, 8)->nullable(); // 経度
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};

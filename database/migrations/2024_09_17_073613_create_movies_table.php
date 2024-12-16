<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id(); // ID фильма
            $table->unsignedBigInteger('kinopoisk_id')->unique(); // Уникальный ID Кинопоиска
            $table->string('slug')->unique()->nullable();
            $table->string('title'); // Название фильма
            $table->string('poster_url')->nullable(); // URL постера
            $table->year('year')->nullable(); // Год выпуска
            $table->string('country')->nullable();
            $table->text('description')->nullable(); // Описание
            $table->decimal('rating', 3, 1)->nullable(); // Рейтинг (например, 8.5)
            $table->string('external_link')->nullable(); // Ссылка на Кинопоиск

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movies');
    }
};





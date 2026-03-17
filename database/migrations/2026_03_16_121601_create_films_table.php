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

         Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->decimal('duration');
            $table->enum('rate',['G' , 'PG' , 'PG-13' , 'R' , 'NC-17'])->default('G');
            $table->string('trailer')->nullable();
            $table->timestamps();
        });

        Schema::create('film_genres', function (Blueprint $table)
        {
            $table->foreignId('genre_id')->constrained('genres', 'id')->onDelete('set null');
            $table->foreignId('film_id')->constrained('films', 'id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('films');
        Schema::dropIfExists('genres');
        Schema::dropIfExists('film_genres');
    }
};

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
        Schema::create('places', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description');
            $table->string('image_url');
            $table->enum('parking', ['free', 'bayar'])->default('bayar');
            $table->enum('wifi', ['lambat', 'biasa', 'lancar'])->nullable();
            $table->enum('room', ['sempit', 'biasa', 'luas'])->default('biasa');
            $table->string('open_hour');
            $table->string('close_hour');
            $table->integer('price_min');
            $table->integer('price_max');
            $table->float('him_rating');
            $table->float('her_rating');
            $table->float('overall_rating');
            $table->boolean('is_fav')->default(false);
            $table->string('map_url');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};

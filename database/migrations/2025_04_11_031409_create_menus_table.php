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
        Schema::create('menus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description');
            $table->string('image_url');
            $table->enum('portion', ['cemil', 'lumayan', 'ngenyangin', 'banyak', 'super'])->default('ngenyangin');
            $table->integer('price');
            $table->float('him_rating')->nullable();
            $table->float('her_rating')->nullable();
            $table->float('overall_rating');
            $table->boolean('is_fav')->default(false);
            $table->uuid('place_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('place_id')->references('id')->on('places')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};

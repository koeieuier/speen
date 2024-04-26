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
        Schema::create('article_category', function (Blueprint $table) {
            $table->id();
            //$table->timestamps();
            $table->integer('article_id');
            $table->integer('category_id');
        });

        Schema::table('articles_categories', function(Blueprint $table) {
            $table->foreign('article_id')->references('id')->on('articles');
            $table->foreign('category_id')->references('id')->on('categories');
        });



        Schema::table('articles', function (Blueprint $table) {
            $table->foreign('image_id')->references('id')->on('images');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('article_id')->references('id')->on('articles');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('images', function (Blueprint $table) {
            $table->foreign('article_id')->references('id')->on('articles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles_categories');
    }
};

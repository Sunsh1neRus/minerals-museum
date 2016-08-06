<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMineralsImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('minerals_images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('mineral_id')->unsigned()->nullable();
            $table->foreign('mineral_id')->references('id')->on('minerals');
            $table->string('url_middle')->unique();
            $table->string('url_original')->unique();
            $table->string('description')->nullable();
            $table->boolean('main_image_of_mineral')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('minerals_images');
    }
}

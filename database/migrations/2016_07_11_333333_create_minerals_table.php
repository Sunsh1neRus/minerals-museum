<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMineralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('minerals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('class')->nullable();
            $table->float('hardness_before')->nullable();
            $table->float('hardness_after')->nullable();
            $table->string('color')->nullable();
            $table->string('color_in_line')->nullable();
            $table->string('transparency')->nullable();
            $table->float('density_before')->nullable();
            $table->float('density_after')->nullable();
            $table->string('shine')->nullable();
            $table->string('cleavage')->nullable();
            $table->string('fracture')->nullable();
            $table->string('genesis')->nullable();
            $table->text('practical_use')->nullable();
            $table->string('chemical_formula')->nullable();
            $table->text('deposit')->nullable();

            $table->boolean('seen')->default(false);
            $table->integer('views');
            $table->integer('last_updater_id')->unsigned()->nullable();
            $table->foreign('last_updater_id')->references('id')->on('users');
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
        Schema::drop('minerals');
    }
}

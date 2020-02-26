<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharactersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('characters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('name');
            $table->smallInteger('class_id');
            $table->integer('experience')->default(1);
            $table->smallInteger('level')->default(1);
            $table->smallInteger('strength');
            $table->smallInteger('dexterity');
            $table->smallInteger('constitution');
            $table->smallInteger('intellect');
            $table->smallInteger('wisdom');
            $table->smallInteger('HP');
            $table->smallInteger('VP');
            $table->integer('coins')->default(0);
            $table->tinyInteger('is_active')->default(0);
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
        Schema::dropIfExists('characters');
    }
}

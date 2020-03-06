<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobiles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('room_inner_id', 12);
            $table->string('name');
            $table->string('description');
            $table->smallInteger('profession_id');
            $table->smallInteger('level')->default(1);
            $table->smallInteger('strength');
            $table->smallInteger('dexterity');
            $table->smallInteger('constitution');
            $table->smallInteger('intellect');
            $table->smallInteger('wisdom');
            $table->smallInteger('size');
            $table->smallInteger('HP');
            $table->smallInteger('VP');
            $table->integer('coins')->default(0);
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
        Schema::dropIfExists('mobiles');
    }
}

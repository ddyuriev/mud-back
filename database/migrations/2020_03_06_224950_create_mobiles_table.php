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
//            $table->string('room_inner_id', 12);
            $table->integer('room_id')->unsigned();
            $table->string('mobile_inner_id', 12);
            $table->string('name');
            $table->string('pseudonyms');
            $table->string('title_inside_of_room');
            $table->string('description', 1000);
            $table->smallInteger('profession_id');
            $table->smallInteger('level')->default(1);
            $table->smallInteger('strength');
            $table->smallInteger('dexterity');
            $table->smallInteger('constitution');
            $table->smallInteger('intellect');
            $table->smallInteger('wisdom');
            $table->smallInteger('size');
            $table->smallInteger('HP');
            $table->smallInteger('maxHP');
            $table->smallInteger('damage_min');
            $table->smallInteger('damage_max');
            $table->smallInteger('attacks_number');
            $table->integer('coins')->default(0);
            $table->integer('exp_reward')->default(0);
            $table->tinyInteger('is_travel')->default(0);
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

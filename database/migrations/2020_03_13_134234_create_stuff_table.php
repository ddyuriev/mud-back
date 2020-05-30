<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStuffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stuff', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->smallInteger('value_level')->unsigned();
            $table->integer('stuff_type_id')->unsigned();
            $table->smallInteger('dest_slot_id')->unsigned();
            $table->smallInteger('material_id')->unsigned()->default(1);
            $table->smallInteger('damage_min')->unsigned()->default(0);
            $table->smallInteger('damage_max')->unsigned()->default(0);
            $table->smallInteger('damroll_bonus')->unsigned()->default(0);
            $table->smallInteger('hitroll_bonus')->unsigned()->default(0);
            $table->smallInteger('armor')->unsigned()->default(0);
            $table->smallInteger('armor_class')->unsigned()->default(0);
            $table->smallInteger('weight')->unsigned()->default(1);
//            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stuff');
    }
}

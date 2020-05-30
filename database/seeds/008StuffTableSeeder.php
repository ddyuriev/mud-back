<?php

use Illuminate\Database\Seeder;

class StuffTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**/
//        \App\Helpers\Debugger::PrintToFile('--StuffTableSeeder', 'StuffTableSeeder');
        /**/

        \DB::table('stuff')->insert([
            'name' => 'Длинный меч',
            'value_level' => 1,
            'stuff_type_id' => 1,
            'dest_slot_id' => \App\Slot::IN_RIGHT_HAND,
            'material_id' => 1,
            'damage_min' => 2,
            'damage_max' => 5,
            'damroll_bonus' => 0,
            'hitroll_bonus' => 0,
            'armor' => 0,
            'armor_class' => 0,
            'weight' => 10,
        ]);

        \DB::table('stuff')->insert([
            'name' => 'Короткий меч',
            'value_level' => 1,
            'stuff_type_id' => 1,
            'dest_slot_id' => \App\Slot::IN_LEFT_HAND,
            'material_id' => 1,
            'damage_min' => 1,
            'damage_max' => 4,
            'damroll_bonus' => 0,
            'hitroll_bonus' => 0,
            'armor' => 0,
            'armor_class' => 0,
            'weight' => 10,
        ]);

        \DB::table('stuff')->insert([
            'name' => 'Большой темный меч',
            'value_level' => 7,
            'stuff_type_id' => 1,
            'dest_slot_id' => \App\Slot::IN_RIGHT_HAND,
            'material_id' => 1,
            'damage_min' => 4,
            'damage_max' => 20,
            'damroll_bonus' => 0,
            'hitroll_bonus' => 0,
            'armor' => 0,
            'armor_class' => 0,
            'weight' => 10,
        ]);

        \DB::table('stuff')->insert([
            'name' => 'Двуручный меч',
            'value_level' => 1,
            'stuff_type_id' => 1,
            'dest_slot_id' => \App\Slot::IN_BOTH_HANDS,
            'material_id' => 1,
            'damage_min' => 4,
            'damage_max' => 8,
            'damroll_bonus' => 0,
            'hitroll_bonus' => 0,
            'armor' => 0,
            'armor_class' => 0,
            'weight' => 10,
        ]);
    }
}

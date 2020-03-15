<?php

use Illuminate\Database\Seeder;

class SlotsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('slots')->insert([
            'id'    => \App\Slot::IN_RIGHT_HAND,
            'name'    => 'в правой руке',
        ]);
        \DB::table('slots')->insert([
            'id'    => \App\Slot::IN_LEFT_HAND,
            'name'    => 'в левой руке',
        ]);
        \DB::table('slots')->insert([
            'id'    => \App\Slot::IN_BOTH_HANDS,
            'name'    => 'в обоих руках',
        ]);

        \DB::table('slots')->insert([
            'id'    => \App\Slot::IN_INVENTORY,
            'name'    => 'инвентарь',
        ]);
    }
}

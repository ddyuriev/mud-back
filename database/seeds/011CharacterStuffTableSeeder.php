<?php

use Illuminate\Database\Seeder;

class CharacterStuffTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('character_stuff')->insert([
            'character_id' => 1,
            'stuff_id' => '3',
            'slot_id' => \App\Slot::IN_RIGHT_HAND,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);

        \DB::table('character_stuff')->insert([
            'character_id' => 1,
            'stuff_id' => '4',
            'slot_id' => \App\Slot::IN_INVENTORY,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);

        \DB::table('character_stuff')->insert([
            'character_id' => 1,
            'stuff_id' => '2',
            'slot_id' => \App\Slot::IN_LEFT_HAND,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);

        \DB::table('character_stuff')->insert([
            'character_id' => 1,
            'stuff_id' => '5',
            'slot_id' => \App\Slot::IN_INVENTORY,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);
    }
}

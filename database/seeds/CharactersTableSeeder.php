<?php

use Illuminate\Database\Seeder;

class CharactersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Character::query()->truncate();

        \DB::table('characters')->insert([
            'user_id' => 1,
            'name' => 'Воин Тэрион',
            'strength' => '14',
            'dexterity' => '12',
            'constitution' => '12',
            'intellect' => '11',
            'wisdom' => '11',
            'is_active' => 1,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);

        \DB::table('characters')->insert([
            'user_id' => 1,
            'name' => 'Маг Таша',
            'strength' => '12',
            'dexterity' => '12',
            'constitution' => '12',
            'intellect' => '12',
            'wisdom' => '12',
//            'is_active' => 0,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);
    }
}

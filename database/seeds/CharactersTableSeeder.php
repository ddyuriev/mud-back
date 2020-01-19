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
            'name' => 'Воин',
            'strength' => '14',
            'dexterity' => '12',
            'constitution' => '12',
            'intellect' => '11',
            'wisdom' => '11',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);

        \DB::table('characters')->insert([
            'name' => 'Маг',
            'strength' => '12',
            'dexterity' => '12',
            'constitution' => '12',
            'intellect' => '12',
            'wisdom' => '12',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);
    }
}

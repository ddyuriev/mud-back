<?php

use Illuminate\Database\Seeder;

class MobilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        \App\Character::query()->truncate();

        \DB::table('mobiles')->insert([
            'room_inner_id' => '1_301',
            'name' => 'Тэрион',
            'profession_id' => 1,
            'experience' => 1,
            'strength' => '14',
            'dexterity' => '12',
            'constitution' => '12',
            'intellect' => '11',
            'wisdom' => '11',
            'HP' => 20,
            'VP' => 70,
            'coins' => 0,
            'delevels_count' => 0,
            'is_active' => 1,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);
    }
}

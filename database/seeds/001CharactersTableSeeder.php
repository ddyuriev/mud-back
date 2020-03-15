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

        \DB::table('characters')->insert([
            'user_id' => 2,
            'name' => 'Маг Таша',
            'profession_id' => 2,
            'experience' => 1,
            'strength' => '12',
            'dexterity' => '12',
            'constitution' => '12',
            'intellect' => '12',
            'wisdom' => '12',
            'HP' => 18,
            'VP' => 60,
            'coins' => 0,
            'delevels_count' => 0,
            'is_active' => 1,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);


        /*-------------------------------------------------------------------*/
        \DB::table('character_skill')->insert([
            'character_id' => 1,
            'skill_id' => \App\Skill::LONG_SWORDS_ID,
            'value' => 7,
        ]);
        \DB::table('character_skill')->insert([
            'character_id' => 1,
            'skill_id' => \App\Skill::SHORT_BLADES_ID,
            'value' => 2,
        ]);
        \DB::table('character_skill')->insert([
            'character_id' => 1,
            'skill_id' => \App\Skill::KNOCK_DOWN_ID,
            'value' => 1,
        ]);
    }
}

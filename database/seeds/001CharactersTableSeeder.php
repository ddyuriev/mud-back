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

//        \DB::table('characters')->insert([
//            'user_id' => 1,
//            'name' => 'Тэрион',
//            'profession_id' => 1,
//            'experience' => 1,
//            'strength' => '11',
//            'dexterity' => '8',
//            'constitution' => '9',
//            'intellect' => '8',
//            'wisdom' => '8',
//            'resistance' => '8',
//            'HP' => 20,
//            'VP' => 70,
//            'coins' => 0,
//            'delevels_count' => 0,
//            'is_active' => 1,
//            'created_at' => \Carbon\Carbon::now(),
//            'updated_at' => \Carbon\Carbon::now(),
//        ]);

        $therion = [
            'user_id' => 1,
            'name' => 'Тэрион',
            'profession_id' => 1,
        ];
        app(\App\Services\CharacterService::class)->createCharacter($therion);


        \DB::table('characters')->insert([
            'user_id' => 2,
            'name' => 'Хармус',
            'profession_id' => 1,
            'experience' => 1,
            'strength' => '11',
            'dexterity' => '8',
            'constitution' => '9',
            'intellect' => '8',
            'wisdom' => '8',
            'resistance' => '8',
            'HP' => 20,
            'VP' => 70,
            'coins' => 0,
            'delevels_count' => 0,
            'is_active' => 1,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);

        \DB::table('characters')->insert([
            'user_id' => 3,
            'name' => 'Маг Таша',
            'profession_id' => 2,
            'experience' => 1,
            'strength' => '8',
            'dexterity' => '8',
            'constitution' => '9',
            'intellect' => '10',
            'wisdom' => '9',
            'resistance' => '8',
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

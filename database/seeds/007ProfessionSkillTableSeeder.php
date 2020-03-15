<?php

use Illuminate\Database\Seeder;

/**
 * Class ProfessionSkillTableSeeder
 *
 * learning_level etc
 */
class ProfessionSkillTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('profession_skill')->insert([
            'profession_id'    => \App\Profession::WARRIOR_ID,
            'skill_id'    => \App\Skill::LONG_SWORDS_ID,
            'learning_level' => 1
        ]);

        \DB::table('profession_skill')->insert([
            'profession_id'    => \App\Profession::WARRIOR_ID,
            'skill_id'    => \App\Skill::SHORT_BLADES_ID,
            'learning_level' => 1
        ]);

        \DB::table('profession_skill')->insert([
            'profession_id'    => \App\Profession::WARRIOR_ID,
            'skill_id'    => \App\Skill::KNOCK_DOWN_ID,
            'learning_level' => 5
        ]);

        \DB::table('profession_skill')->insert([
            'profession_id'    => \App\Profession::WIZARD_ID,
            'skill_id'    => \App\Skill::SHORT_BLADES_ID,
            'learning_level' => 7
        ]);
    }
}

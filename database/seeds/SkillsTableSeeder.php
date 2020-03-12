<?php

use Illuminate\Database\Seeder;

class SkillsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $warriorProfessionId = \App\Profession::WARRIOR_ID;
        $wizardProfessionId = \App\Profession::WIZARD_ID;
        $clericProfessionId = \App\Profession::CLERIC_ID;


        \DB::table('skills')->insert([
            'id' => \App\Skill::LONG_SWORDS_ID,
            'name' => 'одноручные мечи',
//            'learning_level' => <<<STR
//{
//    "$warriorProfessionId": "1"
//}
//STR
        ]);

        \DB::table('skills')->insert([
            'id' => \App\Skill::BASTARD_SWORDS_ID,
            'name' => 'полутораручные мечи',
//            'learning_level' => <<<STR
//{
//    "$warriorProfessionId": "2"
//}
//STR
        ]);

        \DB::table('skills')->insert([
            'id' => \App\Skill::TWO_HANDED_SWORDS_ID,
            'name' => 'двуручные мечи',
//            'learning_level' => <<<STR
//{
//    "$warriorProfessionId": "4"
//}
//STR
        ]);

        \DB::table('skills')->insert([
            'id' => \App\Skill::SHORT_BLADES_ID,
            'name' => 'короткие лезвия',
//            'learning_level' => <<<STR
//{
//    "$warriorProfessionId": "1",
//    "$wizardProfessionId": "2",
//    "$clericProfessionId": "5"
//}
//STR
        ]);

        \DB::table('skills')->insert([
            'id' => \App\Skill::KNOCK_DOWN_ID,
            'name' => 'сбить противника',
//            'learning_level' => <<<STR
//{
//    "$warriorProfessionId": "5"
//}
//STR
        ]);

    }
}

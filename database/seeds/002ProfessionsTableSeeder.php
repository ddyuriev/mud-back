<?php

use Illuminate\Database\Seeder;

class ClassesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('professions')->insert([
            'id' => \App\Profession::WARRIOR_ID,
            'name' => 'Воин',
        ]);

        \DB::table('professions')->insert([
            'id' => \App\Profession::WIZARD_ID,
            'name' => 'Маг',
        ]);

        \DB::table('professions')->insert([
            'id' => \App\Profession::CLERIC_ID,
            'name' => 'Вор',
        ]);
    }
}

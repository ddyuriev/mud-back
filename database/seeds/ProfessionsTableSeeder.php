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
            'name' => 'Воин',
        ]);

        \DB::table('professions')->insert([
            'name' => 'Маг',
        ]);

        \DB::table('professions')->insert([
            'name' => 'Вор',
        ]);
    }
}

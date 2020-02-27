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
        \DB::table('classes')->insert([
            'name' => 'Воин',
        ]);

        \DB::table('classes')->insert([
            'name' => 'Маг',
        ]);

        \DB::table('classes')->insert([
            'name' => 'Вор',
        ]);
    }
}

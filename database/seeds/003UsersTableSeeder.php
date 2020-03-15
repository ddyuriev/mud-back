<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->insert([
            'email'    => 'therion@mail.ru',
            'uuid'     => \Illuminate\Support\Str::uuid()->toString(),
            'name'     => 'Dmitri',
            'password' => app('hash')->make(1),
        ]);

        \DB::table('users')->insert([
            'email'    => 'dimas@mail.ru',
            'uuid'     => \Illuminate\Support\Str::uuid()->toString(),
            'name'     => 'Dmitri',
            'password' => app('hash')->make(1),
        ]);
    }
}

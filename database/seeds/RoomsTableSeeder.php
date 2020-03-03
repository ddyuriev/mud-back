<?php

use Illuminate\Database\Seeder;

class RoomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Room::query()->truncate();

        \DB::table('rooms')->insert([
            'uuid' => \App\Room::START_ROOM_UUID,
            'zone_id' => 1,
            'name' => 'Лесная поляна на перекрестке тропинок.',
            'description' => 'Вы оказались на круглой зеленой поляне, окруженной величественным лесом',
            'exits' => '{"e":"6d3d28eb-212c-4e08-8b4b-c83dd8eb9be6"}',
        ]);

        \DB::table('rooms')->insert([
            'uuid' => "6d3d28eb-212c-4e08-8b4b-c83dd8eb9be6",
            'zone_id' => 1,
            'name' => 'Избушка отшельника',
            'description' => 'Вы подошли к небольшой избушке. Выглядит она складно и добротно. До вас доносится ржание лошади. В стороне вы заметили площадку с тренировачной экипировкой и оружием',
            'exits' => '{"w":"e9ddeb92-1161-408c-b613-4ce779ca8469"}',
        ]);

    }
}

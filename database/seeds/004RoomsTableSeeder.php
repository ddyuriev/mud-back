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
//        $startRoomUuid = \App\Room::START_ROOM_UUID;
        $startRoomInnerId = \App\Room::START_ROOM_INNER_ID;

        \App\Room::query()->truncate();

        \DB::table('rooms')->insert([
            'inner_id' => $startRoomInnerId,
            'zone_id' => 1,
            'name' => 'Лесная поляна на перекрестке тропинок.',
            'description' => 'Вы оказались на круглой зеленой поляне, окруженной величественным лесом',
            'exits' => <<<STR
{
    "n": "1_200",
    "e": "1_300"
}
STR
        ]);

        //start north
        \DB::table('rooms')->insert([
            'inner_id' => "1_200",
            'zone_id' => 1,
            'name' => 'Тропинка в лесу, уходящая в горы на север',
            'description' => 'Ландшафт становится более извилистым и каменистым.',
            'exits' => <<<STR
{
    "n": "1_201",
    "s": "$startRoomInnerId"
}
STR
        ]);

        \DB::table('rooms')->insert([
            'inner_id' => "1_201",
            'zone_id' => 1,
            'name' => 'Башня мага',
            'description' => 'Вы вышли к небольшой скале, на которой кто-то выложил башню, похожую на маяк. Вот только моря тут нет.',
            'exits' => '{"s":"1_200"}',
        ]);
        //end north

        //start east
        \DB::table('rooms')->insert([
            'inner_id' => "1_300",
            'zone_id' => 1,
            'name' => 'Восточная тропинка в лесу',
            'description' => 'Вы петляете в лесу, подмечая, что общее направление на восток сохраняется',
            'exits' => <<<STR
{
    "e": "1_301",
    "w": "$startRoomInnerId"
}
STR
        ]);

        \DB::table('rooms')->insert([
            'inner_id' => "1_301",
            'zone_id' => 1,
            'name' => 'Избушка отшельника',
            'description' => 'Вы подошли к небольшой избушке. Выглядит она складно и добротно. До вас доносится ржание лошади. В стороне вы заметили площадку с тренировочной экипировкой и оружием',
            'exits' => '{"w":"1_300"}',
//            'mobiles' => <<<STR
//{
//    "0": "1_301_001",
//    "1": "1_301_002",
//    "2": "1_301_003"
//}
//STR
        ]);
        //end east


    }
}

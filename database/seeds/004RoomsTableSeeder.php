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
    "s": "$startRoomInnerId",
    "w": "2_100"
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

        //Зона дерево
        \DB::table('rooms')->insert([
            'inner_id' => "2_100",
            'zone_id' => 2,
            'name' => 'В тени развесистого дуба',
            'description' => 'Вы вышли к огромному дереву, могучий ствол которого едва обхватят восемь человек. Вы смекаете, что ветви расположены удобно, можно залезть на дерево',
            'exits' => <<<STR
{
    "e": "1_200",
    "u": "2_110"
}
STR
        ]);

        \DB::table('rooms')->insert([
            'inner_id' => "2_110",
            'zone_id' => 2,
            'name' => 'На широкой нижней ветке',
            'description' => 'Вы залезли на нижнюю ветку. Тут, в царстве полумрака, можно и отдохнуть, но интуиция вам подсказывает, что выше интереснее',
            'exits' => <<<STR
{
    "u": "2_130",
    "d": "2_100"
}
STR
        ]);

        \DB::table('rooms')->insert([
            'inner_id' => "2_130",
            'zone_id' => 2,
            'name' => 'На широкой невысокой ветке',
            'description' => 'Вы залезли чуть выше. Ветка оказалась настолько широкой, что по ней можно прогуливаться...',
            'exits' => <<<STR
{
    "w": "2_131",
    "d": "2_100"
}
STR
        ]);

        \DB::table('rooms')->insert([
            'inner_id' => "2_131",
            'zone_id' => 2,
            'name' => 'На широкой невысокой ветке',
            'description' => 'Вы сделали несколько шагов по невысокой ветке. Тут все, как на обычном дереве. Муравьи, жучки, гусеницы',
            'exits' => <<<STR
{
    "w": "2_132",
    "e": "2_130"
}
STR
        ]);
        \DB::table('rooms')->insert([
            'inner_id' => "2_132",
            'zone_id' => 2,
            'name' => 'На широкой невысокой ветке',
            'description' => 'Вы сделали несколько шагов по невысокой ветке. Тут все, как на обычном дереве. Муравьи, жучки, гусеницы',
            'exits' => <<<STR
{
    "e": "2_131"
}
STR
        ]);


    }
}

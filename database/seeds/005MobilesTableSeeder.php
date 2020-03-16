<?php

use Illuminate\Database\Seeder;

class MobilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        \App\Character::query()->truncate();

        \DB::table('mobiles')->insert([
//            'room_inner_id' => '1_301',
//            'room_id' => '1_301',
            'room_id' => $this->getRoomIdByRoomInnerId('1_301'),
            'mobile_inner_id' => '1_301_001',
            'name' => 'Воин',
            'pseudonyms' => <<<STR
{
    "0": "воин",
    "1": "седой"
}
STR
            ,
            'title_inside_of_room' => 'Седой воин неспеша прогуливается по лужайке',
            'description' => 'Этот человек побывал во многих переделках. Лицо и руки его обильно украшены разнообразными шрамами. Не понятно, почему он выбрал уединение,но возможно вы сможете у него кое-чему научиться',
            'profession_id' => 1,
            'level' => 25,
            'strength' => '20',
            'dexterity' => '20',
            'constitution' => '20',
            'intellect' => '12',
            'wisdom' => '13',
            'size' => 20,
            'HP' => 500,
//            'VP' => 70,
            'coins' => 0,
        ]);

        \DB::table('mobiles')->insert([
//            'room_inner_id' => '1_301',
            'room_id' => $this->getRoomIdByRoomInnerId('1_301'),
            'mobile_inner_id' => '1_301_002',
            'name' => 'Деревянный манекен',
//            'pseudonyms' => 'деревянный,манекен',
            'pseudonyms' => <<<STR
{
    "0": "деревянный",
    "1": "манекен"
}
STR
            ,

            'title_inside_of_room' => 'Деревянный манекен для новичков стоит тут',
            'description' => 'Бревно, палки, проволока... Врядли он сможет дать сдачи',
            'profession_id' => 1,
            'level' => 1,
            'strength' => '20',
            'dexterity' => '20',
            'constitution' => '20',
            'intellect' => '12',
            'wisdom' => '13',
            'size' => 20,
            'HP' => 70,
            'coins' => 0,
        ]);

        \DB::table('mobiles')->insert([
//            'room_inner_id' => '1_301',
            'room_id' => $this->getRoomIdByRoomInnerId('1_301'),
            'mobile_inner_id' => '1_301_003',
            'name' => 'Механический манекен',
//            'pseudonyms' => 'механический,манекен',
            'pseudonyms' => <<<STR
{
    "0": "механический",
    "1": "манекен"
}
STR
            ,
            'title_inside_of_room' => 'Механический манекен расположен под навесом от дождя',
            'description' => 'Перед вами немного ржавая, но добротная кираса, на ней помятый кожаный шлем на обстроганном полене. Мастерски выкованная правая рука держит затупленный меч. Когда механизм приводится в действие помошниками, рука начинает выписывать восьмерки в воздухе. Попробуйте ее остановить!',
            'profession_id' => 1,
            'level' => 10,
            'strength' => '20',
            'dexterity' => '20',
            'constitution' => '20',
            'intellect' => '12',
            'wisdom' => '13',
            'size' => 20,
            'HP' => 200,
            'coins' => 0,
        ]);

//        \DB::table('mobiles')->insert([
//            'room_inner_id' => '1_301',
//            'name' => 'Воин2',
//            'title_inside_of_room' => 'Седой воин2 неспеша прогуливается по лужайке',
//            'description' => 'Этот человек побывал во многих переделках. Лицо и руки его обильно украшены разнообразными шрамами. Не понятно, почему он выбрал уединение,но возможно вы сможете у него кое-чему научиться',
//            'profession_id' => 1,
//            'level' => 25,
//            'strength' => '20',
//            'dexterity' => '20',
//            'constitution' => '20',
//            'intellect' => '12',
//            'wisdom' => '13',
//            'size' => 20,
//            'HP' => 500,
//            'coins' => 0,
//        ]);
//
//        \DB::table('mobiles')->insert([
//            'room_inner_id' => '1_301',
//            'name' => 'Воин3',
//            'title_inside_of_room' => 'Седой воин3 неспеша прогуливается по лужайке',
//            'description' => 'Этот человек побывал во многих переделках. Лицо и руки его обильно украшены разнообразными шрамами. Не понятно, почему он выбрал уединение,но возможно вы сможете у него кое-чему научиться',
//            'profession_id' => 1,
//            'level' => 25,
//            'strength' => '20',
//            'dexterity' => '20',
//            'constitution' => '20',
//            'intellect' => '12',
//            'wisdom' => '13',
//            'size' => 20,
//            'HP' => 500,
//            'coins' => 0,
//        ]);
    }

    protected function getRoomIdByRoomInnerId($roomInnerId)
    {

        return \App\Room::where('inner_id', $roomInnerId)->value('id');
    }
}
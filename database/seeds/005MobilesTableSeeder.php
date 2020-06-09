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
            'description' => 'Этот человек побывал во многих переделках. Лицо и руки его обильно украшены разнообразными шрамами. Непонятно, почему он выбрал уединение,но возможно вы сможете у него кое-чему научиться',
            'profession_id' => 1,
            'level' => 25,
            'strength' => '20',
            'dexterity' => '20',
            'constitution' => '20',
            'intellect' => '12',
            'wisdom' => '13',
            'size' => 20,
            'HP' => 50,
            'maxHP' => 50,
            'damage_min' => 15,
            'damage_max' => 30,
            'attacks_number' => 3,
            'coins' => 0,
            'exp_reward' => 100000,
            'teacher_level' => 1
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
            'maxHP' => 70,
            'damage_min' => 1,
            'damage_max' => 3,
            'attacks_number' => 1,
            'coins' => 0,
            'exp_reward' => 10000,
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
            'maxHP' => 200,
            'damage_min' => 5,
            'damage_max' => 15,
            'attacks_number' => 2,
            'coins' => 0,
            'exp_reward' => 20000,
        ]);


        /* start Зона дерево*/

        foreach ($roomInnerIds = ['2_130', '2_132', '2_140', '2_150'] as $roomInnerId) {
            \DB::table('mobiles')->insert([
                'room_id' => $this->getRoomIdByRoomInnerId($roomInnerId),
                'mobile_inner_id' => '2_130_001',
                'name' => 'Маленький комарик',
                'pseudonyms' => <<<STR
{
    "0": "Маленький",
    "1": "комарик"
}
STR
                ,
                'title_inside_of_room' => 'Маленький комарик жужжит над ухом в ожидании мощного удара.',
                'description' => 'Этот комар достойный соперник.',
                'profession_id' => 1,
                'level' => 1,
                'strength' => '1',
                'dexterity' => '1',
                'constitution' => '1',
                'intellect' => '1',
                'wisdom' => '1',
                'size' => 1,
                'HP' => 10,
                'maxHP' => 10,
                'damage_min' => 1,
                'damage_max' => 1,
                'attacks_number' => 1,
                'coins' => 0,
                'exp_reward' => 20000,
            ]);
        }


        foreach ($roomInnerIds = ['2_130', '2_132'] as $roomInnerId) {
            \DB::table('mobiles')->insert([
                'room_id' => $this->getRoomIdByRoomInnerId($roomInnerId),
                'mobile_inner_id' => '2_130_002',
                'name' => 'Крохотная мушка',
                'pseudonyms' => <<<STR
{
    "0": "крохотная",
    "1": "мушка"
}
STR
                ,
                'title_inside_of_room' => 'Крохотная мушка сидит на листке',
                'description' => 'Вы едва заметили эту крохотную мушку. Можно попробловать на ней свои силы',
                'profession_id' => 1,
                'level' => 1,
                'strength' => '1',
                'dexterity' => '1',
                'constitution' => '1',
                'intellect' => '1',
                'wisdom' => '1',
                'size' => 1,
                'HP' => 10,
                'maxHP' => 10,
                'damage_min' => 1,
                'damage_max' => 1,
                'attacks_number' => 1,
                'coins' => 0,
                'exp_reward' => 20000,
            ]);
        }


        foreach ($roomInnerIds = ['2_131', '2_140'] as $roomInnerId) {
            \DB::table('mobiles')->insert([
                'room_id' => $this->getRoomIdByRoomInnerId($roomInnerId),
                'mobile_inner_id' => '2_130_003',
                'name' => 'Небольшой паучок',
                'pseudonyms' => <<<STR
{
    "0": "небольшой",
    "1": "паучок"
}
STR
                ,
                'title_inside_of_room' => 'Небольшой паучок притаился меж веток',
                'description' => 'Маленький паучок сплел паутинку. Сегодня добычи у него небыло',
                'profession_id' => 1,
                'level' => 1,
                'strength' => '1',
                'dexterity' => '1',
                'constitution' => '1',
                'intellect' => '1',
                'wisdom' => '1',
                'size' => 1,
                'HP' => 10,
                'maxHP' => 10,
                'damage_min' => 1,
                'damage_max' => 1,
                'attacks_number' => 1,
                'coins' => 0,
                'exp_reward' => 20000,
            ]);
        }


        foreach ($roomInnerIds = ['2_131', '2_150', '2_150'] as $roomInnerId) {
            \DB::table('mobiles')->insert([
                'room_id' => $this->getRoomIdByRoomInnerId($roomInnerId),
                'mobile_inner_id' => '2_130_004',
                'name' => 'Небольшой жучок',
                'pseudonyms' => <<<STR
{
    "0": "небольшой",
    "1": "жучок"
}
STR
                ,
                'title_inside_of_room' => 'Небольшой жучок ползет по ветке',
                'description' => 'Крохотный жучок смотрит на вас с холодной яростью. Он готов атаковать. Или показалось?',
                'profession_id' => 1,
                'level' => 1,
                'strength' => '1',
                'dexterity' => '1',
                'constitution' => '1',
                'intellect' => '1',
                'wisdom' => '1',
                'size' => 1,
                'HP' => 10,
                'maxHP' => 10,
                'damage_min' => 1,
                'damage_max' => 1,
                'attacks_number' => 1,
                'coins' => 0,
                'exp_reward' => 20000,
            ]);
        }


        foreach ($roomInnerIds = ['2_140', '2_130', '2_132'] as $roomInnerId) {
            \DB::table('mobiles')->insert([
                'room_id' => $this->getRoomIdByRoomInnerId($roomInnerId),
                'mobile_inner_id' => '2_140_001',
                'name' => 'Бабочка-капустница',
                'pseudonyms' => <<<STR
{
    "0": "бабочка",
    "1": "капустница"
}
STR
                ,
                'title_inside_of_room' => 'Бабочка-капустница пархает тут. Наверное, что-то замышляет.',
                'description' => 'Похоже, что это обычная бабочка-капустница.',
                'profession_id' => 1,
                'level' => 1,
                'strength' => '1',
                'dexterity' => '1',
                'constitution' => '1',
                'intellect' => '1',
                'wisdom' => '1',
                'size' => 1,
                'HP' => 11,
                'maxHP' => 11,
                'damage_min' => 1,
                'damage_max' => 2,
                'attacks_number' => 1,
                'coins' => 0,
                'exp_reward' => 40000,
            ]);
        }

        /* end Зона дерево*/


        //start Зона небольшой поселок
        \DB::table('mobiles')->insert([
            'room_id' => $this->getRoomIdByRoomInnerId('3_021'),
            'mobile_inner_id' => '3_021_001',
            'name' => 'Хозяин таверны',
            'pseudonyms' => <<<STR
{
    "0": "хозяин",
    "1": "таверн"
}
STR
            ,
            'title_inside_of_room' => 'Хозяин таверны стоит за барной стойкой',
            'description' => 'Хозяин таверны подозрительно смотрит на вас. Попробуйте с ним поговорить, возможно это будет полезно.',
            'profession_id' => 1,
            'level' => 14,
            'strength' => 10,
            'dexterity' => 10,
            'constitution' => 10,
            'intellect' => 10,
            'wisdom' => 10,
            'size' => 1,
            'HP' => 90,
            'maxHP' => 90,
            'damage_min' => 5,
            'damage_max' => 11,
            'attacks_number' => 1,
            'coins' => 0,
            'exp_reward' => 400000,
        ]);

        //end Зона небольшой поселок


    }

    protected function getRoomIdByRoomInnerId($roomInnerId)
    {

        return \App\Room::where('inner_id', $roomInnerId)->value('id');
    }
}

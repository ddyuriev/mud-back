<?php

namespace App\Http\Controllers;

use App\Character;
use App\Helpers\Constants;
use App\Helpers\Debugger;
use App\Helpers\Formulas;
use App\Jobs\ExampleJob;
use App\Jobs\SaveCharacterJob;
use App\Room;
use App\Services\CharacterService;
use App\Slot;
use App\Stuff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;

class CharacterController extends Controller
{

    protected $characterService;

    /**
     * CharacterController constructor.
     * @param CharacterService $characterService
     */
    public function __construct(CharacterService $characterService)
    {
        $this->characterService = $characterService;
    }


    public function index()
    {
        /**/
        Debugger::PrintToFile('index', 'index');
        /**/

        return Character::find(1);
    }

    public function userInput(Request $request)
    {
//        $name = 'лов';
//        $parameter = 0;
//
//        switch (true) {
//            case mb_strpos('сила', $name) == 0:
//                $parameter = Constants::STRENGTH;
//                break;
//            case mb_strpos('ловкость', $name) == 0:
//                $parameter = Constants::DEXTERITY;
//                break;
////            case $number > 200 && $number <= 300:
////                $parameter = Constants::CONSTITUTION;
////                break;
////            case $number > 300 && $number <= 400:
////                $parameter = Constants::INTELLECT;
////                break;
////            case $number > 400 && $number <= 500:
////                $parameter = Constants::WISDOM;
////                break;
////            case $number > 500 && $number <= 600:
////                $parameter = Constants::RESISTANCE;
////                break;
//        }
//                dd($parameter);
//
////        dd(mb_strlen('ло'));
////        dd(strpos('сила', 'си'));
////        dd(strpos('телос', 'ло'));
////        dd(mb_strpos('телос', 'ло'));
//        dd(mb_strpos('ловкость', 'ло'));
        /*-----------------------------------*/


//        $character = $this->characterService->getActiveCharacterByUserEmail('therion@mail.ru');

        $character = Character::find(1);

        $character->parameters_increase = ['strength' => 1];
        $character->save();

//        dd($character['parameters_increase']);
        dd(gettype($character['parameters_increase']));

//        $character = $this->characterService->getActiveCharacterByUserEmail('dimas@mail.ru');

//        return array_filter($character['stuff'], function ($v) {
//            return $v['pivot']['slot_id'] != Slot::IN_INVENTORY;
//        });

        return array_column(array_column($character['stuff'], 'pivot'), 'slot_id');


        /*-----------------------------------*/

        $stuff = Stuff::with('slot')->first();
        return $stuff;

        $slot = Slot::with('stuff')->first();
        return $slot;

        $character = Character::with('stuff.slot')->first();
//        dd($character);

        return $character;
        /*-----------------------------------*/

        $character = $this->characterService->getActiveCharacterByUserEmail('therion@mail.ru');
        dd($character);
        $stuff = $this->characterService->getInventoryItems($character);

        dd($stuff);

//        dd(array_column($stuff, 'slot'));

//        $key = array_search('100', array_column($stuff, 'slot'));


//        array_filter($arr, function($v, $k) {
//            return $k == 'b' || $v == 4;
//        }, ARRAY_FILTER_USE_BOTH);

        $filter = array_filter($stuff, function($v, $k) {

//            print_r($v);
//            print_r($k);

//            print_r($v['pivot']['slot_id']);

            return $v['pivot']['slot_id'] == 4;
        }, ARRAY_FILTER_USE_BOTH);


        dd($filter);

        exit();

        /*-----------------------------------*/

//        $str = mb_strpos('у манек', ' ');
//        $str = strpos('у манек', ' ');
//        dd($str);
//        exit();

        /*-----------------------------------*/


        $nexLevel = 5;
        $ccc = 'WARRIOR_LEVEL_5_EXP';
//        $const = constant("Formulas::WARRIOR_LEVEL_{$nexLevel}_EXP");
//        $const = constant("Formulas::WARRIOR_LEVEL_{$nexLevel}_EXP");
        $const = constant("App\Helpers\Formulas::$ccc");
        dd($const);
        exit();

        /*-----------------------------------*/
//        $character = Character::with('user')->where('id' ,'=', 1)->first();
//        dd($character);
//        exit();

        /*-----------------------------------*/

        //null
//        dd(app()->configure('app'));

//        dispatch(new ExampleJob());

//        $character = Character::with('user')->where('id' ,'=', 1)->first()->toArray();
//        dispatch(new SaveCharacterJob($character));
        //тоже самое
//        Queue::push(new SaveCharacterJob);

        /**/
//        Debugger::PrintToFile('----userInput---' . time(), 'userInput');
        /**/

//        exit();

        /*-----------------------------------*/


//        $foo = function() { return 'value'; };
//        echo <<<SOMEHEREDOC
//{$foo()}
//SOMEHEREDOC;
//// Is the same as
//        echo "{$foo()}";
//// Is the ssame as
//        $foo = 'strtoupper';
////        echo "{$foo('hello world')}";
//
//        $character['opponent']['name'] = "АААААААААААмммм";
//
//        $message = <<<STR
//<span class='actor-attack'>Вы аккуратно разрезали {mb_strtolower({$character[`opponent`][`name'`]})}} на две части </span>
//<span class='basic-color'>{$character['opponent']['name']} мертв! R.I.P.</span>
//STR;
//
//        dd($message);
//
//
//        function fn($data) {
//            return $data;
//        }
//        $fn = 'fn';
//
//        $my_string = <<<EOT
//Number of seconds since the Unix Epoch: {$fn(time())}
//EOT;
//        dd($my_string);
//        exit();

//        echo <<<EOT
//One month ago was ${!${''} = date('Y-m-d H:i:s', strtotime('-1 month'))}.
//EOT;

        /*-----------------------------------*/


//        $a = 1;
//        $b =&$a;
//        unset($b);
//
//        print_r($a);
//
//        exit();
//
//        $a = [
//            'a' => 123,
//            'b' => 234
//        ];
//
//        $b = [
//            '1' => 2,
//            '2' => 3,
//            'a' => &$a
//        ];
//
//        print_r($b);
//
//        $a['adf'] = 123;
//
//        print_r($b);
//
//        $c = &$b['2'];
//        print_r($b);
////        unset($c);
//
//        $c = [];
//
//        print_r($b);
//
//        exit();


        /*-----------------------------------*/


//        $userInput = 'счеe';
//        $hiSTACK = 'счет';
//        var_dump(mb_stripos($hiSTACK, $userInput));
//        exit();
//        dd(mb_stripos('счет', 'сч'));


//        $text = 'Email you sent was ololo@example.com. Is it correct?';
//        $regexp = '/(?<mail>[^\s]+)@(?<domain>[^\s\.]+\.[a-z]+)/';
//        $result = preg_match_all($regexp, $text, $match);
//        var_dump(
//            $result,
//            $match
//        );

//        $URL = "https://hello.world.ru/uri/starts/here?get_params=here#anchor";
//        $regexp = "/(?<scheme>http[s]?):\/\/(?<domain>[\w\.-]+)(?<path>[^?$]+)?(?<query>[^#$]+)?[#]?(?<fragment>[^$]+)?/";
//        $result = preg_match($regexp, $URL, $match);
//        var_dump(
//            $result,
//            $match
//        );

//        $userInput = 'ув манек';
        $userInput = 'ударитьманек';
//        $userInput = 'счет';
        //ok
//        dd(preg_match("/^сч/", $userInput));
//        dd(preg_match("/^сч(е)(т)/", $userInput));
//        var_dump(preg_match("/^сч[е]{0,1}[т]{0,1}/i", $userInput));
//        dd(preg_match("/^сч?е{1}?т{1}/i", $userInput));

//        var_dump(preg_match("/сче?т?/", $userInput));

//        var_dump(preg_match("/сч[е]?[т]?/", $userInput));
//        var_dump(preg_match("/сч[е]?[т]?$/", $userInput));
//        var_dump(preg_match("/^сч(е)?(т)?$/", $userInput));
//        var_dump(preg_match("/сче?т?$/", $userInput));

        var_dump(preg_match("/^у(д)?(а)?(р)?(и)?(т)?(ь)?\b( )?.*/", $userInput));

        var_dump(preg_match("/^(у|уд|уда|удар|удари|ударит|ударить)\s.*/", $userInput));

//        var_dump(preg_match("/сч|сче|счет/", $userInput));

//        [^A-Fa-f0-9]
//        [^у(д)?(а)?(р)?(и)?(т)?(ь)]


        exit();
        /*-----------------------------------*/
//        return \Illuminate\Support\Str::uuid()->toString();

        /**/
//        Debugger::PrintToFile('userInput', 'userInput');
        /**/
        /*-----------------------------------*/

//        $level = Formulas::toNextLevel(1,10000,4);
//        dd($level);
//
//        $level = Formulas::calculateLevel(1, 55383333);
//        $level = Formulas::calculateLevel(1, 2000);
//        dd($level);
        /*-----------------------------------*/

//        $damageMessage = Formulas::damageMessage(88);
//        dd($damageMessage);
        /*-----------------------------------*/


        $character = $this->characterService->getActiveCharacterByUserEmail('therion@mail.ru');
//        $characterArray = $this->characterService->getActiveCharacterByUserEmail('therion@mail.ru')->toArray();
        dd($character);

        /*-----------------------------------*/

//        $character = $this->characterService->getActiveCharacterByUserEmail('therion@mail.ru');
//        $characterArray = $this->characterService->getActiveCharacterByUserEmail('therion@mail.ru')->toArray();
//        $start_memory = memory_get_usage();
////        $test = $character;
////        $test2 = $characterArray;
////        $testArray = [1,2,3,4,5,6,7,8,9,11,11,11,11,11,11];
//        $testArray = ['taylor', 'abigail', null];
//        $testArray[]='abigail2';
////        $testArray = array_fill(5, 6, 'banana');
////        $testArray = [];
////        for($i = 0; $i<= 1; $i++){
////            $testArray[]=$i;
////        }
////        $collection = collect(['taylor', 'abigail', null]);
////        echo memory_get_usage() - $start_memory;
//        dd(memory_get_usage() - $start_memory);
//        dd($character);

        /*-----------------------------------*/

//        $staff = Stuff::all();
//        dd($staff);

        /*-----------------------------------*/

//        $room = Room::with('mobilestest')->get();

//        $users = App\Book::with('author:id,name')->get();

        $room = Room::with(['mobiles' => function ($query) {
//            return  $query->select(['*']);
            return $query->select(['id', 'room_id']);
        }])
            ->find(5);


//        \DB::enableQueryLog();
//        dd(\DB::getQueryLog(Room::with(['mobiles' => function ($query) {
//            $query->select(['mobile_inner_id']);
//        }])
//            ->find(5)));

//        $room = Room::with('mobiles:mobile_inner_id')->find(5);
//        $room = Room::with('mobiles:pseudonyms')->get();
//        $room = Room::with('mobiles:id,pseudonyms')->find(5);
//        dd($room->toArray());
//
//        $skills = Skill::with('learning_level_check')->get();
//        dd($skills->toArray());
//
//        \DB::enableQueryLog();
//        dd(\DB::getQueryLog(Skill::with('learning_level_check')->get()));

        /*--*/

        $character = Character::with('skills3')->first();


//        $character = Character::with('skills')->first();
        //ok
//        $character = Character::with('skills.professions')->first();

        //ok
//        $character = Character::with('skills', 'profession_skills')->first();
//        $character = Character::with('skills')->get();
//        $character = Character::with('skills2')->get();

        $character = Character::with('skills.learning_level_check')->first();
//        $character = Character::with(array('skills.learning_level_check' => function ($query) {
//            $query->select(['profession_id']);
//        }))->first();

//        dd($character->toSql());
        dd($character);
//        dd($character->toArray());


//        \DB::enableQueryLog();
//        dd(\DB::getQueryLog(Character::with('skills', 'profession_skills')->first()));

//        \DB::enableQueryLog();
//        dd(\DB::getQueryLog(Character::with(array('skills.learning_level_check' => function ($query) {
//            $query->select('profession_id');
//        }))->first()));

//        return $character;

        //bad
//        return $character->skills->learning_level;

        //ok
//        return $character->skills->first();
//        return $character->skills;
//        return $character->skills->pluck('learning_level')->flatten();

        $learning_levelSomeThing = $character->skills->pluck('learning_level');

        //OK!
        dd($learning_levelSomeThing);
//        dd($learning_levelSomeThing->toArray());
//        return $learning_levelSomeThing->toArray();

//        return array_shift($learning_levelSomeThing);

//        return $character->skills->pluck('learning_level')->first()['1'];
//        return $character->skills->pluck('learning_level')->toArray();

        $keyed = $character->skills->pluck('learning_level')
            ->mapWithKeys(function ($item) {
                Debugger::PrintToFile('$item', $item);

                return [key($item) => array_shift($item)];
//            return [key($item) => 1];
            });

        dd($keyed->all());

        return $keyed->all();


        /*-----------------------------------*/
//        $arr = array('x', 'y', 'z');
////        To insert this into a database field you can use serialize function like this
//        $serializedArr = serialize($arr);
//
////        print_r($serializedArr);
////        dd($serializedArr);
////        dd($arr);
//
//        $character = Character::find(1);
//
//
//        Debugger::PrintToFile('$character', $character);
//
//        $name = $character->name;
////        dd($character);
//
//        print_r($name);
//
////        $character->name = $serializedArr;
////        $character->save();

        /*-----------------------------------*/


        /*-----------------------------------*/
//        $character = Character::find(1);
//        $character->room_uuid = 999999999;
//        dd($character->room_uuid);
//        dd($character['room_uuid']);
        /*-----------------------------------*/


        /*-----------------------------------*/
//        $arr = [
//            10 => 1,
//            20 => 2
//        ];
//        $x = &$arr[10];
//        $x = 1000;
//        return $arr;
        /*-----------------------------------*/


//        Debugger::PrintToFile('zzzzzz', $request->all());
//        exit();

        /*-----------------------------------*/
        return \Illuminate\Support\Str::uuid()->toString();
        /*-----------------------------------*/

        //OK
//        return  $request->ip();

//        return $request;

//        $request->ip()


        /**/
//        $debugFile = 'debug1111111-userInput.txt';
//        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
//        $results = print_r($request->all(), true);
//        !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
//        file_put_contents($debugFile, $current);
        /**/

//        $email = 'therion@mail.ru';
        $email = 'dimas@mail.ru';

//        $result = Character::with(['user' => function ($query) use ($email) {
//            $query->where('email', $email);
//        }])->where('is_active', true)->get();

//        $result = Character::whereHas('user', function ($query) {
//            $query->where('email', 'therion@mail.ru');
//        })->where('is_active', true)->first();


        //косяк
//        $result = Character::with([
//            'user' => function ($query) use ($email) {
//                $query->where('user.email', 'dimas@mail.ru');
//            },
//            'profession'
//        ])->first();


        $result = Character::with('user', 'profession')->whereHas('user', function ($query) use ($email) {
            $query->where('email', $email);
        })->where('is_active', true)->toSql();

        /**/
        $debugFile = 'debug1111111-userInput.txt';
        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
        $results = print_r($result, true);
        !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
        file_put_contents($debugFile, $current);

        /**/

        return $result;
    }


}

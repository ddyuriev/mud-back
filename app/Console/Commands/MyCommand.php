<?php namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MyCommand extends Command
{
    protected $name = 'mycommand:hello-world';

    protected $description = 'Приветствет вас';

    public function fire()
    {
        $this->info('Hello World Started');
        // все аргументы...
        $arguments = $this->argument();
        // все опции...
        $options = $this->option();
        // output all
        dd($arguments, $options);
    }
// ОБЪЯВЛЯЕМ АРГУМЕНТЫ, иначе пошлёт при указании неизвестного аргумента
    // array($name, $mode, $description, $defaultValue)
    protected function getArguments()
    {
        return [
            ['myArgument', InputArgument::OPTIONAL, 'myArgument', null],
        ];
    }
// ОБЪЯВЛЯЕМ ОПЦИИ, иначе пошлёт при указании неизвестной опции
    // array($name, $shortcut, $mode, $description, $defaultValue)
    protected function getOptions()
    {
        return [
            ['myOption', null, InputOption::VALUE_OPTIONAL, 'myOption definition', null],
        ];
    }
    // подробно тут http://laravel.com/docs/5.1/artisan


    /**/

    public function handle()
    {


        $users = User::All();

        /**/
        $debugFile = 'storage\debug1111111-MyCommand.txt';
        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
        $results = print_r([$this->argument(), $this->option()], true);
        !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
        file_put_contents($debugFile, $current);
        /**/

        /**/
        $debugFile = 'storage\debug1111111-$users.txt';
        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
        $results = print_r($users, true);
        !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
        file_put_contents($debugFile, $current);
        /**/
    }

    /**/
}
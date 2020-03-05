<?php

namespace App\Helpers;


class Debugger
{
    public static function PrintToFile(string $fileName, $variable)
    {
        $debugDir = is_dir('app') && is_dir('framework') ? '' : 'debug';

        $debugFile = "$debugDir/debug1111111-$fileName.txt";
        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
        $results = print_r($variable, true);
        !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
        file_put_contents($debugFile, $current);
    }
}

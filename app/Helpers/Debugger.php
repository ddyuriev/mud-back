<?php

namespace App\Helpers;

/**
 * Class Debugger
 *
 * @package App\Helpers
 */
class Debugger
{
    /**
     * @param string $fileName
     * @param        $variable
     */
    public static function PrintToFile(string $fileName, $variable)
    {
        $debugDir = is_dir('app') && is_dir('bootstrap') && is_dir('vendor') ? 'storage/debug' : 'debug';

        $debugFile = "$debugDir/$fileName.txt";
        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = null;
        $results = print_r($variable, true);
        !empty($current) ? $current .= "\r\n" . $results : $current .= "\n" . $results;
        file_put_contents($debugFile, $current);
    }
}

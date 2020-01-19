<?php

namespace App\SocketServer\Contracts;

interface LoggerInterface
{
    public function save($table, $field, $value);
}

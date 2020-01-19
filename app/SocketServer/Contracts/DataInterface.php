<?php

namespace App\SocketServer\Contracts;

interface DataInterface
{
    public function select($table, $field);
}

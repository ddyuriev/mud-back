<?php

declare(strict_types = 1);

namespace App\SocketServer;

use App\SocketServer\Contracts\DataInterface;
use App\SocketServer\Contracts\LoggerInterface;

/*
* Сделал небольшую обертку для запросов.
* Здесь так же пишутся в лог события. Обертку можно еще сильно доработать :)
*/

class MySql implements DataInterface,LoggerInterface
{
    private $db;
    private $dbname;
    private $user;
    private $password;

    public function __construct($config)
    {
        try {
            $this->db = new \PDO("mysql:host=$config[host];dbname=$config[dbname]", $config['user'], $config['password']);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->db->exec("set names utf8");
            return $this->connect = true;
        } catch(\PDOException $e) {
            return $this->connect = false;
        }
    }

/*
* Запрос для пинга в базу
*
*/
    public function ping() : string
    {
        if ($this->connect) {
            try {
                $ping = $this->db->query('SELECT 1');
                $ping->setFetchMode(\PDO::FETCH_ASSOC);
                if ($ping->fetch()) {
                    return 'Запрос к Mysql был отправлен (ПИНГ).</br>';
                }
            } catch(\PDOException $e) {
                    return 'Во время пинга Mysql возникла ошибка: '. $e->getMessage().'</br>';
            }
        } else {
            return 'Подключение к Mysql не установлено.';
        }

    }

/*
* Сохранение данных.
*
*/
    public function save($table, $arrayFields, $arrayValues)
    {
        if ($this->connect) {
            $fieldString = implode(',', $arrayFields); // превращаем массив в строку

            array_walk($arrayFields, function (&$arr) {
                $arr = ':'.$arr;
            });

            $valueString = implode(',', $arrayFields);

            try {
                $stmt = $this->db->prepare("INSERT INTO $table ($fieldString) VALUES ($valueString)");
                foreach ($arrayFields as $key => $value1) {
                    $stmt->bindParam($arrayFields[$key], $arrayValues[$key]);
                }
                $stmt->execute();
                return 'Сообщение сохранено в Mysql.</br>';
            } catch(\PDOException $e) {
                return 'Во время записи в Mysql произошла ошибка: '. $e->getMessage().'</br>';
            }
        } else {
            return 'Подключение к Mysql не установлено.';
        }
    }

/*
* Выборка из БД
*
*
*/
    public function select($table, $field, $join = NULL,  $where = NULL, $value = NULL)
    {
        if ($this->connect) {
            try {
                if (is_array($field)) {
                    $field = implode(',', $field); // превращаем массив в строку
                }

                $stmt = "SELECT $field FROM $table ";

                if ($join != NULL) {
                    $stmt.= "LEFT JOIN $join";
                }

                if ($where != NULL and $value != NULL) {
                    $stmt.= "WHERE $where = ?";
                }

                $stmt = $this->db->prepare($stmt);
                $stmt->bindValue(1, $value);

                $stmt->execute();
                $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                return $rows;
            } catch(\PDOException $e) {
                return 'Во время запроса в Mysql произошла ошибка: '. $e->getMessage().'</br>';
            }
        } else {
            return 'Подключение к Mysql не установлено.';
        }
    }
}
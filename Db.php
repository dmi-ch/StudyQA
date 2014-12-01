<?php

namespace studyqa;
use mysqli;

error_reporting(E_ALL);
ini_set("display_errors","On");
    /**
 * @property array  $mysqli
 */
//Подключение к базе данных
class Db{

    protected static $DB_HOST = "localhost";
    protected static $DB_LOGIN = "root";
    protected static $DB_PASSWORD = "root";
    protected static $DB_NAME = "devstudyqa2";
    protected static $DB_PORT=3306;


    public static function init(){

        $mysqli = new mysqli(self::$DB_HOST,self::$DB_LOGIN,self::$DB_PASSWORD,self::$DB_NAME,self::$DB_PORT);

        if ($mysqli->connect_error) {
            die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
        }
        if (mysqli_connect_errno()) {
            return printf("Не удалось подключиться: %s\n", mysqli_connect_error());
        }

        $mysqli->set_charset("utf8");

        return $mysqli;
    }
}



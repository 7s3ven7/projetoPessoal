<?php

namespace code\dataBase;

use PDO;

Class Db
{

    private const string HOST = "127.0.0.1";

    private const string USER = "root";

    private const string PASSWORD = "Desenv#6";

    private const string DATABASE = "crud";

    private mixed $conn;

    protected function __construct()
    {

        $this->conn = new PDO("mysql:host=" . DB::HOST . ";dbname=" . DB::DATABASE, DB::USER, DB::PASSWORD);

    }

    private function setParams($stmt,$params):void
    {

        foreach ($params as $key => $value) {

            $this->bindParam($stmt, $key, $value);

        }

    }

    private function bindParam($stmt, $key, $value):void
    {

            $stmt->bindParam($key, $value, $this->setType($value));

    }

    private function setType($value):string{

        return match ($value) {
            is_int($value) => PDO::PARAM_INT,
            is_bool($value) => PDO::PARAM_BOOL,
            is_null($value) => PDO::PARAM_NULL,
            default => PDO::PARAM_STR,
        };
    }

    protected function query($rawQuery, $params):object
    {

        $stmt = $this->conn->prepare($rawQuery);

        $this->setParams($stmt, $params);

        $stmt->execute();

        return $stmt;

    }

    protected function select($rawQuery, $params):array
    {
        $stmt = $this->query($rawQuery, $params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

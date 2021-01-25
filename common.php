<?php
require_once 'config.php';

function connectionToDB()
{
    try {
       return $connection = new PDO("'msql:host= SERVER_NAME';dbname=DB_NAME", USERNAME, PASSWORD);
    }catch (PDOException $exception) {
        echo 'Connection fail: ' . $exception->getMessage();
    }
}
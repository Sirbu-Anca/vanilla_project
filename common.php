<?php
require_once 'config.php';

function getConnectionToDB()
{
    try {
        $connection = new PDO("mysql:host=".SERVER_NAME.";dbname=".DB_NAME."", USERNAME, PASSWORD);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;

    }catch (PDOException $exception) {
        echo 'Connection failed: ' . $exception->getMessage();
    }
}

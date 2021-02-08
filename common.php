<?php

require_once 'config.php';
session_start();

function getDbConnection()
{
    try {
        $connection = new PDO('mysql:host=' . SERVER_NAME . ';dbname=' . DB_NAME . '', USERNAME, PASSWORD);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;
    } catch (PDOException $exception) {
       return $exception->getMessage();
    }
}

$translation = [];
function translate($label)
{
    return isset($GLOBALS['translation'][$label]) ? $GLOBALS['translation'][$label] : $label;
}

function checkForAuthentication()
{
    if (!isset($_SESSION['username']) && $_SESSION['username'] !== ADMIN_CREDENTIALS['username']) {
        header('Location: login.php');
        die();
    }
}

function imagePath($pathName)
{
    return substr(strstr($pathName, '/'), 1);
}
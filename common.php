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
    return $GLOBALS['translation'][$label] ?? $label;
}

function isAuthenticated()
{
    return (isset($_SESSION['isAuthenticated']) && $_SESSION['isAuthenticated']);
}

function checkForAuthentication()
{
    if (!isAuthenticated()) {
        header('Location: login.php');
        die();
    }
}
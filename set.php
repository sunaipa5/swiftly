<?php

$host = "host";
$user = "username";
$password = "password";
$dbname = "databaseName";

$connect = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
$connect->exec("SET CHARSET UTF8");

date_default_timezone_set('Europe/Istanbul');
?>


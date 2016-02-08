<?php

header("Content-Type: text/plain");

$dbname = '/media/WD_MyBook_1/db/fdb/weather.fdb';

$dbuser = 'WEATHER';

$dbpassword = 'ololo321';


$str_conn="firebird:host=localhost;dbname=$dbname;charset=UTF8";

$dbh = new PDO($str_conn, $dbuser, $dbpassword);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sth = $dbh->prepare('execute procedure NEW_MEASUREMENT(?,?,?)');

$humidity = $_POST['humidity'];
$temperature = $_POST['temperature'];
$uuid = $_POST['uuid'];

$sth->bindParam(3, $humidity);
$sth->bindValue(2, "humidity");
$sth->bindParam(1, $uuid);

$sth->execute();

$sth->bindParam(3, $temperature);
$sth->bindValue(2, "temperature");
$sth->bindParam(1, $uuid);
$sth->execute();

$dbh->commit();

?>

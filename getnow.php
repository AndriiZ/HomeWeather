<?php
$dbname = '/media/WD_MyBook_1/db/fdb/weather.fdb';

$dbuser = 'WEATHER';

$dbpassword = 'ololo321';


$str_conn="firebird:host=localhost;dbname=$dbname;charset=UTF8";

$dbh = new PDO($str_conn, $dbuser, $dbpassword);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$humidity= 0;
$temperature=0;
$humiditytime = null;
$temperaturetime = null;

foreach($dbh->query("select lm.measurementtime, lm.measurementvalue from LAST_MEASUREMENT('AF993B68-0EF7-4842-8A36-8FD03A695456', 'humidity') lm") as $row)
{
   $humidity = $row['MEASUREMENTVALUE'];
   $humiditytime = $row['MEASUREMENTTIME'];
};

foreach($dbh->query("select lm.measurementtime, lm.measurementvalue from LAST_MEASUREMENT('AF993B68-0EF7-4842-8A36-8FD03A695456', 'temperature') lm") as $row)
{
   $temperature = $row['MEASUREMENTVALUE'];
   $temperaturetime = $row['MEASUREMENTTIME'];
};


 $rows = array("temperature" => $temperature, "humidity" => $humidity, "resulttime" => $temperaturetime);

 //ob_start("ob_gzhandler");
 ob_start();
 header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
 header("Cache-Control: post-check=0, pre-check=0", false);
 header("Pragma: no-cache");
 header('Content-type: application/json');
 header('content-disposition: inline;filename=weather.json');
 echo json_encode($rows, JSON_NUMERIC_CHECK);
 header('Content-Length: ' . ob_get_length());
?>


<?php

function getDataFromDB($guid='AF993B68-0EF7-4842-8A36-8FD03A695456')
{
$dbname = 'WEATHER';

$dbuser = 'WEATHER';

$dbpassword = 'ololo321';


$str_conn="firebird:host=localhost;dbname=$dbname;charset=UTF8";

$dbh = new PDO($str_conn, $dbuser, $dbpassword);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$humidity= 0;
$temperature=0;
$humiditytime = null;
$temperaturetime = null;

foreach($dbh->query("select lm.measurementtime, lm.measurementvalue from LAST_MEASUREMENT('$guid', 'humidity') lm") as $row)
{
   $humidity = $row['MEASUREMENTVALUE'];
   $humiditytime = $row['MEASUREMENTTIME'];
};

foreach($dbh->query("select lm.measurementtime, lm.measurementvalue from LAST_MEASUREMENT('$guid', 'temperature') lm") as $row)
{
   $temperature = $row['MEASUREMENTVALUE'];
   $temperaturetime = $row['MEASUREMENTTIME'];
};

 $rows = array("temperature" => $temperature, "humidity" => $humidity, "resulttime" => $temperaturetime);
 return $rows;
}

function getDataFromFile($guid='AF993B68-0EF7-4842-8A36-8FD03A695456')
{
  $filename = "/tmp/$guid.json";
  if (!file_exists($filename)) return NULL;
  $data = json_decode(file_get_contents($filename), TRUE);
  $data["resulttime"] =  date ("F d Y H:i:s", filemtime($filename));
  unset($data["guid"]);
  return $data;
}

?>

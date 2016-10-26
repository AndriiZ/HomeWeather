<?php

  function getMeasurementFromPost($measurement)
  {
     return array_key_exists($measurement, $_POST) ?  $_POST[$measurement] : NULL;
  }

  function IsNullOrEmptyString($str){
      return (!isset($str) || trim($str)==='');
  }

  function writeMeasurementToFile()
  { 
    $guid =  getMeasurementFromPost("uuid");
    $temperature =  floatval(getMeasurementFromPost("temperature"));
    $humidity =  floatval(getMeasurementFromPost("humidity"));
    $pressure =  floatval(getMeasurementFromPost("pressure"));

    if (IsNullOrEmptyString($guid)) return;

    $data = array("temperature" => $temperature, "humidity" => $humidity,
         "guid" => $guid, "pressure" => $pressure);
     file_put_contents("/tmp/".$data["guid"].".json", json_encode($data));
  }

 writeMeasurementToFile();

set_time_limit(10);
header("Content-Type: text/plain");

$dbname = 'WEATHER';

$dbuser = 'WEATHER';

$dbpassword = 'ololo321';


$str_conn="firebird:host=localhost;dbname=$dbname;charset=UTF8";

$dbh = new PDO($str_conn, $dbuser, $dbpassword);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sth = $dbh->prepare('execute procedure NEW_MEASUREMENT(?,?,?)');

$uuid = $_POST['uuid'];

$allowedMeasurements = array('humidity', 'temperature', 'pressure');

foreach($allowedMeasurements as $measurmentType)
{
  if (!array_key_exists($measurmentType, $_POST))
     continue;
  $measurement = $_POST[$measurmentType];
  if (isset($measurement))
  {
     $sth->bindParam(3, $measurement);
     $sth->bindValue(2, $measurmentType);
     $sth->bindParam(1, $uuid);
     $sth->execute();
  }
}

$dbh->commit();

?>

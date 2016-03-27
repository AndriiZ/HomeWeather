<?php

header("Content-Type: text/plain");

$dbname = '/media/WD_MyBook_1/db/fdb/weather.fdb';

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

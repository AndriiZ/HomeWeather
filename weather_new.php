<?php
  function getDbHandle()
  {
    $dbname = '/media/WD_MyBook_1/db/fdb/weather.fdb';
    $dbuser = 'WEATHER';
    $dbpassword = 'ololo321';
    $host = gethostbyname('localhost');
    $str_conn="firebird:host=$host;dbname=$dbname;charset=UTF8";

    $dbh = new PDO($str_conn, $dbuser, $dbpassword);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $dbh;
  }

  function getStmtHandle($dbh)
  {
    $sql = "select MEASUREMENT_TIMEUNIX as t,  measure as m from listofmeasurementsforsensor(:sensorguid, :measurement, :start)";
    $command = $dbh->prepare($sql);
     return $command;
  }

  function readMeasurement($command, $sensorGuid, $measurement, $mode) {
    $command->bindParam(':sensorguid', $sensorGuid, PDO::PARAM_STR);
    $command->bindParam(':measurement', $measurement, PDO::PARAM_STR);
    $command->bindParam(':start', $mode, PDO::PARAM_STR);
    $command->execute();
    return $command->fetchAll(PDO::FETCH_NUM);
 }

  $mode = $_GET['mode'];
  $measurements = array('temperature', 'humidity');
  $stmt = getStmtHandle(getDbHandle());

  $rows = array("mode" => $mode, "measurements" => $measurements);

  foreach($measurements as  $measurement)
  {
    $data = readMeasurement($stmt, 'AF993B68-0EF7-4842-8A36-8FD03A695456', $measurement, $mode);
    $rows[$measurement] = $data;
    $rows[$measurement."_count"] = count($data);
  }

  //ob_start("ob_gzhandler");
  ob_start();
  header('Content-type: application/json');
  header('content-disposition: inline;filename=weather.json');
  echo json_encode($rows, JSON_NUMERIC_CHECK);
  header('Content-Length: ' . ob_get_length());
?>

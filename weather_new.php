<?php
  function getDbHandle()
  {
    $dbname = 'WEATHER';
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

  $measurements = array('AF993B68-0EF7-4842-8A36-8FD03A695456' => array('temperature', 'humidity'),
			'70528E8C-3467-48CD-BDD4-61D509839397' => array('temperature', 'pressure'),
			'1A57F3B1-252F-4332-872D-C23DA809F287' => array('temperature', 'humidity'));

  $sensors = array('AF993B68-0EF7-4842-8A36-8FD03A695456' => 'mainroom',
                        '70528E8C-3467-48CD-BDD4-61D509839397' => 'lodzhiabmp180',
                        '1A57F3B1-252F-4332-872D-C23DA809F287' => 'lodzhiadht22');


  $stmt = getStmtHandle(getDbHandle());

  $rows = array("mode" => $mode, "sensors" => array_values($sensors));

  foreach($measurements as $key=>$value)
  {
    $uuid = $key;
    $sensor = $sensors[$key];
    foreach($value as $measurement)
    {
      $data = readMeasurement($stmt, $key, $measurement, $mode);
      $rows[$sensor."_".$measurement] = $data;
      $rows[$sensor."_".$measurement."_count"] = count($data);
    }
  }

  //ob_start("ob_gzhandler");
  ob_start();
  header('Content-type: application/json');
  header('content-disposition: inline;filename=weather.json');
  echo json_encode($rows, JSON_NUMERIC_CHECK);
  header('Content-Length: ' . ob_get_length());
?>

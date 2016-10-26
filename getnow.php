<?php

include 'data.php';

$rows = getDataFromFile(); 
if ($rows === NULL)
  $rows = getDataFromDB();

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


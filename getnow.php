<?php

include 'data.php';

function loadWeatherData($guid = 'AF993B68-0EF7-4842-8A36-8FD03A695456')
{
   $rowsMain = getDataFromFile($guid); 
   if ($rowsMain === NULL) {
     $rowsMain = getDataFromDB($guid);
   }
   return $rowsMain;
}

$result = array("main" => loadWeatherData(),
		"loggiadht22" => loadWeatherData('1A57F3B1-252F-4332-872D-C23DA809F287'),
		"loggiabmp180" => loadWeatherData('70528E8C-3467-48CD-BDD4-61D509839397')
);

 //ob_start("ob_gzhandler");
 ob_start();
 header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
 header("Cache-Control: post-check=0, pre-check=0", false);
 header("Pragma: no-cache");
 header('Content-type: application/json');
 header('content-disposition: inline;filename=weather.json');
 echo json_encode($result, JSON_NUMERIC_CHECK);
 header('Content-Length: ' . ob_get_length());
?>


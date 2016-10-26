<html>
<head>
<title>Погода в кімнаті</title>
<meta http-equiv="refresh" content="60">
</head>
<body>
<?php

include 'data.php';

$rows = getDataFromFile();
if ($rows === NULL)
  $rows = getDataFromDB();

$humidity = $rows["humidity"];
$temperature = $rows["temperature"];
$time = $rows["resulttime"];

echo "<div style='font:147px/148px helveticaneuecyr-bold,Tahoma,Verdana,sans-serif'>";
echo "<strong>Вологість: </strong>$humidity%";
echo "<br />";
echo "<strong>Температура: </strong> $temperature";
echo "</div>";
echo "Дані на $time";


?>

<div><a href="/weather/">Графік</a></div>
</body>
</html>

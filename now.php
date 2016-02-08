<html>
<head>
<title>Погода в кімнаті</title>
<meta http-equiv="refresh" content="60">
</head>
<body>
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




echo "<div style='font:147px/148px helveticaneuecyr-bold,Tahoma,Verdana,sans-serif'>";
echo "<strong>Вологість: </strong>$humidity%";
echo "<br />";
echo "<strong>Температура: </strong> $temperature";
echo "</div>";
echo "Дані на $humiditytime";


?>
</body>
</html>

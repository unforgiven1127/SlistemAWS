<?php

//$sDate = date('Y-m-d H:i:s');
//echo $sDate;

$url = 'http://www.eltcalendar.com/rss.xml';
$sxml = simplexml_load_file($url);
//var_dump($sxml);

foreach ($sxml as $key => $value)
{
	echo $value;
	echo "<br><br>";
}
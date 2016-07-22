<?php

//$sDate = date('Y-m-d H:i:s');
//echo $sDate;

$url = 'http://www.eltcalendar.com/rss.xml';

 $xmlstr = file_get_contents($url);
 $xmlcont = new SimpleXMLElement($xmlstr);

var_dump($xmlcont);

foreach ($xmlcont as $key => $value)
{
	echo $value;
	echo "<br><br>";
}
<?php

//$sDate = date('Y-m-d H:i:s');
//echo $sDate;

$url = 'http://www.eltcalendar.com/rss.xml';

 $xmlstr = file_get_contents($url);
 $xmlcont = new SimpleXMLElement($xmlstr);

 $turn = true;
 $i = 0;

 while($turn)
 {
 	if(isset($xmlcont->channel->item[$i]))
 	{
 		$holiday = $xmlcont->channel->item[$i];
 		$holidayName = $holiday->title;
 		$holidayDescription = $holiday->description;
 		$holidayDate = $holiday->pubDate;

 		echo "Holiday: ".$holidayName."<br>";
 		echo "Holiday Description: ".$holidayDescription."<br>";
 		echo "Holiday Date: ".$holidayDate."<br>";
 		echo "<br><br>";
 		$i++;
 	}
 	else
 	{
 		$turn = false;
 	}
 }


/*
echo "<br><br>";
echo "<br><br>TEST";
echo "<br><br>";
echo "<br><br>";

var_dump($xmlcont);

echo "<br><br>";
echo "<br><br>TEST";
echo "<br><br>";
echo "<br><br>";

foreach ($xmlcont as $key => $value)
{
	echo $value;
	echo "<br><br>";
}*/
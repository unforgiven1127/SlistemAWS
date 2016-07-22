<?php

//$sDate = date('Y-m-d H:i:s');
//echo $sDate;

	define('DB_NAME_SLISTEM','slistem');
    define('DB_SERVER_SLISTEM', '127.0.0.1');
    define('DB_USER_SLISTEM', 'slistem');
    define('DB_PASSWORD_SLISTEM', 'smwXN2RTDm6Zz3hR');

	mysql_connect( DB_SERVER_SLISTEM, DB_USER_SLISTEM, DB_PASSWORD_SLISTEM) or die(mysql_error());
    mysql_select_db(DB_NAME_SLISTEM) or die(mysql_error());

    $slistemQuery = "SELECT h.* FROM holidays h where h.flag = 'a' ORDER BY h.holiday_date ASC";

    $slistemQuery = mysql_query($slistemQuery);

    while($data = mysql_fetch_assoc($slistemQuery))
    {
    	echo $data['holiday_date']." ".$data['holiday_day']." ".$data['holiday_name']." ".$data['holiday_type'];
		echo "<br><br>";
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
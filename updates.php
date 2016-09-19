<?php

//$sDate = date('Y-m-d H:i:s');
//echo $sDate;

	define('DB_NAME_SLISTEM','slistem');
    define('DB_SERVER_SLISTEM', '127.0.0.1');
    define('DB_USER_SLISTEM', 'slistem');
    define('DB_PASSWORD_SLISTEM', 'smwXN2RTDm6Zz3hR');

	mysql_connect( DB_SERVER_SLISTEM, DB_USER_SLISTEM, DB_PASSWORD_SLISTEM) or die(mysql_error());
    mysql_select_db(DB_NAME_SLISTEM) or die(mysql_error());

    $slistemQuery = "SELECT l.* FROM client_owners l";

    $slistemQuery = mysql_query($slistemQuery);
    $owners = array();

    while($data = mysql_fetch_assoc($slistemQuery))
    {
        $company_id = $data['company_id'];
        $owner = $data['user_id'];
        if(!isset($owners[$company_id][$owner]))
        {
            $owners[$company_id][$owner] = 1;
        }
    }
    ChromePhp::log($owners);

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
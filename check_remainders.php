<?php

define('DB_NAME_SLISTEM','slistem');
define('DB_SERVER_SLISTEM', '127.0.0.1');
define('DB_USER_SLISTEM', 'slistem');
define('DB_PASSWORD_SLISTEM', 'smwXN2RTDm6Zz3hR');

mysql_connect( DB_SERVER_SLISTEM, DB_USER_SLISTEM, DB_PASSWORD_SLISTEM) or die(mysql_error());
mysql_select_db(DB_NAME_SLISTEM) or die(mysql_error());
$sDate = date('Y-m-d H:i:s');
$slistemQuery = "SELECT * FROM notification n where n.flag = 'a'";

$slistemQuery = mysql_query($slistemQuery);

while($data = mysql_fetch_assoc($slistemQuery))
{
    $message = $data['message'];
    $mailTos = $data['mailto'];
    $mailTos = explode(',',$mailTos);

    foreach ($variable as $key => $value)
    {
    	# code...
    }
}


?>
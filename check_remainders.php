<?php

define('DB_NAME_SLISTEM','slistem');
define('DB_SERVER_SLISTEM', '127.0.0.1');
define('DB_USER_SLISTEM', 'slistem');
define('DB_PASSWORD_SLISTEM', 'smwXN2RTDm6Zz3hR');

mysql_connect( DB_SERVER_SLISTEM, DB_USER_SLISTEM, DB_PASSWORD_SLISTEM) or die(mysql_error());
mysql_select_db(DB_NAME_SLISTEM) or die(mysql_error());
$sDate = date('Y-m-d H:i:s');
$slistemQuery = "SELECT * FROM notification n where n.flag = 'a' ";

$slistemQuery = mysql_query($slistemQuery);

while($data = mysql_fetch_assoc($slistemQuery))
{
    $message = $data['message'];
    $mailTos = $data['mailTo'];
    $mailTos = explode(',',$mailTos);
    $notificatinpk = $data['notificationpk'];

    foreach ($mailTos as $key => $mailTo)
    {
    	$sDate = date('Y-m-d H:i:s');
		$to = $mailTo;
		$subject = "Sl[i]stem daily reminders";

		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headers .= 'From: <slistem@slate.co.jp>' . "\r\n";
		//$headers .= 'Cc: munir_anameric@hotmail.com' . "\r\n";

		mail($to,$subject,$message,$headers);
    }

    $updateQuery = "update notification set flag = 'p' where notificationpk = '".$notificatinpk."' ";
    $updateQuery = mysql_query($updateQuery);
    $updateData = mysql_fetch_assoc($updateQuery);
}


?>
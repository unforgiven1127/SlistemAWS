<?php

    require_once('component/jobboard/jobboard.class.php5');
    require_once('component/taaggregator/resources/lib/encoding_converter.class.php5');

    define('DB_NAME_SLISTEM','slistem');
    define('DB_SERVER_SLISTEM', '127.0.0.1');
    define('DB_USER_SLISTEM', 'slistem');
    define('DB_PASSWORD_SLISTEM', 'smwXN2RTDm6Zz3hR');

    //mysql_connect( DB_SERVER_SLISTEM, DB_USER_SLISTEM, DB_PASSWORD_SLISTEM) or die(mysql_error());
    //mysql_select_db(DB_NAME_SLISTEM) or die(mysql_error());


    $slistemQuery = " SELECT * FROM login l WHERE l.status = '1' ";
    $slistemQuery = mysql_query($slistemQuery);

    while($userData = mysql_fetch_assoc($slistemQuery))
    {
        $pass = $userData['password'];
        $user_id = $userData['loginpk'];
        $pass_encrypted = sha1($pass);

        echo $user_id." - ".$pass_encrypted;
        echo "<br><br>";
    }


    /*JOBBOARD ISLEMLERI ICIN*/



    /*SLISTEM ISLEMLERI ICIN*/
    //mysql_connect( DB_SERVER_SLISTEM, DB_USER_SLISTEM, DB_PASSWORD_SLISTEM) or die(mysql_error());
    //mysql_select_db(DB_NAME_SLISTEM) or die(mysql_error());
    /*SLISTEM ISLEMLERI ICIN*/

	/*mysql_connect( DB_SERVER_SLISTEM, DB_USER_SLISTEM, DB_PASSWORD_SLISTEM) or die(mysql_error());
    mysql_select_db(DB_NAME_SLISTEM) or die(mysql_error());

	foreach ($array as $key => $value)
	{
		$id = TRIM($key);
		$jpTitle = TRIM($value);

		echo $id.$jpTitle."<br><br>";

	    $slistemQuery = " UPDATE sl_location SET location_jp = '".$jpTitle."' WHERE sl_locationpk ='".$id."'";

    	$slistemQuery = mysql_query($slistemQuery);

	}*/


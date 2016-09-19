<?php

//$sDate = date('Y-m-d H:i:s');
//echo $sDate;

	define('DB_NAME_SLISTEM','slistem');
    define('DB_SERVER_SLISTEM', '127.0.0.1');
    define('DB_USER_SLISTEM', 'slistem');
    define('DB_PASSWORD_SLISTEM', 'smwXN2RTDm6Zz3hR');

	mysql_connect( DB_SERVER_SLISTEM, DB_USER_SLISTEM, DB_PASSWORD_SLISTEM) or die(mysql_error());
    mysql_select_db(DB_NAME_SLISTEM) or die(mysql_error());
    $sDate = date('Y-m-d H:i:s');
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
    //var_dump($owners);
    foreach ($owners as $companyKey => $userArray)
    {
        foreach ($userArray as $key => $value)
        {
          $company_id = $companyKey;
          $first_activity = $sDate;
          $last_activity = $sDate ;
          $user_id = $value;

          $sQueryInsert = "INSERT INTO `client_owner_list` (`user_id`,`company_id`, `first_activity`, `last_activity`)
                   VALUES('".$user_id."','".$company_id."','".$first_activity."','".$last_activity."')";

          var_dump($sQueryInsert);
          echo "<br><br>";

          //$sQueryInsert = mysql_query($sQueryInsert);
          //$data =mysql_fetch_assoc($sQueryInsert);
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
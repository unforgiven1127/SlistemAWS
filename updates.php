<?php

$filename = 'churc.sql';
// MySQL host
$mysql_host = 'localhost';
// MySQL username
$mysql_username = 'nopasss';
// MySQL password
$mysql_password = 'smwXN2RTDm6Zz3hR';
// Database name
$mysql_database = 'slistem';

// Connect to MySQL server
mysql_connect($mysql_host, $mysql_username, $mysql_password) or die('Error connecting to MySQL server: ' . mysql_error());
// Select database
mysql_select_db($mysql_database) or die('Error selecting MySQL database: ' . mysql_error());

// Temporary variable, used to store current query
$templine = '';
// Read in entire file
$lines = file($filename);
// Loop through each line
foreach ($lines as $line)
{
// Skip it if it's a comment
if (substr($line, 0, 2) == '--' || $line == '')
    continue;

// Add this line to the current segment
$templine .= $line;
// If it has a semicolon at the end, it's the end of the query
if (substr(trim($line), -1, 1) == ';')
{
    // Perform the query
    mysql_query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
    // Reset temp variable to empty
    $templine = '';
}
}
 echo "Tables imported successfully";

//$sDate = date('Y-m-d H:i:s');
//echo $sDate;

/*$gelen = $_SERVER['HTTP_USER_AGENT'];

if (strpos($gelen, 'Firefox') !== false)
{
    echo 'Firefox';
}
else
{
  echo 'Chrome vs';
}*/
/*$sDate = date('Y-m-d H:i:s');
$to = "munir@slate-ghc.com";
$subject = "Test email";

$message = "
<html>
<head>Date&Time:
".$sDate."
<title>Test email</title>
</head>
<body>
<p>TEST FOR CRONJOB</p>

</body>
</html>
";

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <slistem@slate.co.jp>' . "\r\n";
$headers .= 'Cc: munir_anameric@hotmail.com' . "\r\n";
//print_r(PDO::getAvailableDrivers());
mail($to,$subject,$message,$headers);

	/*define('DB_NAME_SLISTEM','slistem');
    define('DB_SERVER_SLISTEM', '127.0.0.1');
    define('DB_USER_SLISTEM', 'slistem');
    define('DB_PASSWORD_SLISTEM', 'smwXN2RTDm6Zz3hR');

	mysql_connect( DB_SERVER_SLISTEM, DB_USER_SLISTEM, DB_PASSWORD_SLISTEM) or die(mysql_error());
    mysql_select_db(DB_NAME_SLISTEM) or die(mysql_error());
    $sDate = date('Y-m-d H:i:s');
    $slistemQuery = "SELECT slc.sl_companypk as companyID, slc.name as companyName,slpl.positionfk, slpd.title as positionTitle, slpl.status, count(slpl.status) as actionCount
      FROM sl_position_link slpl
      INNER JOIN sl_position_detail slpd on slpd.positionfk = slpl.positionfk
      INNER JOIN sl_position slp on slp.sl_positionpk = slpd.positionfk
      INNER JOIN sl_company slc on slc.sl_companypk = slp.companyfk

      WHERE slpl.date_created >= '2015-03-27 00:00:00'
      GROUP BY slpl.positionfk, slpl.status
      ORDER BY slc.name ASC";

    $slistemQuery = mysql_query($slistemQuery);
    $activities = Array();

    while($data = mysql_fetch_assoc($slistemQuery))
    {
        $statID = $data['status'];
        $statTitle = getStatusTitle($statID);
        echo $data['companyID'].';'.$data['companyName'].';'.$data['positionfk'].';'.$data['positionTitle'].';'.$statTitle.';'.$data['actionCount'].'<br>';
    }


  function getStatusTitle($status_id)
  {
    if($status_id == '1')
    {
      return "Pitch";
    }
    else if($status_id == '2')
    {
      return "Resume sent";
    }
    else if($status_id == '51')
    {
      return "CCM1";
    }
    else if($status_id == '52')
    {
      return "CCM2";
    }
    /*else if($status_id == '53')
    {
      return "CCM3";
    }
    else if($status_id == '54')
    {
      return "CCM4";
    }*/
    /*else if($status_id > '52' && $status_id <= '70')
    {
      return "MCCM";
    }
    else if($status_id == '100')
    {
      return "Offer";
    }
    else if($status_id == '101')
    {
      return "Placed";
    }
    else if($status_id == '150' || $status_id == '151')
    {
      return "Expired";
    }
    else if($status_id == '200')
    {
      return "Fallen off";
    }
    else if($status_id == '201')
    {
      return "Not Interested";
    }
    else
    {
      return "-";
    }

  }*/

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
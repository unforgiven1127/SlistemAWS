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
    else if($status_id > '52' && $status_id <= '70')
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
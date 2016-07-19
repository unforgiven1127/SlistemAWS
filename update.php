<?php

    require_once('component/jobboard/jobboard.class.php5');
    require_once('component/taaggregator/resources/lib/encoding_converter.class.php5');

    define('DB_NAME_SLISTEM','slistem');
    define('DB_SERVER_SLISTEM', '127.0.0.1');
    define('DB_USER_SLISTEM', 'slistem');
    define('DB_PASSWORD_SLISTEM', 'smwXN2RTDm6Zz3hR');

    mysql_connect( DB_SERVER_SLISTEM, DB_USER_SLISTEM, DB_PASSWORD_SLISTEM) or die(mysql_error());
    mysql_select_db(DB_NAME_SLISTEM) or die(mysql_error());


    $slistemQuery = " SELECT * FROM sl_meeting ";
    $slistemQuery = mysql_query($slistemQuery);

    $allMeetings = array();

    $count = 0;

    while($meetingData = mysql_fetch_assoc($slistemQuery))
    {
        array_push($allMeetings,$meetingData);
    }

    foreach ($allMeetings as $key => $meeting)
    {
        $create_date = $meeting['date_meeting'];
        $month = date("m",strtotime($create_date));
        $year = date("Y",strtotime($create_date));

        $effectiveDate = date('Y-m-d', strtotime("+1 month", strtotime($create_date)));

        $new_month = date("m",strtotime($effectiveDate));
        $control_date = $year.'-'.$new_month.'-'.'06 00:00:00';

        $today = date("Y-m-d H:i:s");

        if($meeting['meeting_done'] == 0  && $meeting['date_updated'] == NULL && strtotime($today) >= strtotime($control_date ) )
        {
            //echo "Meeting ID: ".$meeting['sl_meetingpk']." - SHOULD BE CANCELLED !! - Today: ".$today." ControlDate: ".$control_date."<br>";
            $meeting_id = $meeting['sl_meetingpk'];
            //$slistemQueryUpdate = "UPDATE sl_meeting SET meeting_done = '-1', date_updated = '".$today."' WHERE sl_meetingpk = '".$meeting_id."' ";
            //$slistemQueryUpdate = mysql_query($slistemQueryUpdate);
            $count++;
        }
    }

    echo "<br><br>Updated: ".$count;
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


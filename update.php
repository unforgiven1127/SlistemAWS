<?php

	//require_once('component/jobboard/jobboard.class.php5');
	//require_once('component/taaggregator/resources/lib/encoding_converter.class.php5');

	//echo "Updates<br><br>";
	//
$arrayMulti = array(array());
$arrayMulti[0]( '	03-07-2012 17:39	','	354	','	279387	','	354	','	1	','	03-07-2012 17:39	','	Assessed in person	','	1	','	03-07-2012 17:39	');
$arrayMulti[1]( '	01-02-2010 19:46	','	354	','	272772	','	354	','	1	','	01-02-2010 19:46	','	Assessed in person	','	1	','	01-02-2010 19:46	');
$arrayMulti[2]( '	26-09-2008 13:03	','	354	','	262416	','	354	','	1	','	26-09-2008 13:03	','	Assessed in person	','	1	','	26-09-2008 13:03	');
$arrayMulti[3]( '	16-09-2008 20:26	','	354	','	259143	','	354	','	1	','	16-09-2008 20:26	','	Assessed in person	','	1	','	16-09-2008 20:26	');
$arrayMulti[4]( '	11-09-2008 12:22	','	363	','	257856	','	363	','	1	','	11-09-2008 12:22	','	Assessed in person	','	1	','	11-09-2008 12:22	');
$arrayMulti[5]( '	18-09-2008 17:23	','	333	','	257422	','	333	','	1	','	18-09-2008 17:23	','	Assessed in person	','	1	','	18-09-2008 17:23	');
$arrayMulti[6]( '	24-09-2008 12:25	','	270	','	254697	','	270	','	1	','	24-09-2008 12:25	','	Assessed in person	','	1	','	24-09-2008 12:25	');
$arrayMulti[7]( '	28-02-2013 17:27	','	155	','	252792	','	360	','	1	','	28-02-2013 17:27	','	Assessed in person	','	1	','	28-02-2013 17:27	');

foreach ($arrayMulti as $key => $value) {
	var_dump($value);
}

/*foreach ($arrayMulti as $key => $array)
{
	foreach ($array as $key => $value)
	{
		$array[$key] = TRIM($value);
		echo $array[$key]." - ";
	}
	echo "<br><br>";
}*/

	/*JOBBOARD CONNECTION INFO*/
	define('DB_NAME', 'jobboard');
    define('DB_SERVER', '127.0.0.1');
    define('DB_USER', 'jobboard');
    define('DB_PASSWORD', 'KCd7C56XJ8Nud7uF');
    /*JOBBOARD CONNECTION INFO*/

    /*SLISTEM CONNECTION INFO*/
	define('DB_NAME_SLISTEM','slistem');
    define('DB_SERVER_SLISTEM', '127.0.0.1');
    define('DB_USER_SLISTEM', 'slistem');
    define('DB_PASSWORD_SLISTEM', 'smwXN2RTDm6Zz3hR');
    /*SLISTEM CONNECTION INFO*/

    /*JOBBOARD ISLEMLERI ICIN*/
    mysql_connect( DB_SERVER_SLISTEM, DB_USER_SLISTEM, DB_PASSWORD_SLISTEM) or die(mysql_error());
    mysql_select_db(DB_NAME_SLISTEM) or die(mysql_error());


    /*foreach ($array as $key => $value)
    {
    	$slistemQuery = "";

    	$positionData = mysql_query($slistemQuery);

    }*/


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


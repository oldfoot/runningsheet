<?php
	define( '_VALID_DIR_', 1 );
require "../config.php";
require $dr."classes/jqplot_odometer.php";

$eventid = 0;
if (ISSET($_GET['eventid']) && IS_NUMERIC($_GET['eventid'])) {
	$eventid = $_GET['eventid'];
}

$obj = new JQPlotOdometer;
//$obj->SetVar("debug",true);
$obj->SetVar("eventid",$eventid);
echo $obj->Get();
//echo $obj->ShowErrors();
?>
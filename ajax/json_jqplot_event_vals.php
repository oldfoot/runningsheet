<?php
define( '_VALID_DIR_', 1 );

require "../config.php";
require $dr."functions/ArrayToJson.php";

$userid = $_SESSION['userid'];

$eventid = 0;
if (ISSET($_GET['eventid']) && IS_NUMERIC($_GET['eventid'])) {
	$eventid = $_GET['eventid'];
}
// 1. GET RESOURCES
$sql = "CALL sp_graph_task_progress($eventid,".$_SESSION['userid'].");";
//echo $sql."<br />";
$result = $db->Query($sql);
$dataline1 = array();
$dataline2 = array();
$data = array();

if ($db->NumRows($result) == 0) {
	$dataline1 = ""; // SET TO EMPTY STRING
	$dataline2 = ""; // SET TO EMPTY STRING
}
$count = 0;
$line1_total = 0;
$line2_total = 0;
while ($row = $db->FetchArray($result)) {
	$count++;
	$key = $row['legend'];
	//$key = "1";
	$line1_total += $row['line1'];
	$line2_total += $row['line2'];
		
	$dataline1[] = array($key,$line1_total);
	$dataline2[] = array($key,$line2_total);
	
}
// JQPLOT ONLY HANDLES ONE LINE AT A TIME
if (ISSET($_GET['q']) && $_GET['q'] == "1") {
	$data = $dataline1;
}
if (ISSET($_GET['q']) && $_GET['q'] == "2") {
	$data = $dataline2;
}

echo json_encode($data);
?>
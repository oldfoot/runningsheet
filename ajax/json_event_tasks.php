<?php
define( '_VALID_DIR_', 1 );

require "../config.php";
require $dr."functions/ArrayToJson.php";

$userid = $_SESSION['userid'];
if ( ISSET($_GET['term'])) {
	$q = htmlentities($_GET['term']);
}
else {
	$q = "";
}

$eventid = 0;
if (ISSET($_GET['eventid']) && IS_NUMERIC($_GET['eventid'])) {
	$eventid = $_GET['eventid'];
}
//file_put_contents("json_event_tasks.log","File queried: $eventid and search term $q \n",FILE_APPEND); // TO DEBUG
$sql = "SELECT TaskName
		FROM taskmaster
		WHERE EventID = $eventid";
$result = $db->Query($sql);

$items = array();

if ($db->NumRows($result) == 0) {
	$items["No Data"] = "No tasks available";
}
else {		
	while ($row = $db->FetchArray($result)) {		
		$TaskName = $row['TaskName'];
		$TaskName = $row['TaskName'];		
		$items[$TaskName] = $TaskName;
	}
}

$result = array();
foreach ($items as $key=>$value) {
	//if (strpos(strtolower($value), $q) !== false) {
		array_push($result, array("id"=>$value, "label"=>$key, "value" => strip_tags($value)));
	//}
	//if (count($result) > 11)
		//break;
}
echo array_to_json($result);
?>
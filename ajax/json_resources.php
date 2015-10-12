<?php
define( '_VALID_DIR_', 1 );

require "../config.php";
require $dr."functions/ArrayToJson.php";

$eventid=0;
if (ISSET($_GET['eventid']) && IS_NUMERIC($_GET['eventid'])) {
	$eventid = $_GET['eventid'];	
}

$sql = "CALL sp_browse_eventusers($eventid,".$_SESSION['userid'].");";
//echo $sql;
$result = $db->Query($sql);

$items = array();

if ($db->NumRows($result) == 0) {
	$items["No Data"] = "No data available";
}
else {		
	while ($row = $db->FetchArray($result)) {		
		$id = $row['UserLogin'];
		$data = $row['UserLogin'];		
		$items[$data] = $id;
	}
}

$result = array();
foreach ($items as $key=>$value) {
	//if (strpos(strtolower($value), $q) !== false) {
		array_push($result, array("id"=>$value, "value"=>$key));
	//}
	//if (count($result) > 11)
		//break;
}
//echo "{\"Groups\" : ";
echo array_to_json($result);
//echo "}";
?>
<?php
define( '_VALID_DIR_', 1 );

require "../config.php";
require $dr."functions/ArrayToJson.php";

$userid = $_SESSION['userid'];
if ( ISSET($_GET['term'])) {
	$q = htmlentities($_GET['term']);
}
else {
	$q = "e";
}
$eventid = 0;
if (ISSET($_GET['eventid']) && IS_NUMERIC($_GET['eventid'])) {
	$eventid = $_GET['eventid'];
}

$sql = "CALL sp_browse_eventusers($eventid,$userid);";
$result = $db->Query($sql);

$items = array();

if ($db->NumRows($result) == 0) {
	$items["No Data"] = "No users available";
}
else {		
	while ($row = $db->FetchArray($result)) {		
		$userlogin = $row['UserLogin'];
		$fullname = $row['FullName'];		
		$items[$fullname] = $userlogin;
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
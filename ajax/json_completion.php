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


$sql = "SELECT Name
		FROM task_completion_master";
$result = $db->Query($sql);

$items = array();

if ($db->NumRows($result) == 0) {
	$items["No Data"] = "No tasks available";
}
else {		
	while ($row = $db->FetchArray($result)) {		
		$Name = $row['Name'];
		$Name = $row['Name'];		
		$items[$Name] = $Name;
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
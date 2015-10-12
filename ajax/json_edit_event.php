<?php
define( '_VALID_DIR_', 1 );

require "../config.php";
require $dr."functions/ArrayToJson.php";

$userid = $_SESSION['userid'];

$eventid = 0;
if (ISSET($_GET['eventid']) && IS_NUMERIC($_GET['eventid'])) {
	$eventid = $_GET['eventid'];
}
file_put_contents("json_edit_event.log",$_SERVER['QUERY_STRING']."\n",FILE_APPEND);
$sql = "CALL sp_event_browse_id($eventid,".$_SESSION['userid'].");";
file_put_contents("json_edit_event.log",$sql."\n",FILE_APPEND);
$result = $db->Query($sql);

$data = array();

if ($db->NumRows($result) == 0) {
	$data["No Data"] = "No data available";
	
}
else {
	
	while ($row = $db->FetchArray($result)) {		
		$data[] = array(
			'id'=>$row['EventID'],
			'eventname'=>$row['EventName'],
			'datetimestart'=>$row['DateTimeStart'],
			'datetimeend'=>$row['DateTimeEnd'],
			'locked'=>$row['Locked']
			);
	}
}
//print_r($data);
echo json_encode($data);
?>
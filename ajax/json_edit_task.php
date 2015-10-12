<?php
define( '_VALID_DIR_', 1 );

require "../config.php";
require $dr."functions/ArrayToJson.php";

$userid = $_SESSION['userid'];

$taskid = 0;
if (ISSET($_GET['taskid']) && IS_NUMERIC($_GET['taskid'])) {
	$taskid = $_GET['taskid'];
}
// 1. GET RESOURCES
$sql = "CALL sp_task_browse_resources($taskid,".$_SESSION['userid'].");";
//echo $sql."<br />";
$result = $db->Query($sql);
$data_resources = array();
if ($db->NumRows($result) == 0) {
	$data_resources = ""; // SET TO EMPTY STRING
}
while ($row = $db->FetchArray($result)) {
	$data_resources[] = $row['UserLogin'];
}
// 2. GET RESOURCES
$sql = "CALL sp_task_browse_dependencies($taskid,".$_SESSION['userid'].");";
//echo $sql."<br />";
$result = $db->Query($sql);
$data_dependencies = array();
if ($db->NumRows($result) == 0) {
	$data_dependencies = ""; // SET TO EMPTY STRING
}
while ($row = $db->FetchArray($result)) {
	$data_dependencies[] = $row['TaskName'];
}
// 3. GET ONCOMPLETE
$sql = "CALL sp_task_browse_completion($taskid);";
//echo $sql."<br />";
$result = $db->Query($sql);
$data_inprogress = array();
$data_complete = array();
$data_issues = array();
if ($db->NumRows($result) == 0) {	
	$data_inprogress[] = "No data available";	
	$data_complete[] = "No data available";
	$data_issues[] = "No data available";	
}
while ($row = $db->FetchArray($result)) {
	if ($row['Status'] == "inprogress") {		
		$data_inprogress[] = $row['CompletionID'];
	}
	if ($row['Status'] == "complete") {
		$data_complete[] = $row['CompletionID'];
	}
	if ($row['Status'] == "issues") {
		$data_issues[] = $row['CompletionID'];
	}
}
// 4. GET TASK MASTER DATA
$sql = "CALL sp_task_browse_id($taskid,".$_SESSION['userid'].");";
//echo $sql;
$result = $db->Query($sql);

$data = array();
if ($db->NumRows($result) == 0) {
	$data["No Data"] = "No data available";
}
else {	
	while ($row = $db->FetchArray($result)) {		
		$data[] = array(
			'taskid'=>$row['TaskID'],
			'taskname'=>$row['TaskName'],
			'description'=>$row['Description'],
			'datetimereqstart'=>$row['DateTimeReqStart'],
			'datetimereqend'=>$row['DateTimeReqEnd'],
			'resources'=>$data_resources,
			'dependencies'=>$data_dependencies,
			'inprogress'=>$data_inprogress,
			'complete'=>$data_complete,
			'issues'=>$data_issues
			);
	}
}
//print_r($data);
echo json_encode($data);
?>
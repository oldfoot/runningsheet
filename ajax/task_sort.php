<?php
define( '_VALID_DIR_', 1 );

require "../config.php";
require_once $dr."classes/taskmaster.php";

$eventid = 0;
if (ISSET($_GET['eventid']) && IS_NUMERIC($_GET['eventid'])) {
	$eventid = $_GET['eventid'];
}

$sql = "CALL sp_task_browse(".$eventid.",".$_SESSION['userid'].")";
//echo $sql;
$result = $GLOBALS['db']->Query($sql);
$taskids = array();
if ($result) {
	$count=1;
	while ($row = $GLOBALS['db']->FetchArray($result)) {
		$taskids[$count] = $row['TaskID'];
		$count++;
	}
}
foreach ($_GET['listItem'] as $position => $item) {
	
	//$sql = "UPDATE `table` SET `position` = $position WHERE `id` = $item";
	//echo $sql;
	//file_put_contents("task_sort.log",$sql."\n",FILE_APPEND);
	$pos = $position+1;
	$taskid = $taskids[$item];
	$obj_task = new TaskMaster;
	$obj_task->SetVar("taskid",$taskid);
	$obj_task->SetVar("sortorder",$pos);
	$result = $obj_task->ChangeSortOrder();
	if (!$result) {
		$obj_task->ShowErrors();
	}
}
?>
Sorting Complete
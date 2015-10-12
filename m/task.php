<?php
define( '_VALID_DIR_', 1 );

require "config.php";
require "header.php";

$menu = array(""=>"All Tasks","My Tasks","Completed Tasks","Pending Tasks","Tasks with Issues","Tracking");

require "../classes/eventmaster.php";
require_once "../classes/taskmaster.php";

$menu = array();
$userid = $_SESSION['userid'];
$eventid = 0;
$eventid = 0;
// GET EVENT DETAILS
if (ISSET($_GET['eventid']) && IS_NUMERIC($_GET['eventid'])) {
	$eventid = $_GET['eventid'];	
}
// GET TASK DETAILS
if (ISSET($_GET['taskid']) && IS_NUMERIC($_GET['taskid'])) {
	$taskid = $_GET['taskid'];	
	$task = new TaskMaster;	
	$task->SetParameters($taskid);	
	$taskname = $task->GetVar("TaskName");	
	$description = $task->GetVar("Description");	
	$datetimereqstart = $task->GetVar("Description");
	$datetimeactstart = $task->GetVar("DateTimeReqStart");
	$datetimereqend = $task->GetVar("DateTimeActStart");
	$datetimeactend = $task->GetVar("DateTimeReqEnd");
	$status = $task->GetVar("Status");
	$sortorder = $task->GetVar("SortOrder");
}

echo "<p class='intro'>$taskname</p>\n";
echo "<p>$description</p>\n";
echo "<p>Planned Start: $datetimereqstart</p>\n";
echo "<p>Actual Start: $datetimeactstart</p>\n";
echo "<p>Planned End: $datetimereqend</p>\n";
echo "<p>Actual End: $datetimeactend</p>\n";
echo "<p>Status: $status</p>\n";
echo "<p>Task #: $sortorder</p>\n";

if ($status == "pending") {
	echo "<input onClick='document.location.href='task.php?eventid=$eventid&taskid=$taskid&status=inprogress' type='button' value='Start Progress' class='ui-btn ui-btn-corner-all ui-shadow ui-btn-up-b' data-theme='b' data-role='button'>\n";
}
elseif ($status != "complete") {
	echo "<input onClick='document.location.href='task.php' type='button' value='Complete' class='ui-btn ui-btn-corner-all ui-shadow ui-btn-up-b' data-theme='b' data-role='button'>\n";
	echo "<input onClick='document.location.href='task.php' type='button' value='Issues' class='ui-btn ui-btn-corner-all ui-shadow ui-btn-up-b' data-theme='b' data-role='button'>\n";
}	
?>	
	
<input onClick="document.location.href='tasks.php?eventid=<?php echo $eventid;?>'" type="button" value="Back" class="ui-btn ui-btn-corner-all ui-shadow ui-btn-up-c" data-theme="c" data-role="button">
	
<?php
require "footer.php";
?>
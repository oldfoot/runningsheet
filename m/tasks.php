<?php
define( '_VALID_DIR_', 1 );

require "config.php";
require "header.php";

require "../classes/eventmaster.php";
require_once "../classes/taskmaster.php";

$menu = array();
$userid = $_SESSION['userid'];
$eventid = 0;
// GET EVENT DETAILS
if (ISSET($_GET['eventid']) && IS_NUMERIC($_GET['eventid'])) {
	$eventid = $_GET['eventid'];	
	$event = new EventMaster;	
	$event->SetParameters($eventid);	
	$eventtitle = $event->GetVar("EventName");
	$locked = $event->GetVar("Locked");
}
// APPLY FILTERS BY CALLING DIFFERENT STORED PROCEDURES
$filter = "";
if (ISSET($_GET['filter'])&& ($_GET['filter'] == "my" || $_GET['filter'] == "pending" || $_GET['filter'] == "issues" || $_GET['filter'] == "complete")) {
	$filter = $_GET['filter'];
}
if ($filter == "my") {
	$sql = "CALL sp_task_browse_my($eventid,$userid);";	
}
elseif ($filter == "pending" || $filter == "complete" || $filter == "issues") {
	$sql = "CALL sp_task_browse_filter_status($eventid,$userid,'$filter')";	
}
else {
	$sql = "CALL sp_task_browse($eventid,$userid);";
}
$result = $db->Query($sql);
if ($db->NumRows($result) > 0) {
	while ($row = $db->FetchArray($result)) {
		$taskid = $row['TaskID'];		
		$menu[$taskid] = $row['TaskName'];
	}
}
else {
	$menu[] = "No Tasks for $eventtitle";
}
?>
<p class="intro"><strong>Welcome, <?php echo $user->GetVar("FullName");?>.</strong></p>
<div data-role="content">
	<div class="content-primary">
		<ul data-role="listview">
			<li role="heading" data-role="list-divider">Select your Task</li>
			<?php
			foreach ($menu as $id=>$val) {
				echo "<li><a href='task.php?eventid=$eventid&taskid=$id'>$val</a></li>\n";
			}
			?>	
		</ul>
	</div>
</div>
<input onClick="document.location.href='event.php?eventid=<?php echo $eventid;?>'" type="button" value="Back" class="ui-btn ui-btn-corner-all ui-shadow ui-btn-up-c" data-theme="c" data-role="button">
<?php
require "footer.php";
?>
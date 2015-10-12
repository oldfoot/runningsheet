<?php
define( '_VALID_DIR_', 1 );
require "../config.php";
require $dr."classes/eventmaster.php";
require_once $dr."classes/taskmaster.php";
require_once $dr."classes/userevent.php";
require_once $dr."functions/FriendlyDateFromSeconds.php";
require_once $dr."functions/FriendlyFileSize.php";

$userid = $_SESSION['userid'];
$eventid = 0;
$eventtitle = "";
if (ISSET($_GET['eventid']) && IS_NUMERIC($_GET['eventid'])) {
	$eventid = $_GET['eventid'];	
	$event = new EventMaster;	
	$event->SetParameters($eventid);	
	$eventtitle = $event->GetVar("EventName");
	$locked = $event->GetVar("Locked");
}
// PRIVILEGES
$obj = new UserEvent;
//$obj->SetVar("debug",true);
$obj->SetParameters($eventid,$_SESSION['userid']);
$role = $obj->GetVar("RoleName");

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
//echo $sql; // ENABLE FOR DEBUGGING
$result = $db->Query($sql);
$c = "";
if ($db->NumRows($result) == 0) {
	$c .= "<div class='subheader'>No Tasks for Event</div>\n";
}
else {
	$c .= "<script type=\"text/javascript\"> 
			// When the document is ready set up our sortable with it's inherant function(s) 
			$(document).ready(function() { 
			  $(\"#test-list\").sortable({ 
				handle : '.handle', 
				update : function () { 					
				  var order = $('#test-list').sortable('serialize'); 
				  $(\"#info\").load(\"".$GLOBALS['wb']."ajax/task_sort.php?\"+order+\"&eventid=\"+".$eventid."); 				  
				} 
			  }); 
			}); 
			</script>\n";
	//$c .= "<div class='subheader'>$eventtitle tasks</div>\n";
	$c .= "<div id='info'></div>\n";
	$c .= "<table style='width:100%' class='ui-widget ui-widget-content'>\n";
	$c .= "<thead>
			<tr class=' '>				
				<th>Status</th>
				<td>#</th>				
				<td>Task</th>
				<td width='110'>Start</th>				
				<td>Length</th>
				<td>Tracking</th>";
	if ($role != "Management") {
		$c .= "<td>Actions</th>";
	}
	$c .= "</tr>
		</thead>";	
	$count = 0;
	$c .= "<tbody id='test-list'>\n";
	while ($row = $db->FetchArray($result)) {
		$count++;
		$taskid = $row['TaskID'];
		// CHECK PERMISSIONS
		$usertask = false;
		$obj_tasks = new TaskMaster;
		$obj_tasks->SetVar("taskid",$taskid);
		$obj_tasks->SetVar("userid",$_SESSION['userid']);
		if ($obj_tasks->UserTaskExists()) { $usertask = true; }
		// LOOP THE DATA
		$c .= "<tr id='listItem_$count'>\n";			
			$c .= "<td align='center'><img src=images/icons/".$row['Status'].".png></td>\n";
			$c .= "<td onClick='alert()'>".$row['SortOrder']."</td>\n";
			$c .= "<td>".$row['TaskName']."</td>\n";
			$c .= "<td>".$row['DateTimeReqStart']."</td>\n";			
			$c .= "<td>".FriendlyDateFromSeconds($row['sec'],true,false)."</td>\n";
			$c .= "<td>".FriendlyDateFromSeconds($row['ext'],true,true)."</td>\n";
			if ($role != "Management") {
				$c .= "<td width=150>\n";			
				//$c .= $row['Status'];
				if ($usertask) {
					if ($row['Status'] == "pending") {
						$c .= "<img src='images/icons/inprogress.png' alt='In Progress' title='In Progress' onClick=\"AjaxTaskStatusUpdate($taskid,'inprogress')\">\n";
					}
					elseif ($row['Status'] != "complete") {				
						$c .= "<img src='images/icons/complete.png' alt='Completed' title='Completed' onClick=\"AjaxTaskStatusUpdate($taskid,'complete')\">\n";
					}
					elseif ($row['Status'] != "issues" && $row['Status'] != "complete") {
						$c .= "<img src='images/icons/issues.png' alt='Having Issues' title='Having Issues' onClick=\"AjaxTaskStatusUpdate($taskid,'issues')\">\n";				
					}
				}
				if ($locked == "n") {
					$c .= "<img src='images/icons/edit.png' alt='Edit' title='Edit' onClick=\"EditTask($taskid)\">\n";				
					$c .= "<img src='images/icons/delete.png' alt='Delete' title='Delete' onClick=\"AjaxDeleteTask($taskid)\">\n";			
					//$c .= "<img src='images/icons/messages.png' alt='Chat Filter' title='Chat Filter' >\n";
					$c .= "<img src='images/sort.gif' alt='move' width='16' height='16' class='handle' />\n";
				}
				$c .= "</td>";
			}
		$c .= "</tr>\n";		
	}
	$c .= "</tbody>\n";
	$c .= "</table>\n";
}
$sql = "CALL sp_document_browse($eventid,".$_SESSION['userid'].")";
$result = $db->Query($sql);
if ($db->NumRows($result) > 0) {
	$c .= "<br /><br /><table style='width:100%' class='ui-widget ui-widget-content'>\n";
	$c .= "<thead>
			<tr>
				<th colspan=3>Attachments</th>
			</tr>
			</thead>";
	while ($row = $db->FetchArray($result)) {
		$c .= "<tr>\n";
			$c .= "<td><a href='bin/download_document.php?documentid=".$row['DocumentID']."'>".$row['DocumentName']."</a></td>\n";
			$c .= "<td>".FriendlyFileSize($row['DocumentSize'])."</td>\n";
			$c .= "<td><a href='#' onClick=\"AjaxCall('document_delete','documentid=".$row['DocumentID']."&eventid=$eventid','AjaxDiv');AjaxLoadTasks($eventid)\">Delete</a></td>\n";
		$c .= "</tr>\n";
	}
	$c .= "</table>\n";
}
echo $c;
?>
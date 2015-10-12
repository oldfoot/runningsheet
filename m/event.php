<?php
define( '_VALID_DIR_', 1 );

require "config.php";
require "header.php";

$menu = array(""=>"All Tasks","My Tasks","Completed Tasks","Pending Tasks","Tasks with Issues","Tracking");

require "../classes/eventmaster.php";

$menu = array();
$userid = $_SESSION['userid'];
$eventid = 0;
// GET EVENT DETAILS
if (ISSET($_GET['eventid']) && IS_NUMERIC($_GET['eventid'])) {
	$eventid = $_GET['eventid'];	
	$event = new EventMaster;	
	$event->SetParameters($eventid);	
	$eventname = $event->GetVar("EventName");	
}
?>
	<p class="intro">Select from the menu.</p>
			
	<ul class="ui-listview ui-listview-inset ui-corner-all ui-shadow" data-role="listview" data-inset="true" data-theme="c" data-dividertheme="f">
		<li role="heading" data-role="list-divider">Event</li>		
		<li><a href='tasks.php?eventid=<?php echo $eventid;?>&filter=all'>All Tasks</a></li>
		<li><a class='ui-link-inherit' href='tasks.php?eventid=<?php echo $eventid;?>&filter=all'>My Tasks</a></li>
		<li><a class='ui-link-inherit' href='tasks.php?eventid=<?php echo $eventid;?>&filter=all'>Pending Tasks</a></li>
		<li><a class='ui-link-inherit' href='tasks.php?eventid=<?php echo $eventid;?>&filter=all'>Completed Tasks</a></li>
		<li><a class='ui-link-inherit' href='tasks.php?eventid=<?php echo $eventid;?>&filter=all'>Issue Tasks</a></li>
		<li><a class='ui-link-inherit' href='tracking.php?eventid=<?php echo $eventid;?>'>Tracking</a></li>				
	</ul>		
	<input onClick="document.location.href='events.php'" type="button" value="Back" class="ui-btn ui-btn-corner-all ui-shadow ui-btn-up-c" data-theme="c" data-role="button">
<?php
require "footer.php";
?>
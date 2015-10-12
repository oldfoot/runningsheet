<?php
define( '_VALID_DIR_', 1 );

require "../config.php";
//require $dr."classes/rgraph_twoline.php";

$userid = $_SESSION['userid'];

$eventid = 0;
if (ISSET($_GET['eventid']) && IS_NUMERIC($_GET['eventid'])) {
	$eventid = $_GET['eventid'];
}
echo "<br />\n";
echo "<input type='button' value='Add People' class='ui-state-default ui-corner-all' id='cancel_new_event' onClick=\"ToggleDiv('add_resource_form','show')\" /> <br />\n";
echo "<input type='button' value='Edit Event' class='ui-state-default ui-corner-all' id='cancel_new_event' onClick='EditEvent($eventid)' /><br />\n";
echo "<input type='button' value='Delete Event' class='ui-state-default ui-corner-all' id='cancel_new_event' onClick='DeleteEvent($eventid)' /><br />\n";

echo "Charts<br />\n";
echo "<input type='button' value='Actual versus Estimates' class='ui-state-default ui-corner-all' id='cancel_new_event' onClick=\"ShowChart('event_progress')\" /><br />";
?>



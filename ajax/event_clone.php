<?php
define( '_VALID_DIR_', 1 );

require "../config.php";

$eventid=0;
if (ISSET($_GET['eventid']) && IS_NUMERIC($_GET['eventid'])) {
	$eventid = $_GET['eventid'];
}
else {
	die("Error");
}

$sql = "CALL sp_event_clone($eventid,".$_SESSION['userid'].")";
//echo $sql;
$result = $db->Query($sql);
$c = "";
if ($db->AffectedRows($result) == 0) {
	echo "<img src='".$GLOBALS['wb']."images/crystalclear/22x22/actions/agt_update_critical.png'>Failed";
}
else {	
	echo "<img src='".$GLOBALS['wb']."images/crystalclear/22x22/actions/agt_action_success.png'>Success";
}
?>

<?php
	define( '_VALID_DIR_', 1 );
require "../config.php";
require $dr."classes/userevent.php";

$eventid = 0;
if (ISSET($_GET['eventid']) && IS_NUMERIC($_GET['eventid'])) {
	$eventid = $_GET['eventid'];
}

$obj = new UserEvent;
//$obj->SetVar("debug",true);
$obj->SetParameters($eventid,$_SESSION['userid']);
echo $obj->GetVar("RoleName");
?>
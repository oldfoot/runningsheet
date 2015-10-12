<?php
define( '_VALID_DIR_', 1 );
require "../config.php";

require $dr."classes/eventmaster.php";

$event = new EventMaster;
// GRAB THE PARAMS DYNAMICALLY
$event->SetVar("locked","n"); // SET THIS BEFORE - IF NOT IT IS NEVER DEFINED
foreach ($_POST as $key=>$val) {	
	$event->SetVar($key,$val);
}
// NEVER HANDLE THIS IN THE REQUEST
$event->SetVar("userid",$_SESSION['userid']);
//$event->SetVar("debug",true); // ENABLE FOR DEBUGGING
// ADD OR EDIT
if (ISSET($_POST['eventid']) && IS_NUMERIC($_POST['eventid'])) {
	$result = $event->Edit();
}
else {
	$result = $event->Add();
}
echo $event->ShowErrors();
?>
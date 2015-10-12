<?php
define( '_VALID_DIR_', 1 );
require "../config.php";

require $dr."classes/eventmaster.php";

$event = new EventMaster;
// GRAB THE PARAMS DYNAMICALLY
foreach ($_GET as $key=>$val) {
	//echo "$key = $val <br />";
	$event->SetVar($key,$val);
}
// NEVER HANDLE THIS IN THE REQUEST
$event->SetVar("userid",$_SESSION['userid']);
// DELETE
$result = $event->Delete();
if (!$result) {
	echo $event->ShowErrors();
}
else {
	echo $result;
}
?>
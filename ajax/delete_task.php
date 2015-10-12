<?php
define( '_VALID_DIR_', 1 );
require "../config.php";

require $dr."classes/taskmaster.php";

$task = new TaskMaster;
// GRAB THE PARAMS DYNAMICALLY
foreach ($_GET as $key=>$val) {
	//echo "$key = $val <br />";
	$task->SetVar($key,$val);
}
// NEVER HANDLE THIS IN THE REQUEST
$task->SetVar("userid",$_SESSION['userid']);
// ADD
$result = $task->Delete();
if (!$result) {
	echo $task->ShowErrors();
}
else {
	echo $result;
}
?>
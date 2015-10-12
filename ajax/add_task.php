<?php
define( '_VALID_DIR_', 1 );
require "../config.php";

require $dr."classes/taskmaster.php";

$task = new TaskMaster;
// GRAB THE PARAMS DYNAMICALLY
foreach ($_POST as $key=>$val) {	
	$task->SetVar($key,$val);
}
// NEVER HANDLE THIS IN THE REQUEST
$task->SetVar("userid",$_SESSION['userid']);
//$task->SetVar("debug",true);
// ADD OR EDIT
if (ISSET($_POST['taskid']) && IS_NUMERIC($_POST['taskid'])) {
	$result = $task->Edit();
}
else {
	$result = $task->Add();
}
echo $task->ShowErrors();
?>
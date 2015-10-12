<?php
define( '_VALID_DIR_', 1 );
require "../config.php";

require $dr."classes/messages.php";

$message = new Messages;
// GRAB THE PARAMS DYNAMICALLY
foreach ($_POST as $key=>$val) {	
	$message->SetVar($key,$val);
}
// NEVER HANDLE THIS IN THE REQUEST
$message->SetVar("userid",$_SESSION['userid']);
// ADD
$result = $message->Add();
if (!$result) {
	echo $message->ShowErrors();
}
else {
	echo $result;
}
?>
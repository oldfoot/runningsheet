<?php
define( '_VALID_DIR_', 1 );
require "../config.php";

require $dr."classes/userevent.php";

$obj = new UserEvent;
// GRAB THE PARAMS DYNAMICALLY
foreach ($_POST as $key=>$val) {
	//echo "$key = $val <br />";
	$obj->SetVar($key,$val);
}
// NEVER HANDLE THIS IN THE REQUEST
$obj->SetVar("userid",$_SESSION['userid']);
// ADD
//$obj->SetVar("debug",true);
$result = $obj->Add();
echo $obj->ShowErrors();
?>
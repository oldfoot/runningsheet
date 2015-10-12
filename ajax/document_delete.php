<?php
define( '_VALID_DIR_', 1 );
require "../config.php";

require $dr."classes/documentmaster.php";

$document = new DocumentMaster;
// GRAB THE PARAMS DYNAMICALLY
foreach ($_GET as $key=>$val) {
	//echo "$key = $val <br />";
	$document->SetVar($key,$val);
}
// NEVER HANDLE THIS IN THE REQUEST
$document->SetVar("userid",$_SESSION['userid']);
//$document->SetVar("debug",true); // enable for debugging
// DELETE
$result = $document->Delete();
echo $document->ShowErrors();
?>
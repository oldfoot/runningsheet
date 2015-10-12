<?php
define( '_VALID_DIR_', 1 );

require "../config.php";

require "../classes/ajaxevents.php";

$obj = new AjaxEvents;
if (ISSET($_GET['eventid'])) {
	$obj->SetVar("eventid",$_GET['eventid']);
}
$obj->Get();

?>

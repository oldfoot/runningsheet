<?php
define( '_VALID_DIR_', 1 );
require "../config.php";

require $dr."classes/organisation_users.php";

file_put_contents("jqgrid_edit_orgusers.log",date("Y-m-d H:i:s")."\n",FILE_APPEND);

$orgusers = new OrganisationUsers;
// GRAB THE PARAMS DYNAMICALLY
foreach ($_POST as $key=>$val) {	
	$orgusers->SetVar($key,$val);
	file_put_contents("jqgrid_edit_orgusers.log","$key = $val \n",FILE_APPEND);
}
file_put_contents("jqgrid_edit_orgusers.log",$_SESSION['userid']."\n",FILE_APPEND);
// NEVER HANDLE THIS IN THE REQUEST
$orgusers->SetVar("userid",$_SESSION['userid']);
$orgusers->SetVar("organisationid",$GLOBALS['user']->GetVar("organisationid"));

file_put_contents("jqgrid_edit_orgusers.log",$GLOBALS['user']->GetVar("organisationid")."\n",FILE_APPEND);

$debug = $orgusers->SetVar("debug",true); // ENABLE FOR DEBUGGING
file_put_contents("jqgrid_edit_orgusers.log",$debug."\n",FILE_APPEND);
// ADD OR EDIT
$orgusers->Approve();
echo $orgusers->ShowErrors();
file_put_contents("jqgrid_edit_orgusers.log",$orgusers->ShowErrors(),FILE_APPEND);
file_put_contents("jqgrid_edit_orgusers.log","Done \n",FILE_APPEND);
?>
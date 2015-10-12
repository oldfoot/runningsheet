<?php
/** ensure this file is being included by a parent file */
defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );

ob_start();

// TURN OFF ERRORS IN PRODUCTION
if (ISSET($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == "192.168.2.88") {
	//ini_set("display_errors","Off");	
	//echo "Errors on";
	error_reporting(E_ALL);
	//error_reporting(0);
}

require "classes/offline.php";	
$offline = new offline;

if (file_exists("siteoffline")) {
	$offline->SetVar("message_extra","We are doing a bit of maintenance, check back shortly.");
	echo $offline->Show();
	die();
}

require "classes/session.php";	

require_once "site_config.php";

$session = new session();

session_set_save_handler(array($session,"open"),
                         array($session,"close"),
                         array($session,"read"),
                         array($session,"write"),
                         array($session,"destroy"),
                         array($session,"gc")); 

session_start();


require "classes/mysqli.php";	
//require "classes/mysql.php";	

$db = new MySQL;
$conn = $db->Connect($database_hostname,$database_user,$database_password,$database_name,$database_port);
if (!$conn) { 
	$GLOBALS['offline']->SetVar("message_extra","Database is offline");
	echo $GLOBALS['offline']->Show();
}

require_once "functions/MessageCatalogue.php";

function GetSafeVar($from,$name) {
	if ($from == "get") {
		if (ISSET($_GET[$name])) {
			return addslashes($_GET[$name]);
		}
	}
	if ($from == "post") {
		if (ISSET($_POST[$name])) {			
			return addslashes($_POST[$name]);
		}
	}
}
// GLOBAL ERROR HANDLING
require_once "classes/errors.php";
$errors = new errors;
// CURRENT LOGGED IN USER DATA
if (ISSET($_SESSION['userid'])) {
	require_once "classes/usermaster.php";
	$user = new UserMaster;
	//$user->SetVar("debug",true);
	$user->SetParameters($_SESSION['userid']);
	$user->OrgPriv();
	
	require_once "classes/organisation_master.php";
	$org = new OrganisationMaster;
	//$org->SetVar("debug",true);
	$org->SetParameters($user->GetVar("organisationid"));
	$account_type = $org->GetVar("AccountType");	
	//echo "Account type:".$account_type;
	
	// ROLE PRIVILEGES
	require_once "classes/userroles.php";	
	$userroles = new UserRoles;	
}

// THIS IS THE LIST OF OPTIONS
$sql = "SELECT CompletionID, Name FROM task_completion_master";
$result = $db->Query($sql);
$on_complete_opts = array();
while ($row = $db->FetchArray($result)) {
	$id = $row['CompletionID'];
	$name = $row['Name'];
	$on_complete_opts[$id] = $name;
}

// LOG
$url = "";
if (ISSET($_SERVER['SCRIPT_NAME'])) {
	$url .= $_SERVER['SCRIPT_NAME'];
}
if (ISSET($_SERVER['QUERY_STRING'])) {
	$url .= "?".$_SERVER['QUERY_STRING'];
}
$sessionid = session_id();
$sql = "INSERT INTO log (SCRIPT_NAME,DateTimeLogged,SessionID) 
		VALUES (
		'".addslashes($url)."',
		sysdate(),
		'".$sessionid."'
		)";
$db->Query($sql);
?>
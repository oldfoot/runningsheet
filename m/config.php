<?php
/** ensure this file is being included by a parent file */
defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );

ob_start();
session_start();
error_reporting(E_ALL);

if ($_SERVER['SERVER_NAME'] == "192.168.2.8") {		
	ini_set("include_path", "e:/xampp177/htdocs/runningsheet/pear/");
}
else {
	ini_set("include_path", "/var/www/html/pear");
}

require "../classes/mysqli.php";
require_once "../site_config.php";

$db = new MySQL;
$db->Connect($database_hostname,$database_user,$database_password,$database_name,$database_port);

require_once "../functions/MessageCatalogue.php";

if (ISSET($_SESSION['userid'])) {
	require_once "../classes/usermaster.php";
	$user = new UserMaster;
	$user->SetParameters($_SESSION['userid']);
}
?>
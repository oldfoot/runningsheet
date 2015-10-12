<?php
define( '_VALID_DIR_', 1 );

require_once "../../config.php";;

require_once $dr."classes/classtorestws.php";

header("Content-type: text/xml");

// CONVERT THE APIAUTHCODE TO A USER SESSION ID
if (ISSET($_GET['api_auth_code'])) {
	$api_auth_code = htmlentities($_GET['api_auth_code']);
}
elseif (ISSET($_POST['api_auth_code'])) {
	$api_auth_code = htmlentities($_POST['api_auth_code']);
}
else {
	$res = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
    $res .= "<result>\n";
    $res .= "Failed. Missing API Auth Code!";
    $res .= "</result>\n";
	echo $res;
	die();
}
require_once $dr."classes/usermaster.php";
$user = new UserMaster;
//$user->SetVar("debug",true);
$user->SetVar("api_auth_code",$api_auth_code);
$userid = $user->GetIDFromAuthCode();
$_SESSION['userid'] = $userid;
$user->SetParameters($userid);

//echo $user->GetVar("Events");		
//echo $userid;
//die();
if ($userid) {
	$_SESSION['userid']= $userid;
}
else {
	$res = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
    $res .= "<result>\n";
    $res .= "Failed. Not Authorised";
    $res .= "</result>\n";
	echo $res;
	die();
}

$obj = new ClassToRestWS;
$obj->SetVar("file",$dr."classes/eventmaster");
$obj->SetVar("class","EventMaster");
if (ISSET($_GET['method'])) { $method = $_GET['method']; } else { die("No method"); }
$obj->SetVar("method",$method);
$obj->SetVar("ret_col","EventID");
$result = $obj->Handle();

if ($result) {    
  echo $result;
}
else {  
  echo $obj->ShowErrors();
}
?>

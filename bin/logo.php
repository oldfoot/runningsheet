<?php
header("Pragma: no-cache");
define( '_VALID_DIR_', 1 );

if (ISSET($_GET['ignore'])) {
	header('Content-Type: image/gif');
	readfile("../images/logo.gif");
	die();
}

require "../config.php";

require_once $dr."classes/logomaster.php";

$organisationid=0;
if (ISSET($user) && IS_NUMERIC($user->GetVar("organisationid"))) {
	$organisationid = $user->GetVar("organisationid");
}
else {
	header('Content-Type: image/gif');
	readfile("../images/logo.gif");
	die();
}
$dm=new LogoMaster;
//$dm->SetVar("debug",True);
$dm->SetVar("organisationid",$organisationid);
$dm->SetVar("userid",$_SESSION['userid']);
$result = $dm->Info();

if (!$result) {
	//header('Content-Type: image/jpeg');
	//imagejpeg($im);

	// saving to a file
	// you do not need a header() function to save to a file
	// outputting to the browser including quality parameter.
	// skipping parameter2 to output to browser
	header('Content-Type: image/gif');
	readfile("../images/logo.gif");
	die();
}

/*
echo "ok";
echo $dm->GetVar("DocumentType")."<br>";
echo $dm->GetVar("DocumentSize")."<br>";
echo FormatSpaces($dm->GetVar("DocumentName"))."<br>";
echo $dm->GetVar("Document");
die();
*/

header("Content-Type: ".$dm->GetVar("LogoType"));
header("Content-Length: ".$dm->GetVar("LogoSize"));
//header("Content-Disposition: attachment; filename=".FormatSpaces($dm->GetVar("DocumentName")));
echo $dm->GetVar("Logo");

function FormatSpaces($v) {
	return STR_REPLACE(" ","%20",$v);
}
?>
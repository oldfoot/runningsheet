<?php
header("Pragma: no-cache");

define( '_VALID_DIR_', 1 );
require "../config.php";

require_once $dr."classes/documentmaster.php";

$documentid=0;
if (ISSET($_GET['documentid']) && IS_NUMERIC($_GET['documentid'])) {
	$documentid = $_GET['documentid'];
}
else {
	die("Invalid Document");
}
$dm=new DocumentMaster;
//$dm->SetVar("debug",True);
$dm->SetVar("documentid",$documentid);
$dm->SetVar("userid",$_SESSION['userid']);
$result = $dm->Info();

if (!$result) { die($dm->ShowErrors()); }

/*
echo "ok";
echo $dm->GetVar("DocumentType")."<br>";
echo $dm->GetVar("DocumentSize")."<br>";
echo FormatSpaces($dm->GetVar("DocumentName"))."<br>";
echo $dm->GetVar("Document");
die();
*/

header("Content-Type: ".$dm->GetVar("DocumentType"));
header("Content-Length: ".$dm->GetVar("DocumentSize"));
header("Content-Disposition: attachment; filename=".FormatSpaces($dm->GetVar("DocumentName")));
echo $dm->GetVar("Document");

function FormatSpaces($v) {
	return STR_REPLACE(" ","%20",$v);
}
?>
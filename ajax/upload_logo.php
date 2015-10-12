<?php
define( '_VALID_DIR_', 1 );
require "../config.php";
require $dr."classes/logomaster.php";

///*
file_put_contents("upload_logo.log",date("Y-m-d H:i:s")." - starting \n",FILE_APPEND);

foreach ($_POST as $key=>$val) {
	file_put_contents("upload_logo.log","$key - $val \n",FILE_APPEND);
}
//*/
if (!ISSET($_FILES['userfile'])) die("");

$file = $_FILES['userfile'];
$k = count($file['name']);

$filename=$file['name'];
$filetype=$file['type'];
$filesize=$file['size'];

/* READ THE FILE INTO A BINARY VARIABLE */
$handle = fopen($file['tmp_name'],"rb");
$attachment=fread($handle, filesize ($file['tmp_name']));

$orgid = $GLOBALS['user']->GetVar("organisationid");

/* CALL THE OBJECT TO UPLOAD DOCUMENT */
$dm = new LogoMaster;

$dm->SetVar("filename",$filename);
$dm->SetVar("debug",true);
$dm->SetVar("filetype",$filetype);
$dm->SetVar("filesize",$filesize);
$dm->SetVar("attachment",$attachment);
$dm->SetVar("organisationid",$orgid);

$result = $dm->Add();
if (!$result) {
	file_put_contents("upload_logo.log","Failed: ".$dm->ShowErrors(),FILE_APPEND);
}
file_put_contents("upload_logo.log",$dm->ShowErrors(),FILE_APPEND);
echo $dm->ShowErrors();
?>
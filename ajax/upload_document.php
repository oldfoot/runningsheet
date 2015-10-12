<?php
define( '_VALID_DIR_', 1 );
require "../config.php";
require $dr."classes/documentmaster.php";

//file_put_contents("upload_document.log",date("Y-m-d H:i:s")." - starting \n",FILE_APPEND);

//foreach ($_POST as $key=>$val) {
	//file_put_contents("upload_document.log","$key - $val \n",FILE_APPEND);
//}
if (!ISSET($_FILES['userfile'])) die("");

$file = $_FILES['userfile'];
$k = count($file['name']);
$eventid = 0;
if (ISSET($_GET['eventid'])) {
	$eventid = $_GET['eventid'];
}
$taskid = 0;
if (ISSET($_GET['taskid'])) {
	$taskid = $_GET['taskid'];
}

$filename=$file['name'];
$filetype=$file['type'];
$filesize=$file['size'];

if ($account_type != "Professional") {
	die("Please upgrade to use this feature");
}
if ($filesize == 0) {
	die("File could not be uploaded. Too big perhaps?");
}
$organisationid = $user->GetVar("organisationid");
$sql = "CALL sp_document_org_size($organisationid)";
$result = $db->Query($sql);
while ($row = $db->FetchArray($result)) {
	// 2.5MB
	if (($filesize + $row['TotalSize']) > $max_org_file_upload_limit) {
		die("Your organisation has reached it's document size limit (used: ".$row['FormatTotalSize']."KB)");
	}
}
/* READ THE FILE INTO A BINARY VARIABLE */
$handle = fopen($file['tmp_name'],"rb");
$attachment=fread($handle, filesize ($file['tmp_name']));

/* CALL THE OBJECT TO UPLOAD DOCUMENT */
$dm = new DocumentMaster;
$dm->SetVar("filename",$filename);
//$dm->SetVar("debug",true);
$dm->SetVar("filetype",$filetype);
$dm->SetVar("filesize",$filesize);
$dm->SetVar("attachment",$attachment);
$dm->SetVar("eventid",$eventid);
$dm->SetVar("taskid",$taskid);

$dm->Add();
echo $dm->ShowErrors();
?>
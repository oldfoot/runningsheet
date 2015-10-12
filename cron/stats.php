<?php
define( '_VALID_DIR_', 1 );
require "../config.php";

require_once $GLOBALS['dr']."classes/email.php";
$stats = "<html><body>";
$arr = array("Users"=>"usermaster","Events"=>"eventmaster","Tasks"=>"taskmaster");
foreach ($arr as $desc=>$table) {
	// TOTAL MEMBERS
	$sql = "SELECT 'Total $desc', count(*) as total
			FROM $table
			";
	$stats .= Results($sql,false,false);

	// NEW MEMBERS TODAY
	$sql = "SELECT 'New $desc Today', count(*) as total
			FROM $table
			WHERE DateTimeCreated > DATE_ADD(sysdate(), INTERVAL -1 DAY)
			";
	$stats .= Results($sql,false,false);

	// NEW MEMBERS Last 7 days
	$sql = "SELECT 'New $desc in the last 1-7 days', count(*) as total
			FROM $table
			WHERE DateTimeCreated > DATE_ADD(sysdate(), INTERVAL -7 DAY)		
			AND DateTimeCreated < DATE_ADD(sysdate(), INTERVAL -1 DAY)		
			";
	$stats .= Results($sql,false,false);

	// NEW MEMBERS Last 30 days
	$sql = "SELECT 'New $desc in the last 7-30 days', count(*) as total
			FROM $table
			WHERE DateTimeCreated > DATE_ADD(sysdate(), INTERVAL -30 DAY)
			AND DateTimeCreated < DATE_ADD(sysdate(), INTERVAL -7 DAY)		
			";
	$stats .= Results($sql,false,true);
}
// COLLECT STATS
//$stats .= exec('df -h');
$stats .= "Free space: ".number_format(disk_free_space("/"),0);

$stats .= "</body></html>";

echo $stats;
$email = new email;
$email->SetVar("to","general@runningsheet.com");
$email->SetVar("subject","[RunningSheet] Stats");
//$email->SetVar("html",true);

$email->SetVar("body",strip_tags($stats));
$email->SendEmail();

function Results($sql,$row=false,$closetable) {
	
	$c = "";
	
	$db = $GLOBALS['db'];
	$result = $db->Query($sql);
	if (!$row && !$closetable) { $c.= "<table border=1 width=300>\n"; }
	$arr_cols=$db->GetColumns($result);

	/*
	if (!$row) {
		$c .=  "<tr>\n";
		for ($i=1;$i<count($arr_cols);$i++) {
			$col_name=$arr_cols[$i];
			$c .= "<td width=80%>\n";
			$c .= $col_name;
			$c .= "</td>\n";
		}
		$c .= "<tr>\n";
	}
	*/
	
	
	while ($row = $db->FetchArray($result)) {	
		$c .= "<tr>\n";
		for ($i=1;$i<count($arr_cols);$i++) {
			$col_name=$arr_cols[$i];
			$c .= "<td>\n";
			$c .= $row[$col_name];
			$c .= "</td>\n";
		}
		$c .= "<tr>\n";
	}
	if (!$row && $closetable) { $c.= "</table>\n"; }
	
	return $c;
}
?>
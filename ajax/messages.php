<?php
define( '_VALID_DIR_', 1 );
require "../config.php";

require $dr."functions/FriendlyDateFromSeconds.php";

$eventid = 0;
if (ISSET($_GET['eventid']) && IS_NUMERIC($_GET['eventid'])) {
	$eventid = $_GET['eventid'];
}

$sql = "CALL sp_browse_messages($eventid)";
$result = $db->Query($sql);

$c = "<table id='users' class='ui-widget ui-widget-content'>\n";
	
while ($row = $db->FetchArray($result)) {
	$c .= "<tbody><tr>\n";
	$c .= "<td><img src='images/crystalclear/22x22/apps/".$row['MessageType'].".png'></td>\n";
	$c .= "<td style='color:#999999' valign='top'>".$row['FullName']."</td>";
	$c .= "<td valign='top'>".$row['Message']."</td>";
	$c .= "<td style='color:#999999' valign='top'>".FriendlyDateFromSeconds($row['sc'])."</td>";
	$c .= "</tbody></tr>\n";
	$c .= "<tbody><tr>\n";
		$c .= "<td colspan='3'><div style='border-bottom: 1px dotted #80c080'></div></td>\n";
	$c .= "</tbody></tr>\n";
}
$c .= "</table>\n";
echo $c;
?>
<?php
define( '_VALID_DIR_', 1 );

require "../config.php";

$userid = $_SESSION['userid'];

$sql = "CALL sp_userevent_browse($userid)";
$result = $db->Query($sql);
$c = "";
if ($db->NumRows($result) == 0) {
	$c .= "<div id='emptyresult'>No Events</div>\n";
}
else {	
	$c .= "<table id='users' class='ui-widget ui-widget-content'>\n";
	$c .= "<thead>
			<tr class='ui-widget-header '>
				<th>Event</th>
				<th>From</th>
				<th>To</th>
				<th>Status</th>
				<th></th>
			</tr>
		</thead>";	
	$count = 0;
	$total = $db->NumRows($result);
	while ($row = $db->FetchArray($result)) {
		$eventid = $row['EventID'];		
		//$c .= "<span>$eventid</span>";
		$c .= "<tbody id='$count' onClick=\"AjaxLoadTasks($eventid);EventHighlight($count,$total);\"> <tr>\n";
		$c .= "<td>".$row['EventName']."</td>";
		$c .= "<td>".$row['DateTimeStart']."</td>";
		$c .= "<td>".$row['DateTimeEnd']."</td>";		
		$c .= "<td>".$row['Status']."</td>";		
		$c .= "<td><img src='images/crystalclear/22x22/actions/misc.png' alt='More' title='More' onClick=\"EventToggle($count,$total,true);EventMore()\"></td>";		
		$c .= "</tbody> </tr>\n";		
		$count++;
	}
	$c .= "</table>\n";
}
echo $c;
?>

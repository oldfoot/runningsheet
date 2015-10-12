<?php
define( '_VALID_DIR_', 1 );

require "../config.php";

$eventid=0;
if (ISSET($_GET['eventid']) && IS_NUMERIC($_GET['eventid'])) {
	$eventid = $_GET['eventid'];
}

$sql = "CALL sp_event_users($eventid)";
$result = $db->Query($sql);
$c = "";
if ($db->NumRows($result) == 0) {
	$c .= "<div id='emptyresult'>No Users</div>\n";
}
else {	
	$c .= "<table id='users' width=100% class='ui-widget ui-widget-content'>\n";
	$c .= "<thead>
			<tr class='ui-widget-header '>
				<th>Name</th>
				<th>Email</th>
				<th>Contact</th>
			</tr>
		</thead>";	
	$count = 0;
	$total = $db->NumRows($result);
	while ($row = $db->FetchArray($result)) {				
		$c .= "<tbody> <tr>\n";
		$c .= "<td>".$row['FullName']."</td>";
		$c .= "<td>".$row['UserLogin']."</td>";
		$c .= "<td>".$row['ContactDetails']."</td>";
		$c .= "</tbody> </tr>\n";		
		$count++;
	}
	$c .= "</table>\n";
}
echo $c;
?>

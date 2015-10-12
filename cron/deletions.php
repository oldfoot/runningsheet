<?php
define( '_VALID_DIR_', 1 );
require "../config.php";

echo "<h3>Deletions</h3>";
$sql = "SELECT * FROM usermaster WHERE AccountStatus = 'Deleted'";
$result = $db->Query($sql);
while ($row = $db->FetchArray($result)) {
	Debug($row['UserID'], "Marked for deletion");//$row['UserID'] . " - " .$row['UserLogin'] . " - " .$row['FullName']);
	$sql = "SELECT count(*) as total
			FROM userevent ue, eventmaster em
			WHERE ue.UserID = 1
			AND ue.EventID = em.EventID
			AND em.Status = 'In Progress'";
	$total = CountRows($sql);
	if ($total > 0) {
		Debug($row['UserID'],"Active events. Account not deleted");
	}
	else {
		$sql = "DELETE FROM usermaster WHERE UserID = ".$row['UserID'];
		Debug($row['UserID'],"Deleted");
		$db->Query($sql);
	}
}
function Debug($userid,$v) {
	$sql = "CALL sp_admin_userlog('$userid','$v')";
	$result = $GLOBALS['db']->Query($sql);
	echo $v."<br />";
}
function CountRows($sql) {
	$result = $GLOBALS['db']->Query($sql);
	while ($row = $GLOBALS['db']->FetchArray($result)) {
		return $row['total'];
	}
	return 0;
}
?>
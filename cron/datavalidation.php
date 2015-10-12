<?php
define( '_VALID_DIR_', 1 );
require "../config.php";

/*
	ALL USERS MUST HAVE AT LEAST ONE ROLE
*/
$title = "Users without a role";
$sql = "SELECT *
		FROM usermaster
		WHERE UserID NOT IN (SELECT UserID FROM userroles)";
Check($title,$sql);

function Check($title,$sql) {
	echo "<h3>$title</h3>";
	$db = $GLOBALS['db'];
	$result = $db->Query($sql);
	echo "<table border=1>";
	$arr_cols=$db->GetColumns($result);

	echo "<tr>";
	for ($i=1;$i<count($arr_cols);$i++) {
		$col_name=$arr_cols[$i];
		echo "<td>";
		echo $col_name;
		echo "</td>";
	}
	echo "<tr>";

	while ($row = $db->FetchArray($result)) {	
		echo "<tr>";
		for ($i=1;$i<count($arr_cols);$i++) {
			$col_name=$arr_cols[$i];
			echo "<td>";
			echo $row[$col_name];
			echo "</td>";
		}
		echo "<tr>";
	}
	echo "</table>";
}
?>

<?php
define( '_VALID_DIR_', 1 );
require "../config.php";

$arr = array(
"ajaxevents"=>"truncate table ajaxevents",
"contact"=>"truncate table contact",
"taskcompletion"=>"truncate table taskcompletion",
"organisation_users"=>"truncate table organisation_users",
"organisation_master"=>"truncate table organisation_master",
"usermessagehistory"=>"truncate table usermessagehistory",
"usertasks"=>"truncate table usertasks",
"usereventroles"=>"truncate table usereventroles",
"userevent"=>"truncate table userevent",
"taskmaster"=>"truncate table taskmaster",
"taskdependencies"=>"truncate table taskdependencies",
"eventmaster"=>"truncate table eventmaster",
"event_history"=>"truncate table event_history",
"usermaster"=>"truncate table usermaster",
"userroles"=>"truncate table userroles"
);
foreach ($arr as $key=>$val) {
	ExecuteSQL($key,$val);
}

function ExecuteSQL($desc,$sql) {
	//echo $desc;
	$db = $GLOBALS['db'];
	//echo $sql;
	$result = $db->Query($sql);
	echo mysql_error();
	if ($result) {
		if ($db->AffectedRows($result) > 0) {
			echo "<img src='green.png'>$desc => Success <br />";
		}
		else {
			echo "<img src='yellow.png'>$desc => Warning <br />";
		}
	}
	else {
		echo "<img src='red.png'><b>$desc => Failed </b><br />";
	}
}
?>

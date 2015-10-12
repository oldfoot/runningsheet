<?php
define( '_VALID_DIR_', 1 );

require "config.php";
require "header.php";

$menu = array();
$sql = "CALL sp_userevent_browse(".$_SESSION['userid'].")";
$result = $db->Query($sql);
if ($db->NumRows($result) > 0) {
	while ($row = $db->FetchArray($result)) {
		$eventid = $row['EventID'];		
		$menu[$eventid] = $row['EventName'];
	}
}
else {
	$menu[] = "No Events";
}
?>

	<p class="intro"><strong>Welcome <?php echo $user->GetVar("FullName");?>.</strong></p>
			
	<ul class="ui-listview ui-listview-inset ui-corner-all ui-shadow" data-role="listview" data-inset="true" data-theme="c" data-dividertheme="f">
		<li role="heading" data-role="list-divider">Select your event</li>
		<?php
		foreach ($menu as $id=>$val) {
			echo "<li><a href='event.php?eventid=$id'>$val</a></li>\n";
		}
		?>		
	</ul>
	
	<div role="contentinfo" class="ui-footer ui-bar-d" data-role="footer" data-theme="d">
		<h4 aria-level="1" role="heading" tabindex="0" class="ui-title"><a href="logout.php">Logout</a></h4>
	</div>
<?php
require "footer.php";
?>
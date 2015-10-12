<?php
define( '_VALID_DIR_', 1 );
require "config.php";
require "classes/content.php";

require "classes/Mobile_Detect.php";
if (ISSET($_GET['mobile']) && $_GET['mobile'] == "n") {
	$_SESSION['mobile'] = "n";
}
$detect = new Mobile_Detect();
if ($detect->isMobile() && !ISSET($_SESSION['mobile'])) {
	header("Location: m/login.php");
}

$html = new Content;
$html->SetVar("menu_items",$main_menu_items);
$html->Head();
$html->Body();
$content = $html->GetVar("html");

echo $content;
?>


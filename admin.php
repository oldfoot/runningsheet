<?php
define( '_VALID_DIR_', 1 );
require "config.php";
require "classes/content.php";

//$userroles->SetVar("debug",true);
if (!$userroles->CheckUserRolePriv("Admin Console")) {	
	die("Access denied");
}

$html = new Content;
$html->SetVar("menu_items",$main_menu_items_admin);
$html->SetVar("show_footer_banners",false);
$html->SetVar("show_content",false);
$html->Head();
$html->Body();
$content = $html->GetVar("html");

echo $content;
?>


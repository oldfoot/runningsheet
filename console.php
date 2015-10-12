<?php
define( '_VALID_DIR_', 1 );
require "config.php";
require "classes/content.php";

$html = new Content;
$html->SetVar("menu_items",$main_menu_items_console);
$html->SetVar("show_footer_banners",false);
$html->Head();
$html->Body();
$content = $html->GetVar("html");

echo $content;
?>


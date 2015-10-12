<?php
define( '_VALID_DIR_', 1 );
require "../config.php";

header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Expires: ".gmdate("D, d M Y H:i:s",time()+(-1*60))." GMT");

if (!ISSET($_SESSION['userid'])) { die("Not authorised"); }

if (ISSET($_GET['page'])) { $page = $_GET['page']; } else { $page = ""; }
if (ISSET($_GET['rows'])) { $limit = $_GET['rows']; } else { $limit = 10; }
if (ISSET($_GET['sidx'])) { $sidx = $_GET['sidx']; } else { $sidx = 1; }
if (ISSET($_GET['sord'])) { $sord = $_GET['sord']; } else { $sord = ""; }

// connect to the database

$orgid = $GLOBALS['user']->GetVar("organisationid");
$sql = "SELECT COUNT(*) AS count FROM organisation_users ou, usermaster um WHERE ou.OrganisationID = $orgid AND ou.UserID = um.UserID";
//echo $sql;
$result = $db->Query($sql);
$row = $db->FetchArray($result,MYSQL_ASSOC);
$count = $row['count'];
if( $count > 0 ) {
	$total_pages = ceil($count/$limit);
} else {
	$total_pages = 0;
}
if ($page > $total_pages) $page=$total_pages;
$start = $limit*$page - $limit; // do not put $limit*($page - 1)

//$SQL = "SELECT * FROM outage_history ORDER BY $sidx $sord LIMIT $start , $limit";
$SQL = "SELECT um.UserLogin, um.FullName, ou.Approved, ou.OrgRoleID
					FROM organisation_users ou, usermaster um					
					WHERE ou.OrganisationID = $orgid
					AND ou.UserID = um.UserID";					
$result = $db->Query($SQL);

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
header("Content-type: application/xhtml+xml;charset=utf-8"); } else {
header("Content-type: text/xml;charset=utf-8");
}
$et = ">";

echo "<?xml version='1.0' encoding='utf-8'?$et\n";
echo "<rows>\n";
echo "<page>".$page."</page>\n";
echo "<total>".$total_pages."</total>\n";
echo "<records>".$count."</records>\n";
$count = 0;
// be sure to put text data in CDATA
while($row = $db->FetchArray($result,MYSQL_ASSOC)) {
	$count++;
	echo "<row id='".$row['UserLogin']."'>\n";
	echo "<cell>".$row['UserLogin']."</cell>\n";
	//echo "<cell>". $count."</cell>\n";	
	echo "<cell>". $row['FullName']."</cell>\n";		
	echo "<cell>". $row['Approved']."</cell>\n";
	echo "<cell>". $row['OrgRoleID']."</cell>\n";
	//echo "<cell><![CDATA[". $row['Approved']."]]></cell>\n";
	//echo "<cell><![CDATA[". $row['OrgRoleID']."]]></cell>\n";	
	echo "</row>\n";
}
echo "</rows>\n";
?>
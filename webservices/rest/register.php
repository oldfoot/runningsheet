<?php
define( '_VALID_DIR_', 1 );

require_once "../../config.php";;

require_once $dr."classes/classtorestws.php";

$obj = new ClassToRestWS;
$obj->SetVar("file",$dr."classes/usermaster");
$obj->SetVar("class","UserMaster");
if (ISSET($_GET['method'])) { $method = $_GET['method']; } else { die("No method"); }
$obj->SetVar("method",$method);
$obj->SetVar("ret_col","APIAuthCode");
$result = $obj->Handle();
header("Content-type: text/xml");
if ($result) {    
  echo $result;
}
else {  
  echo $obj->ShowErrors();
}
?>

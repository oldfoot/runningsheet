<?php
/** ensure this file is being included by a parent file */
defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );

class Messages {

	public $methods = array("add","edit","delete"); 

	function __construct() {
		/* SET CHECKING TO FALSE */
		$this->parameter_check=False;
		$this->params_add = array("message","eventid","taskid");
		$this->vartypes = array("message"=>"any","eventid"=>"integer","taskid"=>"integer");
		$this->messagetype = "chat";
		$this->errors = "";				
		$this->debug  = false;
	}
	public function GetVar($v) {
		if (ISSET($this->$v)) {
			return $this->$v;
		}
		else {
			return "";
		}
	}	
	public function SetVar($v,$val) {
		$this->$v = $val;
	}

	public function SetParameters($messageid) {

		/* CHECKS */
		if (!IS_NUMERIC($messageid)) { $this->Errors("Invalid MessageID"); return False; }

		/* SET SOME COMMON VARIABLES */
		$this->messageid=$messageid;

		/* CALL THE INFORMATION METHOD */
		$this->Info();

		/* PARAMETER CHECK SUCCESSFUL */
		$this->parameter_check=True;

		return True;
	}

	private function Info() {
		$db=$GLOBALS['db'];
		$sql = "CALL sp_message_browse_id(".$this->messageid.");";					
		//echo $sql."<br>";
		$result = $db->Query($sql);
		if ($db->NumRows($result) > 0) {
			while($row = $db->FetchArray($result)) {
				/* HERE WE CALL THE FIELDS AND SET THEM INTO DYNAMIC VARIABLES */
				$arr_cols=$db->GetColumns($result);
				for ($i=1;$i<count($arr_cols);$i++) {
					$col_name=$arr_cols[$i];
					$this->$col_name=$row[$col_name];
				}
			}
		}
		else {
			return False;
		}
	}
	public function Add() {
		
		if (!$this->CheckVarsSet("add")) {
			$this->Errors("Invalid Parameters");			
			return false;
		}
		if (!$this->CheckVars()) {		
			$this->Errors("Invalid Values");
			return false;
		}
		$sp_params = "";
		foreach ($this->params_add as $p) {
		  $sp_params .= "'".$this->$p."',";
		}
		$sp_params .= "'".$_SESSION['userid']."'";
		//$sp_params = substr($sp_params,0,-1);
		
		$sql = "call sp_messages_add($sp_params,'".$this->messagetype."')";
		$this->debug("SQL: $sql");
		$result = $GLOBALS['db']->Query($sql);
		if ($result) {			
			return MessageCatalogue(21);
		}
		else {			
			return MessageCatalogue(22);
		}		
	}
	public function Delete() {
		
		if (!ISSET($this->taskid) || !IS_NUMERIC($this->taskid)) {
			$this->Errors("Invalid Task");
		}
		$sql = "call sp_taskmaster_delete(".$this->taskid.",".$this->userid.")";
		$result = $GLOBALS['db']->Query($sql);
		if ($result) {
			return MessageCatalogue(19);
		}
		else {
			return MessageCatalogue(20);
		}
	}

	// CHECK VARS ALL SET FOR REQUIRED METHOD
	private function CheckVarsSet($method) {    
		if ($method == "add") {      
			foreach ($this->params_add as $param) {
				if (!ISSET($this->$param)) {          
					//echo "$param not set";
					$this->Errors("Parameter $param not set");
					return False;
				}
			}
		}
    return True;
  }
  // CHECK VAR TYPES
  private function CheckVars() {
	$count = 0;
    foreach ($this->vartypes as $var=>$type) {	
      if ($type == "a-z" && !preg_match("/^\w*$/",$this->$var)) {                
        $this->Errors($var." needs to contain alpha characters only");
        return False;
      }      
      if ($type == "numeric" && !IS_NUMERIC($this->$var)) {
        $this->Errors($this->$var." needs to be numeric");
        return False;        
      }
	  if ($type == "email" && !preg_match("/^\w+@\w+\.\w+$/",$this->$var)) {
        $this->Errors($var." needs to be an email address");
        return False;        
      }
	  if ($type == "date" && !preg_match("/^\d\d\d\d-\d\d-\d\d$/",$this->$var)) {
        $this->Errors($var." needs to be an ISO format date");
        return False;        
      }
	  if ($type == "int-array" && !preg_match("/^[\d,?]*$/",$this->$var)) {
        $this->Errors($var." needs to be a set of integers followed by commas");
        return False;        
      }
	  $count++;
    }
    return True;
  }

	function Errors($err) {
		$this->errors.=$err."\n";
	}

	function ShowErrors() {
		return $this->errors;
	}
	private function debug($msg) {
		if ($this->debug) {
			echo $msg."<br />\n";
		}
	}
}
?>
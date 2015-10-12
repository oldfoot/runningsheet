<?php
/** ensure this file is being included by a parent file */
defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );
require_once "usereventroles.php";

class RunningsheetEventMaster {

	public $methods = array("add","edit","delete"); 

	function __construct() {
		/* SET CHECKING TO FALSE */
		$this->parameter_check=False;
		$this->params_add = array("eventname","datetimestart","datetimeend","locked","userid");
		$this->params_edit = array("eventid","eventname","datetimestart","datetimeend","locked","userid");
		$this->vartypes_add = array("eventname"=>"a-z","datetimestart"=>"datetime","datetimeend"=>"datetime","locked"=>"yn","userid"=>"numeric");
		$this->vartypes_edit = array("eventid"=>"numeric","eventname"=>"a-z","datetimestart"=>"datetime","datetimeend"=>"datetime","locked"=>"yn","userid"=>"numeric");
		$this->errors = "";
		$this->debug = false;
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
		$this->$v = trim($val);
	}

	public function SetParameters($eventid) {

		/* CHECKS */
		if (!IS_NUMERIC($eventid)) { $this->Errors("Invalid EventID"); return False; }

		/* SET SOME COMMON VARIABLES */
		$this->eventid=$eventid;

		/* CALL THE INFORMATION METHOD */
		$this->Info();

		/* PARAMETER CHECK SUCCESSFUL */
		$this->parameter_check=True;

		return True;
	}

	private function Info() {
		$db=$GLOBALS['db'];
		$sql = "CALL sp_event_browse_id(".$this->eventid.",".$_SESSION['userid'].");";					
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
			$this->debug("Parameters not set");
			$this->Errors("Invalid Parameters on add");
			return false;
		}
		if (!$this->CheckVars($this->vartypes_add)) {
			$this->debug("Invalid Variable types");
			$this->Errors("Invalid Values");
			return false;
		}
		
		// CHECK FEATURE LIMITS BASED ON ORG
		$limit = $GLOBALS['user']->GetVar("Events");		
		$this->debug("Limit for this account: $limit");
		if ($limit != "Unlimited") {
			if ($this->OrgPrivCountEvents() >= $limit) {
				$this->debug("Max number of events reached: ".$this->OrgPrivCountEvents()." and limited to ".$GLOBALS['user']->GetVar("Events"));
				$this->Errors(MessageCatalogue(54));
				return False;
			}
		}		
		$sp_params = "";
		foreach ($this->params_add as $p) {
		  $sp_params .= "'".$this->$p."',";
		}		
		$sp_params = substr($sp_params,0,-1);
		
		// LOCKED IS SENT AS A CHECKBOX WITH VALUE "CHECKED"
		//$this->locked = "n";
		//if (ISSET($this->locked) && $this->locked == "checked") { $this->locked = "y"; }
		
		$sql = "call sp_eventmaster_add(".$sp_params.")";				
		$this->debug($sql);
		$result = $GLOBALS['db']->Query($sql);
		if ($result) {
			
			while ($row = $GLOBALS['db']->FetchArray($result)) {			
				$eventid = $row['EventID'];
			}
			$this->debug("EventID : $eventid");
			$sql1 = "call sp_userevent_add('".$eventid."','".$_SESSION['userid']."',2)";			
			$this->debug($sql1);
			$result1 = $GLOBALS['db']->Query($sql1);
			if ($result1) {
				// ADD USER TO ROLE FOR EVENT
				$userrole = new UserEventRoles;
				//$userrole->SetVar("debug",true);				
				$userrole->SetVar("userid",$_SESSION['userid']);
				$userrole->SetVar("eventid",$eventid);
				$userrole->SetVar("roleid",2);
				$result2 = $userrole->Add();
				
				// GET INFO
				$this->SetParameters($eventid);
				// ALL GOOD!
				if ($result2) {
					$this->debug("Added event and role etc");
					$this->Errors(MessageCatalogue(15));
					return True;
				}
				else {
					$this->Debug("Failed to add user to role");					
				}
			}
		}
		$this->Errors(MessageCatalogue(16));
		return False;
	}
	public function OrgPrivCountEvents() {
		$orgid = $GLOBALS['user']->GetVar("organisationid");
		$sql = "call sp_problemstream_org_priv_count_events($orgid)";				
		$this->debug($sql);
		$result = $GLOBALS['db']->Query($sql);
		if ($result) {
			while ($row = $GLOBALS['db']->FetchArray($result)) {	
				return $row['total'];
			}
		}
		return 0;
	}
	public function Edit() {
		
		if (!$this->CheckVarsSet("edit")) {    			
			$this->Errors("Invalid Parameters");
			return false;
		}
		if (!$this->CheckVars($this->vartypes_edit)) {
			$this->Errors("Invalid Values");
			return false;
		}
		
		$sp_params = "";
		foreach ($this->params_edit as $p) {
		  $sp_params .= "'".$this->$p."',";
		}		
		$sp_params = substr($sp_params,0,-1);
		
		$sql = "call sp_eventmaster_edit($sp_params)";				
		//echo $sql;
		$result = $GLOBALS['db']->Query($sql);
		if ($result && $GLOBALS['db']->AffectedRows($result) > 0) {			
			$this->Errors(MessageCatalogue(33));
			return true;
		}
		$this->Errors(MessageCatalogue(34));		
		return false;
	}
	public function Delete() {
		
		if (!ISSET($this->eventid) || !IS_NUMERIC($this->eventid)) {
			$this->Errors("Invalid Event");
			return false;
		}
		$sql = "call sp_eventmaster_delete(".$this->eventid.",".$this->userid.")";		
		echo $sql;
		$result = $GLOBALS['db']->Query($sql);
		if ($result) {			
			$this->Errors(MessageCatalogue(31));
			return true;
		}
		else {
			$this->Errors(MessageCatalogue(32));
			return true;
		}
	}
	public function StatusUpdate() {
		if (!ISSET($this->eventid) || !IS_NUMERIC($this->eventid)) {
			$this->Errors("Invalid Event");
			return false;
		}		
		if (!ISSET($this->status) && ($this->status != "inprogress" || $this->status != "complete" || $this->status != "issues")) {
			$this->Errors("Invalid Event Status");
			return false;
		}		
		
		$sql = "call sp_event_status_update(".$this->eventid.", '".$this->status."', ".$_SESSION['userid'].")";		
		$result = $GLOBALS['db']->Query($sql);
		if ($result) {
			$this->Errors(MessageCatalogue(26));
			return true;			
		}
		else {
			$this->Errors(MessageCatalogue(27));
			return false;
		}
	}

	// CHECK VARS ALL SET FOR REQUIRED METHOD
	private function CheckVarsSet($method) {    
		if ($method == "add") {      
			//echo "ok";
			foreach ($this->params_add as $param) {
				if (!ISSET($this->$param)) {          
					$this->Errors("Parameter $param not set");
					return False;
				}
			}
		}
		elseif ($method == "edit") {      
			//echo "ok";
			foreach ($this->params_edit as $param) {
				if (!ISSET($this->$param)) {          
					$this->Errors("Parameter $param not set");
					return False;
				}
			}
		}
    return True;
  }
  // CHECK VAR TYPES
  private function CheckVars($v) {
	$count = 0;
    foreach ($v as $var=>$type) {
      if ($type == "a-z" && !preg_match("/^[\w\s]+$/",$this->$var)) {                
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
	  if ($type == "datetime" && !preg_match("/^\d\d\d\d-\d\d-\d\d \d\d:\d\d/",$this->$var)) {
        $this->Errors($var." needs to be an ISO format date curr val is ".$this->$var);
        return False;        
      }
	  if ($type == "yn") {
		if (!ISSET($this->$var) || $this->$var == "n") {
			$this->$var = "n";
		}
		else {
			$this->$var = "y";
		}
        //$this->Errors($var." needs to be yes or no, value is ".$this->$var);
        //return False;        
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
<?php
/** ensure this file is being included by a parent file */
defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );

require_once "usermaster.php";
require_once "messages.php";

class UserEvent {

	public $methods = array("add","edit","delete"); 

	function __construct() {
		/* SET CHECKING TO FALSE */
		$this->parameter_check=False;
		$this->params_add = array("email_address","userid","roleid");
		$this->vartypes = array("email_address"=>"email","userid"=>"numeric","roleid"=>"role");
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
	// NOT USED
	public function SetParameters($eventid,$userid) {

		/* CHECKS */
		if (!IS_NUMERIC($eventid)) { $this->debug("Invalid eventid"); $this->Errors("Invalid eventid"); return False; }
		if (!IS_NUMERIC($userid)) { $this->debug("Invalid userid"); $this->Errors("Invalid userid"); return False; }

		/* SET SOME COMMON VARIABLES */
		$this->eventid=$eventid;
		$this->userid=$userid;

		/* CALL THE INFORMATION METHOD */
		$this->Info();

		/* PARAMETER CHECK SUCCESSFUL */
		$this->parameter_check=True;

		return True;
	}
	// NOT USED
	private function Info() {
		$db=$GLOBALS['db'];
		$sql = "CALL sp_problemstream_userevent_browse_user_info(".$this->eventid.",".$this->userid.");";					
		$this->debug($sql);
		$result = $db->Query($sql);
		if ($result && $db->NumRows($result) > 0) {
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
		$this->debug("Add Method");
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
		$sp_params = substr($sp_params,0,-1);
		
		$pieces = explode(",",$sp_params);
		
		// GET THE USERID FROM THE EMAIL ADDRESS
		$userobj = new UserMaster;
		//$userobj->SetVar("debug",true);
		$email_address = str_replace("'","",$this->email_address);
		$userobj->SetVar("email_address",$email_address);
		$userid = $userobj->GetUserIDFromEmail();		
		$this->debug("User ID: $userid");
		if ($userid > 0) {
			// ADD TO THE ORG
			$this->debug("Adding user to the organisation");
			$sql = "CALL sp_core_organisation_user_add(".$GLOBALS['user']->GetVar("organisationid").",$userid,'y',2)";
			$this->debug($sql);
			$result = $GLOBALS['db']->Query($sql);
			if (!$result) {
				$this->debug("Failed to add to organisation master");
			}
			// USER ALREADY EXIST FOR THIS EVENT
			if ($this->UserExists($userid)) {
				$this->debug("User Exists");
				$this->Errors(MessageCatalogue(25));
				return true;
			}
			// WE CAN ADD USERS WITHOUT ASSIGNING THEM TO AN EVENT
			if (ISSET($this->eventid) && $this->eventid > 1) {
				$sql = "call sp_core_userevent_add(".$this->eventid.",".$userid.",".$this->roleid.")";
				$result = $GLOBALS['db']->Query($sql);
				if ($result) {			
					if ($result) {
								
						// ADD HISTORY VIA MESSAGES
						$this->Debug("Starting history via messages");
						$messages = new Messages;
						$messages->SetVar("message","$email_address added");
						$messages->SetVar("eventid",$this->eventid);
						$messages->SetVar("taskid",0);
						$messages->SetVar("messagetype","adduser");
						$this->Debug($messages->ShowErrors());
						$messages->Add();
						$this->Debug("End messages");
						$this->Errors(MessageCatalogue(23));
						return true;
					}
				}
			}
		}
		else {
			$this->debug("Need to add this user to the system");
			$password = md5(microtime());
			$password = substr($password,0,5);
			$userobj->SetVar("userlogin",$email_address);
			$userobj->SetVar("password",$password);
			$userobj->SetVar("fullname",$this->name);
			$userobj->SetVar("register_extra",Chr(13)."Your temporary password is: ".$password.Chr(13));
			$result = $userobj->Add();
			if (!$result) {
				$this->debug($userobj->ShowErrors());
			}
			// ADD USER TO EVENT
			// WE CAN ADD USERS WITHOUT ASSIGNING THEM TO AN EVENT
			if (ISSET($this->eventid) && $this->eventid > 1) {
				$userid = $userobj->GetVar("userid");
				$sql = "call sp_problemstream_userevent_add(".$this->eventid.",".$userid.",".$this->roleid.")";
				$this->debug($sql);
				$result = $GLOBALS['db']->Query($sql);
				if (!$result) {
					$this->debug(mysql_error());
					$this->Errors(MessageCatalogue(43));
					return False;
				}
					
				// ADD HISTORY VIA MESSAGES
				$this->Debug("Starting history via messages");
				$messages = new Messages;
				$messages->SetVar("message","$email_address added");
				$messages->SetVar("eventid",$this->eventid);
				$messages->SetVar("taskid",0);
				$messages->SetVar("messagetype","adduser");
				$this->Debug($messages->ShowErrors());
				$messages->Add();
				$this->Debug("End messages");
			}
			else {
				$this->debug("No event ID, therefore do not add user to any event");
			}
			// ADD USER ROLE			
			//$userobj->AddRole($this->roleid);
			$this->debug("End of add method");
			$this->Errors(MessageCatalogue(38));
			return true;
		}
		return MessageCatalogue(24);
	}	
	private function UserExists($userid) {
		$db=$GLOBALS['db'];
		$sql = "SELECT * FROM problemstream_userevent WHERE UserID = $userid";					
		//echo $sql."<br>";
		$result = $db->Query($sql);
		if ($db->NumRows($result) > 0) {
			return True;
		}
		return False;
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
    return True;
  }
  // CHECK VAR TYPES
	private function CheckVars() {
		$count = 0;
		foreach ($this->vartypes as $var=>$type) {
		  if ($type == "numeric" && !IS_NUMERIC($this->$var)) {
			$this->Errors($this->$var." needs to be numeric");
			return False;        
		  }
		  if ($type == "email" && !preg_match("/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i",$this->$var)) {
			$this->Errors($var." needs to be an email address");
			return False;        
		  }
		  if ($type == "role") {
			if ($this->$var == "1" || $this->$var == "2" || $this->$var == "3") {
				// OK
			}
			else {
				$this->Errors($var." needs to be a valid role value is: ".$this->$var);
				return False;
			}
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
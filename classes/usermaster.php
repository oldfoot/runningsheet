<?php
/** ensure this file is being included by a parent file */
defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );
require_once $GLOBALS['dr']."classes/email.php";

class UserMaster {

	public $methods = array("add","edit","delete"); 

	function __construct() {
		/* SET CHECKING TO FALSE */
		$this->parameter_check=False;
		$this->params_add = array("userlogin","password","fullname");
		$this->vartypes_add = array("userlogin"=>"email","password"=>"any","fullname"=>"a-z");
		$this->params_edit = array("userid","fullname","timezone","contactdetails");
		$this->vartypes_edit = array("userid"=>"numeric","fullname"=>"a-z","timezone"=>"any","contactdetails"=>"any");
		$this->errors = "";
		$this->send_add_email = true;
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

	public function SetParameters($userid) {

		/* CHECKS */
		if (!IS_NUMERIC($userid)) { $this->Errors("Invalid user id"); return False; }

		/* SET SOME COMMON VARIABLES */
		$this->userid=$userid;

		/* CALL THE INFORMATION METHOD */
		$this->Info();
		$this->OrgPriv();

		/* PARAMETER CHECK SUCCESSFUL */
		$this->parameter_check=True;

		return True;
	}

	private function Info() {
		$db=$GLOBALS['db'];
		$sql="SELECT *
					FROM ".$GLOBALS['database_prefix']."usermaster
					WHERE UserID = '".$this->userid."'
					";
		$this->debug($sql);
		//echo $sql;
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
	public function OrgPriv() {
	
		$db=$GLOBALS['db'];
		// GET USER ORGID
		$sql = "SELECT OrganisationID FROM organisation_users WHERE UserID = ".$this->userid;
		$this->debug($sql);
		$result = $db->Query($sql);
		if ($db->NumRows($result) > 0) {
			while($row = $db->FetchArray($result)) {
				$this->organisationid = $row['OrganisationID'];
			}
		}
		// ORG PRIV
		
		$sql="CALL sp_org_priv('".$this->userid."')";					
		$this->debug($sql);
		$result = $db->Query($sql);
		if ($db->NumRows($result) > 0) {
			while($row = $db->FetchArray($result)) {
				$this->debug("Setting ".$row['Feature']." to ".$row['FeatureValue']);
				$this->$row['Feature'] = $row['FeatureValue'];
			}
		}
	}
	public function Add() {
		$this->debug("Add Method, usermaster object");
		if (!$this->CheckVarsSet("add")) {
			$this->debug("Failed to provide correct params");
			return false;
		}
		
		if (!$this->CheckVars($this->vartypes_add)) {
			$this->debug("Invalid data types");
			$this->Errors("Invalid data");
			return false;
		}		
		
		$sp_params = "";
		foreach ($this->params_add as $p) {
		  $sp_params .= "'".$this->$p."',";
		}
		$this->code = md5(microtime());
		$sp_params .= "'".$this->code."'";
		//$sp_params = substr($sp_params,0,-1);
		// DOES THS USER EXIST?
		if (!$this->Exists()) {
			$this->debug("User Exists");
			$sql = "call sp_usermaster_add($sp_params)";
			$this->debug($sql);
			$result = $GLOBALS['db']->Query($sql);
			if (!$result) {
				$this->Errors(MessageCatalogue(1));
				return false;				
			}
			else {
				while ($row = $GLOBALS['db']->FetchArray($result)) {
					$this->userid = $row['userid'];
				}
				// SEND REGISTER EMAIL
				$this->SendEmail();
				
				// RESPONSE - SUCCESS
				$this->Errors(MessageCatalogue(4));
				// CALL THE USER DETAILS IF REQUIRED				
				$this->Info();
				return true;
				
			}
		}
		else {
			$this->debug("User Exists");
			$this->Errors(MessageCatalogue(3));
			return false;
		}		
	}
	public function SendEmail() {
		if ($this->send_add_email) {
			$this->debug("Sending Register Email");
			$email = new email;
			$email->SetVar("to",$this->userlogin);
			
			$email->SetVar("subject",$GLOBALS['register_email_subject']);
			$body = str_replace("%username%",$this->fullname,$GLOBALS['register_email_body']);
			$body = str_replace("%code%",$this->code,$body);
			if (ISSET($this->register_extra)) {
				$body = str_replace("%extra%",$this->register_extra,$body);
			}
			else {
				$body = str_replace("%extra%","",$body);
			}
			$email->SetVar("body",$body);
			
			$email->SendEmail();
		}
	}	
	public function Edit() {
		$this->debug("Edit Method, usermaster object");
		if (!$this->CheckVarsSet("edit")) {
			$this->debug("Failed to provide correct params");
			return false;
		}
		
		if (!$this->CheckVars($this->vartypes_edit)) {
			$this->debug("Invalid data types");
			$this->Errors("Invalid data");
			return false;
		}
		
		$sp_params = "";
		foreach ($this->params_edit as $p) {
		  $sp_params .= "'".$this->$p."',";
		}		
		$sp_params = substr($sp_params,0,-1);
		// DOES THS USER EXIST?
		
		$sql = "call sp_usermaster_edit($sp_params)";
		$this->debug($sql);
		$result = $GLOBALS['db']->Query($sql);
		if ($result) {
			return MessageCatalogue(44);
		}
		else {
			return MessageCatalogue(45);
		}			
	}
	public function AddRole($roleid) {
		if (ISSET($this->userid)) {
			$sql = "call sp_userrole_add('".$this->userid."',$roleid)";	
			$result = $GLOBALS['db']->Query($sql);
			// ERROR
			if (!$result) {
				$this->Errors(MessageCatalogue(39));
				return false;
			}
			$this->Errors(MessageCatalogue(40));
			$this->Errors("User Role Added");
			return true;
		}
		$this->Errors(MessageCatalogue(41));
		return false;
	}
	public function Exists() {
		if (ISSET($this->userlogin)) {
			$sql = "call sp_usermaster_exists('".$this->userlogin."')";	
			$result = $GLOBALS['db']->Query($sql);
			// ERROR
			if (!$result) {
				return false;
			}
			$exists = false;
			// EMAIL ADDRESS EXISTS
			while ($row = $GLOBALS['db']->FetchArray($result)) {		
				if ($row['Total'] > 0) {
					return true;					
				}
			}
		}
		return false;
	}
	
	public function GetUserIDFromEmail() {
		if (ISSET($this->email_address)) {
			$sql = "SELECT UserID FROM usermaster WHERE UserLogin = '".$this->email_address."'";							
			$result = $GLOBALS['db']->Query($sql);
			// ERROR
			if (!$result) {
				return false;
			}			
			// EMAIL ADDRESS EXISTS
			while ($row = $GLOBALS['db']->FetchArray($result)) {		
				return $row['UserID'];
			}
		}
		return false;
	}
	public function GetIDFromAuthCode() {
		if (ISSET($this->api_auth_code)) {
			$sql = "SELECT UserID FROM usermaster WHERE APIAuthCode = '".$this->api_auth_code."'";
			$this->debug($sql);
			$result = $GLOBALS['db']->Query($sql);
			// ERROR
			if (!$result) {
				return false;
			}			
			// EMAIL ADDRESS EXISTS
			while ($row = $GLOBALS['db']->FetchArray($result)) {		
				return $row['UserID'];
			}
		}
		return false;
	}
	
	public function ChangePassword() {

		$db=$GLOBALS['db'];

		if (ISSET($this->userid) && ISSET($this->password)) {
			$sql="CALL sp_usermaster_pw_change(".$this->userid.",'".$this->password."')";
			$this->debug($sql);
			$result = $db->Query($sql);
			$this->debug("ok");
			if ($result) {
				$this->debug("ok, changed");
				$this->Errors(MessageCatalogue(46));
				return true;
			}
			else {
				$this->debug("failed to change: ".mysql_error());
				$this->Errors(MessageCatalogue(47));
				return false;
			}
		}
		else {
			$this->debug("values not set");
			$this->Errors(MessageCatalogue(48));
			return False;
		}
	}
	public function Delete() {

		$db=$GLOBALS['db'];

		if (ISSET($this->userid)) {
			$sql="CALL sp_usermaster_delete(".$this->userid.")";
			$this->debug($sql);
			$result = $db->Query($sql);
			$this->debug("ok");
			if ($result) {
				$this->debug("Account Deleted");
				$this->Errors(MessageCatalogue(49));
				return true;
			}
			else {
				$this->debug("failed to delete: ".mysql_error());
				$this->Errors(MessageCatalogue(50));
				return false;
			}
		}
		else {
			$this->debug("values not set");
			$this->Errors(MessageCatalogue(51));
			return False;
		}
	}

	// CHECK VARS ALL SET FOR REQUIRED METHOD
	private function CheckVarsSet($method) {    
		if ($method == "add") {			
			foreach ($this->params_add as $param) {
				if (!ISSET($this->$param)) {
					$this->Errors("Parameter $param not set");
					return False;
				}
			}
		}
		if ($method == "edit") {
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
  private function CheckVars($vartypes) {
	$count = 0;
    foreach ($vartypes as $var=>$type) {	
      if ($type == "a-z" && !preg_match("/^[A-Z0-9._%+-@ ]*$/i",$this->$var)) {                
        $this->Errors($var." needs to contain alpha characters only");
		$this->debug($var." needs to be $type, you provided: ".$this->$var);
        return False;
      }      
      if ($type == "numeric" && !IS_NUMERIC($this->$var)) {
        $this->Errors($this->$var." needs to be numeric");
		$this->debug($var." needs to be $type, you provided: ".$this->$var);
        return False;        
      }
	  if ($type == "email" && !preg_match("/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i",$this->$var)) {
        $this->Errors($var." needs to be an email address, you provided: ".$this->$var);
		$this->debug($var." needs to be $type, you provided: ".$this->$var);
        return False;        
      }
	  // for any
	  return true;
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
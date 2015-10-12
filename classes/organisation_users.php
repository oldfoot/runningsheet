<?php
/** ensure this file is being included by a parent file */
defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );

class OrganisationUsers {

	function __construct() {
		/* SET CHECKING TO FALSE */
		$this->parameter_check=False;		
		$this->debug = false;
		$this->errors = "";
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
		$v = strtolower($v);
		$this->$v = $val;
	}

	
	public function Approve() {
		$this->debug("approved: ".$this->approved." user: ".$this->id." organisationid: ".$this->organisationid);
		if (ISSET($this->approved) && ISSET($this->id) && ISSET($this->organisationid) && IS_NUMERIC($this->organisationid)) {
			if ($this->approved == "y") {
				$approved = "y";
			}
			else {
				$approved = "n";
			}
			$user = new Usermaster;
			$user->SetVar("email_address",$this->id);
			$userid = $user->GetUserIDFromEmail();
			
			$sql = "call sp_org_user_approve('".$this->organisationid."','".$approved."',".$userid.")";
			$this->debug($sql);
			$result = $GLOBALS['db']->Query($sql);
			// EXISTS
			if ($result && $GLOBALS['db']->AffectedRows($result) > 0) {
				$this->debug("Change was successful, rows affected");
				$this->Errors("OK");
				return True;
			}
			else {
				$this->Errors("NO Changes");
				return False;
			}
		}
		$this->debug("Invalid variables or values");
		return false;
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
			file_put_contents("C:/xampp/htdocs/runningsheet/ajax/jqgrid_edit_orgusers.log",$msg,FILE_APPEND);
		}
	}
}
?>
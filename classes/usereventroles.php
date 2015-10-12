<?php
/** ensure this file is being included by a parent file */
defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );

class UserEventRoles {

	function __construct() {
		/* SET CHECKING TO FALSE */
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
	
	public function Add() {
		$this->debug("Adding now");
		if (!ISSET($this->userid) || !IS_NUMERIC($this->userid) || !ISSET($this->eventid) || !IS_NUMERIC($this->eventid) || !ISSET($this->roleid) || !IS_NUMERIC($this->roleid)) {    			
			$this->debug("Invalid data types or params not set");
			$this->Errors("Invalid Parameters on add");
			return false;
		}
								
		$sql = "call sp_usereventroles_add(".$this->userid.",".$this->eventid.",".$this->roleid.")";				
		$this->Debug($sql);
		$result = $GLOBALS['db']->Query($sql);
		if ($result && $GLOBALS['db']->AffectedRows($result) > 0) {
			$this->Debug("Added User To Event Role");
			return true;			
		}
		$this->Debug("Failed to add User To Event Role");
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
		}
	}
}
?>
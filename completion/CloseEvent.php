<?php
/** ensure this file is being included by a parent file */
defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );
require_once $GLOBALS['dr']."classes/eventmaster.php";

class CloseEvent {
	function __construct() {
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
	
	public function Start() {		
		if (ISSET($this->eventid)) {
			$obj_event = new EventMaster;
			$obj_event->SetVar("eventid",$this->eventid);
			$obj_event->SetVar("status","complete");
			$obj_event->StatusUpdate();
		}		
		return true;
	}
		
	function Errors($err) {
		$this->errors.=$err."\n";
	}

	function ShowErrors() {
		return $this->errors;
	}
}
?>
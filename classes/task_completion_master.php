<?php
/** ensure this file is being included by a parent file */
defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );

class TaskCompletionMaster {
	
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

	public function GetIDFromName() {
		if (ISSET($this->name)) {
			$sql = "SELECT CompletionID FROM task_completion_master WHERE Name = '".$this->name."'";
			$result = $GLOBALS['db']->Query($sql);					
			// EMAIL ADDRESS EXISTS
			while ($row = $GLOBALS['db']->FetchArray($result)) {		
				return $row['CompletionID'];
			}
		}
		return false;
	}

	function Errors($err) {
		$this->errors.=$err."\n";
	}

	function ShowErrors() {
		return $this->errors;
	}
}
?>
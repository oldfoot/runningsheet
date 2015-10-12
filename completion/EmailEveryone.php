<?php
/** ensure this file is being included by a parent file */
defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );
require_once $GLOBALS['dr']."classes/email.php";

class EmailEveryone {
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
		$this->$v = $val;
	}
	
	public function Start() {		
		if (ISSET($this->taskid) && ISSET($this->taskname) && ISSET($this->taskstatus)) {			
			$sql = "CALL sp_usertasks(".$this->taskid.")";
			$result = $GLOBALS['db']->Query($sql);
			if ($result) {
				while ($row = $GLOBALS['db']->FetchArray($result)) {
					$this->SendEmail($row['UserLogin']);					
				}
			}
		}
		return true;
	}
	private function SendEmail($to) {
		$email = new email;
		$email->SetVar("to",$to);
		
		$email->SetVar("subject",$GLOBALS['email_task_complete_subject']);
		$body = str_replace("%taskname%",$this->taskname,$GLOBALS['email_task_complete_body']);
		$body = str_replace("%statusname%",$this->taskstatus,$body);
		$email->SetVar("body",$body);
		//echo "$body being sent to $to <br />";
		$email->SendEmail();
		
		return MessageCatalogue(4);
	}	
	function Errors($err) {
		$this->errors.=$err."\n";
	}

	function ShowErrors() {
		return $this->errors;
	}
}
?>
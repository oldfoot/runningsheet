<?php
/** ensure this file is being included by a parent file */
defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );
require_once $GLOBALS['dr']."classes/email.php";

class EmailNext {
	function __construct() {
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
	
	public function Start() {
		$this->debug("Starting method in EmailNext class");	
		if (ISSET($this->taskid)) {			
			$sql = "CALL sp_taskdependencies(".$this->taskid.")";
			$this->debug($sql);
			$result = $GLOBALS['db']->Query($sql);
			if ($result) {
				while ($row = $GLOBALS['db']->FetchArray($result)) {
					// THIS IS A DEPENDENCY, THEREFORE WE NEED TO SET THIS TO THE NEXT TASK
					$this->taskname = $row['TaskName'];
					$this->dependencytaskid = $row['TaskID'];
					$this->fullname = $row['FullName'];
					
					// GET THE STATUS - IF WE ARE IN A COMPLETE STATUS WE NEED TO HAVE A DIFFERENT EMAIL
					$sql2 = "CALL sp_task_browse_id(".$this->taskid.",".$_SESSION['userid'].")";
					$this->debug($sql2);
					$result2 = $GLOBALS['db']->Query($sql2);
					if ($result2) {
						while ($row2 = $GLOBALS['db']->FetchArray($result2)) {
							$status = $row2['Status'];
							$this->debug("Status of task is: $status");
						}
					}					
					
					$sql1 = "CALL sp_task_browse_resources(".$this->dependencytaskid.",".$_SESSION['userid'].")";
					$this->debug($sql1);
					$result1 = $GLOBALS['db']->Query($sql1);
					if ($result1) {
						while ($row1 = $GLOBALS['db']->FetchArray($result1)) {
							$this->SendEmail($row1['UserLogin'],$status);					
						}
					}				
				}
			}
		}
		return true;
	}
	private function SendEmail($to,$status) {
		$email = new email;
		$email->SetVar("to",$to);
		
		if ($status == "complete") {
			$subject = $GLOBALS['email_task_next_subject'];
			$body = $GLOBALS['email_task_next_body'];
		}
		else {
			$subject = $GLOBALS['email_task_complete_subject'];
			$body = $GLOBALS['email_task_complete_body'];
		}
		
		$email->SetVar("subject",$subject);
		$body = str_replace("%taskname%",$this->taskname,$body);
		$body = str_replace("%statusname%",$this->taskstatus,$body);
		$body = str_replace("%fullname%",$this->fullname,$body);
		$email->SetVar("body",$body);
		//echo "$body being sent to $to <br />";
		$email->SendEmail();
		
		return true;
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
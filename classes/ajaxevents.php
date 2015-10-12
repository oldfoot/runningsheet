<?php
/** ensure this file is being included by a parent file */
defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );

class AjaxEvents {
	
	function __construct() {		
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

	public function Get() {
		if (!ISSET($this->eventid) || !IS_NUMERIC($this->eventid)) {
			$this->Errors(MessageCatalogue(42));
			return False;
		}
		
		$db=$GLOBALS['db'];
		$dt = "0000-00-00 00:00:00";
		if (ISSET($_SESSION['latestdate'])) {
			$dt = $_SESSION['latestdate'];
		}
		$sql = "SELECT AjaxEvent, DateTimeEvent
				FROM ajaxevents
				WHERE DateTimeEvent > '$dt'
				AND EventID = ".$this->eventid."
				ORDER BY DateTimeEvent";
		$this->debug($sql);
		$result = $db->Query($sql);
		if ($db->NumRows($result) > 0) {
			$latestdate = "";
			$str = "";
			//echo "<script language='Javascript'>\n";
			while($row = $db->FetchArray($result)) {
				$latestdate = $row['DateTimeEvent'];
				if ($row['AjaxEvent'] == "events") {
					$this->debug("Refresh Events at time: ".$row['DateTimeEvent']);
				}
				elseif ($row['AjaxEvent'] == "tasks") {
					//echo "AjaxLoadTasks('".$this->eventid."','');";
					$str .= "events,";
					$this->debug("Refresh Events at time: ".$row['DateTimeEvent']);
				}
			}
			echo substr($str,0,-1);
			//echo "</script>\n";
			$this->debug("Setting session latestdate to: $latestdate");
			$_SESSION['latestdate'] = $latestdate; // ALWAYS THE HIGHEST
		}
		else {
			return False;
		}
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
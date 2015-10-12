<?php
/*
define( '_VALID_DIR_',1 ); // or die( 'Direct Access to this location is not allowed.' );

USAGE
require "../config.php";

$obj = new JQPlotOdometer;
$obj->SetVar("eventid",1);
echo $obj->Get();
*/
class JQPlotOdometer {
    public function __construct() {
        $this->output = 0;
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
		$this->$v = trim($val);
	}
	public function Get() {
		if (!ISSET($this->eventid) || !IS_NUMERIC($this->eventid)) {
			$this->debug("invalid eventid, not numeric or not set");
			$this->Errors("Invalid Event");
			return False;
		}
		
		$db = $GLOBALS['db'];
		$sql = "CALL sp_event_chart_odo_perc(".$this->eventid.",".$_SESSION['userid'].")";		
		$this->debug($sql);
		$result = $db->Query($sql);
		while ($row = $db->FetchArray($result)) {
			if ($row['actual'] < $row['required']) {
				return $this->output;
			}
			$diff = $row['actual'] - $row['required'];
			//echo $diff."<br />";
			$total = ($diff / $row['required']) *100;
			if ($total > 100) {
				return 100;
			}			
			return round($total,0);
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

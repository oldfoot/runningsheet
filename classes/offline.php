<?php
class offline {
	public function __construct() {
		$this->message_extra = "";
		$this->message = "";		
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
	public function Show() {
		$this->message .= "<div style='border:1px dotted #dedede;width:100%;text-align:center;font-size:xx-large;color:#999999'><img src='images/logo.gif'><br />Site temporarily unavailable.<br />".$this->message_extra."</div>\n";		
		return $this->message;		
	}
}
?>
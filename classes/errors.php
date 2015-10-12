<?php
class errors {
	public function __construct() {
		$this->errors = "";
		$this->alerts = "";
		$this->count_errors = 0;
		$this->count_alerts = 0;
	}
	public function SetError($err) {	
		$this->errors .= $err;
		$this->count_errors++;		
	}
	public function SetAlert($alert) {
		$this->alerts .= $alert;
		$this->count_alerts++;		
	}
	public function GetErrors() {
		return $this->errors;
	}
	public function GetALerts() {
		return $this->alerts;
	}
	public function ErrorCount() {
		return $this->count_errors;
	}
	public function AlertCount() {
		return $this->count_alerts;
	}
}
?>
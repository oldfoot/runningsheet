<?php
require_once $GLOBALS['dr']."pear/Mail.php";
//require_once "Mail/mime.php";

class email {
	public function __construct() {
		$this->to      = "";
		$this->subject = "";
		$this->body    = "";
	}
	public function SetVar($var,$val) {
		$this->$var = $val;	
	}
	public function GetVar($var) {
		if (ISSET($this->$var)) {
			return $this->$var;
		}
	}
	public function SendEmail() {
		if (!ISSET($this->to)) { $this->errors("Invalid Email"); return false; }
		if (!ISSET($this->subject)) { $this->errors("Invalid Subject"); return false; }
		if (!ISSET($this->body)) { $this->errors("Invalid Body"); return false; }
				 
		$headers = array ('From' => $GLOBALS['register_email_from'],
						  'To' => $this->to,
						  'Subject' =>  $this->subject);
						   $smtp = Mail::factory('smtp',
						   array ('host' =>  $GLOBALS['smtp_server'],
						   'port' =>  $GLOBALS['smtp_port'],
						   'auth' =>  $GLOBALS['smtp_require_auth'],
						   'username' =>  $GLOBALS['smtp_user'],
						   'password' =>  $GLOBALS['smtp_password'],
						   'debug' => false)); 
		 if (ISSET($this->html)) {
			 $mime = new Mail_mime(PHP_EOL);
			// Setting the body of the email
			$mime->setTXTBody(strip_tags($this->body));
			$mime->setHTMLBody($this->body);
			$this->body = $mime->get();
		}

		$mail = $smtp->send($this->to, $headers, $this->body); //call send method
		if (PEAR::isError($mail)) {
			//echo("<p>" . $mail->getMessage() . "</p>");
			//log_error($txt['pearmail_error'] . ': ' . $errno . ' : ' . $errstr);
			$this->errors("Failed to send");
			return false;
		} 
		else {
			$this->errors("Sent successfully");
			return true;
		}
	}
	function Errors($err) {
		$this->errors.=$err."\n";
	}

	function ShowErrors() {
		return $this->errors;
	}
}
?>
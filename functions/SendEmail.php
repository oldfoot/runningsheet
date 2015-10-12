<?php
/*
	THIS IS A CLASS AND FUNCTION FOR SENDING EMAIL. YOU JUST USE THE TOP FUNCTION TO SEND EMAIL
*/
function SendEmail($from, $to, $subject, $body) {

	//$mySMTPserver = 'localhost';
	$mySMTPserver = $GLOBALS['smtp_server'];
	$mySMTPport = $GLOBALS['smtp_port'];
	$fun = new funWithSMTP;
	//$header.="Date: 27 Jan 2002 15:01:01 EDT\r\n";
	$header="From: $from\r\n";
	$header.="To: $to\r\n";
	$header.="Subject: $subject\r\n";
	//$header.="Date: Wed, 17 Apr 2002 20:52:23 -0400\r\n";
	//$header.="Content-Type: multipart/alternative; charset=\"iso-8859-1\"\r\n";
	$header.="MIME-Version: 1.0\r\n";
  	$header.="Content-type: text/html; charset=iso-8859-1\r\n";
	$header.="Content-Transfer-Encoding: 7bit\r\n";
	$header.="X-Priority: 3\r\n";
	/*
		WRAP EVERYTHING IN OUR TABLE FOR NICE FORMATTING
	*/

	$body=str_replace("\r\n","<br>",$body);

	//$body=DesignOne($body);
	//echo $body;
	$separator="\r\n";
	$terminator=".";
	$theEmailText = $header.$separator.$body.$separator.$terminator;
	if ($GLOBALS['mail_type'] == "mail") {
		mail($to,$subject,$body,$header);
	}
	else {

		$fun->open($mySMTPserver,$mySMTPport);
		$fun->send("HELO ".$GLOBALS['smtp_server']);
		if ($GLOBALS['smtp_require_auth'] == "y") {
			$fun->send("AUTH LOGIN");
			$fun->send(base64_encode($GLOBALS['smtp_user']));
			$fun->send(base64_encode($GLOBALS['smtp_password']));
		}
		$fun->send("MAIL FROM:$from");
		$fun->send("RCPT TO:$to");
		$fun->send("DATA");
		$fun->send($theEmailText,False);
		$fun->send("RSET");
		$fun->send("QUIT");
		$fun->close();
		return $fun->returnlogging();
	}
	unset($fun);
}

/*
	DO NOT EDIT THIS CLASS
*/

class funWithSMTP{

	public $fp;

	function send($data,$log=True){
	  if ($log) { $this->logging($data); }
	  fputs($this->fp, $data."\r\n");
	  $this->recv();
	}

	function recv(){
	  $response=fgets($this->fp, 512);
	  list ($errno, $errmsg) = split (" ", $response);
	  if ($errno<500){
	    $this->logging($response);
	  }else{
	    $this->logging($response);
	    //exit;
	  }
	}

	function open($smtpserver,$ti=2){
	  //$this->fp = @fsockopen($smtpserver, $GLOBALS['smtp_port'], $errno, $errstr, $ti);
	  if (!$this->fp){
	    $this->logging("Error opening ".$smtpserver.Chr(13).$errstr ($errno));
	    //exit;
	  }
	  $this->recv();
	}

	function close(){
	  fclose($this->fp);
	}

	function logging($v) {
		//echo $v."<br>";
		$this->log.=Chr(13).$v.Chr(13);
		//echo $this->log."<br>";
	}

	function returnlogging() {
		return $this->log;
	}
}
?>
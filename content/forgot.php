<?php
/** ensure this file is being included by a parent file */
defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );
require_once $GLOBALS['dr']."classes/email.php";

class forgot {

	public function __construct() {
		$this->html = "";
	}	
	
	public function HTML() {
		if (ISSET($_POST['emailaddress'])) {
			$this->Process();
		}		
		$this->html .= "
			<div class='pad'>
				<div class='wrapper'>
					<article class='col1'><h2>Recover your password</h2></article>
				</div>
			</div>	
			<div style='width:600px;padding:20px 20px 20px 20px'>	
				<form id='ContactForm' action='index.php?content=forgot' method='post'>
					<div>	
						<div class='wrapper'>
							<div class='bg'><input class='input' type='text' name='emailaddress'></div>Email Address:
						</div>						
						<a href='#' class='button' onclick=\"document.getElementById('ContactForm').submit()\">Submit</a>
					</div>
				</form>
			</div>
			";
		return $this->html;
	}
	public function Process() {
		// SEND REGISTER EMAIL
		$code = md5(microtime());
		
		$sql = "call sp_usermaster_pw_code('".$_POST['emailaddress']."','".$code."')";			
		$result = $GLOBALS['db']->Query($sql);		
		if ($result) {
			$email = new email;
			$email->SetVar("to",$_POST['emailaddress']);
			
			$email->SetVar("subject",$GLOBALS['forgot_email_subject']);
			$body = str_replace("%code%",$code,$GLOBALS['forgot_email_body']);
			
			$email->SetVar("body",$body);
			
			$email->SendEmail();
			$msg = MessageCatalogue(11);
			$GLOBALS['errors']->SetAlert($msg);
		}
		else {
			$msg = MessageCatalogue(11);
		}
	}
}
?>
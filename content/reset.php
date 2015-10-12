<?php
/** ensure this file is being included by a parent file */
defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );
//require $GLOBALS['dr']."classes/email.php";

class reset {

	public function __construct() {
		$this->html = "";
	}	
	
	public function HTML() {
		if (ISSET($_POST['password']) && ISSET($_POST['code'])) {
			$this->Process();
		} 
		else {
			$this->html .= "
			<div class='pad'>
				<div class='wrapper'>
					<article class='col1'><h2>Reset your password</h2></article>
				</div>
			</div>	
			<div style='width:600px;padding:20px 20px 20px 20px'>	
				<form id='ContactForm' action='index.php?content=reset' method='post'>
					<div>	
						<div class='wrapper'>
							<div class='bg'><input class='input' type='password' name='password'></div>Password:
						</div>
						<input type='hidden' name='code' value='".GetSafeVar("get","code")."'>
						<a href='#' class='button' onclick=\"document.getElementById('ContactForm').submit()\">Change</a>
					</div>
				</form>
			</div>
			";
			return $this->html;
		}
	}
	public function Process() {
		$c = "";		
		$sql = "call sp_usermaster_pw_reset('".$_POST['code']."','".$_POST['password']."')";	
		$result = $GLOBALS['db']->Query($sql);
		// AUTH SUCCESSFUL
		if ($result) {
			$err = MessageCatalogue(12);			
		}			
		else {
			$err = MessageCatalogue(12);
		}
		$GLOBALS['errors']->SetAlert($err);
	}
}
?>
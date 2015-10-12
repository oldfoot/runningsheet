<?php
require_once $GLOBALS['dr']."classes/organisation_master.php";
require_once $GLOBALS['dr']."classes/userroles.php";

class signup {

	public function __construct() {
		$this->html = "";
		$this->debug = false;
	}	
	
	public function HTML() {
		if (ISSET($_POST['fullname']) && ISSET($_POST['password']) && ISSET($_POST['emailaddress']) && ISSET($_POST['organisation'])) {
			$this->Process();
		}
		$this->html .= "
			<div class='pad'>
				<div class='wrapper'>
					<article class='col1'><h2>Enter Your Details</h2></article>
				</div>
			</div>	
			<div style='width:600px;padding:20px 20px 20px 20px'>	
				<form id='ContactForm' action='index.php?content=signup' method='post'>
					<div>
						<div class='wrapper'>
							<div class='bg'><input class='input' type='text' name='fullname'></div>Name:
						</div>
						
						<div class='wrapper'>
							<div class='bg'><input class='input' type='text' name='emailaddress'></div>Email Address:
						</div>

						<div class='wrapper'>
							<div class='bg'><input class='input' type='password' name='password'></div>Password:
						</div>
						
						<div class='wrapper'>
							<div class='bg'><input class='input' type='text' name='organisation'></div>Group / Organisation:
						</div>
						<a href='#' class='button' onclick=\"document.getElementById('ContactForm').submit()\">Signup</a>
					</div>
				</form>
			</div>
			";
		return $this->html;
	}
	public function Process() {
		$this->debug("Processing now...");
		$c = "";
		require_once $GLOBALS['dr']."classes/usermaster.php";
		$um = new UserMaster;
		$um->SetVar("fullname",$_POST['fullname']);
		$um->SetVar("password",$_POST['password']);
		$um->SetVar("userlogin",$_POST['emailaddress']);		
		$result = $um->Add();
		if (!$result) {
			$GLOBALS['errors']->SetAlert($um->ShowErrors());
		}
		else {
			$userid = $um->GetVar("userid");
			$this->debug("Add user to role for event");
			// ADD USER TO ROLE FOR EVENT
			$userrole = new UserRoles;
			//$userrole->SetVar("debug",true);				
			$userrole->SetVar("userid",$userid);			
			$userrole->SetVar("roleid",2);
			$result2 = $userrole->Add();
			$this->debug("Add user to role for event - SUCCESS");
			if (!$result2) {
				$this->debug("User Role Added Failed");
				$GLOBALS['errors']->SetAlert($userrole->ShowErrors());
				return False;
			}
			$this->debug("Add user to organisation");
			$om = new OrganisationMaster;
			//$om->SetVar("debug",true);
			$om->SetVar("organisation",$_POST['organisation']);
			$om->SetVar("userid",$userid);
			
			$result = $om->Add();
			if (!$result) {				
				$GLOBALS['errors']->SetAlert($om->ShowErrors());
				return False;
			}
			else {
				$GLOBALS['errors']->SetAlert($om->ShowErrors());
				return True;
			}
		}
	}
	private function debug($msg) {
		if ($this->debug) {
			echo $msg."<br />\n";
		}
	}
}
?>
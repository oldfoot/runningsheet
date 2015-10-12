<?php
//require_once $GLOBALS['dr']."classes/organisation_master.php";

class login {

	public function __construct() {
		$this->html = "";
		$this->errors = "";
		$this->debug = false;
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
	public function HTML() {
		if (ISSET($_POST['userlogin']) && ISSET($_POST['password'])) {
			$result = $this->Process();
			if ($result) {
				header("Location: app.php");
			}
			else {
				$err = $this->ShowErrors();
				$GLOBALS['errors']->SetAlert($err);
			}				
		}
		if (ISSET($_GET['code'])) {
			$result = $this->Activate();			
			$err = $this->ShowErrors();
			$GLOBALS['errors']->SetAlert($err);
		}
		$this->html .= "
			<div class='pad'>
				<div class='wrapper'>
					<article class='col1'><h2>Enter Your Email & Password</h2></article>
				</div>
			</div>	
			<div style='width:600px;padding:20px 20px 20px 20px'>	
				<form id='ContactForm' action='index.php?content=login' method='post'>
					<div>
						<div class='wrapper'>
							<div class='bg'><input class='input' type='text' name='userlogin'></div>Email:
						</div>

						<div class='wrapper'>
							<div class='bg'><input class='input' type='password' name='password'></div>Password:
						</div>
						<a href='#' class='button' onclick=\"document.location.href='index.php?content=forgot'\">Lost Password</a>
						<a href='#' class='button' onclick=\"document.getElementById('ContactForm').submit()\">Login</a>						
					</div>
				</form>
			</div>
			";
		return $this->html;
	}
	public function Process() {
		$c = "";		
		$sql = "call sp_userauth('".$_POST['userlogin']."','".$_POST['password']."')";	
		$this->debug($sql);
		$result = $GLOBALS['db']->Query($sql);
		// AUTH SUCCESSFUL
		if ($result) {
			$this->debug("SQL executed ok");
			while ($row = $GLOBALS['db']->FetchArray($result)) {
				$this->debug("Found a user");
				if ($row['Activated'] == "y") {
					$this->debug("User Activated");
					$_SESSION['userid'] = $row['UserID'];
					//$GLOBALS['errors']->SetAlert($_SESSION['userid']);
					return True;
					//$c .= MessageCatalogue(8);
				}
				else {
					$this->debug("Not activated");
					$error = MessageCatalogue(10);
					$this->Errors($error);
					return false;					
				}
			}
		}
		$this->debug("SQL Error? Returning False;");
		$error = MessageCatalogue(9);
		$this->Errors($error);
		return false;		
	}
	public function Activate() {
		$sql = "call sp_usermaster_activate('".$_GET['code']."')";	
		$result = $GLOBALS['db']->Query($sql);
		// ERROR
		if (!$result) {			
			$this->Errors(MessageCatalogue(7));
			return False;		
		}
		else {
			$this->Errors(MessageCatalogue(6));
			//while ($row = $GLOBALS['db']->FetchArray($result)) {
				//$userid = $row['UserID'];
				//$emailaddress = $row['UserLogin'];
			//}
			//$om = new OrganisationMaster;
			//$om->SetVar("debug",true);
			//$om->SetVar("userid",$userid);
			//$om->SetVar("emailaddress",$emailaddress);			
			//$result = $om->Add();
			//if (!$result) {
				//$this->Errors($om->ShowErrors());
				//return False;		
			//}
			return True;
		}		
	}	
	private function Errors($err) {
		//echo $err."<br />";
		$this->errors .= $err."<br />\n";    
	}
	public function ShowErrors(){ 
		return $this->errors;
	}
	private function debug($msg) {
		if ($this->debug) {
			file_put_contents("login.php.log",$msg."\n",FILE_APPEND);
			echo $msg."<br />\n";
		}
	}
}
?>
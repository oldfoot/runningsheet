<?php
require_once $GLOBALS['dr']."classes/usermaster.php";

class account {

	public function __construct() {
		$this->html = "";
	}	
	
	public function HTML() {
		// LOGGED IN?
		if (!ISSET($_SESSION['userid'])) {
			header("Location: login.php");
		}
		// PROCESS DELETION
		if (ISSET($_GET['delete'])) {
			$um = new UserMaster;
			$um->SetVar("userid",$_SESSION['userid']);
			$result = $um->Delete();
			$GLOBALS['errors']->SetAlert($um->ShowErrors());
			header("location: logout.php");			
		}
		// PROCESS EDITING AND PASSWORD CHANGE
		if (ISSET($_POST['fullname'])) {
			//echo "ok";
			$um = new UserMaster;
			//$um->SetVar("debug",true);
			$um->SetVar("fullname",$_POST['fullname']);
			$um->SetVar("timezone",$_POST['timezone']);
			$um->SetVar("contactdetails",$_POST['contactdetails']);
			//$um->SetVar("password",$_POST['password']);
			
			$um->SetVar("userid",$_SESSION['userid']);
			$result = $um->Edit();
			if (!$result) {
				$GLOBALS['errors']->SetAlert($um->ShowErrors());
			}
			else {
				if (ISSET($_POST['password'])) {
					$um->SetVar("password",$_POST['password']);
					$result_ch_pw = $um->ChangePassword();					
				}
				$GLOBALS['errors']->SetAlert($result);
			}				
		}
		// INSTANTIATE THE USER OBJECT
		$um = new UserMaster;
		$um->SetParameters($_SESSION['userid']);
		$fullname = $um->GetVar("FullName");
		$timezone = $um->GetVar("Timezone");
		$userlogin = $um->GetVar("UserLogin");
		$contactdetails = $um->GetVar("ContactDetails");
		
		$options = "<option value='GMT'>GMT</option>\n";
		$sql = "CALL sp_timezones_browse()";
		//echo $sql;
		$result = $GLOBALS['db']->Query($sql);
		while ($row =  $GLOBALS['db']->FetchArray($result)) {
			if ($timezone == $row['Name']) { $selected = "selected"; } else { $selected = ""; }
			$options .= "<option value='".$row['Name']."' $selected>".$row['Name']."</option>\n";
		}
		
		$this->html .= "
			<div class='pad'>
				<div class='wrapper'>
					<article class='col1'><h2>My Account</h2></article>
				</div>
			</div>	
			<div style='width:600px;padding:20px 20px 20px 20px'>	
				<form id='ContactForm' action='console.php?content=account' method='post'>
					<div>
						<h3>Editable Information</h3>
						<div class='wrapper'>
							<div class='bg'><input class='input' type='text' name='fullname' value='$fullname'></div>Name:
						</div>
						<div class='wrapper'>
							<div class='bg'><input class='input' type='password' name='password'></div>Change Password:
						</div>
						<div class='wrapper'>
							<div class='bg'><select name='timezone'>$options</select></div>Timezone:
						</div>
						<div class='wrapper'>
							<div class='bg'><input class='input' type='text' name='contactdetails' value='$contactdetails'></div>Contact Details:
						</div>
						<a href='#' class='button' onclick=\"document.getElementById('ContactForm').submit()\">Update</a>
						<div class='wrapper'>
							<article class='col1'><h2>Non-editable Information</h2></article>
						</div>
						<div class='wrapper'>
							Email Address: $userlogin
						</div>
						<div class='wrapper'>
							<article class='col1'><h2>Account Removal</h2></article>
						</div>
						You're free at any time to remove your account. This involves deactivating your account and then removing
						all events and tasks from your account.<a href='#' class='button' onclick=\"document.location.href='console.php?content=account&delete=y'\">Delete</a>
					</div>
				</form>
			</div>
			";
		return $this->html;
	}
	public function Process() {
		$c = "";
		
		$um = new UserMaster;
		
	}
}
?>
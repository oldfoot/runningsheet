<?php
//require_once $GLOBALS['dr']."classes/organisation_master.php";

class contact {

	public function __construct() {
		$this->html = "";
		$this->errors = "";
	}	
	
	public function HTML() {
		if (ISSET($_POST['feedback'])) {
			$result = $this->Process();
			if ($result) {
				$err = $this->ShowErrors();
				$GLOBALS['errors']->SetAlert($err);
			}
			else {
				$err = $this->ShowErrors();
				$GLOBALS['errors']->SetAlert($err);
			}				
		}		
		$this->html .= "
			<div class='pad'>
				<div class='wrapper'>
					<article><h2>We'd love to hear if you think this service is useful</h2></article>
				</div>
			</div>	
			<div style='width:600px;padding:20px 20px 20px 20px'>	
				<form id='ContactForm' action='index.php?content=contact' method='post'>
					<div>
						<div class='wrapper'>
							<div><textarea name='feedback' style='border:1px #999999 solid'></textarea></div>
						</div>												
						<input type='submit' value='Submit Feedback'>
					</div>
				</form>
			</div>
			";
		return $this->html;
	}
	public function Process() {
		$c = "";		
		$sql = "call sp_contact_insert('".GetSafeVar("post","feedback")."')";	
		//echo $sql;
		$result = $GLOBALS['db']->Query($sql);
		// SUCCESS
		if ($result) {			
			$error = MessageCatalogue(60);
			$this->Errors($error);
			return True;
		}			
		// FAILED
		$error = MessageCatalogue(61);
		$this->Errors($error);
		return false;		
	}	
	private function Errors($err) {
		//echo $err."<br />";
		$this->errors .= $err."<br />\n";    
	}
	public function ShowErrors(){ 
		return $this->errors;
	}
}
?>
<?php
//require_once $GLOBALS['dr']."classes/organisation_master.php";

class bugs {

	public function __construct() {
		$this->html = "";
		$this->errors = "";
	}	
	
	public function HTML() {
		if (ISSET($_POST['description'])) {
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
					<article><h2>Provide us with as many details please</h2></article>
				</div>
			</div>	
			<div style='width:600px;padding:20px 20px 20px 20px'>	
				<form id='BugForm' action='index.php?content=bugs' method='post'>
					<div>
						<div class='wrapper'>
							<div><textarea name='description' rows=15 cols=70 style='border:1px #999999 solid'></textarea></div>
						</div>												
						<input type='submit' value='Submit Bug'>
					</div>
				</form>
			</div>
			";
		return $this->html;
	}
	public function Process() {
		$c = "";		
		$sql = "call sp_bug_insert('".GetSafeVar("post","description")."')";	
		//echo $sql;
		$result = $GLOBALS['db']->Query($sql);
		// SUCCESS
		if ($result) {			
			$error = MessageCatalogue(62);
			$this->Errors($error);
			return True;
		}			
		// FAILED
		$error = MessageCatalogue(63);
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
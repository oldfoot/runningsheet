<?php
/** ensure this file is being included by a parent file */
defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );

class OrganisationMaster {

	function __construct() {
		/* SET CHECKING TO FALSE */
		$this->parameter_check=False;		
		$this->debug = false;
		$this->errors = "";
		$this->orgroleid = 2; // DEFAULT USER
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
		$this->$v = $val;
	}

	public function SetParameters($organisationid) {

		/* CHECKS */
		if (!IS_NUMERIC($organisationid)) { $this->Errors("Invalid organisationid"); return False; }

		/* SET SOME COMMON VARIABLES */
		$this->organisationid=$organisationid;

		/* CALL THE INFORMATION METHOD */
		$this->Info();

		/* PARAMETER CHECK SUCCESSFUL */
		$this->parameter_check=True;

		return True;
	}

	private function Info() {
		$db=$GLOBALS['db'];
		$sql = "CALL sp_organisation_browse_id(".$this->organisationid.",".$_SESSION['userid'].");";					
		$this->debug($sql);
		$result = $db->Query($sql);		
		if ($result && $db->NumRows($result) > 0) {			
			while($row = $db->FetchArray($result)) {
				/* HERE WE CALL THE FIELDS AND SET THEM INTO DYNAMIC VARIABLES */
				$arr_cols=$db->GetColumns($result);
				for ($i=1;$i<count($arr_cols);$i++) {
					$col_name=$arr_cols[$i];
					$this->$col_name=$row[$col_name];
				}
			}
		}
		else {
			return False;
		}
	}
	public function Add() {
		
		if (ISSET($this->organisation) && ISSET($this->userid)) {
			//$pieces = explode("@",$this->emailaddress);
			//$domain = $pieces[1];
			//$domain_pieces = explode(".",$domain);
			//$organisation = $domain_pieces[0];
			
			$sql = "call sp_organisation_exists('".$this->organisation."')";
			$this->debug($sql);
			$result = $GLOBALS['db']->Query($sql);
			// EXISTS
			if ($result && $GLOBALS['db']->NumRows($result) > 0) {				
				while ($row = $GLOBALS['db']->FetchArray($result)) {
					$sql = "call sp_organisation_user_add('".$row['OrganisationID']."',".$this->userid.",'n',".$this->orgroleid.")";
				}
			}
			else {
				$sql1 = "call sp_organisation_add('".$this->organisation."')";
				$this->debug($sql1);
				$result1 = $GLOBALS['db']->Query($sql1);
				while ($row1 = $GLOBALS['db']->FetchArray($result1)) {
					$organisation_id = $row1['OrganisationID'];
				}
				$sql = "call sp_organisation_user_add('".$organisation_id."',".$this->userid.",'y',1)";
			}
			$this->debug($sql);
			$result = $GLOBALS['db']->Query($sql);			
			if ($result && $GLOBALS['db']->AffectedRows($result) > 0) {	
				// SUCCESS				
				$this->Errors("Success");
				return True;
			}					
		}
		$this->Errors(MessageCatalogue(16));
		return false;
	}
	
	public function Delete() {
		
		if (!ISSET($this->userid) || !ISSET($this->organisationid) || !IS_NUMERIC($this->organisationid)) {
			$this->Errors("Invalid Data");
			return false;
		}
		$sql = "call sp_organisation_user_del(".$this->organisationid.",".$this->userid.")";		
		echo $sql;
		$result = $GLOBALS['db']->Query($sql);
		if ($result) {
			return MessageCatalogue(52);
		}
		else {
			return MessageCatalogue(53);
		}
	}	
	function Errors($err) {
		$this->errors.=$err."\n";
	}

	function ShowErrors() {
		return $this->errors;
	}
	
	private function debug($msg) {
		if ($this->debug) {
			echo $msg."<br />\n";
		}
	}
}
?>
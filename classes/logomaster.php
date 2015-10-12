<?php
/** ensure this file is being included by a parent file */
defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );

class LogoMaster {

	function __construct() {
		$this->errors = "";				
		$this->debug  = false;
		$this->eventid = 0;
		$this->taskid  = 0;
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
	public function Info() {
		if (!ISSET($this->organisationid) || !ISSET($this->userid)) {
			$this->debug("No org ID");
			$this->Errors("Invalid Document");
			return False;
		}
		$db=$GLOBALS['db'];
		$sql = "CALL sp_logo_browse_id(".$this->organisationid.",".$this->userid.");";					
		$this->debug($sql);
		$result = $db->Query($sql);
		if ($db->NumRows($result) > 0) {
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
			$this->debug("No document");
			$this->Errors("No Document");
			return False;
		}
		return True;
	}
	
	public function Add() {
		//file_put_contents("debug_logomaster.log","here\n",FILE_APPEND);
		if (!ISSET($this->filename) || !ISSET($this->filetype) || !ISSET($this->filesize) || !ISSET($this->organisationid) || !ISSET($this->attachment)) {
			$this->debug("Variables not set");
			$this->Errors("Invalid data");
			return false;
		}
		$this->attachment = addslashes($this->attachment);			
		$sql = "CALL sp_logo_add($this->filesize,'".$this->filetype."','".$this->filename."','".$this->attachment."','".$this->organisationid."')";
		//$this->debug("SQL: $sql");
		file_put_contents("debug_logomaster.log",$sql."\n",FILE_APPEND);
		$result = $GLOBALS['db']->Query($sql);
		if ($result) {
			$this->debug("Added successfully");
			$this->Errors(MessageCatalogue(56));
			return true;
		}
		else {
			$this->debug("Add Failed");
			$this->Errors(MessageCatalogue(57));
			return true;		
		}		
	}
	public function Delete() {
		
		if (!ISSET($this->documentid) || !IS_NUMERIC($this->documentid) || !ISSET($this->eventid) || !IS_NUMERIC($this->eventid)) {
			$this->debug("Variables not set");
			$this->Errors("Invalid Data");
			return false;
		}
		$sql = "call sp_document_delete(".$this->documentid.",".$this->eventid.",".$this->userid.")";
		$this->debug($sql);
		$result = $GLOBALS['db']->Query($sql);
		if ($result) {
			$this->Errors(MessageCatalogue(58));
			return true;
		}
		else {
			$this->Errors(MessageCatalogue(59));
			return false;
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
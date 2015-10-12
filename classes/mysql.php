<?php
require "storedprocedure.php";
class mysql {
	function __construct() {
		$this->errors = "";
		$this->conn = "";
	}
	function Connect($hostname,$port,$database,$username,$password) { // DATABASE CONNECTION */
		$this->conn = mysql_connect($hostname.":".$port,$username,$password) or die ('Connection to database failed.'); // DATABASE CONNECTION
		if (!$this->conn) {
			$this->Error('Connection to database server at: '.$this->hostname.' failed.');
		 	return false;
		}
		else {
			$sel_db=mysql_select_db($database); // SELECT THE DATABASE
			if (!$sel_db) die("unable to select db, does the database <b>".$database."</b> exist?") ;
			mysql_query("set autocommit=1"); // RUN IN AUTOCOMMIT MODE FOR INNODB TABLES
			return $this->conn;
		}
	}
	function pconnect() { // PERSISTENT CONNECTION
		$result = mysql_pconnect($this->hostname, $this->username, $this->password);

		if (!$result) {
			echo 'Connection to database server at: '.$this->hostname.' failed.';
			return false;
		}
		return $result;
	}
	function Query($query,$query_no="") { // THE METHOD TO EXECUTE QUERIES
	/*
	if (preg_match("/call/i",$query)) {
		$storedproc = new StoredProcedure;
		$storedproc->SetVar("query",$query);
		$storedproc->Process();
		$query = $storedproc->GetVar("result_query");
	}
	*/
  	$result = mysql_query($query) or die("Error in SQL:".mysql_error());
  	return $result;
  }
  function FetchArray($result) { // A METHOD TO RETURN THE RESULT AS AN ARRAY
  	return mysql_fetch_array($result);
  }
  function FetchAssoc($result) { // AN ALTERNATIVE METHOD TO RETURN AS AN ASSOCIATIVE ARRAY
  	return mysql_fetch_assoc($result);
  }
  function FetchRow($result) { // AN ALTERNATIVE METHOD TO RETURN ROWS
    $query = mysql_fetch_row($result);
    return $result;
  }
  function ReturnQueryNum() { // A METHOD TO RETURN THE QUERY NUMBER
    return $this->query_num;
  }
  function NumRows($result) { // A METHOD TO RETURN THE NUMBER OF ROWS IN A RESULT
  	return mysql_num_rows($result);
  }
  function AffectedRows() { // A METHOD TO DETERMINE HOW MANY ROWS WERE AFFECTED BY THE QUERY
  	return mysql_affected_rows();
  }
  function GetColumns($result) {
  	//return mysql_fetch_field($result, $i);
  	$i = 0;
  	//echo mysql_num_fields($result);
  	$fields_arr[]="";
		for ($i=0;$i<mysql_num_fields($result);$i++) {
    	$meta= mysql_fetch_field($result, $i);
    	array_push($fields_arr,$meta->name);
    }
    return $fields_arr;
  }
  function LastInsertId() { // A METHOD TO OBTAIN THE LAST INSERTED AUTOINCREMENT ID
  	return mysql_insert_id();
  }
  function NextResult() { // MYSQLi COMPATIBILITY
	return true;
	}
  function Begin() { // A METHOD TO START A TRANSACTION
  	mysql_query("set autocommit=0");
  }
  function commit() { // COMMIT
  	mysql_query("commit");
  }
  function Rollback() { // ROLLBACK
  	mysql_query("rollback");
  }
  function Error($err) {
  	$this->errors.=$err."<br />";
  }
  function ShowErrors() {
  	return $this->errors;
  }
}
?>
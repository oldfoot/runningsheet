<?php
//require "storedprocedure.php";
class mysql {
	function __construct() {
		$this->errors = "";
		$this->conn = "";
	}
	function Connect($hostname,$username,$password,$database,$port) { // DATABASE CONNECTION */
	
		$this->conn = @mysqli_connect($hostname,$username,$password,$database,$port);
		if (!$this->conn) {			
			return false;			
		}		
		else {
			$sel_db=mysqli_select_db($this->conn,$database); // SELECT THE DATABASE
			if (!$sel_db) die("unable to select db, does the database <b>".$database."</b> exist?") ;
			mysqli_query($this->conn,"set autocommit=1"); // RUN IN AUTOCOMMIT MODE FOR INNODB TABLES
			return $this->conn;
		}
	}
	function pconnect() { // PERSISTENT CONNECTION
		$result = mysqli_pconnect($this->hostname, $this->username, $this->password);

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
	$this->NextResult();
	//echo $query."<br />\n";
  	$result = mysqli_query($this->conn,$query); //or die(mysql_error());
	//if (!$result) { echo "Failed"; }
	//echo "ok";
	$this->Error(mysql_error());
  	return $result;
  }
  function FetchArray($result) { // A METHOD TO RETURN THE RESULT AS AN ARRAY
  	return mysqli_fetch_array($result);	
  }
  function FetchAssoc($result) { // AN ALTERNATIVE METHOD TO RETURN AS AN ASSOCIATIVE ARRAY
  	return mysqli_fetch_assoc($result);
  }
  function FetchRow($result) { // AN ALTERNATIVE METHOD TO RETURN ROWS
    $query = mysqli_fetch_row($result);
    return $result;
  }
  function ReturnQueryNum() { // A METHOD TO RETURN THE QUERY NUMBER
    return $this->query_num;
  }
  public function NumRows($result) { // A METHOD TO RETURN THE NUMBER OF ROWS IN A RESULT	
  	return mysqli_num_rows($result);
  }
  function AffectedRows($result) { // A METHOD TO DETERMINE HOW MANY ROWS WERE AFFECTED BY THE QUERY
  	return mysqli_affected_rows($this->conn);
  }
  function GetColumns($result) {
  	//return mysql_fetch_field($result, $i);
  	$i = 0;
  	//echo mysql_num_fields($result);
  	$fields_arr[]="";
		for ($i=0;$i<mysqli_num_fields($result);$i++) {
    	$meta= mysqli_fetch_field($result);
    	array_push($fields_arr,$meta->name);
    }
    return $fields_arr;
  }
  function LastInsertId() { // A METHOD TO OBTAIN THE LAST INSERTED AUTOINCREMENT ID
  	return mysqli_insert_id($this->conn);
  }
  function NextResult() {
	if (mysqli_more_results($this->conn)) {
		mysqli_next_result($this->conn);
	}
  }
  function Begin() { // A METHOD TO START A TRANSACTION
  	mysqli_query($this->conn,"set autocommit=0");
  }
  function Commit() { // COMMIT
  	mysqli_query($this->conn,"commit");
  }
  function Rollback() { // ROLLBACK
  	mysqli_query($this->conn,"rollback");
  }  
  function Error($err) {
  	$this->errors.=$err."<br />";
  }
  function ShowErrors() {
  	return $this->errors;
  }
}
?>
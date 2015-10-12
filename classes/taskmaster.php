<?php
/** ensure this file is being included by a parent file */
defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );

require_once "usermaster.php";
require_once "task_completion_master.php";
require_once "messages.php";

class TaskMaster {

	public $methods = array("add","edit","delete"); 

	function __construct() {
		/* SET CHECKING TO FALSE */
		$this->parameter_check=False;
		$this->params_add = array("taskname","description","eventid","taskdatetimestart","taskdatetimeend","userid"); // ,"resources","dependencies"
		$this->params_edit = array("taskid","eventid","taskname","description","taskdatetimestart","taskdatetimeend","userid");
		$this->vartypes_add = array("taskid"=>"integer","taskname"=>"a-z","description"=>"words","eventid"=>"integer","taskdatetimestart"=>"date","taskdatetimeend"=>"date","dependencies"=>"int-array","resources"=>"csv","dependencies"=>"csv","userid"=>"numeric");
		$this->vartypes_edit = array("eventid"=>"integer","taskname"=>"a-z","description"=>"words","taskdatetimestart"=>"date","taskdatetimeend"=>"date","dependencies"=>"int-array","resources"=>"csv","dependencies"=>"csv","userid"=>"numeric");
		$this->errors = "";
		$this->debug  = false;
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

	public function SetParameters($taskid) {

		/* CHECKS */
		if (!IS_NUMERIC($taskid)) { $this->Errors("Invalid TaskID"); return False; }

		/* SET SOME COMMON VARIABLES */
		$this->taskid=$taskid;

		/* CALL THE INFORMATION METHOD */
		$this->Info();

		/* PARAMETER CHECK SUCCESSFUL */
		$this->parameter_check=True;

		return True;
	}

	private function Info() {
		$this->debug("Entering Info Function");
		$db=$GLOBALS['db'];
		$sql = "CALL sp_task_browse_id(".$this->taskid.",".$_SESSION['userid'].");";					
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
		$this->debug("Add Method");
		
		$this->debug("Starting transaction");
		$GLOBALS['db']->Begin();
		
		if (!$this->CheckVarsSet("add")) {    			
			$this->Errors("Invalid Parameters");
			return false;
		}
		if (!$this->CheckVars($this->vartypes_add)) {
			$this->Errors("Invalid Values");
			return false;
		}
		
		// CHECK FEATURE LIMITS BASED ON ORG
		$limit = $GLOBALS['user']->GetVar("Tasks per Event");		
		$this->debug("Limit for this account: $limit");
		if ($limit != "Unlimited") {
			if ($this->OrgPrivCountTasks() >= $limit) {
				$this->debug("Max number of tasks reached: ".$this->OrgPrivCountTasks()." and limited to ".$GLOBALS['user']->GetVar("Tasks"));
				$this->Errors(MessageCatalogue(55));
				return False;
			}
		}
		
		$sql = "call sp_task_name_exists('".$this->eventid."','".$this->taskname."')";				
		$result = $GLOBALS['db']->Query($sql);
		if ($result) {
			if ($GLOBALS['db']->NumRows($result) > 0) {
				$this->Errors(MessageCatalogue(37));
				return false;
			}
		}
		
		$sp_params = "";
		foreach ($this->params_add as $p) {
		  $sp_params .= "'".$this->$p."',";
		}
		//$sp_params .= "'".$_SESSION['userid']."'";
		$sp_params = substr($sp_params,0,-1);
		
		$sql = "call sp_taskmaster_add($sp_params)";
		$this->debug("Add Task: $sql");
		$result = $GLOBALS['db']->Query($sql);
		if ($result) {
			while ($row = $GLOBALS['db']->FetchArray($result)) {
				$this->taskid = $row['TaskID'];
			}			
			// RESOURCES, DEPENDENCIES, ONCOMPLETE
			$this->AddEditOtherData();
			
			// ADD HISTORY VIA MESSAGES
			$this->Debug("Starting history via messages");
			$messages = new Messages;
			$messages->SetVar("message","Added Task: ".$this->taskname);
			$messages->SetVar("eventid",$this->eventid);
			$messages->SetVar("taskid",$this->taskid);
			$messages->SetVar("messagetype","addtask");
			//$messages->SetVar("debug",true);
			$this->Debug($messages->ShowErrors());
			$messages->Add();
			$this->Debug("End messages");
			
			$this->debug("Initiating Commit");
			$GLOBALS['db']->Commit();
			$this->Errors(MessageCatalogue(17));
			return True;
		}
		else {
			$this->debug("Initiating Rollback");
			$GLOBALS['db']->Rollback();			
			$this->Errors(MessageCatalogue(18));
			return False;
		}		
	}
	public function OrgPrivCountTasks() {
		$sql = "call sp_org_priv_count_tasks(".$this->eventid.")";				
		$this->debug($sql);
		$result = $GLOBALS['db']->Query($sql);
		if ($result) {			
			while ($row = $GLOBALS['db']->FetchArray($result)) {	
				return $row['total'];
			}
		}
		return 0;
	}
	private function AddEditOtherData() {
		// ADD RESOURCES
		$this->debug("Entering Add Edit Other Data Method");
		$this->debug("Adding Resources to Task");
		$users = explode(",",$this->resources);
		foreach ($users as $email_address) {
			$obj_user = new UserMaster;
			$obj_user->SetVar("email_address",$email_address);
			$userid = $obj_user->GetUserIDFromEmail($email_address);
			if (IS_NUMERIC($userid)) {
				$sql = "CALL sp_usertasks_add('".$this->taskid."','$userid')";
				$this->debug("Add User to task SQL: $sql");
				$result = $GLOBALS['db']->Query($sql);
			}			
		}	
		$this->debug("End Calling Adding Resources");
		// ADD DEPENDENCIES
		$this->debug("Adding Dependencies to Task");
		$dependencies = explode(",",$this->dependencies);			
		foreach ($dependencies as $dependency_task_name) {
			if (!EMPTY($dependency_task_name)) {
				$this->name = $dependency_task_name;					
				$p_task_id = $this->GetTaskIDFromName();
				//$sql = "call sp_taskmaster_add_dependencies($p_task_id,$piece)";
				if (IS_NUMERIC($p_task_id)) {
					$sql = "call sp_taskmaster_add_dependencies('".$this->taskid."','$p_task_id',".$_SESSION['userid'].")";				
					$this->debug("Dependencies SQL: $sql");
					$result = $GLOBALS['db']->Query($sql);
					if (!$result) {
						$this->debug("Error in calling SQL for adding dependencies");
						$this->debug(mysql_error());
					}
				}
				else {
					$this->debug("Non numeric task ID - possibly a trailing space or comma");
				}
			}
		}
		$this->debug("End Dependencies");		
		// ADD COMPLETION
		$this->debug("Start Completion");
		foreach ($GLOBALS['statuses'] as $status) {
			foreach ($GLOBALS['on_complete_opts'] as $id=>$name) {
				$post_var = "oncomplete_".$status."_".$id;
				//echo "Posted var: $post_var <br />";
				$post_var_val = "";
				if (ISSET($_POST[$post_var]) && $_POST[$post_var] == "checked") {
					$obj_comp = new TaskCompletionMaster;
					//echo "Value: ".$_POST[$post_var]."<br />";
					$completion_id = $obj_comp->GetIDFromName();
					$sql = "REPLACE INTO taskcompletion (TaskID,CompletionID,Status) VALUES ('".$this->taskid."','$id','$status')";
					$this->debug("Completion SQL: $sql");
					$result = $GLOBALS['db']->Query($sql);
				}
			}
		}
		// MAKE THE AJAX REFRESH CALL		
		$sql = "call sp_ajaxevents ('tasks',".$this->eventid.")";
		$this->debug("AJAX Events: $sql");
		$result = $GLOBALS['db']->Query($sql);
		
		$this->debug("End Completion");		
	}
	public function Edit() {
		$this->debug("Starting Edit Method");
		if (!$this->CheckVarsSet("edit")) {    			
			$this->Errors("Invalid Parameters");
			$this->debug("Parameters are not set");
			return false;
		}
		if (!$this->CheckVars($this->vartypes_edit)) {
			$this->debug("Wrong data types for values");
			$this->Errors("Invalid Values");
			return false;
		}
		
		$sp_params = "";
		foreach ($this->params_edit as $p) {
		  $sp_params .= "'".$this->$p."',";
		}
		//$sp_params .= "'".$_SESSION['userid']."'";
		$sp_params = substr($sp_params,0,-1);
		
		$this->debug("Calling Stored Proc Now");
		$sql = "call sp_taskmaster_edit($sp_params)";				
		//echo $sql; // DEBUG
		$this->debug("Stored Proc: $sql");
		$result = $GLOBALS['db']->Query($sql);
		$this->debug("End Calling Stored Proc");
		if ($result) {
			$this->debug("Stored Proc Query Successful");
			// RESOURCES, DEPENDENCIES, ONCOMPLETE			
			$this->debug("Add / Edit Other Data");
			$this->AddEditOtherData();
			$this->Errors(MessageCatalogue(35));
			return true;
		}
		else {
			$this->Errors(MessageCatalogue(36));
			return false;
		}		
	}
	public function Delete() {
		
		$this->Info(); // RUN BEFORE WE DELETE
		
		if (!ISSET($this->taskid) || !IS_NUMERIC($this->taskid)) {
			$this->Errors("Invalid Task");
			return false;
		}
		$sql = "call sp_taskmaster_delete(".$this->taskid.",".$this->userid.")";		
		$result = $GLOBALS['db']->Query($sql);
		if ($result) {
			// MAKE THE AJAX REFRESH CALL
			
			$sql = "call sp_ajaxevents ('tasks',".$this->EventID.")";
			$this->debug("AJAX Events: $sql");
			$result = $GLOBALS['db']->Query($sql);
			return MessageCatalogue(19);
		}
		else {
			return MessageCatalogue(20);
		}
	}
	public function ChangeSortOrder() {
		if (!ISSET($this->taskid) || !IS_NUMERIC($this->taskid)) {
			$this->Errors("Invalid Task");
			return false;
		}
		if (!ISSET($this->sortorder) || !IS_NUMERIC($this->sortorder)) {
			$this->Errors("Invalid Sort Order");
			return false;
		}
		$sql = "call sp_task_update_sortorder(".$this->taskid.",".$this->sortorder.")";
		//echo $sql."<br />\n";
		//return;
		$result = $GLOBALS['db']->Query($sql);
		if ($result) {
			return true;
		}
		else {
			return false;
		}
	}
	public function StatusUpdate() {
		if (!ISSET($this->taskid) || !IS_NUMERIC($this->taskid)) {
			$this->Errors("Invalid Task");
			return false;
		}		
		if (!ISSET($this->status) && ($this->status != "inprogress" || $this->status != "complete" || $this->status != "issues")) {
			$this->Errors("Invalid Task Status");
			return false;
		}
		
		// CHECK ANY DEPENDENCIES
		$sql = "call sp_task_dependency_check(".$this->taskid.")";		
		$this->debug($sql);
		$result = $GLOBALS['db']->Query($sql);
		if ($result && $GLOBALS['db']->NumRows($result) > 0) {
			$this->Errors(MessageCatalogue(28));
			return false;
		}
		
		$sql = "call sp_task_status_update(".$this->taskid.", '".$this->status."', ".$_SESSION['userid'].")";		
		$this->debug($sql);
		$result = $GLOBALS['db']->Query($sql);
		if ($result) {
			// EXECUTE THE ACTUAL START TIME
			if ($this->status != "complete") {				
				$sql1 = "call sp_task_set_actstart(".$this->taskid.")";						
				$result1 = $GLOBALS['db']->Query($sql1);
			}
			// EXECUTE THE ACTUAL END TIME
			if ($this->status == "complete") {
				$sql1 = "call sp_task_set_actend(".$this->taskid.")";		
				$result1 = $GLOBALS['db']->Query($sql1);
			}
			// EXECUTE COMPLETIONS			
			$this->ExecuteCompletions();
			// MAKE THE AJAX REFRESH CALL
			$this->Info(); // GET THE EVENT ID FROM THE TASK ID
			$sql = "call sp_ajaxevents ('tasks',".$this->EventID.")";
			$this->debug("AJAX Events: $sql");
			$result = $GLOBALS['db']->Query($sql);
			
			// ADD HISTORY VIA MESSAGES
			$this->Debug("Starting history via messages");			
			$messages = new Messages;
			//$messages->SetVar("debug",true);
			$messages->SetVar("message",$this->TaskName." is now ".$this->status);
			$messages->SetVar("eventid",$this->EventID);
			$messages->SetVar("taskid",$this->taskid);
			$messages->SetVar("messagetype","statusupdate");
			
			$this->Debug($messages->ShowErrors());
			$messages->Add();
			$this->Debug("End messages");
			
			$this->Errors(MessageCatalogue(26));
			return true;
		}
		else {
			$this->Errors(MessageCatalogue(27));
			return false;
		}
	}
	private function ExecuteCompletions() {
		$this->Info();
		$sql = "CALL sp_task_completion(".$this->taskid.", '".$this->status."')";
		$this->debug($sql);
		$result = $GLOBALS['db']->Query($sql);
		if ($result) {
			if ($GLOBALS['db']->NumRows($result) == 0) {
				$this->debug("No task completion found.");
				return false;
			}
			while ($row = $GLOBALS['db']->FetchArray($result)) {
				$execfile = $row['ExecFile'];
				$this->debug("Loading $execfile");
				require_once $GLOBALS['dr']."completion/".$execfile.".php";
				$obj_dependency = new $execfile;
				$obj_dependency->SetVar("eventid",$this->EventID);
				$obj_dependency->SetVar("taskid",$this->taskid);
				$obj_dependency->SetVar("taskname",$this->TaskName);
				$obj_dependency->SetVar("taskstatus",$this->Status);
				$obj_dependency->Start();
			}
		}
	}
	private function GetTaskIDFromName() {				
		if (ISSET($this->name) && ISSET($this->eventid)) {		
			$sql = "SELECT TaskID FROM taskmaster WHERE TaskName = '".$this->name."' AND EventID = ".$this->eventid;							
			//echo $sql;
			$result = $GLOBALS['db']->Query($sql);					
			// EMAIL ADDRESS EXISTS
			while ($row = $GLOBALS['db']->FetchArray($result)) {
				return $row['TaskID'];
			}
		}
		return false;
	}
	public function UserTaskExists() {
		if (ISSET($this->taskid)) {
			$sql = "SELECT 'x' FROM usertasks WHERE TaskID = '".$this->taskid."' AND UserID = ".$this->userid;							
			$result = $GLOBALS['db']->Query($sql);					
			// EMAIL ADDRESS EXISTS
			if ($GLOBALS['db']->NumRows($result) > 0) {		
				return true;
			}
		}
		return false;
	}

	// CHECK VARS ALL SET FOR REQUIRED METHOD
	private function CheckVarsSet($method) {    
		if ($method == "add") {      
			//echo "ok";
			foreach ($this->params_add as $param) {
				if (!ISSET($this->$param)) {          
					$this->Errors("Parameter $param not set");
					return False;
				}
			}
		}
		elseif ($method == "edit") {
			//echo "ok";
			foreach ($this->params_edit as $param) {
				if (!ISSET($this->$param)) {          
					$this->Errors("Parameter $param not set");
					return False;
				}
			}
		}
    return True;
  }
  // CHECK VAR TYPES
  private function CheckVars($v) {
	$count = 0;
    foreach ($v as $var=>$type) {
      if ($type == "a-z" && !preg_match("/^[\w\s]*$/",$this->$var)) {                
        $this->Errors($var." needs to contain alpha characters only");
        return False;
      }      
      if ($type == "numeric" && !IS_NUMERIC($this->$var)) {
        $this->Errors($this->$var." needs to be numeric");
        return False;        
      }
	  if ($type == "email" && !preg_match("/^\w+@\w+\.\w+$/",$this->$var)) {
        $this->Errors($var." needs to be an email address");
        return False;        
      }
	  if ($type == "date" && !preg_match("/^\d\d\d\d-\d\d-\d\d \d\d:\d\d:?\d?\d?$/",$this->$var)) {
        $this->Errors($var." needs to be an ISO format date");
        return False;        
      }
	  if ($type == "int-array" && !preg_match("/^[\d,?]*$/",$this->$var)) {
        $this->Errors($var." needs to be a set of integers followed by commas");
        return False;        
      }
	  if ($type == "csv") {        
        
      }
	  if ($type == "words") {        
        
      }
	  $count++;
    }
    return True;
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
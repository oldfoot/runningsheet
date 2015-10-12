<?php
class StoredProcedure {
	public function __construct() {
		$this->debug = false;
		$this->params = "";
		$this->result_query = "";
	}
	public function SetVar($var,$val) {
		$this->$var = $val;
	}
	public function GetVar($var) {
		if (ISSET($this->$var)) {
			return $this->$var;
		}
	}
	public function Process() {
		if (ISSET($this->query)) {
			$this->debug("Query: ".$this->query);
			// BREAK THE QUERY APART
			$pieces = explode(" ",$this->query);
			$stored_proc_part = $pieces[1];			
			
			$this->debug("Stored proc part: ".$stored_proc_part);	
			$params_pieces = explode("(",$stored_proc_part);
			
			$stored_proc_name = $params_pieces[0];
			$this->debug("Stored proc name: ".$stored_proc_name);	
			
			$this->params = str_replace(")","",$params_pieces[1]);
			$this->debug("Param1: ".$this->params);	
			// JOIN OTHER PARAMS BECAUSE OF THE SPLIT BY SPACE
			for ($i=2;$i<count($pieces);$i++) {
				$this->params .= " ".$pieces[$i];
			}
			$this->params = str_replace(")","",$this->params);
			
			$stored_proc_name = str_replace("()","",$stored_proc_name);
			
			$this->debug("Stored proc to execute: ".$stored_proc_name);
			
			$this->result_query = $this->$stored_proc_name();
			$this->debug("Result query: ".$this->result_query);
			return $this->result_query;
		}
		return false;
	}
	private function sp_usermaster_add() {
		$this->debug("Entering sp_usermaster_add function");
		$params = explode(",",$this->params);		
		$this->result_query = "INSERT INTO outage_usermaster (UserLogin,UserPassword,FullName,ActivationCode)	VALUES (".$params[0].",MD5(".$params[1]."),".$params[2].",".$params[3].")";
		return $this->result_query;
	}
	private function sp_usermaster_exists() {
		$this->debug("Entering sp_usermaster_exists function");
		$this->result_query = "SELECT COUNT(*) as Total FROM outage_usermaster WHERE UserLogin = ".$this->params;
		$this->debug("Query: ".$this->result_query);
		return $this->result_query;
	}
	private function sp_userauth() {
		$this->debug("Entering sp_userauth function");
		$param = explode(",",$this->params);
		$this->result_query = "SELECT UserID,Activated                                                                                                                                                                                                                                                                                               
                       	FROM outage_usermaster                                                                                                                                                                                                                                                                                                
                       	WHERE UserLogin = ".$param[0]."                                                                                                                                                                                                                                                                                
                       	AND UserPassword = MD5(".$param[1].")";
		$this->debug("Query: ".$this->result_query);
		return $this->result_query;
	}
	private function sp_usermaster_activate() {
		$this->debug("Entering sp_usermaster_activate function");
		$sql = "UPDATE outage_usermaster SET Activated = 'y' WHERE ActivationCode = ".$this->params;
		$this->debug("Query: ".$this->result_query);
		return $this->result_query;
	}
	private function sp_usermaster_pw_code() {
		$params = explode(",",$this->params);
		$this->debug("Entering sp_usermaster_pw_code function");
		$this->result_query = "UPDATE outage_usermaster SET PasswordResetCode = ".$params[0]." WHERE UserLogin = ".$params[1];                                    
		$this->debug("Query: ".$this->result_query);
		return $this->result_query;
	}
	private function sp_usermaster_pw_reset() {
		$params = explode(",",$this->params);
		$this->debug("Entering sp_usermaster_pw_reset function");
		$this->result_query = "UPDATE outage_usermaster SET UserPassword = md5(".$params[0].") WHERE PasswordResetCode = ".$params[0];
		$this->debug("Query: ".$this->result_query);
		return $this->result_query;
	}
	
	private function sp_banners_browse() {
		$this->debug("Entering sp_banners_browse function");
		$this->result_query = "SELECT ID, Content FROM outage_banners ORDER BY RAND() LIMIT 4;";		
		return $this->result_query;
	}
	private function sp_userevent_browse() {
		$this->debug("Entering sp_userevent_browse function");
		$this->result_query = "SELECT em.EventID, em.EventName, em.DateTimeStart, em.DateTimeEnd
                               FROM outage_userevent ue, outage_eventmaster em
                               WHERE ue.UserID = '".$_SESSION['userid']."'
                               AND ue.EventID = em.EventID
							   ORDER BY ue.EventID DESC;";
		//echo $this->result_query;
		return $this->result_query;
	}
	private function sp_eventmaster_add() {
		$this->debug("Entering sp_eventmaster_add function");
		$params = explode(",",$this->params);		
		$this->result_query = "INSERT INTO outage_eventmaster (EventName,DateTimeStart,DateTimeEnd,UserID,dateTimeCreated,UniqueID)	VALUES (".$this->params.",sysdate(),MD5('".microtime()."'))";				
		//echo $sql;
		return $this->result_query;
	}
	private function sp_userevent_add() {
		$this->debug("Entering sp_userevent_add function");
		$params = explode(",",$this->params);
		$this->result_query = "INSERT INTO outage_userevent (EventID,UserID) VALUES (".$this->params.")";		
		//echo $this->result_query;
		return $this->result_query;
	}
	private function sp_event_browse_id() {
		$this->debug("Entering sp_event_browse_id function");
		$this->result_query = "SELECT * FROM outage_eventmaster WHERE EventID = ".$this->params;
		return $this->result_query;
	}
	private function sp_task_browse() {	
		$this->debug("Entering sp_task_browse function");
		$this->params = str_replace(";","",$this->params);
		$params = explode(",",$this->params);		
		$this->result_query = "SELECT TaskID,TaskName,Description,DateTimeReqStart,DateTimeReqEnd,SortOrder 
                          FROM outage_taskmaster
                          WHERE EventID = ".$params[0]."
                          AND EventID in (SELECT EventID FROM outage_userevent WHERE UserID = ".$params[1].")
                          ORDER BY SortOrder";
		//echo $this->result_query;
		return $this->result_query;
	}
	private function sp_browse_messages() {
		$this->debug("Entering sp_browse_messages function");
		$this->result_query = "SELECT um.FullName, om.MessageID, om.Message, om.DateTimePosted, (UNIX_TIMESTAMP(sysdate()) - UNIX_TIMESTAMP(om.DateTimePosted)) as sc                                                                                                                                                                                                                                                                                                              
                              	FROM outage_messages om, outage_usermaster um                                                                                                                                                                                                                                                                                                                                                                                                       
                              	WHERE om.EventID = ".$this->params."                                                                                                                                                                                                                                                                                                                                                                                                                         
                              	AND om.UserID = um.UserID                                                                                                                                                                                                                                                                                                                                                                                                                           
                              	ORDER BY MessageID Desc                                                                                                                                                                                                                                                                                                                                                                                                                             
                              	LIMIT 50";
		//echo $this->result_query;
		return $this->result_query;
	}  
	private function sp_messages_add() {
		$this->debug("Entering sp_messages_add function");		
		$this->result_query = "INSERT INTO outage_messages                                                                                                                                                                                                                                                                                                                                    
                           	(Message,UserID,EventID,TaskID,DateTimePosted)                                                                                                                                                                                                                                                                                                                 
                           	VALUES (".$this->params.",sysdate())";						
		return $this->result_query;
	}
	private function sp_taskmaster_add() {
		$this->debug("Entering sp_taskmaster_add function");		
		$this->result_query = "INSERT INTO outage_taskmaster
								(TaskName,Description,EventID,DateTimeReqStart,DateTimeReqEnd,DependancyTaskID,UserID)
                             	VALUES (".$this->params.")";		
		echo $this->result_query;
		return $this->result_query;
	}
	private function sp_taskmaster_add_dependencies() {
		$this->debug("Entering sp_taskmaster_add_dependencies function");		
		$this->result_query = "INSERT INTO outage_taskdependencies(TaskID,DependencyTaskID) VALUES (".$this->params.");";                             			
		return $this->result_query;
	}	
	private function sp_browse_eventusers() {
		$this->debug("Entering sp_browse_eventusers function");
		$this->params = str_replace(";","",$this->params);
		$this->result_query = "SELECT um.UserLogin, um.FullName                                                                                                                                                                                                                                                                                                    
                                	FROM outage_userevent om, outage_usermaster um                                                                                                                                                                                                                                                                                  
                                	WHERE om.EventID = ".$this->params."                                                                                                                                                                                                                                                                                                     
                                	AND om.UserID = um.UserID                                                                                                                                                                                                                                                                                                       
                                	ORDER BY um.FullName";                             			
		//echo $this->result_query;
		return $this->result_query;
	}
	
	private function debug($str) {
		if ($this->debug) {
			echo $str."<br />\n";
		}
	}
}
?>
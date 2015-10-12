<?php
class ClassToRestWS {    
  public function __construct() {        
    $this->file = "";
    $this->class = "";
    $this->method = "";
    $this->errors = "";
    $this->xml_head = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
    $this->xml_head .= "<result>\n";
    $this->xml_body = "";
    $this->xml_foot = "</result>\n";
	$this->ret_col = "";
  }
  public function GetVar($v) {
    if (ISSET($v)) {
      return $this->$v;
    }
  }
  public function SetVar($var,$val) {
    $this->$var = $val;    
  }
  public function Handle() {    
    $file = $this->file.".php";
    if (file_exists($file)) {      
      require $file;
    }
    else {
      $this->xml_body = "File does not exist";      
      return $this->GenResponse();
    }
    //$class = str_replace("_","",$file);
    //$class = str_replace(".php","",$class);
    $obj = new $this->class;
	//$obj->SetVar("debug",true);
    $methods = $obj->GetVar("methods");
    $valid = false;
    foreach ($methods as $m) {
      if ($this->method == $m) {
        $valid = true;
      }
    }
    // PROCEED - WE ARE PERFORMING ONE OF THE METHODS DEFINED IN THE OBJECT        
    if ($valid) {
      $params = $_GET;
      
      foreach ($params as $key=>$val) {
        $obj->SetVar($key,$val);
      }      
      $method = $this->method;
      $result = $obj->$method();
      if ($result) {
        if (strlen($this->ret_col) > 0) {
			$this->xml_body = $obj->GetVar($this->ret_col);
		}
		else {
			$this->xml_body = $result;
		}
		return $this->GenResponse();		
      }
      else {
        $this->xml_body = $obj->ShowErrors();
        return $this->GenResponse();
      }
    }
    else {
      $this->xml_body = "Invalid method specified";      
      return $this->GenResponse();
    }
    
  }
  private function GenResponse() {	
	return $this->xml_head.$this->xml_body.$this->xml_foot;		
  }
  private function Errors($err) {
    $this->errors .= $err."<br />\n";    
  }
  public function ShowErrors(){ 
    return $this->errors;
  }
}
?>

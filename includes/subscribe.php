<?php
  class Subscribe{
    public $code; //  nmcl_id
    public $name;
    public $description;
	public $active;
	public $message;
    function __construct($code = '0') {
	 global $sql;
	 $this->code = $code;
	 if (isset($code) && $code!='0'){
	  $lsSql = sprintf("select * from subscribe where code='%s'", $sql->escape_string($this->code));
	  if ($sql->query($lsSql)){
       if ($row = $sql->fetchObject()){
	    $this->code = $row->code;
        $this->name = $row->name;
        $this->description = $row->description;
		$this->active = $row->active;
	   } else {
	     $this->code = '0';
	   }
	  }
	  else{
 	   $this->message =  "failed get subscribe (db error: ".$sql->error().")";
	   $this->code = -1;
	  }
	 }
    }
    function create(){
	 if ($this->code == '0') {
	   $this->message = "subscribe code not defined";
	   return -1;
	 }
	 global $sql;
     $lsSql = sprintf("insert into subscribe (code,name,description) values('%s','%s','%s')",
	    $sql->escape_string($this->code), 
		$sql->escape_string($this->name),
		$sql->escape_string($this->description)
	 );
     if(!$sql->query($lsSql)){
        $this->message = "failed create subscribe (db error: ".$sql->error().")";
        return -1;
     }/*elseif ($sql->query("select LAST_INSERT_ID() as last_id")){
      if (!$row = $sql->fetchObject()){
	   $this->message = "failed to get registration id";
	   return -1;
	  }
      $this->id = $row->last_id;*/
      return $this->code;
     /*}else{
	  $this->message = "failed to get registration id (db error: ".$sql->error().")";
      return -1;
     }*/
    }
	function update($name,$description){
	 global $sql;
	 if ($this->code <=0){
        $this->message = "error: invalid registration";
        return false;
     }
     $lsSql = sprintf("update subscribe set name = '%s' , description = '%s' where code='%s'",
		$sql->escape_string($name),
		$sql->escape_string($description), 
	    $sql->escape_string($this->code)
	 );
     if(!$sql->query($lsSql)){
        $this->message = "failed update subscribe (db error: ".$sql->error().")";
        return false;
     }
	 $this->name = $name;
	 $this->description = $description;
	 return true;
    }
	function setActive(){
	 global $sql;
	 if ($this->code =='0' or $this->code =='-1'){
        $this->message = "invalid object";
        return false;
     }
     $lsSql = sprintf("update subscribe set active = case code when '%s' then 1 else 0 end where code = '%s' or active = 1",
	    $sql->escape_string($this->code),$sql->escape_string($this->code)
	 );
     if(!$sql->query($lsSql)){
        $this->message = "failed set active (db error: ".$sql->error().")";
        return false;
     }
	 $this->active = 1;
	 return true;
    }
	function getActive(){
	 global $sql;
	 if ($this->code != '0'){
        $this->message = "error: invalid object";
        return false;
     }
	 $lsSql = "select * from subscribe where active=1";
	 if ($sql->query($lsSql)){
       if ($row = $sql->fetchObject()){
	    $this->code = $row->code;
        $this->name = $row->name;
        $this->description = $row->description;
		$this->active = $row->active;
		return true;
	   } else {
	     return false;
	   }
	  }
	  else{
 	   $this->message =  "failed get active subscribe (db error: ".$sql->error().")";
	   $this->code = -1;
	   return false;
	  }	 
    }
  }
  
  function getSubscribes($filter = "")
  {
     // Returns an array of all items

     global $sql;
		 
		 if(isset($filter) && strlen(trim($filter))>0) $where = " where $filter";
		 else $where = "";

     $laRet = array();
     $sql->query("SELECT * from subscribe ORDER BY create_date");
      while ($row = $sql->fetchObject()) {
	    $item = new Subscribe(); 
	  	$item->code = $row->code;
        $item->name = $row->name;
        $item->description = $row->description;
		$item->active = $row->active;
        $laRet[$row->code] = $item;
      }

    return $laRet;
  } // end getOrders()
//------------------------------------------------------------------------------
?>
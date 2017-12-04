<?php
  class Discount{
    public $code;
	public $distype_id;
	public $description;
	public $published;
	public $message;
    function __construct($code = '0') {
	 global $sql;
	 $this->code = $code;
	 if (isset($code) && $code!='0'){
	  $lsSql = "select * from discount where code='$code'";
	  if ($sql->query($lsSql)){
       if ($row = $sql->fetchObject()){
	    $this->code = $row->code;
        $this->distype_id = $row->distype_id;
        $this->description = $row->description;
        $this->published = $row->published;
	   }
	  }
	  else{
 	   $this->message =  "failed get discount (db error: ".$sql->error().")";
	   $this->code = '-1';
	  }
	 }
    }
    function create(){
	 global $sql;
     $lsSql =
     "insert into discount (code,distype_id,description,published) ".
     "values('$this->code','$this->distype_id','$this->description','$this->published')";
     if(!$sql->query($lsSql)){
        $this->message = "failed create discount (db error: ".$sql->error().")";
        return false;
     }
     return $this->code;
    }
	function update(){
	 global $sql;
     $lsSql =
     "update discount set distype_id = '$this->distype_id' 
	 ,description = '$this->description', published = $this->published
	 where code = '$this->code'";
     if(!$sql->query($lsSql)){ 		 
        $this->message = "failed update  discount (db error: ".$sql->error().")";
        return false;
     }
	 return true;
    }
  }
  
  function getDiscounts($aStatusFilter = 0)
  {
     // Returns an array of all item of group

     global $sql;
		 
		 if(isset($aStatusFilter) || strlen(trim($aStatusFilter))>0) $lStatusFilter=$aStatusFilter;
		 else $lStatusFilter='0,1,2,3,4,5,6,7,8,9';

     $laRet = array();
	 $i=0;
     $sql->query("SELECT * from discount".
                 " ORDER BY published desc");
      while ($row = $sql->fetchObject()) {
	    $item = new Discount(); 
	  	$item->code = $row->code;
        $item->distype_id = $row->distype_id;
        $item->description = $row->description;
        $item->published = $row->published;
        $laRet[$i++] = $item;		
      }

    return $laRet;
  } // end getOrders()
//------------------------------------------------------------------------------
  ?>
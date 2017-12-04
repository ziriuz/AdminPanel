<?php
  class Registry{
    public $id; //  nmcl_id
	public $subscr_code;
    public $firstName;
    public $lastName;
    public $email;
	public $phone;
	public $postCode;
	public $address;
	public $kidSize;
	public $bigSize;
	public $referer;
	public $postId;
	public $dateReg;
	public $dateSend;
	public $status;
	public $sid;
	public $message;
    function __construct($id = '0') {
	 global $sql;
	 $this->id = $id;
	 if (isset($id) && $id!='0'){
	  $lsSql = "select * from registry where id='$id'";
	  if ($sql->query($lsSql)){
       if ($row = $sql->fetchObject()){
	    $this->id = $row->id;
		$this->subscr_code = $row->subscr_code;
        $this->firstName = $row->firstname;
        $this->lastName = $row->lastname;
        $this->email = $row->email;
	    $this->phone = $row->phone;
	    $this->postCode = $row->postcode;
	    $this->address = $row->address;
	    $this->kidSize = $row->kidsize;
	    $this->bigSize = $row->bigsize;
	    $this->referer = $row->referer;
	    $this->postId = $row->postid;
	    $this->dateReg = $row->datereg;
	    $this->dateSend = $row->datesend;
	    $this->status = $row->status;
	    $this->sid = $row->sid;
	   }
	  }
	  else{
 	   $this->message =  "failed get registration (db error: ".$sql->error().")";
	   $this->id = -1;
	  }
	 }
    }
    function create(){
	 global $sql;
     $lsSql = sprintf(
     "insert into registry (subscr_code,firstname,lastname,email,phone,postCode,address,kidsize,bigsize,referer,postId,dateSend,status,sid) ".
     "values( '%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
	 $sql->escape_string($this->subscr_code),
	 $sql->escape_string($this->firstName),
	 $sql->escape_string($this->lastName),
	 $sql->escape_string($this->email),
	 $sql->escape_string($this->phone),
	 $sql->escape_string($this->postCode),
	 $sql->escape_string($this->address),
	 $sql->escape_string($this->kidSize),
	 $sql->escape_string($this->bigSize),
	 $sql->escape_string($this->referer),
	 $sql->escape_string($this->postId),
	 $sql->escape_string($this->dateSend),
	 $sql->escape_string($this->status),
	 $sql->escape_string($this->sid)
	 );
     if(!$sql->query($lsSql)){
        $this->message = "failed create registration (db error: ".$sql->error().")";
        return -1;
     }elseif ($sql->query("select LAST_INSERT_ID() as last_id")){
      if (!$row = $sql->fetchObject()){
	   $this->message = "failed to get registration id";
	   return -1;
	  }
      $this->id = $row->last_id;
      return $this->id;
     }else{
	  $this->message = "failed to get registration id (db error: ".$sql->error().")";
      return -1;
     }
    }
	function updatePostId(){
	 global $sql;
	 if ($this->dateSend != '0000-00-00') $this->status = 4;
     $lsSql =
     "update registry set postid = '$this->postId' 
	 ,datesend = '$this->dateSend', status = $this->status
	 where id = $this->id";
     if(!$sql->query($lsSql)){ 		 
        $this->message = "failed update  postId (db error: ".$sql->error().")";
        return false;
     }
	 return true;
    }
	function activate(){
	 global $sql;
	 if ($this->id <=0){
        $this->message = "Activation error: invalid registration";
        return false;
     }
     $lsSql = "update registry set status = 1,active=1  where id=$this->id";
     if(!$sql->query($lsSql)){
        $this->message = "failed activate registration (db error: ".$sql->error().")";
        return false;
     }
	 return true;
    }
	function updateStatus($status){
	 global $sql;
	 if ($this->id <=0){
        $this->message = "error: invalid registration";
        return false;
     }
     $lsSql = "update registry set status = $status where id=$this->id";
     if(!$sql->query($lsSql)){
        $this->message = "failed updat status registration (db error: ".$sql->error().")";
        return false;
     }
	 $this->status = $status;
	 return true;
    }
  }
  
  function getRegs($filter = "")
  {
     // Returns an array of all item of group

     global $sql;
		 
	 if(isset($filter) && strlen(trim($filter))>0) $where = sprintf(" where subscr_code='%s' ",$sql->escape_string($filter));
	 else $where = "";

     $laRet = array();
     $sql->query("SELECT * from registry $where ORDER BY id");
      while ($row = $sql->fetchObject()) {
	    $item = new Registry(); 
	  	$item->id = $row->id;
		$item->subscr_code = $row->subscr_code;
        $item->firstName = $row->firstname;
        $item->lastName = $row->lastname;
        $item->email = $row->email;
	    $item->phone = $row->phone;
	    $item->postCode = $row->postcode;
	    $item->address = $row->address;
	    $item->kidSize = $row->kidsize;
	    $item->bigSize = $row->bigsize;
	    $item->referer = $row->referer;
	    $item->postId = $row->postid;
	    $item->dateReg = $row->datereg;
	    $item->dateSend = $row->datesend;
	    $item->status = $row->status;
	    $item->sid = $row->sid;
        $laRet[(int)$row->id] = $item;
      }

    return $laRet;
  } // end getOrders()
//------------------------------------------------------------------------------
  function changeRegs($action,$aaRegs,$aaValue,$aaDate)
  {
    global $sql;
    if (!isset($aaRegs)||!isset($action)||!isset($aaValue)) return false;
    if ($action == 'UPDATE_POSTID'){
      foreach($aaRegs as $i=>$reg_id){
		$reg = new Registry($reg_id);
		if ($reg->id < 0)  return $reg->message;
		if (strcmp($aaValue[$i],$reg->postId)!=0||$aaDate[$i]!=$reg->dateSend){
		  if ($aaValue[$i]!= $reg->postId && $aaDate[$i] == '0000-00-00')
		    $reg->dateSend = date("Y-m-d");
		  else
 		    $reg->dateSend = $aaDate[$i];
		  $reg->postId = $aaValue[$i];
		  if (!$reg->updatePostId()) return $reg->message;
		}
	  }
    } else return 'INVALID PARAMETER';
    return 'Данные подписки успешно обновлены';
  }
//------------------------------------------------------------------------------
  function sendConf($aaRegs)
  {
    global $sql;
    if (isset($aaRegs)){
     foreach($aaRegs as $i=>$reg_id){
		$reg = new Registry($reg_id);
		if ($reg->email == 's.park@mail.ru'||$reg->email == 's.parrrrrk@mail.ru') { //($reg->status ==0) {
		  $sid = $reg->sid;
		  ob_start();
		  require('../templates/mailtext.htm');
		  $mail_body = ob_get_contents();
		  ob_end_clean();
		  $header = 'From:info@mycomanda.com\n'.
		            'Cc:info@prof-decor.ru\n'.
					'Return-Path:info@prof-decor.ru\n'.
					'Return-Receipt-To: info@prof-decor.ru\n';
          $additionalParameters = '-O DeliveryMode=d'; 					
		  if (mail($reg->email,'Подтвердите заявку на подарок от Мы Команда', $mail_body ,$header,$additionalParameters)){
		   if(!$reg->updateStatus(2)) return $reg->message;
		  } else return 'email not send';
		}
	 }
    } else return 'INVALID PARAMETER';
    return 'Отправка завершена';
  }  
  ?>
<?php
  session_start();
  $message = '';
  if (!isset($_POST["action"])||$_POST["action"]!="setactive_subscribe")
  {
    $err=true;
    $message = "Invalid parameters!";
  }elseif( !isset($_POST["active_subscribe"])||strlen($_POST["active_subscribe"])==0){
    $err=true;
    $message = "Не указан код подписки!";
  }else{
    require("../includes/prd_db.php");
    require("../includes/subscribe.php");
    $code= $_POST["active_subscribe"];
    $reg = new Subscribe($code);
	if ($reg->code < 0){
      $err=true;
      $message = $reg->message;
	}
	elseif ($reg->code == '0'){
      $err=true;
      $message = "Подписка [$code] не найдена";
	}
	else{
	  $err=false;
      if (!$reg->setActive()){
        $err=true;
	    $message = $reg->message;
      } else $message = "Подписка [$code] активирована";
	}
  }
  if ($err) $message = 'Error: '.$message;
  else $message =  'Success! '.$message;
  $message = iconv("WINDOWS-1251", "UTF-8", $message);
?>
<?php if (!$err):?>
{"success":true,"token":"setactive_subscribe","message":"<?=$message?>",
"code":"<?=$code?>"
}
<?php else:?>
{
    "success":false,
	"token":"setactive_subscribe",
    "message":"<?=$message?>"
}
<?php endif;?>
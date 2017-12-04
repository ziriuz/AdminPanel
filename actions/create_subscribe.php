<?php
  session_start();
  $message = '';
  if (!isset($_POST["action"])||$_POST["action"]!="subscribe")
  {
    $err=true;
    $message = "Invalid parameters!";
  }elseif( !isset($_POST["subscribe_code"])||
           !isset($_POST["subscribe_name"])||//subscribe_description
		   strlen($_POST["subscribe_code"])==0||
		   strlen($_POST["subscribe_name"])==0
		 ){
    $err=true;
    $message = "Заполните пожалуйста Код и Название!";
  } else{
  require("../includes/prd_db.php");
  require("../includes/subscribe.php");
  $sid = session_id();
  $code= $_POST["subscribe_code"];
  $reg = new Subscribe($code);
  if ($reg->code == '0'){
   $reg->code = strip_tags(iconv("UTF-8", "WINDOWS-1251", $_POST["subscribe_code"]));
   $reg->name = strip_tags(iconv("UTF-8", "WINDOWS-1251", $_POST["subscribe_name"]));
   $reg->description =     iconv("UTF-8", "WINDOWS-1251", $_POST["subscribe_description"]);
   if ($reg->create()<0) $err=true;
   else {
     $err=false;
   		  //Отправим письмо покупателю
		  /*if(strpos($reg->email,'@')>1 && strlen($reg->email)>4){
		  ob_start();
		  require('../templates/mailtext.htm');
		  $mail_body = ob_get_contents();
		  ob_end_clean();	
		  $header = "From:info@mycomanda.com\n".
		            "Cc:info@prof-decor.ru\n".
					"Return-Path:info@prof-decor.ru\n".
					"Return-Receipt-To: info@prof-decor.ru\n";	  
          mail($reg->email,'Регистрация',$mail_body,$header);
          }		  */
   }
  } elseif ($reg->code < 0) {$err=true; $message = $reg->message;}
  else {$err=true; $message = "Подписка с кодом ".$_POST['subscribe_code']." уже существует!";}
  }
  if ($err) $message = 'Ошибка оформления подписки: '.$message;
  else $message =  'Подписка успешно создана! '.$message;
  $message = iconv("WINDOWS-1251", "UTF-8", $message);
?>
<?php if (!$err):?>
{"success":true,"token":"create_subscribe","message":"<?=$message?>",
"code":"<?=$reg->code?>",
"name":"<?=$reg->name?>"}
<?php else:?>
{"success":false,"token":"create_subscribe","message":"<?=$message?>"}
<?php endif;?>
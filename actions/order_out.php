<?php

  $err = false;
  $done = true;
  $MESSAGES = array();
  $msg = '';
  $order = getOrderInfo($forder_id);
  if($order->status <= 22){
  //echo ' сначала отменим все резервы';
  /*foreach ($fcard_id as $i=>$card_id){
    if (isset($freserve) &&  in_array($card_id,$freserve)){
    //echo "<br>зарезервировать $card_id $forder_id $freserve_qty[$i] штук<br>";
	if (createWrhTransaction($card_id,1,1,$freserve_qty[$i],$forder_id,$msg)<0){
	 $err = true;
	 $MESSAGES[$card_id] = $msg;
	 $done = false;
	 //echo $MESSAGES[$card_id];
	}
	}
  }*/
  //echo ' теперь сделаем отгрузку';
  if ($done){
  foreach ($fcard_id as $i=>$card_id){
    if (isset($fwrhout) &&  in_array($card_id,$fwrhout)){
    //echo "<br>зарезервировать $card_id $forder_id $freserve_qty[$i] штук<br>";
	if (createWrhTransaction($card_id,-1,0,$fwrhout_qty[$i],$forder_id,$msg)<0){
	 $err = true;
	 $MESSAGES[$card_id] = $msg;
	 $done = false;
	 //echo $MESSAGES[$card_id];
	}
	}
  }
  }


  if ($done && !$err){    
	if ($order->status <= 2){
	$a=array($forder_id);
    changeOrders('CHANGE_STATUS',$a,3);
	}
  }
  }
  else
  $MESSAGE = 'Статус заказа не позволяет делать отгрузку! Товар уже отгружен!';

//forder_item[]	308-141
//fprod_order[]	326-66
//fprod_order_qty[]	1

?>

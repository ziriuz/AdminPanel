<?php

  $err = false;
  $done = false;
  $MESSAGES = array();
  $msg = '';
  $order = getOrderInfo($forder_id);
  if($order->status <= 22){
  foreach ($fcard_id as $i=>$card_id){
    if (isset($freserve) &&  in_array($card_id,$freserve)){
    //echo "<br>зарезервировать $card_id $forder_id $freserve_qty[$i] штук<br>";
	if (createWrhTransaction($card_id,1,1,$freserve_qty[$i],$forder_id,$msg)<0){
	 $err = true;
	 $MESSAGES[$card_id] = $msg;
	 //echo $MESSAGES[$card_id];
	}
	else $done = true;
	}
  }
  }
  else
  $MESSAGE = 'Статус заказа не позволяет делать резерв!';

//forder_item[]	308-141
//fprod_order[]	326-66
//fprod_order_qty[]	1

?>

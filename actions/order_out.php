<?php

  $err = false;
  $done = true;
  $MESSAGES = array();
  $msg = '';
  $order = getOrderInfo($forder_id);
  if($order->status <= 22){
  //echo ' ������� ������� ��� �������';
  /*foreach ($fcard_id as $i=>$card_id){
    if (isset($freserve) &&  in_array($card_id,$freserve)){
    //echo "<br>��������������� $card_id $forder_id $freserve_qty[$i] ����<br>";
	if (createWrhTransaction($card_id,1,1,$freserve_qty[$i],$forder_id,$msg)<0){
	 $err = true;
	 $MESSAGES[$card_id] = $msg;
	 $done = false;
	 //echo $MESSAGES[$card_id];
	}
	}
  }*/
  //echo ' ������ ������� ��������';
  if ($done){
  foreach ($fcard_id as $i=>$card_id){
    if (isset($fwrhout) &&  in_array($card_id,$fwrhout)){
    //echo "<br>��������������� $card_id $forder_id $freserve_qty[$i] ����<br>";
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
  $MESSAGE = '������ ������ �� ��������� ������ ��������! ����� ��� ��������!';

//forder_item[]	308-141
//fprod_order[]	326-66
//fprod_order_qty[]	1

?>

<?php

  $err = false;
  $done = false;
  $MESSAGES = array();
  $msg = '';
  $order = getOrderInfo($forder_id);
  if($order->status <= 22){
  foreach (getOrderItems($forder_id) as $i=>$item){
   $oi = new OrderItem($item->item_id);
   if (isset($fprod_order) &&  in_array($item->item_id,$fprod_order))
	$oi->prod = ($fprod_order_qty[$i]<=0?1:$fprod_order_qty[$i]);
   else $oi->prod = 0;
   if (!$oi->save()){
	  $MESSAGE = 'Ошибка обновления заказа'.$ei->getMessage();				
	}else{		
      $MESSAGE = 'Заказ успешно обновлен';
	}
   }
  }
  else
  $MESSAGE = 'Статус заказа не позволяет делать заказ пошива!';

//forder_item[]	308-141
//fprod_order[]	326-66
//fprod_order_qty[]	1

?>

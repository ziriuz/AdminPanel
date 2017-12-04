<?php
$entity='transp_order';
$windowId="dialog";
$formId="transp_order_form";
$idField = 'order_id';
?>
<!-- dialogue window -->
<div id="mask" class="mask mask_hide"></div>
<div class="dialog_window mask_hide" id="dialog">
<div class="form-container">
<a class="closeLink"  id="close_dialog"><span class="visuallyhidden">Close Notification</span></a>
<div class="form-title"><h2>Заказ #<em id="order_name">order_name</em></h2></div>
<form method="post" id="transp_order_form">
<input type="hidden" id = "order_number" name="order_number" value="<?=$ORDER->sid?>">
<input type="hidden" id = "order_id" name="order_id" value="<?=$ORDER->order_id?>">
<div><em id="ajaxmessage_box" style="color:red;font-size:1em;line-height:16px"></em></div>
  <!--div class="form-label" style="display:none">Ваше Имя</div>
  <input class="form-field" type="text" name="firstname" maxlength=32 value="" placeholder="Ваше Имя" id="name_field"-->
<?=$ORDER->address?>  
<div class="input-group">
  <span id="basic-addon1" class="input-group-addon">Индекс</span>
  <input type="text" id="post_id" name="post_id" value="<?=$ORDER->post_id?>" placeholder="укажите почтовый код города доставки" class="form-control" aria-describedby="basic-addon1">
</div>
<div class="input-group">
  <span class="input-group-addon">Пункт доставки</span>
  <input type="text" id="transp_dest_code" name="transp_dest_code" value="<?=$ORDER->transp_dest_code?>" placeholder="Пункт доставки СДЭК" class="form-control" >
  <select  class="form-control"  id="transp_code" name="transp_code">
   <option value="NO" <?=$ORDER->transp_dest_code==null?'selected':''?>> - </option>
   <?php foreach($sdekPvzList as $i=>$item):?>
   <option value="<?=$item["Code"]?>" <?=$ORDER->transp_dest_code==$item["Code"]?'selected':''?>><?=$item["Code"].' '.$item["Name"].' - '.$item["FullAddress"]?></option>
   <?php endforeach;?>
  </select>  
</div>
<br/>
<div class="input-group">
  <span class="input-group-addon">ФИО получателя</span>
  <input type="text" id="customer_name" name="customer_name" value="<?=$ORDER->name?>" class=" form-control" >
</div>
<div class="input-group">
  <span class="input-group-addon">Телефон получателя</span>
  <input type="text" id="phone" name="phone" value="<?=$ORDER->phone?>" class="form-control" >
</div>
<br/>
<div class="input-group">
  <span class="input-group-addon">Налож. платеж</span>
  <input type="text" id="tk_cash_on_delivery" name="tk_cash_on_delivery" value="<?=$ORDER->amount?>" class="form-control" >
  <span class="input-group-addon">Сумма доставки</span>
  <input type="text" id="delivery_price" name="delivery_price" value="<?=$ORDER->delivery_price?>" class="form-control" >
</div>
<br/>
<div class="input-group">
  <span class="input-group-addon">Вес посылки (грамм)</span>
  <input type="text" id="weight_total" name="weight_total" value="<?=$ORDER->weight_total?>" class="form-control" >
</div>
<div class="input-group">
  <span class="input-group-addon">Комментарии</span>
  <textarea class="form-control" id="tk_comments" name="tk_comments"  rows="4" maxlength="255" placeholder="комментарий по доставке"><?=$ORDER->note?></textarea>
</div>
<br>
<div class="submit-container">
<div class="form-info">
<a id="send_transp_order" class="submit-button" type="submit" value="Отправить" name="order_action_conform">Отправить заказ в СДЭК</a>
</div>
</div>
</form>
</div>
</div>
<script language="JavaScript">
 //document.getElementById('close_dialog').onclick=close_dialog;
 document.getElementById('close_dialog').onclick=function(){close_dialog("<?=$windowId?>")};
 document.getElementById('send_transp_order').onclick=sendTranspOrder;
 //document.getElementById('edit_order_5338252492').onclick=loadOrder('5338252492');
</script>
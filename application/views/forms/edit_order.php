<!-- dialogue window -->
<div id="mask" class="mask mask_hide"></div>
<div class="dialog_window mask_hide" id="dialog">
<div class="form-container">
<a class="closeLink"  id="close_dialog"><span class="visuallyhidden">Close Notification</span></a>

<form method="post" id="order_input_form">
<input type="hidden" name="action" value="save">
<input type="hidden" id = "order_id" name="order_id" value="0">
<h2>Order <span id="order_name">order_name</span></h2>
  <div><em id="ajaxmessage_box" style="color:red;font-size:0.6em;line-height:16px"></em></div>
  <!--div class="form-label" style="display:none">Ваше Имя</div>
  <input class="form-field" type="text" name="firstname" maxlength=32 value="" placeholder="Ваше Имя" id="name_field"-->
<div class="input-group">
  <span class="input-group-addon" id="basic-addon3">delivery_dt</span>
  <input type="text" class="form-control" id="delivery_dt" name="delivery_dt" aria-describedby="basic-addon3" placeholder="желаемые дата и время доставки (курьерская)">
</div>
<br/>
<div class="input-group">
  <span class="input-group-addon" id="basic-addon3">delivery_address</span>
  <input type="text" class="form-control" id="delivery_address" name="delivery_address" aria-describedby="basic-addon3" placeholder="адрес доставки, если другой">
</div>
<br/><div class="input-group">
  <span class="input-group-addon" id="basic-addon3">итого доставка</span>
  <input type="text" class="form-control" id="delivery_total" name="delivery_total" aria-describedby="basic-addon3" placeholder="итого доставка">
</div>
<br/><div class="input-group">
  <span class="input-group-addon" id="basic-addon3">налож. платеж</span>
  <input type="text" class="form-control" id="cash_on_delivery" name="cash_on_delivery" aria-describedby="basic-addon3" placeholder="налож. платеж">
</div>
<br/><div class="input-group">
  <span class="input-group-addon" id="basic-addon3">comments</span>
  <textarea class="form-control" id="comments" name="comments" aria-describedby="basic-addon3" rows="4" maxlength="255" placeholder="комментарий (обязательно при отмене заказа, заказа в статусе согласование)"></textarea>
</div>
<br>
<div class="submit-container">
<div class="form-info">
<a id="send_order" class="submit-button" type="submit" value="Отправить" name="order_action_conform">Сохранить</a>
</div>
</div>
</form>
</div>
</div>
<script language="JavaScript">
 document.getElementById('close_dialog').onclick=function(){close_dialog("dialog")};
 document.getElementById('send_order').onclick=sendOrder;
 //document.getElementById('edit_order_5338252492').onclick=loadOrder('5338252492');
</script> 
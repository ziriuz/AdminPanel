<?php require(FORMS_DIR."/transp_order.php");?>
<?php require(BLOCKS_DIR."/notification_bar.php");?>
<div class=orderinfo>
<a target=_blank href="orders.php?forder_id=<?=$ORDER->order_id?>&action=PRINT_ORDER">Печать</a>
<a target=_blank href="orders.php?forder_id=<?=$ORDER->order_id?>&action=PRINT_POST_CHECK">Почтовый перевод</a>
<a href="orders.php?forder_id=<?=$ORDER->order_id?>&action=IMPORT_ORDER">Импорт</a>
<a href="orders.php?forder_id=<?=$ORDER->order_id?>&action=PRINT_LABELS">Этикетки</a>
<h2><img src="images/status<?=$ORDER->status?>.gif" style="margin-bottom:0px"> Заказ №<?=$ORDER->sid?> от <?=$ORDER->order_date?></h2>
<table>
<tr>
<td>ФИО: <b><?=$ORDER->name?></b>
<td rowspan="7" valign="top">
<form action = "orders.php?forder_id=<?=$ORDER->order_id?>" method=post>
 <input type=hidden name=f_action value = "CHANGE_STATUS">
 <input type=hidden name=f_order_id[] value=<?=$ORDER->order_id?>>
 <select name=f_status>
  <option value=0 <?=($ORDER->status==0?'selected':'')?>><img src="images/status0.gif" >Новый</option>
  <option value=1 <?=($ORDER->status==1?'selected':'')?>><img src="images/status1.gif" >Согласовывается</option>
  <option value=2 <?=($ORDER->status==2?'selected':'')?>><img src="images/status2.gif" >Подтвержден</option>
  <option value=3 <?=($ORDER->status==3?'selected':'')?>><img src="images/status3.gif" >Создан заказ в крурьерской службе</option>
  <option value=4 <?=($ORDER->status==4?'selected':'')?>><img src="images/status4.gif" >Отправлен</option>
  <option value=5 <?=($ORDER->status==5?'selected':'')?>><img src="images/status5.gif" >Получен клиентом</option>
  <option value=6 <?=($ORDER->status==6?'selected':'')?>><img src="images/status6.gif" >Отменен</option>
 </select>
 <input type=submit value="Изменить статус">
</form>
<br/>
<form method=post>
 <input type=hidden name=f_action value="CHANGE_NOTE2">
 <input type="image" src="images/btn-save.jpg" alt="Сохранить">
 <table>
 <tr>
 <td>
  <label>желаемые дата и время доставки (курьерская)</label><br><input type="text"  size="50" id="delivery_dt" name="delivery_dt" value="<?=$ORDER->delivery_dt?>"/>
  <br/><label>адрес доставки, если другой</label><br><input type="text" size="50"  id="delivery_address" name="delivery_address" value="<?=$ORDER->delivery_address?>"/>
  <br/><label>статус оплаты</label><br><!--input type="text" size="50"  id="payment_status" name="payment_status" value="<?=$ORDER->payment_status?>"/-->
  <select  id="payment_status" name="payment_status">
   <option value="NO" <?=$ORDER->payment_status=="NO"?'selected':''?>> - </option>
   <option value="CARD" <?=$ORDER->payment_status=="CARD"?'selected':''?>> оплачено на карту </option>
   <option value="CASH" <?=$ORDER->payment_status=="CASH"?'selected':''?>> оплачено курьеру </option>
   <option value="PAYPAL" <?=$ORDER->payment_status=="PAYPAL"?'selected':''?>> оплачен paypal </option>
   <option value="DELIVERY" <?=$ORDER->payment_status=="DELIVERY"?'selected':''?>> оплачен наложеный платеж </option>
  </select>
  <br/><label>итого доставка</label><br><input type="text" size="50"  id="delivery_total" name="delivery_total" value="<?=$ORDER->delivery_total?>"/>
  <br/><label>ID заказа ТК</label><br><input type="text" size="50"  id="transp_number" name="transp_number" value="<?=$ORDER->transp_number?>"/>
  <br/><label>налож. платеж</label><br><input type="text" size="50"  id="cash_on_delivery" name="cash_on_delivery" value="<?=$ORDER->cash_on_delivery?>"/>
  <!--br><label>дата последнего статуса</label><br--><input type="hidden" size="50"  id="status_dt" name="status_dt" value="<?=$ORDER->status_dt?>"/>
  <!--br><label>дата оплаты</label><br--><input type="hidden" size="50"  id="payment_dt" name="payment_dt" value="<?=$ORDER->payment_dt?>"/>
  </td>
  <td>
   <label>Доп. сведения</label>
   <br/>
   <textarea rows="5" cols="50" label="test" id="note2" name="f_note2"><?=$ORDER->note2?></textarea>
  </td>
  </tr>
 </table>
</form>
</td>
<tr><td>E-mail: <b><?=$ORDER->email?></b></td></tr>
<tr><td>Конт. телефон: <b><?=$ORDER->phone?></b></td></tr>
<tr><td>Адрес: <b><?=$ORDER->address?></b></td></tr>
<tr><td>Комментарии: <br><b><?=$ORDER->note?></b></td></tr>
<tr><td>Код скидки: <br><b><?=$ORDER->dis_code?></b></td></tr>
<tr>
<td>
<button id="edit_order_<?=$ORDER->order_id?>" onclick="loadOrder('<?=$ORDER->order_id?>');return false;">Заказ СДЭК</button><form id="order_<?=$ORDER->order_id?>"><input type="hidden" name="order_id" value="<?=$ORDER->order_id?>"/></form>
<?php if(isset($ORDER->shippingOrder)):?>
<b>SDEK</b>
<br/>order:  <?=($ORDER->shippingCheck["ErrNumber"]?'<img src="images/warning.png" alt="uwaga"/>':'')?><span style="color: #0000ff;"><b><?=$ORDER->shippingOrder["Number"]?></b> <?=$ORDER->shippingOrder["Date"]?></span> (mode:<?=$ORDER->shippingOrder["deliveryMode"]?> variant: <?=$ORDER->shippingOrder["deliveryVariant"]?>)
<br/>status: <span style="color: #0000ff;"><?=$ORDER->shippingOrder->DeliveryStatus["Description"].' '.$ORDER->shippingOrder->RecCity["PostCode"].' '.$ORDER->shippingOrder->RecCity["Name"]?></span>
<br/>DeliverySum: <span style="color: #0000ff;"><?=$ORDER->shippingOrder["DeliverySum"]?></span> <?=($ORDER->shippingCheck["ErrDeliverySum"]?'<img src="images/warning.png" alt="uwaga"/>':'')?>
<br/>addServiceSum: <span style="color: #0000ff;"><?=$ORDER->shippingOrder["addServiceSum"]?></span> <?=($ORDER->shippingCheck["ErrAddServiceSum"]?'<img src="images/warning.png" alt="uwaga"/>':'')?>
<br/>CashOnDeliv: <span style="color: #0000ff;"><?=$ORDER->shippingOrder["CashOnDeliv"]?></span> <?=($ORDER->shippingCheck["ErrCashOnDeliv"]?'<img src="images/warning.png" alt="uwaga"/>':'')?>
<br/>CashOnDelivFact: <span style="color: #0000ff;"><?=$ORDER->shippingOrder["CashOnDelivFact"]?></span>
<?php endif; ?>
</td>
</tr>
</table>
<?php if($ORDERITEMS):?>
<?php if($ORDER->deliv_type=='pickpoint')$tabcount=3;else $tabcount=2;?>
<div class=tabpages>
<div class=tabs>
 <span class="tab selected" id=od_tab1 onclick="switchTab('od_tabpage','od_tab',1,<?=$tabcount?>);">
 Позиции заказа
 </span>
 <span class="tab" id=od_tab2 onclick="switchTab('od_tabpage','od_tab',2,<?=$tabcount?>);">
 Подробно
 </span>
 <?php if($ORDER->deliv_type=='pickpoint'):?>
 <span class="tab" id=od_tab3 onclick="switchTab('od_tabpage','od_tab',3,<?=$tabcount?>);">
 <img src="images/dt_pickpoint.png">Pickpoint
 </span>
 <?php endif;?>
</div>
<div class=tab_page id=od_tabpage1>
 <div class=tab_content id=od1>
 <div class="toolbar">
 <a href="javascript:changeEditMode('<?=$ORDER->order_id?>','order','edit')"><img src="images/btn-edit.jpg" alt="Перейти в режим редактирования"></a>
 <em id=ajaxmesorder<?=$ORDER->order_id?>></em>
 </div>
 <div  id=order<?=$ORDER->order_id?>>
 <?php require("templates/printorder0.htm");?>
 </div>   
 </div>
</div>
<div class=tab_page_hide id=od_tabpage2>
 <div class=tab_content id=od2>
 <?php require("templates/printorder1.htm");?>
 </div>
</div>
<?php if($ORDER->deliv_type=='pickpoint'):?>
<div class=tab_page_hide id=od_tabpage3>
 <div class=tab_content id=od3>
 <?php require("templates/pickpointform.htm");?>
 </div>
</div>
<?php endif;?>      
</div>    
<?php else:?>
Нет позиций в вашем заказе
<?php endif;?>
<br/>
<li id="orderstat<?=$ORDER->order_id?>" class="filter fclose">
<span onclick="switchClass(document.getElementById('orderstat<?=$ORDER->order_id?>'), 'fclose'); ">
 <i></i><font color="#003366">Статистика поведения пользователя при заказе</font>
</span>
<div class="filter-content statreport">
<table>
 <thead style="font-weight:bold;border-bottom:1px solid">
     <tr><td>Эту страницу смотрел</td><td>Адрес страницы</td><td>Отсюда пришел</td></tr>
 </thead>
 <?php foreach(getOrderStat($ORDER->order_id, 1) as $log_id=>$ROW):?>
 <?php if (!(strpos($ROW->request,'order_action=orderstep1')===false)): ?>
 <tr><td><b><?=$ROW->page_title?></b></td><td><b><?=$ROW->uri?></b></td><td><b><?=$ROW->referer?></b></td></tr>
 <?php else:?>
 <tr><td><?=substr($ROW->page_title,0,50).(strlen($ROW->page_title)>50?'...':'')?></td><td><?=$ROW->uri?></td><td><?=$ROW->referer?></td></tr>
 <?php endif;?>
 <?php endforeach;?>
</table>
</div>
</li>
</div>
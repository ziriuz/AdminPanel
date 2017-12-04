<div id=contMain>
<table border="0" cellspacing="0" cellpadding="0"  width=100%>
 <tr>
  <!-- .......................... Левое меню .......................... -->
  <td valign=top width=170px>
    <div class=nav>
    <h3>Заказы</h3>
    <div class=navItem>
      <img src="images/status0.gif" > Новый
      <br><img src="images/status1.gif" > Согласовывается
      <br><img src="images/status2.gif" > Подтвержден
      <br><img src="images/status3.gif" > Создан заказ в крурьерской службе
      <br><img src="images/status4.gif" > Отправлен
      <br><img src="images/status5.gif" > Получен клиентом
      <br><img src="images/status6.gif" > Отменен
    </div>
		  
    <div class=navItem>
		  <form method=post>
      <label><u>Фильтр списка заказов</u></label>
	  <br><input type="checkbox" value="0" name="f_status[]" <?=$g_status[0]?>> Новый
      <br><input type="checkbox" value="1" name="f_status[]" <?=$g_status[1]?>> Согласовывается
      <br><input type="checkbox" value="2" name="f_status[]" <?=$g_status[2]?>> Подтвержден
      <br><input type="checkbox" value="3" name="f_status[]" <?=$g_status[3]?>> Создан заказ в крурьерской службе
      <br><input type="checkbox" value="4" name="f_status[]" <?=$g_status[4]?>> Отправлен
      <br><input type="checkbox" value="5" name="f_status[]" <?=$g_status[5]?>> Получен клиентом
      <br><input type="checkbox" value="6" name="f_status[]" <?=$g_status[6]?>> Отменен
			<input type=hidden name=actoin value="APPLY_FILTER">
			<br><input type=submit value="Применить">
			</form>			
    </div>
   </div>
  </td>
  <!-- .......................... Содержимое .......................... -->
  <td valign=top>
    <div id=catContent>
     <div class=searchform>
     <form>
      <label>Номер заказа</label>
      <input type=text name=order_number value="<?=$ORDER_NUBER?>">
      <input type=submit value="Найти">
      <em><?=$MESSAGE?></em>
     </form>
     </div>
     <br>

     <h3> Заказы </h3>
     <br>
     <div class=orderlist>
     <form method=post>
     <input type=hidden name=f_action value = "CHANGE_STATUS">
      <label>Изменить статус для выделенных на</label>
      <select name=f_status>
       <option value=0><img src="images/status0.gif" > Новый </option>
       <option value=1><img src="images/status1.gif" > Согласовывается </option>
       <option value=2><img src="images/status2.gif" > Подтвержден </option>
       <option value=3><img src="images/status3.gif" > Создан заказ в крурьерской службе </option>
       <option value=4><img src="images/status4.gif" > Отправлен </option>
       <option value=5><img src="images/status5.gif" > Получен клиентом </option>
       <option value=6><img src="images/status6.gif" > Отменен </option>
      </select>
      <input type=submit value="Применить">
      <input type=submit name="f_print_ge" value="PRINT_GE">
     <table>
      <tr valign=top>
      <td><b></b></td><td><b>ID</b></td><td><b></b></td><td><b>Статус оплаты</b></td><td nowrap><b>Дата</b></td><td><b>Имя</b></td><td> </td><td><b>Доставка</b></td><td><b>Сумма</b></td><td><b>Сумма Итого</b></td><td><b>Код скидки</b></td>
      <td><b>Примечание</b></td><td><b>Налож. платеж</b></td><td><b>Доп. Сведения</b></td>
     </tr>

     <?php foreach($orders as $order_id => $ORDER):?>
     <?php //$ITEM = new Item($item_id);?>
     <tr valign=top>
      <td>
        <input type=checkbox name=f_order_id[] value=<?=$order_id?>>
      </td>
      <td>
        <a href="orders.php?forder_id=<?=$order_id?>">
        <?=$ORDER['order_number']?></a>
      </td>
      <td>
        <img src="images/status<?=$ORDER['status']?>.gif" >
      </td><td><div><?=$ORDER['gateway']?></div>
          <div><span style="color: #e54545;"><?=$ORDER['payment_status']?></span></div></td>
      <td nowrap>
        <?=$ORDER['created_at']?>
      </td>
      <td>
        <div><b><?=$ORDER['customer_name']?></b></div>
        <div><?=$ORDER['email']?></div>
        <div>тел: <span style="color: #0275d8;"><?=$ORDER['phone']?></span></div>
      </td>
	  <td>
        <img src="images/dt_<?=$ORDER['shipping_type']?>.png" >
      </td>
      
      <td>
          <div><b><?=$ORDER['shipping_type']?> - <?=$ORDER['shipping_price']?> руб</b></div>
          <div><span style="color: #0275d8;"><?=$ORDER['shipping_address']?></span></div>
          <div>другой адрес:<span style="color: #e54545;"><?=$ORDER['delivery_address']?></span></div>
          <div>время доставки:<span style="color: #e54545;"><?=$ORDER['delivery_dt']?></span></div>
          <div>итого доставка:<span style="color: #e54545;"><?=$ORDER['delivery_total']?></span></div>
          <div>ID заказа ТК:<span style="color: #e54545;"><?=$ORDER['transp_number']?></span></div>
      </td>
      <td nowrap>
        <?=$ORDER['subtotal_price']?>
      </td>
      <td nowrap>
        <?=$ORDER['total_price']?>
      </td>
      <td nowrap>
        <?=$ORDER['total_discounts']?>
      </td>
      <td>
        <?=$ORDER['note']?>
      </td>
      <td><?=$ORDER['cash_on_delivery']?></td>
      <td>
        <?=$ORDER['comments']?>
      </td>
     </tr>
     <?php endforeach;?>
     </table>
     </form>
     </div>
    </div>
  </td>
 </tr>
</table>
</div>
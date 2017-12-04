<?php
  session_start();
	include("includes/functions.php");


  if(!$USER=displayLogin()){ if(isset($sql)) $sql->close(); exit;}

  $gorder_id = '';
	$LISTORDERS = false;
  $SHOWORDER = false;
  $PRINTORDER = false;
  $IMPORTORDER = false;
  $AJAXORDERITEMS = false;
  $ORDERS = array();
  $MESSAGES = array();
  $MESSAGE = '';
  $TITLE = 'Панель управления магазином';
	$g_status = array('checked','checked','checked','checked','checked','','');
	if (!isset($_SESSION['STATUS_FILTER'])){
	  $STATUS_FILTER = '0,1,2,3,4';
		$_SESSION['STATUS_FILTER'] = $STATUS_FILTER;
	} else  $STATUS_FILTER = $_SESSION['STATUS_FILTER'];
  if (isset($forder_id)){
    $gorder_id = $forder_id;

    if (isset($action)&&$action=='PRINT_ORDER') $PRINTORDER = true;
    elseif (isset($action)&&$action=='IMPORT_ORDER') $IMPORTORDER = true;
	elseif (isset($action)&&$action=='AJAX_ORDER_ITEMS') $AJAXORDERITEMS = true;
    else $SHOWORDER = true;

    if (isset($f_action) && $f_action == 'CHANGE_NOTE2') changeOrders('CHANGE_NOTE2',$forder_id,$f_note2);
    elseif (isset($f_action) && $f_action = 'CHANGE_STATUS' && isset($f_order_id)) changeOrders('CHANGE_STATUS',$f_order_id,$f_status);

    if (isset($action)&&$action=='MAKE_RESERVE') {
	  if(isset($forderout ))require('actions/order_out.php');
	  else require('actions/make_reserve.php');
	}
    $ORDER = getOrderInfo($forder_id);
	$MIN_PRICE = ((int)$ORDER->price_range==0?200:(int)$ORDER->price_range);
	$DELIV_PRICE = ($ORDER->deliv_price>0?$ORDER->deliv_price:$MIN_PRICE + 50*((int)(($ORDER->amount + $MIN_PRICE)/ 1000)));
		
	if ($ORDER->deliv_price + (int)$ORDER->price_range == 0){
	  $DELIV_PRICE = 'не указана';
	}
	
    $ORDERITEMS = getOrderItems($forder_id);
    $TITLE = "Заказ № $ORDER->order_id от $ORDER->order_date";
  }
  else {
	  $LISTORDERS = true;
    if (isset($f_action) && $f_action = 'CHANGE_STATUS' && isset($f_order_id)){
      changeOrders('CHANGE_STATUS',$f_order_id,$f_status);
    }		
    if (isset($f_actoin) && $f_actoin = 'APPLY_FILTER'){
		  $STATUS_FILTER = implode(',',$f_status);
  		$_SESSION['STATUS_FILTER'] = $STATUS_FILTER;
    }   
  }
  foreach($g_status as $i=>$s){
    if (strpos($STATUS_FILTER,(string)$i)!==false) $g_status[$i] = 'checked'; else $g_status[$i] = '';
  }
	if ($LISTORDERS)  $ORDERS = getOrders($STATUS_FILTER);


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//RU" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML lang="ru,en">
<HEAD>
    <TITLE><?=$TITLE?></TITLE>
    <META content="text/html"; charset="windows-1251" http-equiv="Content-Type" />
    <link rel="stylesheet" href="prd_main.css"/>
    <!--[if IE7]><link rel="stylesheet" type="text/css" href="prd_main_ie.css" media="all" /><![endif]-->
    <script language="JavaScript" src="ajax.js"></script>

	<script type="text/javascript" src="extjs/adapter/ext/ext-base.js"></script>
    <script type="text/javascript" src="extjs/ext-all.js"></script>
	
    <link rel="stylesheet" type="text/css" href="extjs/resources/css/ext-all.css" />
    <?php if($LISTORDERS):?>
	
	<script type="text/javascript">
	Ext.onReady(function(){
	<?php foreach($ORDERS as $order_id => $ORDER):?>
	/*new Ext.ToolTip({
        target: 'ajax-tip<?=$ORDER->order_id?>',
        width: 600,
        autoLoad: {url: 'orders1.php?forder_id=<?=$ORDER->order_id?>&action=AJAX_ORDER_ITEMS'},
        dismissDelay: 15000 // auto hide after 15 seconds
    });*/
	new Ext.ToolTip({
        autoLoad: {url: 'orders1.php?forder_id=<?=$ORDER->order_id?>&action=AJAX_ORDER_ITEMS'},
        target: 'ajax-tip<?=$ORDER->order_id?>',
        anchor: 'right',
        html: null,
        width: 600,
        autoHide: false,
        closable: true,
        //contentEl: 'content-tip', // load content from the page
        listeners: {
            'render': function(){
                this.header.on('click', function(e){
                    e.stopEvent();
                    Ext.Msg.alert('Link', 'Link to something interesting.');
                    Ext.getCmp('content-anchor-tip').hide();
                }, this, {delegate:'a'});
            }
        }
    });
	<?php endforeach;?>
	Ext.QuickTips.init();
	});
	</script>
	<?php endif;?>
</HEAD>
<BODY>
<?php if($PRINTORDER):?>
<?php require("templates/printorder.htm"); require("templates/printorder.htm"); exit(); ?>
<?php elseif($IMPORTORDER):?>
<?php require("templates/importorder.htm"); exit(); ?>
<?php elseif($AJAXORDERITEMS):?>
<?php require("templates/ajaxorderitems.htm"); exit(); ?></BODY>
</HTML>
<?php endif ?>
<!-- .......................... Заголовок .......................... -->
<?php require("templates/menu.htm");?>
<div id=contMain>
<table border="0" cellspacing="0" cellpadding="0"  width=100%>
 <tr>
  <!-- .......................... Левое меню .......................... -->
  
  <td valign=top width=170px>
    <div class=nav>
    <h3>Заказы</h3>
    <div class=navItem>
      <img src="images/status0.gif" > Новый
      <br><img src="images/status1.gif" > Подтвержден
      <br><img src="images/status2.gif" > Изготавливается в цеху
      <br><img src="images/status3.gif" > Передан курьеру
      <br><img src="images/status4.gif" > Отправлен по почте
      <br><img src="images/status5.gif" > Выполнен
      <br><img src="images/status6.gif" > Отменен
    </div>
		  
    <div class=navItem>
		  <form method=post>
      <label><u>Фильтр списка заказов</u></label>
			<br><input type="checkbox" value="0" name="f_status[]" <?=$g_status[0]?>> Новый
      <br><input type="checkbox" value="1" name="f_status[]" <?=$g_status[1]?>> Подтвержден
      <br><input type="checkbox" value="2" name="f_status[]" <?=$g_status[2]?>> Изготавливается в цеху
      <br><input type="checkbox" value="3" name="f_status[]" <?=$g_status[3]?>> Передан курьеру
      <br><input type="checkbox" value="4" name="f_status[]" <?=$g_status[4]?>> Отправлен по почте
      <br><input type="checkbox" value="5" name="f_status[]" <?=$g_status[5]?>> Выполнен
      <br><input type="checkbox" value="6" name="f_status[]" <?=$g_status[6]?>> Отменен
			<input type=hidden name=f_actoin value="APPLY_FILTER">
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
      <label>ID заказа</label>
      <input type=text name=forder_id value="<?=$gorder_id?>">
      <input type=submit value="Найти">
      <em><?=$MESSAGE?></em>
     </form>
     </div>
     <br>
     <?php if($SHOWORDER):?>
       <div class=orderinfo>
       <a target=_blank href="orders.php?forder_id=<?=$ORDER->order_id?>&action=PRINT_ORDER">Печать</a>
       <a href="orders.php?forder_id=<?=$ORDER->order_id?>&action=IMPORT_ORDER">Импорт</a>
       <h2><img src="images/status<?=$ORDER->status?>.gif" style="margin-bottom:0px"> Заказ №<?=$ORDER->order_id?> от <?=$ORDER->order_date?></h2>
       <table>
        <tr><td>ФИО: <b><?=$ORDER->name?></b>
        <td rowspan="5" valign="top">

       <form action = "orders.php?forder_id=<?=$ORDER->order_id?>" method=post>
       <input type=hidden name=f_action value = "CHANGE_STATUS">
       <input type=hidden name=f_order_id[] value=<?=$ORDER->order_id?>>       
       <select name=f_status>
       <option value=0 <?=($ORDER->status==0?'selected':'')?>><img src="images/status0.gif" > Новый </option>
       <option value=1 <?=($ORDER->status==1?'selected':'')?>><img src="images/status1.gif" > Подтвержден </option>
       <option value=2 <?=($ORDER->status==2?'selected':'')?>><img src="images/status2.gif" > Изготавливается в цеху </option>
       <option value=3 <?=($ORDER->status==3?'selected':'')?>><img src="images/status3.gif" > Передан курьеру</option>
       <option value=4 <?=($ORDER->status==4?'selected':'')?>><img src="images/status4.gif" > Отправлен по почте </option>
       <option value=5 <?=($ORDER->status==5?'selected':'')?>><img src="images/status5.gif" > Выполнен</option>
       <option value=6 <?=($ORDER->status==6?'selected':'')?>><img src="images/status6.gif" > Отменен</option>
       </select>
       <input type=submit value="Изменить статус">
       </form>
<br>
			<form method=post>
			<input type=hidden name=f_action value="CHANGE_NOTE2">
			<input type="image" src="images/btn-save.jpg" alt="Сохранить">
			<label>Доп. сведения</label>
			<br><textarea rows="5" cols="50" label="test" id="note2" name="f_note2"><?=$ORDER->note2?></textarea>
			</form>
		 </td>
        <tr><td>E-mail: <b><?=$ORDER->email?></b>
        <tr><td>Конт. телефон: <b><?=$ORDER->phone?></b>
        <tr><td>Адрес: <b><?=$ORDER->address?></b>
        <tr><td>Комментарии: <br><b><?=$ORDER->note?></b>
       </table>
       <?php if($ORDERITEMS):?>
	     <form method=post>
		 <input type=hidden name=action value=MAKE_RESERVE>
         <table cellspacing=0 cellpadding=0 border=0>
		 <thead>
         <tr>
              <td align=left>Артикул</td>
              <td align=left>Наименование</td>
              <td align=center>Цена</td>
              <td align=center>Кол-во</td>
              <td align=center>Стоимость</td>
			  <td align=center>Зарезервировать</td>
			  <td align=center>Надо шить</td>
			  <td align=center>Отгрузка</td>
         </tr>
		 </thead>
		 <tbody>
		 <?php $nmcl = 0;$flag=0?>
         <?php foreach ($ORDERITEMS as $i => $ORDERITEM):?>
           <?php $ITEM = new Item($ORDERITEM->nmcl_id);
		     if ($nmcl!=$ORDERITEM->nmcl_id) $flag=0; else $flag++;
             $nmcl = $ORDERITEM->nmcl_id;
		   ?>
           <tr>  <td align=left> <?=$ITEM->alt_code?></td>
		         <td align=left> <?=$ITEM->name?><a href=index.php?fitem_id=<?=$ORDERITEM->nmcl_id?>>...</a><br>Размер: <?=$ORDERITEM->ctg_name?>
                 <td align=center nowrap> <?=$ORDERITEM->price?> руб.
                 <td align=center nowrap> <span class="<?=($ORDERITEM->quantity>1?'highlight':'')?>"><?=$ORDERITEM->quantity?></span>
                 <td align=center nowrap> <?=$ORDERITEM->amount?> руб.
				 <?php if ($flag==0):?>
				 <td align=right nowrap rowspan=<?=$ORDERITEM->rowspan?>>
				 <?php foreach(getOrderItemWrhRests($ORDER->order_id,$ORDERITEM->nmcl_id) as $i=>$o):?>				   
				   <input type=hidden name=fcard_id[] value=<?=$o->card_id?>>
				   <p>размер: <span class=<?=($ORDERITEM->ctg_id==$o->ctg_id?'highlight':'')?>><b><?=$o->ctg_name?></b></span>  ост. <?=$o->quantity?> шт.
				   <input type=checkbox name=freserve[] value=<?=$o->card_id?> <?=$o->reserved_qty>0?'checked':''?>> 
				   <input type=text size=3 name=freserve_qty[] value=<?=$o->reserved_qty>0?$o->reserved_qty:$ORDERITEM->quantity?>> шт.
				   <?=(isset($MESSAGES[$o->card_id])?'<br><em>'.$MESSAGES[$o->card_id].'</em>':'')?>
				   </p>				 
				 <?php endforeach;?>
				 </td>
				 <?php endif?>
                 <td align=center nowrap>
				  <input type=hidden name=forder_item[] value=<?=$ORDERITEM->nmcl_id.'-'.$ORDERITEM->ctg_id?>>				 
				  <input type=checkbox name=fprod_order[] value=<?=$ORDERITEM->nmcl_id.'-'.$ORDERITEM->ctg_id?>>	
				  <input type=text size=3 name=fprod_order_qty[] value=<?=$ORDERITEM->quantity?>> шт.	 				 
				 <?php if ($flag==0):?>
				 <td align=right nowrap rowspan=<?=$ORDERITEM->rowspan?>>
				 <?php foreach(getOrderItemWrhRests($ORDER->order_id,$ORDERITEM->nmcl_id) as $i=>$o):?>				   
				   <input type=hidden name=fout_card_id[] value=<?=$o->card_id?>>
				   <p>размер: <span class=<?=($ORDERITEM->ctg_id==$o->ctg_id?'highlight':'')?>><b><?=$o->ctg_name?></b></span>  ост. <?=$o->quantity?> шт.
				   <input type=checkbox name=fwrhout[] value=<?=$o->card_id?> <?=$o->reserved_qty+$o->out_qty>0?'checked':''?>> 
				   <input type=text size=3 name=fwrhout_qty[] value=<?=$o->reserved_qty+$o->out_qty>0?$o->reserved_qty+$o->out_qty:0?>> шт.
				   <?=(isset($MESSAGES1[$o->card_id])?'<br><em>'.$MESSAGES1[$o->card_id].'</em>':'')?>
				   </p>				 
				 <?php endforeach;?>
				 </td>
				 <?php endif?>
           </tr>
         <?php endforeach;?>
           <tr> <td align=right nowrap colspan=4> <b>Итого к оплате:
                <td align=center nowrap> <b> <?=$ORDER->amount?> руб.</b>
				<td colspan=2 align=center><input type=submit name='fmakereserve' value="Зарезервировать / заказать пошив">
				<td align=center><input type=submit name='forderout' value="Отгрузить">
           </tr>
			 		<?php if(strlen($ORDER->deliv_name)>0):?>
          <tr> <td align=left nowrap colspan=4> Способ доставки:<br> <?=$ORDER->deliv_name?> (<?=$ORDER->geo_zone?>) 
                <td align=center nowrap> <br><?=(strlen($ORDER->deliv_price)>0?$ORDER->deliv_price:$ORDER->price_range)?> руб.
				<td><td>
					</tr>
          <?php endif?>
			 		<?php if($ORDER->deliv_price>0):?>
          <tr> <td align=right nowrap colspan=4> <b>Итого c доставкой</b>
                <td align=center nowrap> <b><?=$ORDER->amount+$ORDER->deliv_price?> руб.</b>
				<td><td>
					</tr>
          <?php endif?>
		 </tbody>	 
         </table>
		 </form>
       <?php else:?>
          Нет позиций в вашем заказе
       <?php endif;?>
			 <br>
			 <li id="orderstat<?=$ORDER->order_id?>" class="filter fclose">
       <span onclick="switchClass(document.getElementById('orderstat<?=$ORDER->order_id?>'), 'fclose'); ">
          <i></i><font color="#003366">Статистика поведения пользователя при заказе</font>
       </span>
       <div class="filter-content statreport">
			 <table>
			 <thead style="font-weight:bold;border-bottom:1px solid">
			 	 <tr><td>Эту страницу смотрел</td><td>Адрес страницы</td><td>Отсюда пришел</td>
			 </thead>
			 <?php foreach(getOrderStat($ORDER->order_id, 1) as $log_id=>$ROW):?>
			   <?php if (!(strpos($ROW->request,'order_action=orderstep1')===false)): ?>
			   <tr><td><b><?=$ROW->page_title?></b></td><td><b><?=$ROW->uri?></b></td><td><b><?=$ROW->referer?></b></td>
				 <?php else:?>
			   <tr><td><?=substr($ROW->page_title,0,50).(strlen($ROW->page_title)>50?'...':'')?></td><td><?=$ROW->uri?></td><td><?=$ROW->referer?></td>
				 <?php endif;?>
			 <?php endforeach;?>
			 </table>
			 </div></li>
       </div>
     <?php else:?>
     <h3> Заказы </h3>
     <br>
     <div class=orderlist>
     <form method=post>
     <input type=hidden name=f_action value = "CHANGE_STATUS">
      <label>Изменить статус для выделенных на</label>
      <select name=f_status>
       <option value=0><img src="images/status0.gif" > Новый </option>
       <option value=1><img src="images/status1.gif" > Подтвержден </option>
       <option value=2><img src="images/status2.gif" > Изготавливается в цеху </option>
       <option value=3><img src="images/status3.gif" > Передан курьеру</option>
       <option value=4><img src="images/status4.gif" > Отправлен по почте </option>
       <option value=5><img src="images/status5.gif" > Выполнен</option>
       <option value=6><img src="images/status6.gif" > Отменен</option>
      </select>
      <input type=submit value="Применить">
     <table>
      <tr valign=top>
      <td><b></b></td><td><b>ID</b></td><td><b></b></td><td nowrap><b>Дата</b></td><td><b>Имя</b></td><td width="100px"><b>email</b></td>
      <td><b>Телефон</b></td><td><b>Адрес</b></td><td><b>Сумма</b></td><td><b>Примечание</b></td><td><b>Доп. Сведения</b></td>
     </tr>

     <?php foreach($ORDERS as $order_id => $ORDER):?>
     <?php //$ITEM = new Item($item_id);?>
     <tr valign=top>
      <td>
        <input type=checkbox name=f_order_id[] value=<?=$order_id?>>
      </td>
      <td>
        <a href="orders.php?forder_id=<?=$order_id?>">
        <?=$order_id?></a>
      </td>
      <td>
        <img src="images/status<?=$ORDER->status?>.gif" >
      </td>
      <td nowrap>
        <?=$ORDER->order_date?>
      </td>
      <td>
        <?=$ORDER->name?>
      </td>
      <td>
        <?=$ORDER->email?>
      </td>
      <td>
        <?=$ORDER->phone?>
      </td>
      <td>
        <?=$ORDER->address?>
      </td>
      <td>
	  <div id="ajax-tip<?=$ORDER->order_id?>"><?=$ORDER->amount?></div>        
      </td>
      <td>
        <?=$ORDER->note?>
      </td>
      <td>
        <?=$ORDER->note2?>
      </td>
     </tr>
     <?php endforeach;?>
     </table>
     </form>
     </div>
     <?php endif;?>
    </div>
  </td>
 </tr>
</table>
</div>

<!-- .......................... Подвал .......................... -->
<div id="FooterBar">
</div>
</BODY>
</HTML>
<?php if(isset($sql)) $sql->close();?>
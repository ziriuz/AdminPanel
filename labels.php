<?php
  session_start();
	include("includes/functions.php");
include("actions/labelscontroller.php");

  if(!$USER=displayLogin()){ if(isset($sql)) $sql->close(); exit;}
$PRINTLABELS = false;
  $CATEGORY = getRefElements('wrh_size','SIZE');
  $action = (isset($_GET["action"])?$_GET["action"]:null);
  $labelList = array();
  $DEF_ID = 0;
  if (isset($action)){
      switch ($action){
      case 'ADD_ARTICUL':
	 $quantity=$_POST["filter_quantity"];
	 $size=$_POST["filter_size"];
	 $alt_code=$_POST["filter_alt_code"];
         $label = array(
             "quantity"=>$quantity,
             "size"=>$size,
             "alt_code"=>$alt_code
             );
         if(isset($_SESSION["label_list"]))
             $labelList = $_SESSION["label_list"];
         $labelList[] = $label;
         $_SESSION["label_list"] = $labelList;
	 $controller=new LabelController();
	 $aLabels=$controller->getLabels($alt_code,$size,$quantity);
         if (isset($_SESSION["labels"]))
             $labels = $_SESSION["labels"];
         else
             $labels = array();
         $labels = array_merge($labels, $aLabels);
         $_SESSION["labels"] = $labels;
         break;
     case 'PRINT_LABELS':
         $PRINTLABELS = true;
         $labels = $_SESSION["labels"];
         break;
     default : break;
  }
  }
  else{
      unset($_SESSION["label_list"]);
      unset($_SESSION["labels"]);
  }
  $gorder_id = '';
  $LISTORDERS = false;
  $SHOWORDER = false;
  $PRINTORDER = false;
  $PRINTPOST = false;
  $PRINTPOSTCHECK = false;
  $IMPORTORDER = false;
  
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
    elseif (isset($action)&&$action=='PRINT_POST') $PRINTPOST = true;
    elseif (isset($action)&&$action=='PRINT_POST_CHECK') $PRINTPOSTCHECK = true;
    elseif (isset($action)&&$action=='IMPORT_ORDER') $IMPORTORDER = true;
    elseif (isset($action)&&$action=='PRINT_LABELS') $PRINTLABELS = true;    
    else $SHOWORDER = true;

    if (isset($f_action) && $f_action == 'CHANGE_NOTE2') changeOrders('CHANGE_NOTE2',$forder_id,$f_note2);
    elseif (isset($f_action) && $f_action = 'CHANGE_STATUS' && isset($f_order_id)) changeOrders('CHANGE_STATUS',$f_order_id,$f_status);

    if (isset($action)&&$action=='MAKE_RESERVE') {
	  if(isset($forderout ))require('actions/order_out.php');
	  if(isset($forderin ))require('actions/order_in.php');
	  elseif(isset($fmakereserve)) require('actions/make_reserve.php');
	  elseif(isset($fcancelreserve)) require('actions/cancel_reserve.php');
	  elseif(isset($forderprod)) require('actions/make_prodorder.php');
	}
    $ORDER = getOrderInfo($forder_id);
	$MIN_PRICE = ((int)$ORDER->price_range==0?200:(int)$ORDER->price_range);
	$DELIV_PRICE = ($ORDER->deliv_price>0?$ORDER->deliv_price:$MIN_PRICE + 50*((int)(($ORDER->amount + $MIN_PRICE)/ 1000)));
		
	if ($ORDER->deliv_price + (int)$ORDER->price_range == 0){
	  $DELIV_PRICE = 'не указана';
	}
	class PostTransfer{
	 public $amount;
	 public $toName;
	 public $toAddress;
	 public $toIndex;
	 public $toInn;
	 public $toKor;
	 public $toBank;
	 public $toAccount;
	 public $toBik;
	 public $fromName=array(' ');
	 public $fromAddress=array(' ');
	 public $fromIndex;
	 function __construct(){
 	  $this->toName='ИП Валиева Алина Талгатовна';
	  $this->toAddress='Москва, Юрловский проезд 25-10';
	  $this->toIndex ='127566';
	  $this->toInn='771565287490';
	  $this->toKor='30101810000000000201';
	  $this->toBank='ОАО АКБ &quot;АВАНГАРД&quot;';
	  $this->toAccount='40802810300030003775';
	  $this->toBik='044525201';
	  //$postTrans->fromName=array(' ');
	  //$postTrans->fromAddress=array(' ');
	  return null;
	 }
    }
	
	$postTrans = new PostTransfer();
	if ($PRINTPOSTCHECK){
	$postTrans->amount=$ORDER->amount+$DELIV_PRICE;
	$postTrans->fromName[0]=$ORDER->last_name;
	$postTrans->fromName[1]=$ORDER->first_name.' '.$ORDER->middle_name;
	$postTrans->fromAddress[0]=' ';
	$postTrans->fromAddress[1]=$ORDER->address;
	$postTrans->fromIndex='000000';
	}elseif($PRINTPOST){
	  $postTrans->toName=$_POST['toName'];
	  $postTrans->toAddress=$_POST['toAddress'];
	  $postTrans->toIndex =$_POST['toIndex'];
	  $postTrans->toInn=$_POST['toInn'];
	  $postTrans->toKor=$_POST['toKor'];
	  $postTrans->toBank=$_POST['toBank'];
	  $postTrans->toAccount=$_POST['toAccount'];
	  $postTrans->toBik=$_POST['toBik'];
	$postTrans->amount=$_POST['amount'];
	$postTrans->fromName[0]=$_POST['fromName0'];
	$postTrans->fromName[1]=$_POST['fromName1'];
	$postTrans->fromAddress[0]=$_POST['fromAddress0'];
	$postTrans->fromAddress[1]=$_POST['fromAddress1'];
	$postTrans->fromIndex=$_POST['fromIndex'];
	}
	
    $ORDERITEMS = getOrderItems($forder_id);
	if (count($ORDERITEMS)>0)
	  $_SESSION['currentrow'] = max(array_keys($ORDERITEMS));
		   
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

   if($PRINTPOST) {require("templates/printpost.htm"); exit(); }
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//RU" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML lang="ru,en">
<HEAD>
    <TITLE><?=$TITLE?></TITLE>
    <META content="text/html"; charset="windows-1251" http-equiv="Content-Type" />
    <link rel="stylesheet" href="prd_main.css"/>
	<link rel="stylesheet" href="comment.css"/>
    <!--[if IE7]><link rel="stylesheet" type="text/css" href="prd_main_ie.css" media="all" /><![endif]-->
    <script language="JavaScript" src="ajax.js"></script>
</HEAD>
<BODY>
<?php if($PRINTORDER):?>
<?php require("templates/printorder.htm"); require("templates/printorder.htm"); exit(); ?>
<?php elseif($PRINTPOSTCHECK):?>
<?php require("templates/printpostcheck.htm"); exit(); ?>
<?php elseif($PRINTPOST):?>
<?php require("templates/printpost.htm"); exit(); ?>
<?php elseif($IMPORTORDER):?>
<?php require("templates/importorder.htm"); exit(); ?>
<?php elseif($PRINTLABELS):?>
<?php require("templates/printorder-label.htm"); exit(); ?>
</BODY>
</HTML>
<?php endif ?>
<!-- .......................... Заголовок .......................... -->
<?php require("templates/menu.htm");?>
<div id=contMain>
<div>
      <form method=post>
	  <input id=fnmcl_id<?=$DEF_ID?> type=hidden name=fnmcl_id[]  size="20" value="">
      Поиск по ключу <input autocomplete="off" id=item<?=$DEF_ID?> type=text name=fnmcl_name[]  size="20" value="" onkeyupp = "changeEditMode(<?=$DEF_ID?>,'findnmcl','search','item<?=$DEF_ID?>')">
	  <a href="javascript:changeEditMode(<?=$DEF_ID?>,'findnmcl','search','item<?=$DEF_ID?>')">
	  <img src="images/btn-find.jpg" alt="Найти товар"><span id=ajaxmesfindnmcl<?=$DEF_ID?>></span>
	  </a>
      <div id=findnmcl<?=$DEF_ID?> class=searchlist></div>
     </form>
     <form method=post action="?action=ADD_ARTICUL">
	  <label for=filter_alt_code>Артикул</label><input id=filter_alt_code type=text name="filter_alt_code"/>
	  <label for=filter_quantity>Количество</label><input id=filter_quantity type=text name="filter_quantity"/>
	  <label for=filter_size>Размер</label><select name="filter_size">	  
	<option value=''> </option>
    <?php foreach($CATEGORY as $id=>$name):?>
	<option value=<?=$id?> <?=($ORDERITEM->ctg_id==$id?'selected':'')?>><?=$name?></option>
	<?php endforeach;?>
   </select><input type=submit value="add">
     </form>
    <form method=post action="?action=PRINT_LABELS" target="_blank">
	<input type=hidden name="filter_alt_code[]"/>
        <input type=hidden name="filter_quantity[]"/>
	<input type=hidden name="filter_size[]"/>
        <?php foreach($labelList as $i=>$label1):?>
        <p><label>Артикул</label><?=$label1["alt_code"]?>
	  <label>Количество</label><?=$label1["quantity"]?>
	  <label>Размер</label><?=$label1["size"]?>
        </p>
        <?php endforeach;?>
	<input type=submit value="print">
     </form>    
</div>
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
	<div class=navItem>
	 <a href="javascript:callPickpoint()"><img src="images/dt_pickpoint.png" alt="pickpoint"></a>
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
       <a target=_blank href="orders.php?forder_id=<?=$ORDER->order_id?>&action=PRINT_POST_CHECK">Почтовый перевод</a>
       <a href="orders.php?forder_id=<?=$ORDER->order_id?>&action=IMPORT_ORDER">Импорт</a>
       <a href="orders.php?forder_id=<?=$ORDER->order_id?>&action=PRINT_LABELS">Этикетки</a>
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
        <tr><td>Код скидки: <br><b><?=$ORDER->dis_code?></b>
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
        </div></div>
        <div class=tab_page_hide id=od_tabpage2>
         <div class=tab_content id=od2>
          <?php require("templates/printorder1.htm");?>
        </div></div>
		 <?php if($ORDER->deliv_type=='pickpoint'):?>
        <div class=tab_page_hide id=od_tabpage3>
         <div class=tab_content id=od3>
          <?php require("templates/pickpointform.htm");?>
        </div></div>
		 <?php endif;?>		
        </div>	 
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
      <td><b>Телефон</b></td><td> </td><td><b>Адрес</b></td><td><b>Сумма</b></td><td><b>Код скидки</b></td><td><b>Примечание</b></td><td><b>Доп. Сведения</b></td>
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
        <img src="images/dt_<?=$ORDER->deliv_type?>.png" >
      </td>
      <td>
        <?=$ORDER->address?>
      </td>
      <td nowrap>
        <?=$ORDER->amount?>
      </td>
      <td nowrap>
        <?=$ORDER->dis_code?>
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
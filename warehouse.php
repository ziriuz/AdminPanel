<?php
  session_start();
  include("includes/functions.php");


  if(!$USER=displayLogin()){if(isset($sql)) $sql->close(); exit;}

  $TITLE = 'Панель управления магазином';

  $MESSAGE = '';
  $ISAJAX = false;
  $ADDTRANSACTION=false;
  $WRH = getRefElements('warehouse');
  $ACTION_TEMPLATE = 'default';
  if(!isset($fwrh_id)) $WRH_ID=1; else $WRH_ID=$fwrh_id;
  $WRHNAME = $WRH[$WRH_ID];
  $RESTS_TOTAL = wrhrests_totals($WRH_ID);
  if(isset($action))
  { 
  	$WRH = getRefElements('warehouse');
	$GOODS_TYPE = getRefElements('wrh_goods_type');
	$UNIT = getRefElements('wrh_um');
	$CATEGORY = getRefElements('wrh_size','SIZE');
	$NEWTRANROW = 0;
	$NEWITEM = 0;
	$_SESSION['currentrow'] = 0;
  switch ($action){
    case 'wrh_in':
	 $dc_flag=1;
	 $is_reserve=0;
	 $ADDTRANSACTION=true;
	break;
    case 'wrh_out':
	 $dc_flag=-1;
	 $is_reserve=0;	
	 $ADDTRANSACTION=true;
	break;
    case 'wrh_reserve':
	 $dc_flag=-1;
	 $is_reserve=1;
	 $ADDTRANSACTION=true;
	break;
    case 'wrh_unreserve':
	 $dc_flag=1;
	 $is_reserve=1;
	 $ADDTRANSACTION=true;
	break;
	case 'add_wrh_transactions':
	  foreach($fnmcl_id as $i=>$fitem_id){
	     if (strlen($fnmcl_name[$i])>0 && (float)$fquantity[$i] > 0.0){
	     $ITEM = new item($fitem_id);
			$tran_id = $ITEM->createWrhTransaction(
			  $fwrh_id,
			  $fgoods_type_id[$i],
			  (strlen($fctg_id[$i])>0?$fctg_id[$i]:'null'),
			  (strlen($fprice[$i])>0?$fprice[$i]:'null'),
			  (strlen($fum_id[$i])>0?$fum_id[$i]:'null'),
			  $fdc_flag,
			  (isset($fis_reserve)?$fis_reserve:0),
			  $fquantity[$i],
			  'null'
			);
			if ($tran_id < 0){  			  
			  echo $ITEM->getMessage();				
			}
		}
	  }
	break;
	case 'make_model':break;;	
	case 'make_prod_order01':
	     $ACTION_TEMPLATE = 'templates/forms/'.$action.'.htm';
		 $WRH_ACTION=$action;
		 $DOCTP = 'PRODORDER';
		 $DOCS = getDocuments(1);
		 $VIEWDOC = false;
         if (isset($fdoc_id)) {	
           if($fdoc_id >= 0){
		   $DOC = new Document($fdoc_id);
		   if (count($DOC->items)>0)
		   $_SESSION['currentrow'] = max(array_keys($DOC->items));
		   }
		   $VIEWDOC = true;
		 }
		 break;
	case'workout_orders':break;
	case'wrh_msk_out':break;
	case'wrh_msk_in':break;
	case 'make_prod_order02':;
	case 'wrh_bish_in':
	     $ACTION_TEMPLATE = 'templates/forms/'.$action.'.htm';
		 $WRH_ACTION=$action;
		 $DOCTP = 'PRODORDER';
		 $DOCS = getDocuments(1);
		 $VIEWDOC = false;
         if (isset($fdoc_id)) {	
           if($fdoc_id >= 0){
		   $DOC = new Document($fdoc_id);
		   if (count($DOC->items)>0)
		   $_SESSION['currentrow'] = max(array_keys($DOC->items));
		   }
		   $VIEWDOC = true;
		 }
		 break;
	case'wrh_bish_in':break;
	case'wrh_bish_out': 
	     $ACTION_TEMPLATE = 'templates/forms/'.$action.'.htm';
	     $WRH_ACTION=$action;
		 $DOCTP = 'WRHMOVE';
		 $DOCS = getDocuments(2);
		 $VIEWDOC = false;
         if (isset($fdoc_id)) {	
           if($fdoc_id >= 0){
		   $DOC = new Document($fdoc_id);
		   if (count($DOC->items)>0)
		   $_SESSION['currentrow'] = max(array_keys($DOC->items));
		   }
		   $VIEWDOC = true;
		 }
		 break;
	
  }}
  
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//RU" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML lang="ru,en">
<HEAD>
    <TITLE><?=$TITLE?></TITLE>
    <META content="text/html"; charset="windows-1251" http-equiv="Content-Type" />
    <link rel="stylesheet" href="prd_main.css"/>
    <!--[if IE7]><link rel="stylesheet" type="text/css" href="prd_main_ie.css" media="all" /><![endif]-->
    <script language="JavaScript" src="ajax.js"></script>
</HEAD>
<BODY>
<!-- .......................... Заголовок .......................... -->
<?php require("templates/menu.htm");?>
<div id=contMain>
<table border="0" cellspacing="0" cellpadding="0"  width=100%>
 <tr>
  <!-- .......................... Левое меню .......................... -->
  <td valign=top width=170px>
    <div class=nav>
    <h3>Склад</h3>
    <div class=navItem>
	  <?php foreach($WRH as $id=>$wrh): ?>
	  <?=$wrh?><br>
	  <form method=get>
	    <input type=hidden name=action value=wrh_in>
		<input type=hidden name=fwrh_id value=<?=$id?>>
		<input type=submit value="Приход">				
	  </form>
	  <form method=get>
	    <input type=hidden name=action value=wrh_out>
		<input type=hidden name=fwrh_id value=<?=$id?>>
		<input type=submit value="Расход">				
	  </form>
	  <form method=get>
	    <input type=hidden name=action value=wrh_reserve>
		<input type=hidden name=fwrh_id value=<?=$id?>>
		<input type=submit value="Резерв">				
	  </form>
	  <form method=get>
	    <input type=hidden name=action value=wrh_unreserve>
		<input type=hidden name=fwrh_id value=<?=$id?>>
		<input type=submit value="Отменить Резерв">				
	  </form>
 	  <br><br>
	  <?php endforeach?>
    </div>
   </div>
  </td>
  <!-- .......................... Содержимое .......................... -->
  <td valign=top>
   <div id=catContent>
   <?php if($ACTION_TEMPLATE=='default'):?>
   <table>
   <tr>
   <td>
     <h2>Москва</h2>
	 <p><a href=warehouse.php?action=make_model>Разработать модель</a></p>
	 <p><a href=warehouse.php?action=make_prod_order01>Заказать пошив</a></p>
	 <p><a href=warehouse.php?action=workout_orders>Обработать заказы</a></p>
	 <p><a href=warehouse.php?action=wrh_msk_out>Отгрузить</a></p>
	 <p><a href=warehouse.php?action=wrh_msk_in>Получить Товар</a></p>
   </td>
   <td>
     <h2>Бишкек</h2>
	 <p><a href=warehouse.php?action=make_prod_order02>Передать заказы в цех</a></p>
	 <p><a href=warehouse.php?action=wrh_bish_in>Принять из цеха на склад</a></p>
	 <p><a href=warehouse.php?action=wrh_bish_out>Отправить в москву</a></p>
   </td>
   </tr>
   </table>
   <?php else:?>
   <?php require($ACTION_TEMPLATE);?>   
   <?php endif;?>
   
   <br><br><br><br><br><br><br>
   <div class=editform>
   <form  method="get" name=wrhrest>
    <input type="hidden" name="action" value="view_wrh"/>
    <div class=blockedit>
    <p><label>Остатки на складе</label>
    <select name=fwrh_id>
	<?php foreach($WRH as $id=>$wrh): ?>
	<option <?=($id==$WRH_ID?'selected':'')?> value=<?=$id?>><?=$wrh?></option>	  
	<?php endforeach?>
    </select>
	<input type=submit value="Применить">
	<label>Сумма</label> <?=$RESTS_TOTAL->amount?> <label>Сумма мелк. опт</label> <?=$RESTS_TOTAL->amount_mid?> <label>Сумма опт</label> <?=$RESTS_TOTAL->amount_min?>
    </p>
    </div>
   </form>
   </div>
   <br>
   <?php if($ADDTRANSACTION):?>
   <?php require('templates/addwrhform.htm');?>
   <?php else:?>
   <?php require('templates/wrhrests.htm');?>
   <?php endif?>
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
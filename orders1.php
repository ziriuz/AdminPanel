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
  $TITLE = '������ ���������� ���������';
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
	  $DELIV_PRICE = '�� �������';
	}
	
    $ORDERITEMS = getOrderItems($forder_id);
    $TITLE = "����� � $ORDER->order_id �� $ORDER->order_date";
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
<!-- .......................... ��������� .......................... -->
<?php require("templates/menu.htm");?>
<div id=contMain>
<table border="0" cellspacing="0" cellpadding="0"  width=100%>
 <tr>
  <!-- .......................... ����� ���� .......................... -->
  
  <td valign=top width=170px>
    <div class=nav>
    <h3>������</h3>
    <div class=navItem>
      <img src="images/status0.gif" > �����
      <br><img src="images/status1.gif" > �����������
      <br><img src="images/status2.gif" > ��������������� � ����
      <br><img src="images/status3.gif" > ������� �������
      <br><img src="images/status4.gif" > ��������� �� �����
      <br><img src="images/status5.gif" > ��������
      <br><img src="images/status6.gif" > �������
    </div>
		  
    <div class=navItem>
		  <form method=post>
      <label><u>������ ������ �������</u></label>
			<br><input type="checkbox" value="0" name="f_status[]" <?=$g_status[0]?>> �����
      <br><input type="checkbox" value="1" name="f_status[]" <?=$g_status[1]?>> �����������
      <br><input type="checkbox" value="2" name="f_status[]" <?=$g_status[2]?>> ��������������� � ����
      <br><input type="checkbox" value="3" name="f_status[]" <?=$g_status[3]?>> ������� �������
      <br><input type="checkbox" value="4" name="f_status[]" <?=$g_status[4]?>> ��������� �� �����
      <br><input type="checkbox" value="5" name="f_status[]" <?=$g_status[5]?>> ��������
      <br><input type="checkbox" value="6" name="f_status[]" <?=$g_status[6]?>> �������
			<input type=hidden name=f_actoin value="APPLY_FILTER">
			<br><input type=submit value="���������">
			</form>			
    </div>
   </div>
  </td>
  <!-- .......................... ���������� .......................... -->
  <td valign=top>
    <div id=catContent>
     <div class=searchform>
     <form>
      <label>ID ������</label>
      <input type=text name=forder_id value="<?=$gorder_id?>">
      <input type=submit value="�����">
      <em><?=$MESSAGE?></em>
     </form>
     </div>
     <br>
     <?php if($SHOWORDER):?>
       <div class=orderinfo>
       <a target=_blank href="orders.php?forder_id=<?=$ORDER->order_id?>&action=PRINT_ORDER">������</a>
       <a href="orders.php?forder_id=<?=$ORDER->order_id?>&action=IMPORT_ORDER">������</a>
       <h2><img src="images/status<?=$ORDER->status?>.gif" style="margin-bottom:0px"> ����� �<?=$ORDER->order_id?> �� <?=$ORDER->order_date?></h2>
       <table>
        <tr><td>���: <b><?=$ORDER->name?></b>
        <td rowspan="5" valign="top">

       <form action = "orders.php?forder_id=<?=$ORDER->order_id?>" method=post>
       <input type=hidden name=f_action value = "CHANGE_STATUS">
       <input type=hidden name=f_order_id[] value=<?=$ORDER->order_id?>>       
       <select name=f_status>
       <option value=0 <?=($ORDER->status==0?'selected':'')?>><img src="images/status0.gif" > ����� </option>
       <option value=1 <?=($ORDER->status==1?'selected':'')?>><img src="images/status1.gif" > ����������� </option>
       <option value=2 <?=($ORDER->status==2?'selected':'')?>><img src="images/status2.gif" > ��������������� � ���� </option>
       <option value=3 <?=($ORDER->status==3?'selected':'')?>><img src="images/status3.gif" > ������� �������</option>
       <option value=4 <?=($ORDER->status==4?'selected':'')?>><img src="images/status4.gif" > ��������� �� ����� </option>
       <option value=5 <?=($ORDER->status==5?'selected':'')?>><img src="images/status5.gif" > ��������</option>
       <option value=6 <?=($ORDER->status==6?'selected':'')?>><img src="images/status6.gif" > �������</option>
       </select>
       <input type=submit value="�������� ������">
       </form>
<br>
			<form method=post>
			<input type=hidden name=f_action value="CHANGE_NOTE2">
			<input type="image" src="images/btn-save.jpg" alt="���������">
			<label>���. ��������</label>
			<br><textarea rows="5" cols="50" label="test" id="note2" name="f_note2"><?=$ORDER->note2?></textarea>
			</form>
		 </td>
        <tr><td>E-mail: <b><?=$ORDER->email?></b>
        <tr><td>����. �������: <b><?=$ORDER->phone?></b>
        <tr><td>�����: <b><?=$ORDER->address?></b>
        <tr><td>�����������: <br><b><?=$ORDER->note?></b>
       </table>
       <?php if($ORDERITEMS):?>
	     <form method=post>
		 <input type=hidden name=action value=MAKE_RESERVE>
         <table cellspacing=0 cellpadding=0 border=0>
		 <thead>
         <tr>
              <td align=left>�������</td>
              <td align=left>������������</td>
              <td align=center>����</td>
              <td align=center>���-��</td>
              <td align=center>���������</td>
			  <td align=center>���������������</td>
			  <td align=center>���� ����</td>
			  <td align=center>��������</td>
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
		         <td align=left> <?=$ITEM->name?><a href=index.php?fitem_id=<?=$ORDERITEM->nmcl_id?>>...</a><br>������: <?=$ORDERITEM->ctg_name?>
                 <td align=center nowrap> <?=$ORDERITEM->price?> ���.
                 <td align=center nowrap> <span class="<?=($ORDERITEM->quantity>1?'highlight':'')?>"><?=$ORDERITEM->quantity?></span>
                 <td align=center nowrap> <?=$ORDERITEM->amount?> ���.
				 <?php if ($flag==0):?>
				 <td align=right nowrap rowspan=<?=$ORDERITEM->rowspan?>>
				 <?php foreach(getOrderItemWrhRests($ORDER->order_id,$ORDERITEM->nmcl_id) as $i=>$o):?>				   
				   <input type=hidden name=fcard_id[] value=<?=$o->card_id?>>
				   <p>������: <span class=<?=($ORDERITEM->ctg_id==$o->ctg_id?'highlight':'')?>><b><?=$o->ctg_name?></b></span>  ���. <?=$o->quantity?> ��.
				   <input type=checkbox name=freserve[] value=<?=$o->card_id?> <?=$o->reserved_qty>0?'checked':''?>> 
				   <input type=text size=3 name=freserve_qty[] value=<?=$o->reserved_qty>0?$o->reserved_qty:$ORDERITEM->quantity?>> ��.
				   <?=(isset($MESSAGES[$o->card_id])?'<br><em>'.$MESSAGES[$o->card_id].'</em>':'')?>
				   </p>				 
				 <?php endforeach;?>
				 </td>
				 <?php endif?>
                 <td align=center nowrap>
				  <input type=hidden name=forder_item[] value=<?=$ORDERITEM->nmcl_id.'-'.$ORDERITEM->ctg_id?>>				 
				  <input type=checkbox name=fprod_order[] value=<?=$ORDERITEM->nmcl_id.'-'.$ORDERITEM->ctg_id?>>	
				  <input type=text size=3 name=fprod_order_qty[] value=<?=$ORDERITEM->quantity?>> ��.	 				 
				 <?php if ($flag==0):?>
				 <td align=right nowrap rowspan=<?=$ORDERITEM->rowspan?>>
				 <?php foreach(getOrderItemWrhRests($ORDER->order_id,$ORDERITEM->nmcl_id) as $i=>$o):?>				   
				   <input type=hidden name=fout_card_id[] value=<?=$o->card_id?>>
				   <p>������: <span class=<?=($ORDERITEM->ctg_id==$o->ctg_id?'highlight':'')?>><b><?=$o->ctg_name?></b></span>  ���. <?=$o->quantity?> ��.
				   <input type=checkbox name=fwrhout[] value=<?=$o->card_id?> <?=$o->reserved_qty+$o->out_qty>0?'checked':''?>> 
				   <input type=text size=3 name=fwrhout_qty[] value=<?=$o->reserved_qty+$o->out_qty>0?$o->reserved_qty+$o->out_qty:0?>> ��.
				   <?=(isset($MESSAGES1[$o->card_id])?'<br><em>'.$MESSAGES1[$o->card_id].'</em>':'')?>
				   </p>				 
				 <?php endforeach;?>
				 </td>
				 <?php endif?>
           </tr>
         <?php endforeach;?>
           <tr> <td align=right nowrap colspan=4> <b>����� � ������:
                <td align=center nowrap> <b> <?=$ORDER->amount?> ���.</b>
				<td colspan=2 align=center><input type=submit name='fmakereserve' value="��������������� / �������� �����">
				<td align=center><input type=submit name='forderout' value="���������">
           </tr>
			 		<?php if(strlen($ORDER->deliv_name)>0):?>
          <tr> <td align=left nowrap colspan=4> ������ ��������:<br> <?=$ORDER->deliv_name?> (<?=$ORDER->geo_zone?>) 
                <td align=center nowrap> <br><?=(strlen($ORDER->deliv_price)>0?$ORDER->deliv_price:$ORDER->price_range)?> ���.
				<td><td>
					</tr>
          <?php endif?>
			 		<?php if($ORDER->deliv_price>0):?>
          <tr> <td align=right nowrap colspan=4> <b>����� c ���������</b>
                <td align=center nowrap> <b><?=$ORDER->amount+$ORDER->deliv_price?> ���.</b>
				<td><td>
					</tr>
          <?php endif?>
		 </tbody>	 
         </table>
		 </form>
       <?php else:?>
          ��� ������� � ����� ������
       <?php endif;?>
			 <br>
			 <li id="orderstat<?=$ORDER->order_id?>" class="filter fclose">
       <span onclick="switchClass(document.getElementById('orderstat<?=$ORDER->order_id?>'), 'fclose'); ">
          <i></i><font color="#003366">���������� ��������� ������������ ��� ������</font>
       </span>
       <div class="filter-content statreport">
			 <table>
			 <thead style="font-weight:bold;border-bottom:1px solid">
			 	 <tr><td>��� �������� �������</td><td>����� ��������</td><td>������ ������</td>
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
     <h3> ������ </h3>
     <br>
     <div class=orderlist>
     <form method=post>
     <input type=hidden name=f_action value = "CHANGE_STATUS">
      <label>�������� ������ ��� ���������� ��</label>
      <select name=f_status>
       <option value=0><img src="images/status0.gif" > ����� </option>
       <option value=1><img src="images/status1.gif" > ����������� </option>
       <option value=2><img src="images/status2.gif" > ��������������� � ���� </option>
       <option value=3><img src="images/status3.gif" > ������� �������</option>
       <option value=4><img src="images/status4.gif" > ��������� �� ����� </option>
       <option value=5><img src="images/status5.gif" > ��������</option>
       <option value=6><img src="images/status6.gif" > �������</option>
      </select>
      <input type=submit value="���������">
     <table>
      <tr valign=top>
      <td><b></b></td><td><b>ID</b></td><td><b></b></td><td nowrap><b>����</b></td><td><b>���</b></td><td width="100px"><b>email</b></td>
      <td><b>�������</b></td><td><b>�����</b></td><td><b>�����</b></td><td><b>����������</b></td><td><b>���. ��������</b></td>
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

<!-- .......................... ������ .......................... -->
<div id="FooterBar">
</div>
</BODY>
</HTML>
<?php if(isset($sql)) $sql->close();?>
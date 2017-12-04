<?php
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
	 private function init(){
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
	  //return null;
	 }
     function __construct($order,$data,$check){
        $this->init();
        if ($check){
            $this->amount=$order->amount+$DELIV_PRICE;
            $this->fromName[0]=$order->last_name;
            $this->fromName[1]=$order->first_name.' '.$order->middle_name;
            $this->fromAddress[0]=' ';
            $this->fromAddress[1]=$order->address;
            $this->fromIndex='000000';
        }else{
            $this->toName=$data['toName'];
            $this->toAddress=$data['toAddress'];
            $this->toIndex =$data['toIndex'];
            $this->toInn=$data['toInn'];
            $this->toKor=$data['toKor'];
            $this->toBank=$data['toBank'];
            $this->toAccount=$data['toAccount'];
            $this->toBik=$data['toBik'];
            $this->amount=$data['amount'];
            $this->fromName[0]=$data['fromName0'];
            $this->fromName[1]=$data['fromName1'];
            $this->fromAddress[0]=$data['fromAddress0'];
            $this->fromAddress[1]=$data['fromAddress1'];
            $this->fromIndex=$data['fromIndex'];
        }
     }
    }
  function getTranspStatus(&$ORDER){
     if ($ORDER->transp_number!=NULL){
        try{
            $ORDER->shippingOrder=getSdekOrderInfo($ORDER->transp_number);
            $ORDER->shippingCheck["ErrNumber"]=false;
            $ORDER->shippingCheck["ErrCashOnDeliv"]=false;
            $ORDER->shippingCheck["ErrAddServiceSum"]=false;
            $ORDER->shippingCheck["ErrDeliverySum"]=false;
            $ORDER->shippingCheck["ErrNumber"]=$ORDER->shippingOrder["Number"]!=$ORDER->sid;
            $ORDER->shippingCheck["ErrCashOnDeliv"]=$ORDER->shippingOrder["CashOnDeliv"] != $ORDER->amount_total;
            if($ORDER->shippingOrder["CashOnDelivFact"]>0){
                $ORDER->shippingCheck["ErrCashOnDeliv"]=$ORDER->shippingOrder["CashOnDelivFact"] != $ORDER->amount_total;
                $ORDER->shippingCheck["ErrAddServiceSum"]=round(floatval($ORDER->shippingOrder["addServiceSum"])/floatval($ORDER->shippingOrder["CashOnDelivFact"]),3)!=0.03;
            }
            if ($ORDER->shippingOrder["DeliverySum"]!=125){
                $ORDER->shippingCheck["ErrDeliverySum"]=true;
            }
        } catch (SdekException $ex) {
            $ORDER->comments .= ' ['.$ex->getMessage().']';
        }
     }
  }
    
    
  session_start();
  include("includes/functions.php");
  require("lib/sdek_api.php");
  if(!$USER=displayLogin()){ if(isset($sql)) $sql->close(); exit;}

  $ORDER_NUBER = '';
  $LISTORDERS = false;
  $SHOWORDER = false;
  $PRINTORDER = false;
  $PRINTPOST = false;
  $PRINTPOSTCHECK = false;
  $IMPORTORDER = false;
  $PRINTLABELS = false;
  $ORDERS = array();
  $MESSAGES = array();
  $MESSAGE = '';
  $TITLE = 'Панель управления магазином';
  $g_status = array('checked','checked','checked','checked','checked','','');
  $orderId = filter_input(INPUT_GET,'forder_id', FILTER_VALIDATE_FLOAT);
  $orderNumber = filter_input(INPUT_GET,'order_number',FILTER_SANITIZE_STRING);
  $action = filter_input(INPUT_GET,'action',FILTER_SANITIZE_STRING);
  $printGE = filter_input(INPUT_POST,'f_print_ge',FILTER_SANITIZE_STRING);
  if (!isset($_SESSION['STATUS_FILTER'])){
   $STATUS_FILTER = '0,1,2,3,4';
   $_SESSION['STATUS_FILTER'] = $STATUS_FILTER;
  } else  $STATUS_FILTER = $_SESSION['STATUS_FILTER'];
  if ($orderNumber){
    try{
      $orderId = findOrder($orderNumber);
    }
    catch (Exception $e){
      $MESSAGE = $e->getMessage();
      $orderId = false;
    }
  }
  if ($orderId){
    try{
    $ORDER = getOrderInfo($orderId);
    $ORDERITEMS = getOrderItems($orderId);
	if (count($ORDERITEMS)>0)
	  $_SESSION['currentrow'] = max(array_keys($ORDERITEMS));
    $TITLE = "Заказ № $ORDER->order_number от $ORDER->order_date";
    $ORDER_NUBER = $ORDER->order_number;
    $details = $_POST;
    if (isset($action)&&$action=='PRINT_ORDER') $PRINTORDER = true;
    elseif (isset($action)&&$action=='PRINT_POST'){
        $PRINTPOST = true;
        $postTrans = new PostTransfer($ORDER,$details,false);
    }
    elseif (isset($action)&&$action=='PRINT_POST_CHECK'){
        $PRINTPOSTCHECK = true;
        $postTrans = new PostTransfer($ORDER,$details,true);
    }
    elseif (isset($action)&&$action=='IMPORT_ORDER') $IMPORTORDER = true;
    elseif (isset($action)&&$action=='PRINT_LABELS') $PRINTLABELS = true;    
    else $SHOWORDER = true;

    if (isset($f_action) && $f_action == 'CHANGE_NOTE2') {
        $details["order_id"] = $orderId;
        saveOrderDetails($details);
        changeOrders('CHANGE_NOTE2',$orderId,$f_note2);
        $ORDER = getOrderInfo($orderId);
    }
    elseif (isset($f_action) && $f_action = 'CHANGE_STATUS' && isset($f_order_id)) {
        changeOrders('CHANGE_STATUS',$f_order_id,$f_status);
        $ORDER = getOrderInfo($orderId);
    }
    if (isset($action)&&$action=='MAKE_RESERVE') {
	  if(isset($forderout ))require('actions/order_out.php');
	  if(isset($forderin ))require('actions/order_in.php');
	  elseif(isset($fmakereserve)) require('actions/make_reserve.php');
	  elseif(isset($fcancelreserve)) require('actions/cancel_reserve.php');
	  elseif(isset($forderprod)) require('actions/make_prodorder.php');
	}
	$MIN_PRICE = ((int)$ORDER->price_range==0?200:(int)$ORDER->price_range);
	$DELIV_PRICE = ($ORDER->deliv_price>0?$ORDER->deliv_price:$MIN_PRICE + 50*((int)(($ORDER->amount + $MIN_PRICE)/ 1000)));
		
	if ($ORDER->deliv_price + (int)$ORDER->price_range == 0){
	  $DELIV_PRICE = 'не указана';
	}
        getTranspStatus($ORDER);
        $sdekPvzList = SdekApi::getPvzList($ORDER->post_id);
    }    
    catch (Exception $e){
      $MESSAGE = $e->getMessage();
      $orderId = false;
    }
  }
  if (!$orderId) {
	$LISTORDERS = true;
    $ordersList = array();
    if (isset($f_action) && $f_action = 'CHANGE_STATUS' && isset($f_order_id)){
      if ($printGE == 'PRINT_GE'){
          $ordersList = $f_order_id;
          $action ='PRINT_GE';
      } else {
        changeOrders('CHANGE_STATUS',$f_order_id,$f_status);
      }
    }		
    if (isset($f_actoin) && $f_actoin = 'APPLY_FILTER'){
		$STATUS_FILTER = implode(',',$f_status);
  		$_SESSION['STATUS_FILTER'] = $STATUS_FILTER;
    }   
  }
  foreach($g_status as $i=>$s){
    if (strpos($STATUS_FILTER,(string)$i)!==false) $g_status[$i] = 'checked'; else $g_status[$i] = '';
  }
  
  if ($action =='PRINT_GE') {
      $ORDERS = getOrdersByIds($ordersList);
  }elseif ($LISTORDERS) {
      $ORDERS = getOrders($STATUS_FILTER);
  }
  header('Content-type: text/html; charset=utf-8');
  if($PRINTPOST) {require("templates/printpost.htm"); exit(); }
  if ($action =='PRINT_GE')  {require("templates/print_ge.htm"); exit(); }
?><!DOCTYPE HTML>
<HTML lang="ru,en">
<HEAD>
    <TITLE><?=$TITLE?></TITLE>
    <META content="text/html"; charset="windows-1251" http-equiv="Content-Type" />
    <!-- Bootstrap core CSS -->
    <link href="application/views/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="application/views/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link rel="stylesheet" href="application/views/css/common.css"/>
    <link rel="stylesheet" href="application/views/css/notification.css"/>
    <link rel="stylesheet" href="application/views/css/form.css"/>
    <link rel="stylesheet" href="comment.css"/>
    <!--[if IE7]><link rel="stylesheet" type="text/css" href="prd_main_ie.css" media="all" /><![endif]-->
    <script language="JavaScript" src="ajax.js"></script>
    <script language="JavaScript" src="application/views/js/ajax2.js"></script>
    <script language="JavaScript" src="application/views/js/ui.js"></script>
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
<div id=contMain >

  <!-- .......................... Левое меню .......................... -->
    <!--div class=nav-->
    <div class="col-md-2">
    <h3>Заказы</h3>
    <div class="navItem nav">
      <img src="images/status0.gif" > Новый
      <br><img src="images/status1.gif" > Согласовывается
      <br><img src="images/status2.gif" > Подтвержден
      <br><img src="images/status3.gif" > Создан заказ в крурьерской службе
      <br><img src="images/status4.gif" > Отправлен
      <br><img src="images/status5.gif" > Получен клиентом
      <br><img src="images/status6.gif" > Отменен
    </div>
    <div class="navItem nav">
     <form method=post><input type=hidden name=f_actoin value="APPLY_FILTER">
      <label><u>Фильтр списка заказов</u></label>
      <br><input type="checkbox" value="0" name="f_status[]" <?=$g_status[0]?>> Новый
      <br><input type="checkbox" value="1" name="f_status[]" <?=$g_status[1]?>> Согласовывается
      <br><input type="checkbox" value="2" name="f_status[]" <?=$g_status[2]?>> Подтвержден
      <br><input type="checkbox" value="3" name="f_status[]" <?=$g_status[3]?>> Создан заказ в крурьерской службе
      <br><input type="checkbox" value="4" name="f_status[]" <?=$g_status[4]?>> Отправлен
      <br><input type="checkbox" value="5" name="f_status[]" <?=$g_status[5]?>> Получен клиентом
      <br><input type="checkbox" value="6" name="f_status[]" <?=$g_status[6]?>> Отменен
      <br><input type=submit value="Применить">
     </form>
    </div>
   </div>
  <!-- .......................... Содержимое .......................... -->
   <div class="col-md-10"">
    <div id=catContent>
     <div class=searchform>
     <form>
      <label>Номер заказа</label>
      <input type=text name=order_number value="<?=$ORDER_NUBER?>">
      <input type=submit value="Найти">
      <em><?=$MESSAGE?></em>
     </form>
     <?php if(!$SHOWORDER):?>
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
     <?php endif;?>
     </div>      
     <?php if($SHOWORDER):?>
     <?php require(VEIW_IM_PATH.'/admin_order_view.php');?>
     <?php else:?>
     <div>
     <div id="orders_table" style="margin-top:8px;display:block;height:500px; overflow-y: auto;" >
     <table class="table orderlist">
      <tr valign=top>
      <td><b></b></td><td><b>ID</b></td><td><b></b></td><td><b>Статус оплаты</b></td><td nowrap><b>Дата</b></td><td><b>Имя</b></td><td> </td><td><b>Доставка</b></td><td><b>Сумма</b></td><td><b>Сумма Итого</b></td><td><b>Код скидки</b></td>
      <td><b>Примечание</b></td><td><b>Налож. платеж</b></td><td><b>Доп. Сведения</b></td>
     </tr>
     <tbody>
     <?php foreach($ORDERS as $order_id => $ORDER):?>
     <?php //$ITEM = new Item($item_id);?>
     <tr valign=top>
      <td>
        <input type=checkbox name=f_order_id[] value=<?=$order_id?>>
      </td>
      <td>
        <a href="orders.php?forder_id=<?=$order_id?>">
        <?=$ORDER->sid?></a>
      </td>
      <td>
        <img src="images/status<?=$ORDER->status?>.gif" >
      </td><td><div><?=$ORDER->gateway?></div>
          <div><span style="color: #e54545;"><?=$ORDER->payment_status?></span></div></td>
      <td nowrap>
        <?=$ORDER->order_date?>
      </td>
      <td>
        <div><b><?=$ORDER->name?></b></div>
        <div><?=$ORDER->email?></div>
        <div>тел: <span style="color: #0275d8;"><?=$ORDER->phone?></span></div>
      </td>
      <td>
        <img src="images/dt_moscow.png" >
      </td>
      
      <td>
          <div><b><?=$ORDER->deliv_type?> - <?=$ORDER->deliv_price?> руб</b></div>
          <div><span style="color: #0275d8;"><?=$ORDER->address?></span></div>
          <div>другой адрес:<span style="color: #e54545;"><?=$ORDER->delivery_address?></span></div>
          <div>время доставки:<span style="color: #e54545;"><?=$ORDER->delivery_dt?></span></div>
          <div>итого доставка:<span style="color: #e54545;"><?=$ORDER->delivery_total?></span></div>
          <div>ID заказа ТК:<span style="color: #e54545;"><?=$ORDER->transp_number?></span></div>
      </td>
      <td nowrap>
        <?=$ORDER->amount?>
      </td>
      <td nowrap>
        <?=$ORDER->amount_total?>
      </td>
      <td nowrap>
        <?=$ORDER->dis_code?>
      </td>
      <td>
        <?=$ORDER->note?>
      </td>
      <td><?=$ORDER->cash_on_delivery?></td>
      <td>
        <?=$ORDER->note2?>
      </td>
     </tr>
     <?php endforeach;?>
     </tbody>
     </table>
     </div>
     </form>
     </div>
     <?php endif;?>
    </div>
  </div>
</div>

<!-- .......................... Подвал .......................... -->
<div id="FooterBar">
</div>
<script>
var orders_table = document.getElementById('orders_table');
if(orders_table!==null && orders_table !== undefined) {
   orders_table.style.height=(document.documentElement.clientHeight - getCoords(orders_table).top) + "px";
}
</script>
</BODY>
</HTML>
<?php if(isset($sql)) $sql->close();?>
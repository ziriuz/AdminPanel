<?php
class Controller_Orders2 extends Controller
{
  function action_index(){
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
  $action = filter_input(INPUT_POST,'action',FILTER_SANITIZE_STRING);
  $printGE = filter_input(INPUT_POST,'f_print_ge',FILTER_SANITIZE_STRING);
if (!isset($_SESSION['STATUS_FILTER'])){
   $STATUS_FILTER = '0,1,2,3,4';
   $_SESSION['STATUS_FILTER'] = $STATUS_FILTER;
  } else  $STATUS_FILTER = $_SESSION['STATUS_FILTER'];
  /*  if ($orderNumber){
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
    if (isset($f_actoin) && $f_actoin == 'APPLY_FILTER'){
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
  */
  if ($action == 'APPLY_FILTER'){
      $STATUS_FILTER = implode(',',$f_status);
      $_SESSION['STATUS_FILTER'] = $STATUS_FILTER; 
  }
  foreach($g_status as $i=>$s){
    if (strpos($STATUS_FILTER,(string)$i)!==false) $g_status[$i] = 'checked'; else $g_status[$i] = '';
  }
  $this->load->model('order');

        $data = $this->model_order->getOrders();
        $data['g_status'] = $g_status;
        $data['MESSAGE'] = $MESSAGE;
        $data['ORDER_NUBER'] = $ORDER_NUBER;
        //$data = $this->model->getOrders();
        header('Content-type: text/html; charset=utf-8');
        $this->view->generate('admin_orders_view.php', 'admin_template_view.php', $data);
    }    
}

<?php
  session_start();
  include("includes/functions.php");
  include("includes/articles.php");
  header('Content-type: text/html; charset=utf-8');
  if(!$USER=displayLogin()){if(isset($sql)) $sql->close(); exit;}
  $ISAJAX = true;
  if (isset($_POST['action'])) $action = $_POST['action'];
  elseif(isset($_GET['action'])) $action = $_GET['action'];
  if (isset($_POST['fitem_id'])) $fitem_id = $_POST['fitem_id'];
  elseif(isset($_GET['fitem_id'])) $fitem_id = $_GET['fitem_id'];
  if (!isset($action)) echo 'no_action|FAILED:::InvalidRequest';
  else switch($action){
   case 'create_pickpoint':
    if (!isset($fitem_id)) echo 'create_pickpoint|FAILED|'.$fitem_id.'|'.':::'.'Не задан элемент fitem_id';
	else{
     $forder_id = $fitem_id;
     $ORDER = getOrderInfo($forder_id);
     $pp_invoicenumber = $_POST['pp_invoicenumber'];
     $pp_id = $_POST['pp_id'];
     $pp_name = $_POST['pp_client_name'];
     $pp_phone = $_POST['pp_phone'];
     $pp_email = $_POST['pp_email'];
     $pp_payment = (isset($_POST['pp_payment'])?10003:10001);
     $pp_amount = $_POST['pp_amount'];
	 if (strlen($pp_invoicenumber)>0) echo 'create_pickpoint|FAILED|'.$forder_id.':::'."Заказ $forder_id уже зарегистрирован в PickPoint";
	 else{
	  require('pickpoint.php');
	  $ppApi = new PickpointApi(false);
	  $message = '';
	  $ppOk = true;
	  $pp_dlv_amount = $_POST['pp_dlv_amount'];
	  if (strlen($pp_dlv_amount)==0) { $message = "Укажите сумму доставки";  $ppOk = false;}
	  if ($ppOk)
      if (!$ppOk = $ppApi->login()) $message = $ppApi->getMessage();//iconv("UTF-8", "WINDOWS-1251",$ppApi->getMessage());
	  if ($ppOk){
	   if (!$ppOk = $ppApi->createSending($forder_id,$pp_name,$pp_id,$pp_phone,$pp_email,$pp_payment,101,(float)$pp_amount+(float)$pp_dlv_amount,0)) $message = $ppApi->getMessage();// iconv("UTF-8", "WINDOWS-1251", $ppApi->getMessage());
	   else foreach($ppApi->createdSendings as $i=> $created){
           $forder_id = $created->EDTN;
           $pp_invoicenumber = $created->InvoiceNumber;
           $pp_barcode = $created->Barcode;
	   }
	  }
	  if ($ppOk) {
       if (!$ppOk = $ppApi->makeLabel($pp_invoicenumber))  $message = $ppApi->getMessage();// iconv("UTF-8", "WINDOWS-1251", $ppApi->getMessage());
       else $pp_label = $ppApi->labelFile;
	  }
      if($ppApi->session) $ppApi->logout();
	  if ($ppOk)
	    if (!$ppOk = updateOrderPP($forder_id,$pp_invoicenumber,$pp_barcode,$pp_label)) $message = "DB Error:$pp_invoicenumber|$pp_barcode|$pp_label";
	  if ($ppOk) echo "create_pickpoint|OK|$fitem_id|$pp_invoicenumber|$pp_barcode|$pp_label:::отправление зарегистрировано";
	  else echo "create_pickpoint|FAILED|$fitem_id:::$message";
     }
	}
    break;
    case 'modify_comment':
      if (isset($section)&&isset($article)&&isset($commid)&&isset($status)){
        $ARTICLE=new article($section,$article,true);
        if ($ARTICLE->exists)
          if ($ARTICLE->modifyComment($commid,$status)) echo 'modify_comment|OK|'.$commid.'|'.$status;
          else echo 'modify_comment|FAILED|'.$commid.'|'.$status;
      }
      break;
    case 'change_edit_mode':      
        if (!isset($ftoken)) echo 'change_edit_mode|FAILED|'.$fitem_id.'|'.':::'.'Не задан элемент token';
        elseif (!isset($fmode)) echo 'change_edit_mode|FAILED|'.$fitem_id.'|'.$ftoken.':::'.'Не задан режим [редактирование/просмотр]';
        else 
         switch($ftoken){
         case 'item':
          if (isset($fitem_id)) $ITEM=new item($fitem_id);
          else { echo 'change_edit_mode|FAILED|'.$fitem_id.'|'.':::'.'Не задан элемент fitem_id'; break;}
            if ($fmode == 'view') echo 'change_edit_mode|OK|'.$fitem_id.'|'.$ftoken.':::'.$ITEM->description;
          elseif ($fmode == 'edit') {
           $GROUPS = getCatalog();
             echo 'change_edit_mode|OK|'.$fitem_id.'|'.$ftoken.':::';
           require("templates/itemform.htm");
          }
          else echo 'change_edit_mode|FAILED|'.$fitem_id.'|'.$ftoken.':::'.$fmode.' - Неверный режим [редактирование/просмотр]';
          break;
         case 'order':
          if (isset($fitem_id)){
             $forder_id = $fitem_id;
             $ORDER = getOrderInfo($forder_id);
           $ORDERITEMS = getOrderItems($forder_id);
           $CATEGORY = getRefElements('wrh_size','SIZE');
          }
          else { echo 'change_edit_mode|FAILED|'.$fitem_id.'|'.':::'.'Не задан элемент fitem_id'; break;}
            if ($fmode == 'view'){            
            echo 'change_edit_mode|OK|'.$fitem_id.'|'.$ftoken.':::';
          require("templates/printorder0.htm");
          }
          elseif ($fmode == 'edit') {          
             echo 'change_edit_mode|OK|'.$fitem_id.'|'.$ftoken.':::';
           require("templates/orderform.htm");
          }
          else echo 'change_edit_mode|FAILED|'.$fitem_id.'|'.$ftoken.':::'.$fmode.' - Неверный режим [редактирование/просмотр]';
          break;
          case 'edititem':
          if (isset($fitem_id)){
             if(isset($_GET["exparam"]))$fitem_id_del = $_GET["exparam"];
             if (!isset($fitem_id_del)) 
             echo 'del_orderitem|FAILED|'.$fitem_id.'|'.$fitem_id_del.':::Order item_id not defined!!!';
           else            
             echo 'del_orderitem|OK|'.$fitem_id.'|'.$fitem_id_del.'|'.($fmode == 'delete'?1:0);
          }
         break;
            case 'newtran':
          if (isset($fitem_id)) $ITEM=new item($fitem_id);
          else { echo 'change_edit_mode|FAILED|'.$fitem_id.'|'.':::'.'Не задан элемент fitem_id'; break;}
            if ($fmode == 'view') echo 'change_edit_mode|OK|'.$fitem_id.'|'.$ftoken.':::';
          elseif ($fmode == 'edit') {
           $WRH = getRefElements('warehouse');
           $GOODS_TYPE = getRefElements('wrh_goods_type');
           $UNIT = getRefElements('wrh_um');
           $CATEGORY = getRefElements('wrh_size','SIZE');
             echo 'change_edit_mode|OK|'.$fitem_id.'|'.$ftoken.':::';
           require("templates/addwrhtransform.htm");
          }
          break;
         case 'newwrhtran':
            if ($fmode == 'view') echo 'change_edit_mode|OK|'.$fitem_id.'|'.$ftoken.':::';
          elseif ($fmode == 'edit') {
           $WRH = getRefElements('warehouse');
           $GOODS_TYPE = getRefElements('wrh_goods_type');
           $UNIT = getRefElements('wrh_um');
           $CATEGORY = getRefElements('wrh_size','SIZE');
           if(!isset($_SESSION['currentrow'])) $_SESSION['currentrow']=0;
           $NEWTRANROW = $_SESSION['currentrow']+1;
           $_SESSION['currentrow'] = $NEWTRANROW;
             echo 'add_wrhrow|OK|'.$fitem_id.'|'.$ftoken.':::';
           require("templates/addwrhrow.htm");
          }
          break;
         case 'neworderitem':
            if ($fmode == 'view') echo 'change_edit_mode|OK|'.$fitem_id.'|'.$ftoken.':::';
          elseif ($fmode == 'edit') {
           $CATEGORY = getRefElements('wrh_size','SIZE');
           if(!isset($_SESSION['currentrow'])) $_SESSION['currentrow']=0;
           $NEWITEM = $_SESSION['currentrow']+1;
           $_SESSION['currentrow'] = $NEWITEM;
           $ORDERITEM = new OrderItem(0);
             echo 'add_orderitem|OK|'.$fitem_id.'|'.$ftoken.':::';
           require("templates/forms/addorderitem.htm");
          }
          break;                    
         case 'newdocitem':
            if ($fmode == 'view') echo 'change_edit_mode|OK|'.$fitem_id.'|'.$ftoken.':::';
          elseif ($fmode == 'edit') {
           $CATEGORY = getRefElements('wrh_size','SIZE');
           if(!isset($_SESSION['currentrow'])) $_SESSION['currentrow']=0;
           $NEWITEM = $_SESSION['currentrow']+1;
           $_SESSION['currentrow'] = $NEWITEM;
           $DOCITEM = new DocItem(0);
             echo 'add_docitem|OK|'.$fitem_id.'|'.$ftoken.':::';
           require("templates/forms/adddocitem.htm");
          }
          break;          
         case 'loaddocitems':
            if ($fmode == 'view') echo 'change_edit_mode|OK|'.$fitem_id.'|'.$ftoken.':::';
          else{
           $CATEGORY = getRefElements('wrh_size','SIZE');
           if(!isset($_SESSION['currentrow'])) $_SESSION['currentrow']=0;
           $NEWITEM = $_SESSION['currentrow'];
           if ($fmode == 'from-orders') $lines=getItemsToProd($fitem_id);
           elseif ($fmode == 'wrh-rests') $lines=getItemsToMove(2);
           echo 'add_docitem|OK|'.$fitem_id.'|'.$ftoken.':::';
           foreach($lines as $i => $DOCITEM){
            $NEWITEM++;
            require("templates/forms/adddocitem.htm");
           }
           $_SESSION['currentrow'] = $NEWITEM;
          }
          break;
         case 'category':
          if (isset($fitem_id)) $CATEGORY = new Category((int)$fitem_id);
          else { echo 'change_edit_mode|FAILED|'.$fitem_id.'|'.':::'.'Не задан элемент fitem_id'; break;}
            if ($fmode == 'view') echo 'change_edit_mode|OK|'.$fitem_id.'|'.$ftoken.':::'.$CATEGORY->name;
          elseif ($fmode == 'edit') {
           $CTGTYPES = getCategoryTypes();
             echo 'change_edit_mode|OK|'.$fitem_id.'|'.$ftoken.':::';
           require("templates/editcategory.htm");
          }
          elseif ($fmode == 'delete') {
           echo 'change_edit_mode|OK|'.$fitem_id.'|'.$ftoken.':::';
           require("templates/delcategory.htm");
          }
          break;
         case 'document':
          if (isset($fitem_id)) $DOC = new Document((int)$fitem_id);
          else { echo 'change_edit_mode|FAILED|'.$fitem_id.'|'.':::'.'Не задан элемент fitem_id'; break;}
            if ($fmode == 'view') echo 'change_edit_mode|OK|'.$fitem_id.'|'.$ftoken.':::'.$DOC->doc_name;
          elseif ($fmode == 'edit') {
           //$CTGTYPES = getCategoryTypes();
             echo 'change_edit_mode|OK|'.$fmode.'|'.$ftoken.':::';
           require("templates/forms/editdoc.htm");
          }
          elseif ($fmode == 'delete') {
           echo 'change_edit_mode|OK|'.$fitem_id.'|'.$ftoken.':::';
           require("templates/delcategory.htm");
          }
          break;
         case 'findnmcl':
          if ($fmode == 'view'){
            if (isset($exparam)){
           $ITEM= new item($exparam);
           echo 'set_tran_nmcl|OK|'.$fitem_id.'|'.$ftoken.'|'.$ITEM->id.'|'.$ITEM->price.':::'.$ITEM->alt_code.' '.$ITEM->name;
          }
          else
            echo 'change_edit_mode|OK|'.$fitem_id.'|'.$ftoken.':::';
          }
          elseif ($fmode == 'edit'||$fmode =='search') {
           if (isset($exparam) && strlen(trim($exparam))){
             //$exparam = iconv("UTF-8", "WINDOWS-1251", $exparam);
             $ITEMS = findItems($exparam);
           }
           else $ITEMS = array();
             echo 'change_edit_mode|OK|'.$fitem_id.'|'.$ftoken.':::';
           if (count($ITEMS)>0) foreach($ITEMS as $id=>$ITEM){               
              $itm = new Item($id);
            if ($fmode == 'edit')
              echo "<img src=../prd_lib/images/".$itm->foto_preview[0]." width=30px>".
                 "<a href=\"javascript:changeEditMode($fitem_id,'$ftoken','view','$ITEM->id');\">".                
                str_ireplace($exparam,'<b>'.$exparam.'</b>',$ITEM->alt_code.' '.$ITEM->name).'</a><br>';
            else
            echo "<img src=../prd_lib/images/".$itm->foto_preview[0]." width=30px>".
                 "<a href=index.php?fitem_id=$id>".                
                str_ireplace($exparam,'<b>'.$exparam.'</b>',$ITEM->alt_code.' '.$ITEM->name).'</a><br>';
                //str_ireplace($exparam,'<b>'.$exparam.'</b>',$ITEM->name).'</a><br>';

           }
           else echo 'Не найдено ни одной номенклатуры! '.$exparam;
           //require("templates/editcategory.htm");
          }
          break;
        }
    break;
    case 'modify_item':
      if (isset($fitem_id)){
        $ITEM = new item($fitem_id);
        $ITEM->grp_id = (int)$_POST["fgrp_id"];//(iconv("UTF-8", "WINDOWS-1251", $_POST["fgrp_id"]));
        $ITEM->name = strip_tags(filter_input(INPUT_POST,'fname',FILTER_SANITIZE_STRING));//(iconv("UTF-8", "WINDOWS-1251", $_POST["fname"]));
        $ITEM->title = strip_tags(filter_input(INPUT_POST,'ftitle',FILTER_SANITIZE_STRING));//(iconv("UTF-8", "WINDOWS-1251", $_POST["ftitle"]));
        $ITEM->alt_code = strip_tags(filter_input(INPUT_POST,'falt_code',FILTER_SANITIZE_STRING));//(iconv("UTF-8", "WINDOWS-1251", $_POST["falt_code"]));
        $ITEM->price = filter_input(INPUT_POST,'fprice', FILTER_VALIDATE_FLOAT);//(float)iconv("UTF-8", "WINDOWS-1251", $_POST["fprice"]);
        $ITEM->price_mid = filter_input(INPUT_POST,'fprice_mid', FILTER_VALIDATE_FLOAT);//(float)iconv("UTF-8", "WINDOWS-1251", $_POST["fprice_mid"]);
        $ITEM->price_min = filter_input(INPUT_POST,'fprice_min', FILTER_VALIDATE_FLOAT);//(float)iconv("UTF-8", "WINDOWS-1251", $_POST["fprice_min"]);
        $ITEM->status = filter_input(INPUT_POST,'fstatus', FILTER_VALIDATE_INT);//(int)iconv("UTF-8", "WINDOWS-1251", $_POST["fstatus"]);
        $ITEM->create_date = (filter_input(INPUT_POST,'fcreate_date',FILTER_SANITIZE_STRING));//iconv("UTF-8", "WINDOWS-1251", $_POST["fcreate_date"]);
        $ITEM->foto_alt_src = strip_tags(filter_input(INPUT_POST,'ffoto_alt',FILTER_SANITIZE_STRING));//(iconv("UTF-8", "WINDOWS-1251", $_POST["ffoto_alt"]));
        $ITEM->foto_name_src = strip_tags(filter_input(INPUT_POST,'ffoto_name',FILTER_SANITIZE_STRING));//(iconv("UTF-8", "WINDOWS-1251", $_POST["ffoto_name"]));
        //["fgrp_id"]
        $ITEM->description = filter_input(INPUT_POST,'fdescription',FILTER_DEFAULT);//iconv("UTF-8", "WINDOWS-1251", $_POST["fdescription"]);
        $ITEM->tech_description = filter_input(INPUT_POST,'ftech_description',FILTER_SANITIZE_STRING);//iconv("UTF-8", "WINDOWS-1251", $_POST["ftech_description"]);

           if (!$ITEM->updateItem()){
          echo 'modify_item|FAILED|'.$fitem_id.':::';
          echo $ITEM->getMessage();        
        }else{
          $ITEM = new item($fitem_id);
          echo 'modify_item|OK|'.$fitem_id."|$ITEM->alt_code - $ITEM->name|$ITEM->price|$ITEM->foto_alt|$ITEM->status".':::';
          require("templates/itemdesc.htm");
        }
      }
    break;
    case 'add_wrh_transaction':
      if (isset($fitem_id)){
      $ITEM = new item($fitem_id);
      $tran_id = $ITEM->createWrhTransaction(
        $fwrh_id,
        $fgoods_type_id,
        (strlen($fctg_id)>0?$fctg_id:'null'),
        (strlen($fprice)>0?$fprice:'null'),
        (strlen($fum_id)>0?$fum_id:'null'),
        $fdc_flag,
        (isset($fis_reserve)?$fis_reserve:0),
        $fquantity,
        'null'
      );
      if ($tran_id < 0){
          echo 'add_transaction|FAILED|'.$fitem_id.':::';
        echo $ITEM->getMessage();        
      }else{          
        $ITEM->getWrhTransactions();        
          echo 'add_transaction|OK|'.$fitem_id.':::';
        require('templates/itemtranstable.htm');
      }
      }
    break;
    case 'edit_category':
      if (isset($fitem_id)){
      $CATEGORY = new Category((int)$fitem_id);
      $CATEGORY->id=$fid;
      $CATEGORY->name=filter_input(INPUT_POST,'fname',FILTER_SANITIZE_STRING);//strip_tags(iconv("UTF-8", "WINDOWS-1251", $_POST["fname"]));      
      $CATEGORY->title=filter_input(INPUT_POST,'ftitle',FILTER_SANITIZE_STRING);//strip_tags(iconv("UTF-8", "WINDOWS-1251", $_POST["ftitle"]));
      //$CATEGORY->type_id=$ftype_id;
      $CATEGORY->type=ltrim($fitem_id,'0');
      $CATEGORY->description=filter_input(INPUT_POST,'fdescription',FILTER_DEFAULT);//iconv("UTF-8", "WINDOWS-1251", $_POST["fdescription"]);
      $CATEGORY->foto_name=filter_input(INPUT_POST,'ffoto_name',FILTER_SANITIZE_STRING);//strip_tags(iconv("UTF-8", "WINDOWS-1251", $_POST["ffoto_name"]));
      $CATEGORY->status=$fstatus;
      $CATEGORY->select_type=filter_input(INPUT_POST,'fselect_type',FILTER_SANITIZE_STRING);//strip_tags(iconv("UTF-8", "WINDOWS-1251", $_POST["fselect_type"]));
      if (!$CATEGORY->save()){
          echo 'edit_category|FAILED|'.$fitem_id.':::';
        echo $CATEGORY->getMessage();        
      }else{          
          echo 'edit_category|OK|'.$fitem_id.':::';
        if ((int)$fitem_id ==0) require('templates/category.htm');
        else echo $CATEGORY->name;        
      }
      }
    break;
    case 'edit_document':
      if (isset($fitem_id)){
      $DOC = new Document((int)$fid);
      $DOC->id=$fid;      
      $DOC->doctp_id=$fdoctp_id;
      $DOC->doc_date=filter_input(INPUT_POST,'fdoc_date',FILTER_DEFAULT);//iconv("UTF-8", "WINDOWS-1251", $_POST["fdoc_date"]);
      $DOC->doc_number=filter_input(INPUT_POST,'fdoc_number',FILTER_DEFAULT);//iconv("UTF-8", "WINDOWS-1251", $_POST["fdoc_number"]);
      $DOC->note=filter_input(INPUT_POST,'fnote',FILTER_DEFAULT);//iconv("UTF-8", "WINDOWS-1251", $_POST["fnote"]);
      $DOC->status=$_POST["fstatus"];
      $WRH_ACTION=$fwrh_action;
      if(isset($_POST["frowid"]))$frowid = $_POST["frowid"];
      else $frowid = array();
      $DOC->items = array();
      foreach ($frowid as $i=>$val){        
        $docitem = new DocItem($fline_id[$i]);
        $docitem->doc_id = $fid;
        $docitem->order_id = $forder[$i];
        $docitem->nmcl_id = $fnmcl_id[$i];
        $docitem->ctg_id = $fctg_id[$i];
        $docitem->quantity = $fquantity[$i];
        $DOC->items[$i] = $docitem;
      }
      if (!$DOC->save()){
          echo 'edit_document|FAILED|'.$fitem_id.':::';
        echo $DOC->getMessage();        
      }else{    
              $messages='';      
        foreach ($DOC->items as $i=>$val) $messages .= ';;'.$frowid[$i].'--'.$val->line_id.'--'.$val->getMessage();
        $messages = ltrim($messages,';');
          echo 'edit_document|OK|'.$fitem_id.'|'.$messages.'|'.$DOC->id.':::';
        if ((int)$fitem_id ==0) require('templates/forms/document.htm');
        else echo $DOC->doc_name;  
      }
      }
    break;
    case 'edit_order':
      if (isset($fitem_id)){      
      /*$DOC->id=$fid;      
      $DOC->doctp_id=$fdoctp_id;
      $DOC->doc_date=iconv("UTF-8", "WINDOWS-1251", $_POST["fdoc_date"]);
      $DOC->doc_number=iconv("UTF-8", "WINDOWS-1251", $_POST["fdoc_number"]);
      $DOC->note=iconv("UTF-8", "WINDOWS-1251", $_POST["fnote"]);
      $DOC->status=$_POST["fstatus"];
      $WRH_ACTION=$fwrh_action;*/
      if (!isset($forder_id)||$forder_id<=0) 
        echo 'edit_order|FAILED|'.$fitem_id.'||'.$forder_id.':::ORDER_ID not defined!!!';
      else{      
      if(isset($_POST["frowid"]))$frowid = $_POST["frowid"];
      else $frowid = array();
      $messages = '';
            $noerrors = true;      
      foreach ($frowid as $i=>$val){        
        $item = new OrderItem($fitems[$i]);
        if (!($fitems[$i]==0 and $fdeleted[$i] == 1)){
        $item->order_id = $forder_id;
        $item->nmcl_id = $fnmcl_id[$i];
        $item->nmcl_name = $fnmcl_name[$i];//iconv("UTF-8", "WINDOWS-1251", $fnmcl_name[$i]);
        $item->ctg_id = $fctg_id[$i];
        $item->quantity = $fquantity[$i];
        $item->price = $fprice[$i];
        $item->amount = $famount[$i];
        $item->deleted = $fdeleted[$i];
        if (!$item->save()) $noerrors = false;
        }
          $messages .= ';;'.$frowid[$i].'--'.$item->item_id.'--'.$item->getMessage(); 
      }
      $messages = ltrim($messages,';');
      if (!$noerrors){
        echo 'edit_order|FAILED|'.$fitem_id.'|'.$messages.'|'.$forder_id.':::Faild to save order items';
      } else{
        $ORDER = getOrderInfo($forder_id);
        $ORDERITEMS = getOrderItems($forder_id);
        echo 'edit_order|OK|'.$fitem_id.'|'.$messages.'|'.$forder_id.':::';
        require("templates/printorder0.htm");
      }
      /*
      if (!$DOC->save()){
          echo 'edit_document|FAILED|'.$fitem_id.':::';
        echo $DOC->getMessage();        
      }else{    
              $messages='';      
        foreach ($DOC->items as $i=>$val) $messages .= ';;'.$frowid[$i].'--'.$val->line_id.'--'.$val->getMessage();
        $messages = ltrim($messages,';');
          echo 'edit_document|OK|'.$fitem_id.'|'.$messages.'|'.$DOC->id.':::';
        if ((int)$fitem_id ==0) require('templates/forms/document.htm');
        else echo $DOC->doc_name;  
      }
      */
      }
      }
    break;
    case 'del_category':
      if (isset($fitem_id)){
      $CATEGORY = new Category((int)$fitem_id);
      if (!$CATEGORY->remove()){
          echo 'del_category|FAILED|'.$fitem_id.':::';
        echo $CATEGORY->getMessage();        
      }else{    
          echo 'del_category|OK|'.$fitem_id.':::';
        echo '[Удалено]'.$CATEGORY->name;        
      }
      }
    break;
        default:  echo 'unknown_action|FAILED:::action '.$action.' is not supported';
    }
if(isset($sql)) $sql->close();    
?>
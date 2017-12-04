<?php
  session_start();
  include("includes/functions.php");

  if(!$USER=displayLogin()) {if(isset($sql)) $sql->close();    exit;}


  $MESSAGE = '';
  $DEF_ID = 0;
  $ITEMS = array();
  $COMPACTVIEW=false;
  //----------------------------------------------------------------------------
  $gaCtgTypes = getCategoryTypes();
  if (isset($save_action) && $save_action == 'modify'){
    foreach ($gaCtgTypes as $sCode => $sName) {
      $gaItemCategories = getItemCategories($sCode,$fiItemID);
      if (isset($gaItemCategories)||isset(${$sCode.$fiItemID})){
        if (!isset(${$sCode.$fiItemID})) ${$sCode.$fiItemID} = array();
        modifyItemCategories($sCode,$fiItemID,${$sCode.$fiItemID});
      }
    }
    $fitem_id = $fiItemID;
    if (isset($facoitems)){  
        modifyItemCombinations($fiItemID,$facoitems);
    }
    if (isset($next_action)){
      $fitem_id = $fnext_id;
    }
    if (isset($prev_action)){
      $fitem_id = $fprev_id;
    }
  }
  if (isset($save_action) && $save_action == 'modify_item'){
   if ($fitem_id == 0){
   $ITEM = new item($fitem_id);
   $ITEM->id = $fitem_id;
   $ITEM->grp_id = (int)$_POST["fgrp_id"];
   $ITEM->name = strip_tags($_POST["fname"]);
   $ITEM->title = strip_tags($_POST["ftitle"]);
   $ITEM->alt_code = strip_tags( $_POST["falt_code"]);
   $ITEM->price = (float)$_POST["fprice"];
   $ITEM->price_mid = (float)$_POST["fprice_mid"];
   $ITEM->price_min = (float)$_POST["fprice_min"];
   $ITEM->status = (int)$_POST["fstatus"];
   $ITEM->create_date = strip_tags($_POST["fcreate_date"]);
   $ITEM->foto_alt_src = strip_tags($_POST["ffoto_alt"]);
   $ITEM->foto_name_src = strip_tags($_POST["ffoto_name"]);
   //["fgrp_id"]
   $ITEM->description =  $_POST["fdescription"];
   $fitem_id = $ITEM->createItem();
   if ($fitem_id < 0){
     $MESSAGE = $ITEM->getMessage();
   $create_item = true;
   }
   }
  }
  $action = (isset($_POST["action"])?$_POST["action"]:"");
  if(isset($action)){
    if($action=="add_articul"){
	 $articules=$_POST["fnmcl_name"];
	 if(is_array($articules))$articules=$articules[0];
	 if(isset($_SESSION["articule_list"])&&strlen($_SESSION["articule_list"])>0) $articules = $articules.(strlen($articules)>0?',':'').$_SESSION["articule_list"];
	 $_SESSION["articule_list"] = $articules;
	 $ITEMS  = searchItems($articules);	
	 $COMPACTVIEW=true;
	}elseif($action=="remove_from_sale"){
	 $nmcl_list = $_POST["flag"];
	 removeFromSale($nmcl_list);
	 if(isset($_SESSION["articule_list"])) $articules =$_SESSION["articule_list"];
	 else $articules = "null";
	 $ITEMS  = searchItems($articules);	
	 $COMPACTVIEW=true;		
	}
  }
  //----------------------------------------------------------------------------


  $ISAJAX = false;
  $GROUPS = getCatalog();
  $CATEGORY = getRefElements('wrh_size','SIZE');
  $gitem_id = '';
  $SHOWITEM = false;
  $NEWITEM = false;
  $GROUPNAME = '';
  $PARAMSCLOSED='';

  if (isset($create_item)){
    $NEWITEM = true;
  $GROUPNAME = 'Новый товар';
  if (!isset($ITEM)) $ITEM = new Item(0);
  $ITEM->id = 0;  
  $ITEM->create_date = date('Y-m-d');
  if (isset($grp_id)) $ITEM->grp_id = $grp_id;
  $fitem_id=0;
  }
  elseif (isset($fitem_id)&& strlen($fitem_id)>0) {
    $ITEM = new Item($fitem_id);
    $SHOWITEM = true;
    if( $ITEM->id){
      $gitem_id = $fitem_id;
      $ITEMS[$ITEM->id] = $ITEM->name;
      //$GRP_ID = $ITEM->grp_id;
      $GROUPNAME = $ITEM->grp_name;
      if(isset($action) && $action='drop_comment' && isset($comm_id)){
        if ($ITEM->updateComment($comm_id,0)) $MESSAGE = "Комментарий [id:$comm_id] забанен!";
        else $MESSAGE = $ITEM->getMessage();
      }
    if (!$ITEM->getWrhRests())$MESSAGE = $ITEM->getMessage();
    if (!$ITEM->getWrhTransactions())$MESSAGE = $ITEM->getMessage();
    } else $MESSAGE = 'Не найдена номенклатура с id = '.$fitem_id;
  }
  elseif(isset($grp_id)&& strlen($grp_id)>0){
    $ITEMS  = getItems($grp_id);
    $INFO   = getCatalogInfo($grp_id);
    $GROUPNAME = $INFO->grp_name;
    $PARAMSCLOSED=' fclose';
  }
  elseif(isset($preview)&& $preview = 'all'){
    $ITEMS  = getItems();
    $GROUPNAME = 'Каталог';
    $PARAMSCLOSED=' fclose';
  }
  if(count($ITEMS)==0) $_SESSION["articule_list"] = "";
/*  if (!$SHOWITEM){
    if(!isset($grp_id))$GRP_ID = 174;
    else {
      $GRP_ID = $grp_id;
      $ITEMS  = getItems($GRP_ID);
    }
    $INFO   = getCatalogInfo($GRP_ID);
    $GROUPNAME = $INFO->grp_name;
    $PARAMSCLOSED=' fclose';
  }*/
  header('Content-type: text/html; charset=utf-8');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//RU" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML lang="ru,en">
<HEAD>
    <TITLE>Панель управления магазином</TITLE>
    <META content="text/html"; charset="utf-8" http-equiv="Content-Type" />
    <link rel="stylesheet" href="application/views/css/common.css"/>
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
    <h3>Коллекции</h3>
    <?php foreach($GROUPS as $i => $GROUP):?>
    <div class="navItem grpstatus<?=$GROUP->status?>"><a href=index.php?grp_id=<?=$i?>><?=$GROUP->grp_name?></a></div>
    <?php endforeach;?>
    <div class=navItem><a href=index.php?preview=all><b>Показать все</b></a></div>
    </div>
  </td>
  <!-- .......................... Содержимое .......................... -->
  <td valign=top>
    <div id=catContent>
     <div class=searchform>
     <form style="float:left">
      <label>ID номенклатуры</label>
      <input id=fnmcl_id<?=$DEF_ID?> type=text name=fitem_id value="<?=$gitem_id?>">
      <input type=submit value="Найти">
     </form>
   <form method=post>
      &nbsp&nbsp&nbsp&nbsp<input type=submit name="create_item" value="Добавить товар">
     </form>
     <form method=post action="index.php">
	  <input id=fnmcl_id<?=$DEF_ID?> type=hidden name=fnmcl_id[]  size="20" value="">
      Поиск по ключу <input autocomplete="off" id=item<?=$DEF_ID?> type=text name=fnmcl_name[]  size="20" value="" onkeyupp = "changeEditMode(<?=$DEF_ID?>,'findnmcl','search','item<?=$DEF_ID?>')">
	  <a href="javascript:changeEditMode(<?=$DEF_ID?>,'findnmcl','search','item<?=$DEF_ID?>')">
	  <img src="images/btn-find.jpg" alt="Найти товар"><span id=ajaxmesfindnmcl<?=$DEF_ID?>></span><input type=submit name=action value=add_articul />
	  </a>
      <div id=findnmcl<?=$DEF_ID?> class=searchlist></div>
	  
     </form>

     </div>
     <div><?=$MESSAGE?></div>
     <br>
     <h3> <?=$GROUPNAME?> </h3>
     <br>
   <?php if($NEWITEM) require('templates/itemform.htm');?>   
   <?php if(count($ITEMS)==0) require('templates/categories.htm');?>   
     
	 <?php if($COMPACTVIEW):?><form method=post action="index.php"><input type=submit name=action value="remove_from_sale" /><?php endif;?>
	 <table>
     <?php foreach($ITEMS as $item_id => $sItemName):?>
     <?php if(!$SHOWITEM) $ITEM = new Item($item_id);?>
     <tr valign=top>
      <td  style="border-bottom:1px dotted red;">
      <img src="images/itemstatus<?=$ITEM->status?>.jpg" id=itemstatusimg<?=$item_id?>><br>
      <a href=<?=$ITEM->foto_name[0]?>>
       <img src=<?=$ITEM->foto_name[0]?>  width=140 alt="<?=$ITEM->foto_alt?>" id=itemimg<?=$item_id?>>
      </a>
      <br><br>Цена <b><span id=itemprice<?=$item_id?>><?=$ITEM->price?></span></b> руб.
      </td>
      <td width=100% style="border-bottom:1px dotted red;">
       <a name="<?=$item_id?>" />
       <b><span id=itemname<?=$item_id?>><?=$ITEM->alt_code?> - <?=$ITEM->name?></span></b> [ <a href=index.php?fitem_id=<?=$item_id?>><?=$item_id?></a> ] 
       <?php if($COMPACTVIEW):?>
	    <input type=checkbox name=flag[] value="<?=$item_id?>">
	   <?php else:?>
	   <div class=tabpages>
        <div class=tabs>
         <span class="tab" id=tab<?=$item_id?>1 onclick="switchTab('tabpage<?=$item_id?>','tab<?=$item_id?>',1,5);">
          1. Описание
         </span>
         <span class="tab selected" id=tab<?=$item_id?>2 onclick="switchTab('tabpage<?=$item_id?>','tab<?=$item_id?>',2,5);">
          2. Параметры
         </span>
         <span class="tab" id=tab<?=$item_id?>3 onclick="switchTab('tabpage<?=$item_id?>','tab<?=$item_id?>',3,5);">
          3. Фотографии
         </span>
         <span class="tab" id=tab<?=$item_id?>4 onclick="switchTab('tabpage<?=$item_id?>','tab<?=$item_id?>',4,5);">
          4. Статистика
         </span>
         <span class="tab" id=tab<?=$item_id?>5 onclick="switchTab('tabpage<?=$item_id?>','tab<?=$item_id?>',5,5);">
          5. Складской учет
         </span>
        </div>
        <div class=tab_page_hide id=tabpage<?=$item_id?>1>
         <div class=tab_content>
         <div class="toolbar">
           <a href="javascript:changeEditMode('<?=$item_id?>','item','view')"><img src="images/btn-view.jpg" alt="Перейти в режим просмотра"></a>
           <a href="javascript:changeEditMode('<?=$item_id?>','item','edit')"><img src="images/btn-edit.jpg" alt="Перейти в режим редактирования"></a>
           <a href="javascript:modifyItem('<?=$item_id?>')"><img src="images/btn-save.jpg" alt="Сохранить изменения"></a>
           <em id=ajaxmesitem<?=$item_id?>></em>
         </div>
         <div  id=item<?=$item_id?>>
          <?php require("templates/itemdesc.htm");?>
         </div>
        </div></div>
        <div class=tab_page id=tabpage<?=$item_id?>2>
         <div class=tab_content id=itemparam_<?=$item_id?>>
          <?php require("templates/paramsform.htm");?>
        </div></div>
        <div class=tab_page_hide id=tabpage<?=$item_id?>3>
         <div class=tab_content id=itemstat_<?=$item_id?>>
          <?php if($SHOWITEM): require("templates/itemphoto.htm");?>
          <?php else: ?>
		    Для просмотра/загрузки фотографий перейдите на <a href=index.php?fitem_id=<?=$item_id?>>страницу карточки товара [ <?=$item_id?> ]</a> 
          <?php endif; ?>
        </div></div>
        <div class=tab_page_hide id=tabpage<?=$item_id?>4>
         <div class=tab_content id=itemstat_<?=$item_id?>>
          <?php if($SHOWITEM): echo'отключено. обратитесь к разработчику';//require("templates/itemstat.htm");?>
          <?php else: ?>
		    Для просмотра статистики перейдите на <a href=index.php?fitem_id=<?=$item_id?>>страницу карточки товара [ <?=$item_id?> ]</a> 
          <?php endif; ?>
        </div></div>
        <div class=tab_page_hide id=tabpage<?=$item_id?>5>
        <div class=tab_content id=itemwrh_<?=$item_id?>>
         <?php require("templates/itemwarehouse.htm");?>
        </div></div>
       </div>
	   <?php endif;?>
      </td>
     </tr>
     <?php endforeach;?>
     </table>
	 <?php if($COMPACTVIEW):?></form><?php endif;?>
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
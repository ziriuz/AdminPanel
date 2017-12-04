<?php
require_once("application/env.php");
require_once("prd_db.php");
extract($_GET);extract($_POST);
function getLastID(){
  global $sql;
  if (!$sql->query("select LAST_INSERT_ID() as last_id")){echo("failed to get last_id (db error: ".$sql->error().")");return -1;}
  if (!$row = $sql->fetchObject()){echo("failed to get last_id");return -1;}
  return $row->last_id;
}
//------------------------------------------------------------------------------
function getCatalog()
{
  // Returns an array of all nomenclature groups
  global $sql;
  $laRet = array();
  $sql->query("SELECT * FROM nmcl_groups ng WHERE parent_id=200 ORDER BY status, grp_name");
  while ($row = $sql->fetchObject()) {
    $laRet[(int)$row->grp_id] = $row;
  }
  return $laRet;
} // end getCatalog()
//------------------------------------------------------------------------------
function getItems($aiGrpID = -1)
{
     // Returns an array of all item of group

     global $sql;

     $laRet = array();
     if (isset($aiGrpID)&& $aiGrpID > 0) {
       $liGrpID = (int)$aiGrpID;
       $sql->query("SELECT * FROM nomenclatures WHERE grp_id = $liGrpID ".
                   " ORDER BY alt_code,nmcl_id");
     }
     else $sql->query("SELECT n.* FROM nomenclatures n, nmcl_groups ng".
                      " WHERE ng.grp_id = n.grp_id and ng.parent_id=200 ".
                      " ORDER BY ifnull(n.alt_code,'zzz'),n.nmcl_id");

     while ($row = $sql->fetchObject()) {
        $laRet[$row->nmcl_id] = $row->nmcl_name;
     }

    return $laRet;

} // end getItems()
function searchItems($articules){
     // Returns an array of all item of group

     global $sql;

     $laRet = array();
     if (isset($articules)&& strlen($articules) > 0) {
       $stmt = "SELECT * FROM nomenclatures WHERE concat(',','".$sql->escape_string($articules)."',',') like concat('%,',alt_code,',%') ".
                   " ORDER BY alt_code,nmcl_id";	   
     if($sql->query($stmt))
     while ($row = $sql->fetchObject()) {
        $laRet[(int)$row->nmcl_id] = $row->nmcl_name;
     }
	 else  echo "[$stmt]:".$sql->$sql->error();
	 }
    return $laRet;

} // end getItems()
function removeFromSale($nmcl_list){
	foreach($nmcl_list as $key=>$value){
		$item = new Item($value);
		$item->updateStatus(1);
	}
}
//------------------------------------------------------------------------------
class Item{
  public $id; //  nmcl_id
  public $next_id; //  nmcl_id
  public $prev_id; //  nmcl_id
	public $name; // nmcl_name
	public $title; // nmcl_name
	public $grp_id;
	public $alt_code;
	public $grp_name;
	public $description;
	public $tech_description;
	public $foto_preview;
	public $foto_name;
	public $foto_name_src;
	public $foto_alt;
	public $foto_alt_src;
	public $price;
	public $price_mid;
	public $price_min;
	public $status;
	public $create_date;
	public $wrh_transactions = array();
	public $wrh_rests = array();
	private $err_message;
  function __construct($aiItemID) {
    if($row = getItemInfo($aiItemID))
    $this->init($row);
    else return null;
  }
  function init($aoDBRow) {
    $this->id = $aoDBRow->nmcl_id;
    $this->next_id = $aoDBRow->next_id;
    $this->prev_id = $aoDBRow->prev_id;
	  $this->name = $aoDBRow->nmcl_name;
	  $this->title = $aoDBRow->title;
	  $this->grp_id = $aoDBRow->grp_id;
	  $this->alt_code = $aoDBRow->alt_code;
	  $this->grp_name = $aoDBRow->grp_name;
	  $this->description = $aoDBRow->description;
	  $this->tech_description = $aoDBRow->tech_description;
    $this->price = $aoDBRow->price;
    $this->price_mid = $aoDBRow->price_mid;
    $this->price_min = $aoDBRow->price_min;	
	  $this->status = $aoDBRow->status;
	  $this->create_date = $aoDBRow->create_date;
	  $this->foto_name_src = $aoDBRow->foto_name;
	  $this->foto_alt = (strlen($aoDBRow->foto_alt)>0?$aoDBRow->foto_alt:$aoDBRow->nmcl_name);
	  $this->foto_alt_src = $aoDBRow->foto_alt;
    if (strlen($aoDBRow->foto_name)>0)
	    $this->foto_name = explode('|',$aoDBRow->foto_name);
    else
      $this->foto_name[0] = 'no_image.gif';
    $lsFotoPreview = explode('|',$aoDBRow->foto_preview);
    foreach ($this->foto_name as $i => $sName){
      if (isset($lsFotoPreview[$i]) && strlen($lsFotoPreview[$i])>0)
        $this->foto_preview[$i] = $lsFotoPreview[$i];
      else
        $this->foto_preview[$i] = "140/$sName";
    }
  }
	function getMessage(){return $this->err_message;}
	function updateItem(){
    global $sql;
		$this->err_message = null;
    $lsStatment =
        "UPDATE nomenclatures ".
        " SET ".
				" nmcl_name = '".str_replace("'","''",$this->name)."', ".
				" title = '".str_replace("'","''",$this->title)."', ".
				" alt_code = '".str_replace("'","''",$this->alt_code)."',".
				" grp_id = $this->grp_id, ".
				" price = $this->price, ".
				" price_mid = ".(strlen($this->price_mid)>0?$this->price_mid:'null').", ".
				" price_min = ".(strlen($this->price_min)>0?$this->price_min:'null').", ".
				" status = $this->status, ".
				" create_date = '".$this->create_date."',".
				" foto_name = '".str_replace("'","''",$this->foto_name_src)."',".
				" foto_alt = '".str_replace("'","''",$this->foto_alt_src)."',".
				" description = '".str_replace("'","''",$this->description)."',".
				" tech_description = '".str_replace("'","''",$this->tech_description)."'".
        " WHERE nmcl_id = ".$this->id;

    if(!$sql->query($lsStatment)){
      $this->err_message = "failed change nomenclature[id:$this->id](db error: ".$sql->error().")";
      global $HTTP_SERVER_VARS;
      //error_log("\r[".date("d.m.Y G:i:s")."] [client ".$HTTP_SERVER_VARS["REMOTE_ADDR"]."] db error: ".$sql->error(),3,"./log/error.log");
    }
		return $sql->result;
	}
  //------------------------------
  	function updatePhotoName(){
    global $sql;
		$this->err_message = null;
		if(get_magic_quotes_gpc()) {
            $this->foto_name_src = stripslashes($this->foto_name_src);
        } 
        $lsStatment = sprintf("UPDATE nomenclatures set foto_name = '%s' WHERE nmcl_id = %d",
		            $sql->escape_string($this->foto_name_src),$this->id);
    if(!$sql->query($lsStatment)){
      $this->err_message = "failed change photo name for nomenclature [id:$this->id](db error: ".$sql->error().")";
      global $HTTP_SERVER_VARS;
      //error_log("\r[".date("d.m.Y G:i:s")."] [client ".$HTTP_SERVER_VARS["REMOTE_ADDR"]."] db error: ".$sql->error(),3,"./log/error.log");
    }
		return $sql->result;
	}
  //------------------------------
	function createItem(){
    global $sql;
	$this->err_message = null;	
    $lsStatment = sprintf(
        "INSERT INTO nomenclatures (nmcl_name,title,alt_code ,grp_id,price,price_mid,price_min,status,create_date,foto_name , foto_alt,description , tech_description)".
        " VALUES( '%s','%s','%s',%d,%f,%f,%f,%d,'%s','%s','%s','%s','%s')",
				$sql->escape_string($this->name),
				$sql->escape_string($this->title),
				$sql->escape_string($this->alt_code),
				$sql->escape_string($this->grp_id),
				$sql->escape_string($this->price),
				$sql->escape_string($this->price_mid),
				$sql->escape_string($this->price_min),
				$sql->escape_string($this->status),
				$sql->escape_string($this->create_date),
				$sql->escape_string($this->foto_name_src),
				$sql->escape_string($this->foto_alt_src),
				$sql->escape_string($this->description),
				$sql->escape_string($this->tech_description));
    if(!$sql->query($lsStatment)){
      $this->err_message = "failed create nomenclature(db error: ".$sql->error().")";
	  return -1;
    }
	return getLastID();
	}
  //------------------------------
  function updateComment($acomm_id,$status){
   $this->err_message = null;
   $lcomm_id = (int) $acomm_id;
   if (!isset($lcomm_id)||$lcomm_id <= 0||!isset($status)||!($status==1||$status==0)) return false;
	 global $sql;
    $lsStatment =
        "UPDATE nmcl_comments ".
        " SET ".
				" status = $status ".
        " WHERE comm_id = $lcomm_id";

    if(!$sql->query($lsStatment)){
      $this->err_message = "failed update item comment[id:$lcomm_id](db error: ".$sql->error().")";
      global $HTTP_SERVER_VARS;
      //error_log("\r[".date("d.m.Y G:i:s")."] [client ".$HTTP_SERVER_VARS["REMOTE_ADDR"]."] db error: ".$sql->error(),3,"./log/error.log");
    }
		return $sql->result;
	}
  function updateStatus($status){
   $this->err_message = null;
   if (!isset($status)||!($status==1||$status==0)) return false;
   $this->status=$status;
	 global $sql;
    $lsStatment =
        "UPDATE nomenclatures SET status = $status  WHERE nmcl_id = $this->id";
    if(!$sql->query($lsStatment)){
      $this->err_message = "failed update item status[id:$this->id](db error: ".$sql->error().")";
      global $HTTP_SERVER_VARS;
      //error_log("\r[".date("d.m.Y G:i:s")."] [client ".$HTTP_SERVER_VARS["REMOTE_ADDR"]."] db error: ".$sql->error(),3,"./log/error.log");
    }
		return $sql->result;
	}
  function getWrhTransactions(){

   $this->err_message = null;
   global $sql;
   $lsStatment ="
     SELECT t.*,c.nmcl_id,c.ctg_id,ctg.ctg_name as ctg_name,c.goods_type_id,gt.name as gt_name,c.um_id, u.short_name as um_name,c.wrh_id,w.name as wrh_name,c.price
     FROM `wrh_card` c
     join wrh_transactions t on (c.card_id = t.card_id)
     join wrh_goods_type gt on (c.goods_type_id = gt.goods_type_id)
     join warehouse w on (w.wrh_id = c.wrh_id)
     left outer join wrh_um u on(u.um_id = c.um_id)
	 left outer join  categories ctg on(ctg.ctg_id = c.ctg_id)
	 where c.nmcl_id = $this->id
	 order by t.opr_time desc
     ";
    if(!$sql->query($lsStatment)){
      $this->err_message = "failed get item transactions (db error: ".$sql->error().")";
      global $HTTP_SERVER_VARS;
      //error_log("\r[".date("d.m.Y G:i:s")."] [client ".$HTTP_SERVER_VARS["REMOTE_ADDR"]."] db error: ".$sql->error(),3,"./log/error.log");
    } else 
	while ($row = $sql->fetchObject()) {
        $this->wrh_transactions[$row->tran_id] = $row;
    }	
	return $sql->result;
  }
  function getWrhRests(){

   $this->err_message = null;
   global $sql;
   $lsStatment ="
     SELECT 1 as ord,c.wrh_id,c.ctg_id,gt.goods_type_id,u.um_id,c.price,
	        concat(w.name,' (',w.location,')') as wrh_name,
	        ctg.ctg_name,
 	        sum(qty_rest) as qty_rest, sum(qty_reserved) as qty_reserved,
	        u.short_name as um_name
     FROM `wrh_card` c
     join wrh_goods_type gt on (c.goods_type_id = gt.goods_type_id)
     join warehouse w on (w.wrh_id = c.wrh_id)
     left outer join wrh_um u on(u.um_id = c.um_id)
	 left outer join  categories ctg on(ctg.ctg_id = c.ctg_id)
	 where c.nmcl_id = $this->id
	 group by c.wrh_id,c.ctg_id,c.um_id
union all
     SELECT 0 as ord,c.wrh_id,null,null,null,null,
	        concat(w.name,' (',w.location,')') as wrh_name,
	        concat(w.name,' (',w.location,')') as wrh_name,
 	        sum(qty_rest) as qty_rest, sum(qty_reserved) as qty_reserved,
	        null as um_name
     FROM `wrh_card` c
     join wrh_goods_type gt on (c.goods_type_id = gt.goods_type_id)
     join warehouse w on (w.wrh_id = c.wrh_id)
     left outer join wrh_um u on(u.um_id = c.um_id)
	 left outer join  categories ctg on(ctg.ctg_id = c.ctg_id)
	 where c.nmcl_id = $this->id
	 group by c.wrh_id	
	 order by wrh_id, ord, ctg_name

     ";
	$i=0;
    if(!$sql->query($lsStatment)){
      $this->err_message = "failed get item rests (db error: ".$sql->error().")";
    } else 
	while ($row = $sql->fetchObject()) {
        $this->wrh_rests[$i++] = $row;
    }	
	return $sql->result;
  }
  //------------------------------
  function getWrhCard($awrh_id,$agoods_type_id,$actg_id,$aprice,$aum_id)
  {
     global $sql;
	 $this->err_message = null;
	 $awrh_id = (strlen($awrh_id)>0?$awrh_id:'null');
	 $agoods_type_id = (strlen($agoods_type_id)>0?$agoods_type_id:'null');
	 $actg_id = (strlen($actg_id)>0?$actg_id:'null');
	 $aprice = (strlen($aprice)>0?$aprice:'null');
	 $aum_id = (strlen($aum_id)>0?$aum_id:'null');
	 
	 
     $lsStatment="
	   select card_id from wrh_card where 
       `wrh_id` = $awrh_id and
       `goods_type_id` = $agoods_type_id and
       `nmcl_id` = $this->id and
       ifnull(`price`,0) = ifnull($aprice,0) and
       ifnull(`um_id`,-1) = ifnull($aum_id,-1) and
	   ifnull(`ctg_id`,-1) = ifnull($actg_id,-1)
	 ";
     if(!$sql->query($lsStatment)){
	   $this->err_message = "failed get warehouse card(db error: ".$sql->error().")";
	   echo $this->err_message;
	   return -1;
     } 
	 elseif ($row = $sql->fetchObject()) return $row->card_id;
	 else {
       $lsStatment="
	    insert into wrh_card (wrh_id,goods_type_id,nmcl_id,ctg_id,price,um_id)
        values($awrh_id,$agoods_type_id,$this->id,$actg_id,$aprice,$aum_id)
	   ";
	   if (!$sql->query($lsStatment)){ 
	     $this->err_message = "failed create warehouse card(db error: ".$sql->error().")";
	     return -1;
       } else return getLastID();	 	 
	 }
  } // end getItemInfo()
  function createWrhTransaction($awrh_id,$agoods_type_id,$actg_id,$aprice,$aum_id,$adc_flag,$ais_reserve,$aquantity,$aorder_id){
   $this->err_message = null;

	
   $lcard_id = $this->getWrhCard($awrh_id,$agoods_type_id,$actg_id,$aprice,$aum_id);
   if ($lcard_id < 1) return -1;
   
   $atran_id=createWrhTransaction($lcard_id,$adc_flag,$ais_reserve,$aquantity,$aorder_id,$this->err_message);
   
   return  $atran_id;

   global $sql;
   $lsStatment =
        "INSERT into wrh_transactions (`card_id`,`dc_flag`,`is_reserve`,`quantity`,`order_id`)
		 values ($lcard_id,$adc_flag,$ais_reserve,$aquantity,$aorder_id)";

   if(!$sql->query($lsStatment)){
      $this->err_message = "failed create transaction (db error: ".$sql->error().")";
	  return -1;
   }
   $atran_id=getLastID();
   
   $lsStatment =
        "UPDATE wrh_card SET qty_rest = qty_rest + ($aquantity*$adc_flag*(1-$ais_reserve)),
		        qty_reserved = qty_reserved - ($aquantity*$adc_flag*$ais_reserve)
		  WHERE card_id = $lcard_id";

   if(!$sql->query($lsStatment)){
      $this->err_message = "failed update warehouse rests (db error: ".$sql->error().")";
	  return -1;
   }
   
   return $atran_id;
  }
}

//------------------------------------------------------------------------------
function getItemInfo($aiItemID)
{
     // Returns an item info

     global $sql;

     $liItemID = (float)$aiItemID;
     $sql->query("SELECT ng.grp_name, n.*, ".
                 "      (select min(n1.nmcl_id) from nomenclatures n1 where n1.nmcl_id > n.nmcl_id) as next_id,".
                 "      (select max(n1.nmcl_id) from nomenclatures n1 where n1.nmcl_id < n.nmcl_id) as prev_id".
                 "  FROM nomenclatures n, nmcl_groups ng".
                 " WHERE nmcl_id = $liItemID and n.grp_id = ng.grp_id");
     $row = $sql->fetchObject();
     return $row;
} // end getItemInfo()
//------------------------------------------------------------------------------
  function getCatalogInfo($aiGrpID)
  {
     // Returns an group info

     global $sql;

     $liGrpID = (int)$aiGrpID;
     $sql->query("SELECT ng.* FROM nmcl_groups ng WHERE $liGrpID = ng.grp_id");
     $row = $sql->fetchObject();
     return $row;
  } // end getItemInfo()
//------------------------------------------------------------------------------
  function getCategoryTypes()
  {
     // Returns an array of category types

     global $sql;

     $laRet = array();

     $sql->query(
       "SELECT ct.alt_code, ct.ctg_type_name FROM category_types ct ".
       " WHERE active_flag = 1 and ( filter_flag+tag_flag+special_flag > 0 )".
       " ORDER BY ct.alt_code");

     while ($row = $sql->fetchObject()) {
        $laRet[$row->alt_code] = $row->ctg_type_name;
     }

    return $laRet;

  } // end getCategoryTypes()
//------------------------------------------------------------------------------
  function getCategories($asCtgAltCode)
  {
     // Returns an array of categories for given type

     global $sql;

     $laRet = array();

     $sql->query(
       "SELECT c.ctg_id,c.ctg_name,c.select_type ".
       "  FROM category_types ct, categories c ".
       " WHERE ct.alt_code='$asCtgAltCode' and c.ctg_type_id=ct.ctg_type_id ".
       " ORDER BY cast(c.ctg_name as UNSIGNED),c.ctg_name"
     );

     while ($row = $sql->fetchObject()) {
        $laRet[(int)$row->ctg_id] = $row;//->ctg_name;
     }

    return $laRet;

  } // end getCategories()
//------------------------------------------------------------------------------
  function getItemCategories($asCtgAltCode,$aiNmclID)
  {
     // Returns an array of categories for given item

     global $sql;

     $laRet = array();

     $sql->query(
       "SELECT c.ctg_id,c.ctg_name,c.select_type ".
       "  FROM category_types ct, categories c, nmcl_categories nc ".
       " WHERE ct.alt_code='$asCtgAltCode' and c.ctg_type_id=ct.ctg_type_id".
       "   and nc.nmcl_id = $aiNmclID and nc.ctg_id = c.ctg_id".
       " ORDER BY cast(c.ctg_name as UNSIGNED),c.ctg_name");

     while ($row = $sql->fetchObject()) {
        $laRet[(int)$row->ctg_id] = $row;//->ctg_name;
     }

    return $laRet;

  } // end getItemCategories()
//------------------------------------------------------------------------------
  function modifyItemCategories($asCtgAltCode,$aiNmclID,$aaNmclCategories)
  {
     // Modify a set of categories for given item

    global $sql;
    if (!isset($asCtgAltCode)||!isset($aiNmclID)) return false;
    $liNmclID = (int)$aiNmclID;
    $lsCtgAltCode = (string)$asCtgAltCode;

    // ------- Удалим все категории указанного типа для указанной номенклатуры
    $lsStatment =
      "DELETE FROM nmcl_categories WHERE nmcl_id = $liNmclID and ctg_id in ".
      "(SELECT c.ctg_id FROM categories c, category_types ct ".
      "  WHERE c.ctg_type_id = ct.ctg_type_id and ct.alt_code = '$lsCtgAltCode')";
    if(!$sql->query($lsStatment)){
        echo "failed delete item[id:$liNmclID} categories of $lsCtgAltCode category type (db error: ".$sql->error().")";
        global $HTTP_SERVER_VARS;
        error_log("\r[".date("d.m.Y G:i:s")."] [client ".$HTTP_SERVER_VARS["REMOTE_ADDR"]."] db error: ".$sql->error(),3,"./log/error.log");
        return $sql->result;
    }
    if (isset($aaNmclCategories) && count($aaNmclCategories)>0 ){
    // ------- Добавим новый перечень категорий указанного типа для указанной номенклатуры
      $lsCategories = implode(",", $aaNmclCategories);
      $lsStatment =
        "INSERT INTO nmcl_categories (ctg_id,nmcl_id) ".
        " SELECT c.ctg_id, $liNmclID ".
        " FROM categories c, category_types ct ".
        " WHERE c.ctg_type_id = ct.ctg_type_id and ct.alt_code = '$lsCtgAltCode' and c.ctg_id in ($lsCategories)";
      if(!$sql->query($lsStatment)){
        echo "failed insert item[id:$liNmclID} categories of $lsCtgAltCode category type (db error: ".$sql->error().")";
        global $HTTP_SERVER_VARS;
        error_log("\r[".date("d.m.Y G:i:s")."] [client ".$HTTP_SERVER_VARS["REMOTE_ADDR"]."] db error: ".$sql->error(),3,"./log/error.log");
      }
    }
    return $sql->result;

  } // end modifyItemCategories()
//------------------------------------------------------------------------------
  function getItemsByNmcl($aiNmclID)
  {
     // Returns an array of all items combined with item

     global $sql;

     $laRet = array();
     if (isset($aiNmclID)&& $aiNmclID > 0) {
       $liNmclID = (int)$aiNmclID;
       $sql->query("
         SELECT n.nmcl_id, n.nmcl_name, n.create_date  FROM `nmcl_combinations` nc, `nomenclatures` n  WHERE nc.nmcl_id = $liNmclID and n.nmcl_id = nc.comb_nmcl_id
         /*union all
         SELECT n.nmcl_id, n.nmcl_name, n.create_date  FROM `nmcl_combinations` nc, `nomenclatures` n  WHERE nc.comb_nmcl_id = $liNmclID and n.nmcl_id = nc.nmcl_id
         */
         ORDER BY create_date desc, nmcl_name
			 ");
       while ($row = $sql->fetchObject()) {
        $laRet[(int)$row->nmcl_id] = $row->nmcl_name;
       }
     }

    return $laRet;

  } // end getItemsByNmcl()
//------------------------------------------------------------------------------
  function modifyItemCombinations($aiNmclID,$aaCombinations)
  {
    global $sql;
    if (!isset($aiNmclID)||!isset($aaCombinations)) return false;
    $liNmclID = (int)$aiNmclID;

    // ------- Удалим все комбинации
    $lsStatment =
      "DELETE FROM nmcl_combinations WHERE nmcl_id = $liNmclID/* or comb_nmcl_id = $liNmclID*/";
    if(!$sql->query($lsStatment)){
        echo "failed delete item[id:$liNmclID] combinations (db error: ".$sql->error().")";
        global $HTTP_SERVER_VARS;
        error_log("\r[".date("d.m.Y G:i:s")."] [client ".$HTTP_SERVER_VARS["REMOTE_ADDR"]."] db error: ".$sql->error(),3,"./log/error.log");
        return $sql->result;
    }
    if (isset($aaCombinations) && count($aaCombinations)>0 ){
    // ------- Добавим новый перечень комбинаций для указанной номенклатуры
      $lsCombinations='';
      foreach($aaCombinations as $i => $item)
        if((int)$item) $lsCombinations.= ",".(int)$item;
      $lsCombinations = ltrim($lsCombinations,',');
      if (strlen($lsCombinations)>0){
       $lsStatment =
        "INSERT INTO nmcl_combinations (nmcl_id,comb_nmcl_id) ".
        " SELECT $liNmclID, n.nmcl_id".
        " FROM nomenclatures n".
        " WHERE n.nmcl_id in ($lsCombinations)";
       if(!$sql->query($lsStatment)){
        echo "failed insert item[id:$liNmclID] combinations for $lsCombinations (db error: ".$sql->error().")";
        global $HTTP_SERVER_VARS;
        error_log("\r[".date("d.m.Y G:i:s")."] [client ".$HTTP_SERVER_VARS["REMOTE_ADDR"]."] db error: ".$sql->error(),3,"./log/error.log");
       }
      }
    }
    return $sql->result;
  }
//
//------------------------------------------------------------------------------
class item_comment {
  public $comm_id;
  public $nmcl_id;
	public $username;
	public $email;
	public $subject;
	public $comment_text;
	public $add_time;
	public $status;
	//------------------------------
  function __construct($nmcl_id,$username,$email,$subject,$comment_text,$add_time,$status)
  {
  $this->nmcl_id=$nmcl_id;
	$this->username=$username;
	$this->email=$email;
	$this->subject=$subject;
	$this->comment_text=$comment_text;
	$this->add_time=$add_time;
	$this->status=$status;
  }
}
//----------------------------------- User/Login functions ---------------------
function nvl($val,$nullval){
   return (isset($val)?$val:$nullval);
}
  function displayLogin(){

    global $_SESSION;
    //global $login_action,$fsUid,$fsUpwd;
    global $ssUid,$siUid;
    global $_POST;
    global $_GET;
    $lsLoginMessage = '';
    $login_action=(isset($_POST['login_action'])?$_POST['login_action']:(isset($_GET['login_action'])?$_GET['login_action']:null));
    $fsUid=(isset($_POST['fsUid'])?$_POST['fsUid']:(isset($_GET['fsUid'])?$_GET['fsUid']:null));
    $fsUpwd=(isset($_POST['fsUpwd'])?$_POST['fsUpwd']:(isset($_GET['fsUpwd'])?$_GET['fsUpwd']:null));
    if  ($login_action&&$fsUid&&$fsUpwd){     
     $lsUid='guest';
     if (isset($login_action)&&isset($fsUid)&&isset($fsUpwd))
      if ($login_action=='login'){
        if ($siUid = getUid($fsUid,$fsUpwd)) {
          $ssUid = $fsUid;
          $lsUid=$ssUid;
          $_SESSION["ssUid"]= $ssUid;
          $_SESSION["siUid"]= $siUid;
        }else $lsLoginMessage = 'Неверный логин или пароль';
      }
     if(isset($_SESSION['ssUid'])) return getUserInfo($_SESSION['siUid']);//$lsUid=$_SESSION['ssUid'];
    } elseif(isset($_SESSION['ssUid'])) return getUserInfo($_SESSION['siUid']);
	header('Content-type: text/html; charset=utf-8');
    echo ("
      <div>
        Необходимо авторизироваться
        <br><font color=#FF0000>$lsLoginMessage</font>
      </div>
      <form method=post>
      <input type=hidden name=login_action value=login>
      <table>
       <tr> <td> Логин  <td> <input type=text size = 10 name=fsUid maxlength=32>
       <tr> <td> Пароль <td> <input type=password size = 12 name=fsUpwd maxlength=32>
       <tr> <td> &nbsp; <td align=right> <input type=submit value=Войти>
      </table>
      </form>
    ");
  }
//------------------------------------------------------------------------------
  function getUid($asLogin,$asPwd){
    //Проверить зарегистрирован ли пользователь
    global $sql;
      if ($sql->query("SELECT uid FROM users WHERE UPPER(nick) = UPPER('$asLogin') and pwd = '$asPwd' and status=10")) {
        if (($sql->numRows() == 1) && ($row = $sql->fetchObject())) return $row->uid;
        return;
      }
      elseif (!$sql->result){
        echo("(db error: ".$sql->error().")");
        global $HTTP_SERVER_VARS;
        error_log("\r[".date("d.m.Y G:i:s")."] [client ".$HTTP_SERVER_VARS["REMOTE_ADDR"]."] db error: ".$sql->error(),3,"./log/error.log");
      }
  } // end userExists
//------------------------------------------------------------------------------
  function getUserInfo($aiUid)
  {
     // Returns User Info
     global $sql;
     $liUid = (int)$aiUid;

     if ($sql->query("SELECT * FROM users WHERE uid = $liUid")){
       $row = $sql->fetchObject();
       return $row;
     } else {
       echo("(db error: ".$sql->error().")");
       global $HTTP_SERVER_VARS;
       error_log("\r[".date("d.m.Y G:i:s")."] [client ".$HTTP_SERVER_VARS["REMOTE_ADDR"]."] db error: ".$sql->error(),3,"./log/error.log");
    }
  } // end getUserInfo()
//------------------------------------------------------------------------------
//-------------------------- Order functions -----------------------------------
function getOrders($aStatusFilter)
{
     // Returns an array of all item of group

     global $sql;
		 
		 if(isset($aStatusFilter) || strlen(trim($aStatusFilter))>0) $lStatusFilter=$aStatusFilter;
		 else $lStatusFilter='0,1,2,3,4,5,6,7,8,9';

     $laRet = array();
     $sql->query("SELECT o.*, kn.*, concat_ws(' ',o.last_name,o.first_name,o.middle_name) as name, ".
                 "  (select sum(amount) from order_details od where od.order_id = o.order_id and deleted=0) as amount ".
                 "  FROM orders o ".
                 "  LEFT JOIN kn_orders kn ON o.order_id = kn.shopify_id ".
                 "  WHERE o.status in ($lStatusFilter)".
                 " ORDER BY order_date desc, order_id desc");
      while ($row = $sql->fetchObject()) {
        $row->delivery_price = ($row->delivery_total>0?$row->delivery_total:$row->deliv_price);
        $row->amount_total = $row->amount+$row->delivery_price-$row->dis_code;
        $laRet[$row->order_id] = $row;
      }

    return $laRet;

} // end getOrders()
function getOrdersByIds($aIds)
{
     // Returns an array of all item of group

     global $sql;
		 
		 if(isset($aIds) && is_array($aIds) )
             $lIds=implode(',',$aIds);
		 else $lIds ='0';

     $laRet = array();
     $stmt = "SELECT o.*, kn.*, concat_ws(' ',o.last_name,o.first_name,o.middle_name) as name, ".
                 "  (select sum(amount) from order_details od where od.order_id = o.order_id and deleted=0) as amount ".
                 "  FROM orders o ".
                 "  LEFT JOIN kn_orders kn ON o.order_id = kn.shopify_id ".
                 "  WHERE o.order_id in ($lIds)".
                 " ORDER BY order_date desc, order_id desc";
    $query =  $sql->query($stmt);
              if (!$query){
              throw new Exception($sql->error().":".$stmt);
          }
      while ($row = $sql->fetchObject()) {
        $laRet[$row->order_id] = $row;
      }

    return $laRet;

} // end getOrders()
//------------------------------------------------------------------------------
  function getOrderItems($aiOrderId)
  {
     // Returns an array of all item in the order

     global $sql;
     $laRet = array();
     $liOrderId = (float)$aiOrderId;
	 
	 
	 //	 $lsStatment = "SELECT od.*, c.ctg_name FROM order_details od LEFT OUTER JOIN categories c ON (od.ctg_id=c.ctg_id) WHERE order_id = $liOrderId";

	 
	 
	 $lsStatment = "
	 SELECT od.item_id,od.order_id,od.nmcl_id,od.ctg_id,od.quantity,od.price,od.amount,od.prod,
     case when length(od.nmcl_name)=0 or isnull(od.nmcl_name)then n.nmcl_name
     else od.nmcl_name end nmcl_name,
     od.deleted,od.last_modify, c.ctg_name,
	  ( select count(*) from order_details od1 where od1.order_id=od.order_id and ifnull(od1.nmcl_id,-1) = ifnull(od.nmcl_id,-1)) as rowspan,
	  (select sum(quantity) from wrh_doc_items wdi where wdi.order_id = od.order_id and wdi.nmcl_id = od.nmcl_id and ifnull(wdi.ctg_id,-1)=ifnull(od.ctg_id,-1)) prodorder_qty,
	  (select min(doc_id) from wrh_doc_items wdi where wdi.order_id = od.order_id and wdi.nmcl_id = od.nmcl_id and ifnull(wdi.ctg_id,-1)=ifnull(od.ctg_id,-1)) prod_doc_id,
	  (
       select group_concat(qty) from(
         select w.nmcl_id, w.ctg_id, concat(' ',wrh.name,'(',ctg_name,')',' - ',sum(qty_rest-qty_reserved)) qty  
		   from wrh_card w join warehouse wrh using(wrh_id) join categories using(ctg_id)       
          group by wrh_id,nmcl_id,ctg_id) w
        where w.nmcl_id = od.nmcl_id /* and (od.ctg_id = 0 or od.ctg_id = w.ctg_id)*/
     ) qty , concat(lpad(n.alt_code,4,'0'),lpad(color_id,4,'0'),lpad(od.ctg_id,4,'0')) label_code,color_name,n.alt_code
	 FROM order_details od LEFT OUTER JOIN categories c ON (od.ctg_id=c.ctg_id) 
     LEFT JOIN nomenclatures n on n.nmcl_id = od.nmcl_id
     LEFT JOIN (select nmcl_id, min(ctg_id) as color_id,group_concat(ctg_name) as color_name from nmcl_categories join categories using(ctg_id) where ctg_type_id=3 group by nmcl_id ) cl
            ON n.nmcl_id = cl.nmcl_id     
     WHERE order_id =  $liOrderId
	 order by ifnull(od.nmcl_id,1000000000000000),nmcl_id, ctg_id
	 ";

     if($sql->query($lsStatment)){
     $i=0;
     while ($row = $sql->fetchObject()) {
        $laRet[$i++] = $row;
     }
	 } else echo $sql->error();
     return $laRet;

  } // end getOrderItems()
//------------------------------------------------------------------------------
 class OrderItem{
    public $id;
	public $item_id;
	public $order_id;
	public $nmcl_id;
	public $nmcl_name;
	public $ctg_id;
	public $ctg_name;
	public $quantity;
	public $price;
	public $amount;
	public $status;
	public $prod;
	public $deleted;
	private $err_message;
  function __construct($aLineID) {
    if (isset($aLineID)&&$aLineID>0){
	  $lid = (float)$aLineID;
	  global $sql;
      if(!$sql->query("SELECT d.* FROM order_details d where d.item_id = $lid")){
	    $this->err_message = 'Ошибка BD:'.$sql->error();
		return;
	  }
      if (!$row = $sql->fetchObject()){
	    $this->err_message = 'Ошибка BD:'.$sql->error();
		return;
	  }
      $this->id=$row->item_id; 
	  $this->item_id=$row->item_id; 
	  $this->order_id=$row->order_id;
	  $this->nmcl_id=$row->nmcl_id;
	  $this->nmcl_name=$row->nmcl_name;
	  $this->ctg_id=$row->ctg_id;
	  $this->quantity=$row->quantity;
	  $this->prod=$row->prod;
	  $this->price=$row->price;
	  $this->amount=$row->amount;
	  $this->amount=$row->deleted;
	} else {$this->id = 0;$this->item_id=0;}
  }
  function getMessage(){return $this->err_message;}
  function save(){
    global $sql;
	$this->err_message = null;
	
	$this->nmcl_id = (float)$this->nmcl_id;
	$this->nmcl_name = $this->nmcl_name;
	$this->ctg_id = (int)$this->ctg_id;
	$this->order_id = (float)$this->order_id;
    $this->quantity = (float)$this->quantity;
    $this->price = (float)$this->price;
    $this->amount = (float)$this->quantity * (float)$this->price;
    $this->prod = (int)$this->prod;
    $this->deleted= (int)$this->deleted;
    if (($this->nmcl_id <=0 && strlen($this->nmcl_name)==0)||$this->quantity <=0){
	  $this->err_message = 'Не все обязательные поля заполнены!!!';
	  return false;
	}

	if($this->id > 0)
    $lsStatment =
        "UPDATE order_details ".
        " SET ".
				" nmcl_id = ".($this->nmcl_id<=0?'null':$this->nmcl_id).", ".
				" nmcl_name = ".(strlen($this->nmcl_name)==0?'null':"'".$this->nmcl_name."'").", ".
				" ctg_id = ".$this->ctg_id.", ".
				" order_id = $this->order_id, ".
				" quantity = $this->quantity, ".
				" price = $this->price, ".
				" amount = $this->amount, ".
				" prod = $this->prod,".
				" deleted = $this->deleted ".
        " WHERE item_id = ".$this->id;
	else
    $lsStatment =
        "INSERT INTO order_details (nmcl_id,nmcl_name,ctg_id,order_id,quantity,price,amount) ".
        "VALUES ( ".($this->nmcl_id<=0?'null':$this->nmcl_id).",'".$this->nmcl_name."',".$this->ctg_id.",".$this->order_id.",".$this->quantity.",".$this->price.",".$this->amount.
        " )";

    if(!$sql->query($lsStatment)){
      $this->err_message = "failed change Order Item[id:$this->id](db error: ".$sql->error().")".$lsStatment;
    } else
	if($this->id == 0) $this->id = getLastID();
	$this->item_id = $this->id;
	return $sql->result;
  }
}
//--------------------------------------------------------------------------
  function getOrderItemWrhRests($aiOrderId,$aiNmclId,$aiWrhId=0)
  {     

     global $sql;
     $laRet = array();
     $liOrderId = (float)$aiOrderId;
	 $liNmclId = (isset($aiNmclId)?(float)$aiNmclId:0);
	 $liWrhId = (isset($aiWrhId)?(int)$aiWrhId:0);
	 
	 //	 $lsStatment = "SELECT od.*, c.ctg_name FROM order_details od LEFT OUTER JOIN categories c ON (od.ctg_id=c.ctg_id) WHERE order_id = $liOrderId";

	 
	 
	 $lsStatment = "	 
	 SELECT w.card_id, w.ctg_id,c.ctg_name,qty_rest-qty_reserved as quantity,
	 sum(case is_reserve when 1 then ifnull(quantity*(-dc_flag),0) else 0 end) reserved_qty,
	 sum(case is_reserve when 0 then ifnull(quantity*(-dc_flag),0) else 0 end) out_qty	  
     FROM wrh_card w
     LEFT JOIN wrh_transactions wt ON ( w.card_id = wt.card_id AND wt.order_id =$liOrderId )
     LEFT JOIN categories c ON (w.ctg_id=c.ctg_id)
     WHERE nmcl_id =$liNmclId and ($liWrhId=0 or $liWrhId=wrh_id)
     GROUP BY card_id,w.qty_rest,w.qty_reserved
     HAVING w.qty_rest-w.qty_reserved +
	 sum(case is_reserve when 1 then ifnull(quantity*(-dc_flag),0) else 0 end) +
	 sum(case is_reserve when 0 then ifnull(quantity*(-dc_flag),0) else 0 end) >0
	 ";

     if($sql->query($lsStatment)){
     $i=0;
     while ($row = $sql->fetchObject()) {
        $laRet[$i++] = $row;
     }
	 } else echo $sql->error().'---'.$lsStatment;
     return $laRet;

  } // end getOrderItems()
//------------------------------------------------------------------------------
  function getOrderInfo($aiOrderID)
  {
    // Returns an array of all item of group

    global $sql;

    $liOrderID = (float)$aiOrderID;
    $query = $sql->query(" SELECT o.*, kn.*, o.sid as order_number, ".
                 "        concat_ws(' ',o.last_name,o.first_name,o.middle_name) as name, ".
								 "  dt.deliv_name,dt.geo_zone, dt.price as deliv_tarif, dt.price_range, ".
                 "       (select sum(amount) from order_details od where od.order_id = o.order_id and deleted=0) as amount ".
                 " FROM orders o left join delivery_types dt on (o.deliv_id = dt.deliv_id) ".
             "  LEFT JOIN kn_orders kn ON o.order_id = kn.shopify_id ".
								 "WHERE order_id = $liOrderID");
    if (!$query) {
       throw new Exception($sql->error());
    }
    if ($sql->numRows()>0) {
       $row = $sql->fetchObject();
       $row->delivery_price = ($row->delivery_total>0?$row->delivery_total:$row->deliv_price);
       $row->amount_total = $row->amount+$row->delivery_price-$row->dis_code;
       $row->weight_total = 500; //temporary
    } else {
       throw new Exception("Order not found [order_id:{$liOrderID}]");
    }
    return $row;
  } // end getOrderInfo()
//------------------------------------------------------------------------------
  function findOrder($aOrderNumber)
  {
    // Returns an array of all item of group

    global $sql;
    $query = $sql->query(sprintf("SELECT o.order_id FROM orders o WHERE sid = '%s'",$sql->escape_string($aOrderNumber)));
    if (!$query) {
       throw new Exception($sql->error());
    }
    if ($sql->numRows()>0) {
       $row = $sql->fetchObject();
       $res = $row->order_id;
    } else {
       throw new Exception("Order not found [order_number:{$aOrderNumber}]");
    }
    return $res;
  } // end getOrderInfo()
//------------------------------------------------------------------------------
  	function updateOrderPP($order,$invnumber,$barcode,$label){
     global $sql;
     $lsStatment = sprintf("UPDATE orders set pp_invoicenumber = '%s',pp_barcode = '%s',pp_label = '%s' WHERE order_id = %d",
		            $invnumber,$barcode,$label,$order);
     $sql->query($lsStatment);
     return $sql->result;
	}
 //------------------------------
  function getOrderImages($aiOrderID)
  {
     // Returns an array of all item of group

     global $sql;

     $liOrderID = (float)$aiOrderID;
	 $lsStatment = "select substr(c.foto_name,1,if(instr(c.foto_name,'|')>0,instr(c.foto_name,'|')-1,1000)) foto_name, sum(quantity) qty
	 FROM order_details od LEFT OUTER JOIN nomenclatures c ON (od.nmcl_id=c.nmcl_id) WHERE order_id = $liOrderID
	 GROUP BY substr(c.foto_name,1,if(instr(c.foto_name,'|')>0,instr(c.foto_name,'|')-1,1000))
	 ";

    if($sql->query($lsStatment)){
     $i=0;
     while ($row = $sql->fetchObject()) {	    
        $laRet[$i++] = $row;
     }
	 } else echo $sql->error();
     return $laRet;
  } // end getOrderInfo()
//------------------------------------------------------------------------------
  function changeOrders($action,$aaOrders,$aValue)
  {
    global $sql;
    if (!isset($aaOrders)||!isset($action)||!isset($aValue)) return false;
    if ($action == 'CHANGE_STATUS'){
      $lsOrders = implode($aaOrders,',');
      $liStatus = (int) $aValue;
      if (strlen($lsOrders)>0 && $liStatus >= 0){
       $lsStatment =
        "UPDATE orders ".
        " SET status = $liStatus".
        " WHERE order_id in ($lsOrders)";
       if(!$sql->query($lsStatment)){
        echo "failed change order status[id:$lsOrders](db error: ".$sql->error().")";
        global $HTTP_SERVER_VARS;
        error_log("\r[".date("d.m.Y G:i:s")."] [client ".$HTTP_SERVER_VARS["REMOTE_ADDR"]."] db error: ".$sql->error(),3,"./log/error.log");
       } elseif($liStatus==1||$liStatus==5||$liStatus==6){
	    //резерв на складе
	    foreach($aaOrders as $i=>$order_id){
		  foreach (getOrderItems($order_id) as $j=>$order){
		    $Item = new item($order->nmcl_id);
			$fwrh_id =1;$fgoods_type_id=1;$fum_id=1;$fprice=0;
			$order->ctg_id=(strlen($order->ctg_id)>0?$order->ctg_id:'null');
			switch($liStatus){
			 case 1:// утверждение заказа - устанавливаем резерв
			   //$tran_id = 
			   //$Item->createWrhTransaction($fwrh_id,$fgoods_type_id,
			   //       $order->ctg_id,$fprice,$fum_id,-1,1,$order->quantity,$order_id);
               //if ($tran_id < 0) echo 'add_transaction|FAILED|'.$Item->getMessage();				
			   break;  
			 case 5:// исполнение заказа - 1. снимаем резерв 2. расход со склада
			   //$tran_id = 
			   //$Item->createWrhTransaction($fwrh_id,$fgoods_type_id,
			   //       $order->ctg_id,$fprice,$fum_id,1,1,$order->quantity,$order_id);
			   //$tran_id = 
			   //$Item->createWrhTransaction($fwrh_id,$fgoods_type_id,
			   //       $order->ctg_id,$fprice,$fum_id,-1,0,$order->quantity,$order_id);
               //if ($tran_id < 0) echo 'add_transaction|FAILED|'.$Item->getMessage();				
			   break;
			 case 6:// отмена заказа - снимаем резерв
			   //$tran_id = 
			   //$Item->createWrhTransaction($fwrh_id,$fgoods_type_id,
			   //       $order->ctg_id,$fprice,$fum_id,1,1,$order->quantity,$order_id);
			}
			
		  }
		}
	   }
      }
    }
    elseif ($action == 'CHANGE_NOTE2'){
      if (strlen($aaOrders)>0 && $aaOrders >= 0){
			 $liOrder = (float)$aaOrders;
			 $lsNote2 = str_replace("'","''",strip_tags($aValue));
       $lsStatment =
        "UPDATE orders ".
        " SET note2 = '$lsNote2'".
        " WHERE order_id = $liOrder";
       if(!$sql->query($lsStatment)){
        echo "failed change order note2[id:$liOrder](db error: ".$sql->error().")";
        global $HTTP_SERVER_VARS;
        error_log("\r[".date("d.m.Y G:i:s")."] [client ".$HTTP_SERVER_VARS["REMOTE_ADDR"]."] db error: ".$sql->error(),3,"./log/error.log");
       }
      }
    }
    return $sql->result;
  }
  function getOrderDetails($orderId){
    global $sql;
    $query = $sql->query(sprintf(
       "SELECT o.*,kn.*, concat(o.first_name,' ',o.last_name) customer_name, od.amount subtotal_price, od.amount+ifnull(o.deliv_price,0) total_price, 'pending' financial_status, 1 confirmed "
       . " FROM orders o LEFT JOIN kn_orders kn ON o.order_id = kn.shopify_id "
       . " LEFT JOIN (SELECT sum(amount) amount, order_id FROM order_details GROUP BY order_id) od ON o.order_id = od.order_id "
       . " WHERE o.order_id = %f"
            ,$sql->escape_string($orderId))
    );
    if ($query) {
       $result = $sql->fetchAssoc();
       /*foreach($this->mapping as $key => $field){
         $result[$key] = $query->row[$field];
       }
       $queryItems = $this->db->query(sprintf(
            "SELECT od.* FROM order_details od WHERE od.order_id = %f"
            ,$this->db->escape($orderId))
       );
       if ($queryItems->num_rows>0) {
           $result['items'] =  $queryItems->rows;
       }*/
    } else {
        throw new Exception("Order not found [order_id:{$orderId}]");
    }
    return $result;
  }
    function formatStringValue($value){
      global $sql;
      $val = $sql->escape_string($value);
      return ((isset($val)&&strlen($val)>0)?"'{$val}'":'null');
    }     
  function saveOrderDetails(&$details){
      $res = true;
      global $sql;
      if ($details){
          $order = getOrderDetails($details['order_id']);
          if ($order["shopify_id"] != null) {
            $stmt = sprintf("UPDATE kn_orders set ".
            //"shopify_upd = ".$this->formatValue($details['shopify_upd']).
            "delivery_dt = %s".
            ",delivery_address = %s".
           // ",status = ".$this->formatValue($details['status']).
           // ",status_dt = ".$this->formatValue($details['status_dt']).
           // ",payment_status = ".$this->formatValue($details['payment_status']).
           // ",payment_dt = ".$this->formatValue($details['payment_dt']).
            ",delivery_total = %s".
            ",cash_on_delivery = %s".
                    ",payment_status = %s".
            ",comments = %s".
            ",transp_number = %s".
            " WHERE shopify_id = %f",
            formatStringValue($details['delivery_dt']),
            formatStringValue($details['delivery_address']),
           // ",status = ".formatValue($details['status']).
           // ",status_dt = ".formatValue($details['status_dt']).
           // ",payment_status = ".formatValue($details['payment_status']).
           // ",payment_dt = ".formatValue($details['payment_dt']).
            formatStringValue($details['delivery_total']),
            formatStringValue($details['cash_on_delivery']),
            formatStringValue($details['payment_status']),
            formatStringValue($details['f_note2']),
            formatStringValue($details['transp_number']),
            $sql->escape_string($details['order_id'])
                    );
          } else {
            $stmt = sprintf(
                 "INSERT INTO kn_orders (shopify_id,delivery_dt,delivery_address,delivery_total,cash_on_delivery,payment_status,comments,transp_number) "
                 ."VALUES (%f,%s,%s,%s,%s,%s,%s,%s)",
                    $sql->escape_string($details['order_id']),
                    formatStringValue($details['delivery_dt']),
                    formatStringValue($details['delivery_address']),
                    formatStringValue($details['delivery_total']),
                    formatStringValue($details['cash_on_delivery']),
                    formatStringValue($details['payment_status']),
                    formatStringValue($details['f_note2']),
                    formatStringValue($details['transp_number'])
                 );
          }
          $query = $sql->query($stmt);
          if (!$query){
              throw new Exception($sql->error().":".$stmt);
          }
          if ($sql->affectedRows()>0){
             $details["message"] = "Success";
          } else {
             $details["message"] = "Order not modified (order_id:[{$details['order_id']}])";
          }
      } else {
          throw new Exception("Invalid parameters");
      } 
     return $res;
  }
//------------------------------------------------------------------------------
class Category{
    public $id;
	public $name;
	public $title;
	public $type_id;
	public $type;
	public $description;
	public $foto_name;
	public $status;
	public $select_type;
	public $nmcl_count;
	private $err_message;
  function __construct($aCtgID) {
    if (isset($aCtgID)&&$aCtgID>0){
	  $lid = (int)$aCtgID;
	  global $sql;
      if(!$sql->query("SELECT c.*, ct.alt_code,(select count(*) from nmcl_categories n where c.ctg_id = n.ctg_id) as nmcl_count FROM categories c, category_types ct where c.ctg_id = $lid and c.ctg_type_id = ct.ctg_type_id")){
	    $this->err_message = 'Ошибка BD:'.$sql->error();
		return;
	  }
      if (!$row = $sql->fetchObject()){
	    $this->err_message = 'Ошибка BD:'.$sql->error();
		return;
	  }
      $this->id=$row->ctg_id; 
	  $this->name=$row->ctg_name; 
	  $this->title=$row->title;
	  $this->type_id=$row->ctg_type_id;
	  $this->type_id=$row->alt_code;
	  $this->description=$row->description;
	  $this->foto_name=$row->foto_name;
	  $this->status=$row->status;
	  $this->select_type=$row->select_type;
	  $this->nmcl_count=$row->nmcl_count;
	  
	} else $this->id = 0;
  }
  function getMessage(){return $this->err_message;}
    function save(){
    global $sql;
	$this->err_message = null;
	$status = (strlen($this->status)==0?'null':$this->status);
	if($this->id > 0)
    $lsStatment =
        "UPDATE categories ".
        " SET ".
				" ctg_name = '".str_replace("'","''",$this->name)."', ".
				" title = '".str_replace("'","''",$this->title)."', ".
				" status = $status, ".
				" foto_name = '".str_replace("'","''",$this->foto_name)."',".
				" select_type = '".str_replace("'","''",$this->select_type)."',".
				" description = '".str_replace("'","''",$this->description)."'".
        " WHERE ctg_id = ".$this->id;
	else
    $lsStatment =
        "INSERT INTO categories (ctg_name,title,status,foto_name,select_type,description,ctg_type_id) ".
        "VALUES ( ".
				" '".str_replace("'","''",$this->name)."', ".
				" '".str_replace("'","''",$this->title)."', ".
				" $status, ".
				" '".str_replace("'","''",$this->foto_name)."',".
				" '".str_replace("'","''",$this->select_type)."',".
				" '".str_replace("'","''",$this->description)."',".
				" (select ctg_type_id from category_types where alt_code = '$this->type') ".
        " )";

    if(!$sql->query($lsStatment)){
      $this->err_message = "failed change category[id:$this->id](db error: ".$sql->error().")".$lsStatment;
    } else
	if($this->id == 0) $this->id = getLastID();
	return $sql->result;
  }
  function remove(){
    global $sql;
	$this->err_message = null;
	if($this->id <= 0) {$this->err_message='Категория не идентифицирована'; return false;}
    $lsStatment = "select count(*) as cnt from order_details where ctg_id = $this->id";
    if(!$sql->query($lsStatment)){
      $this->err_message = "failed get order count (db error: ".$sql->error().")".$lsStatment;
	  return $sql->result;
    }
    $row = $sql->fetchObject(); 
	if ($row->cnt > 0) {$this->err_message='Невозможно удалить категорию, имеются подчиненые записи в таблице Заказы'; return false;}
    $lsStatment = "delete from nmcl_categories where ctg_id = $this->id";
    if(!$sql->query($lsStatment)){
      $this->err_message = "failed delete nmcl_categories (db error: ".$sql->error().")".$lsStatment;
	  return $sql->result;
    }
    $lsStatment = "delete from categories where ctg_id = $this->id";
    if(!$sql->query($lsStatment)){
      $this->err_message = "failed delete category [id:$this->id] (db error: ".$sql->error().")".$lsStatment;
	}
	return $sql->result;
  }
 }
//------------------------------------------------------------------------------
// Складской учет
//------------------------------------------------------------------------------
function getRefElements($aRef,$aParam = null){
     // Returns an array of all item in the order

     global $sql;
     $laRet = array();
     switch($aRef){
	  case 'warehouse': $id_col='wrh_id';$order_col = '1'; break;
	  case 'wrh_goods_type':$id_col='goods_type_id';$order_col = '1'; break;
	  case 'wrh_um':$id_col='um_id';$order_col = '1'; break;
	  case 'wrh_size':$id_col='ctg_id';$order_col = 'alt_code desc, cast(name as UNSIGNED),name'; 
	    $aRef =
	         "( SELECT c.ctg_id,c.ctg_name as name, ct.alt_code ".
       "  FROM category_types ct, categories c ".
       " WHERE ct.alt_code in('$aParam','$aParam".'_CH'."') and c.ctg_type_id=ct.ctg_type_id and c.status=1)";

	  break;
	 }
     if (!$sql->query("SELECT $id_col as id, name FROM $aRef a order by $order_col")){
	   echo $sql->error();
	   return	$laRet; 
	 }
     while ($row = $sql->fetchObject()) {
        $laRet[$row->id] = $row->name;
     }
     return $laRet;
}

function wrhrests($awrh_id = 1)
{

     global $sql;

     $laRet = array();

		 $lsSql = "
SELECT nmcl_id,ctg_id,um_id,n.alt_code,n.nmcl_name, u.short_name as um_name, ctg_name as size, sum(`qty_rest`) as qty_rest,sum(`qty_reserved`) as qty_reserved,
n.price,n.price_mid,n.price_min,sum(`qty_rest`)*n.price as amount,sum(`qty_rest`)*n.price_mid as amount_mid,sum(`qty_rest`)*n.price_min as amount_min
FROM `wrh_card` wc 
join nomenclatures n using(nmcl_id)
left join categories c using(ctg_id)
left join wrh_um u using (um_id)
WHERE `wrh_id`=$awrh_id and qty_rest!=0
group by nmcl_id,ctg_id,um_id
order by ifnull(nullif(alt_code,''),'яяя'),nmcl_name,ctg_name		 ";
     if ($sql->query($lsSql)){
	   $i=0;
       while ($row = $sql->fetchObject()){ 
        $laRet[$i++] = $row;
			 }
     } else {
		   echo $sql->error();
		 }

    return $laRet;

}
function wrhrests_totals($awrh_id = 1)
{

     global $sql;

     $laRet = null;

		 $lsSql = "
SELECT sum(`qty_rest`) as qty_rest,sum(`qty_reserved`) as qty_reserved,
sum(`qty_rest`*n.price) as amount,sum(`qty_rest`*n.price_mid) as amount_mid,sum(`qty_rest`*n.price_min) as amount_min
FROM `wrh_card` wc 
join nomenclatures n using(nmcl_id)
WHERE `wrh_id`=$awrh_id and qty_rest!=0";
     if ($sql->query($lsSql)){
	   $i=0;
       while ($row = $sql->fetchObject()){ 
        $laRet = $row;
			 }
     } else {
		   echo $sql->error();
		 }

    return $laRet;
}
function findItems($strtofind){
     // Returns an array of items

    global $sql;

    $laRet = array();

	$lsSql = "SELECT n.*,n.nmcl_id as `id`,n.nmcl_name as `name` FROM nomenclatures n, nmcl_groups ng".
                      " WHERE ng.grp_id = n.grp_id and ng.parent_id=200 ".
					  "and (upper(concat(alt_code,' ',nmcl_name)) like upper('%$strtofind%'))".
                      " ORDER BY ifnull(n.alt_code,'яяя'),n.nmcl_id";    
     if ($sql->query($lsSql)){
       while ($row = $sql->fetchObject()){ 
        $laRet[$row->nmcl_id] = $row;
			 }
     } else {
		   echo $sql->error();
		 }

    return $laRet;
}
function execsql($aStatment,&$amsg,$aid=0){
  global $sql;
  $i=0;
  $laRet = array();
  if ($sql->query($aStatment)){
    while ($row = $sql->fetchObject()){ 
	if($aid==0)$laRet[$i++] = $row;
	else $laRet[$row->id] = $row;
	}
  } else {
	$amsg = $sql->error().' ('.$aStatment.')';
  }
  return $laRet;
}
function createWrhTransaction($acard_id,$adc_flag,$ais_reserve,$aquantity,$aorder_id,&$amsg){
   $err_message = null;

   $lcard_id = (int) $acard_id;
   if ($lcard_id < 1) return -1;
   $lsStatment = "SELECT qty_rest /*+ ($aquantity*$adc_flag*(1-$ais_reserve))*/ as q_rest,
		        qty_reserved /*- ($aquantity*$adc_flag*$ais_reserve)*/ as q_reserved,
				(select abs(sum(quantity*dc_flag)) from wrh_transactions wt 
				 where wt.card_id = w.card_id and ifnull(order_id,-1) = ifnull($aorder_id,-1) and is_reserve = 1 /*$ais_reserve*/
				) as order_reserve
				FROM wrh_card w
		  WHERE card_id = $lcard_id";
   if (count($rests=execsql($lsStatment,$amsg))<=0) return;
   //echo "$ais_reserve..$adc_flag....".$rests[0]->q_rest.':'.$rests[0]->order_reserve.':'.$rests[0]->q_reserved.'<br>';
   //--------------------- check rests ---------------------------------
   if ($ais_reserve==0){
     if ($adc_flag ==-1 && $aquantity > $rests[0]->q_rest - $rests[0]->q_reserved + $rests[0]->order_reserve){
       $amsg = 'Данная операция приведет к отрицательному остатку на складе';
	   return -1;
     }
   } else {
   if ($adc_flag < 0 &&  $aquantity > $rests[0]->q_rest - $rests[0]->q_reserved + $rests[0]->order_reserve){
     $amsg = "Невозможно зарезервир. $aquantity шт., остаток с учетом резервов: ".
	         ($rests[0]->q_rest - $rests[0]->q_reserved + $rests[0]->order_reserve);
	 return -1;
   }
   if ($adc_flag > 0 &&  $aquantity > $rests[0]->order_reserve ){
     $amsg = 'Не возможно отменить больше чем было зарезервировано';
	 return -1;
   }   
   }
   //--------------------- ------------ ---------------------------------
   global $sql;
   // сначала удалим старый резерв если он был
   if ($adc_flag == -1 && $ais_reserve == 1 && $rests[0]->order_reserve > 0){
     $lsStatment =
        "DELETE from wrh_transactions 
		 where card_id = $acard_id and ifnull(order_id,-1) = ifnull($aorder_id,-1) and is_reserve = $ais_reserve";
     if(!$sql->query($lsStatment)){
      $err_message = "failed delete transaction (db error: ".$sql->error().")";
	  $amsg = $err_message;
	  return -1;
     }
   }
   // добавляем проводку если количество больше 0
   $atran_id=0;
   if($aquantity>0){
	 if ($adc_flag==-1 && $ais_reserve == 0 && $rests[0]->order_reserve > 0){
	 // при отгрузке со склад отменим соответствующий резерв
	 $aqty = min($rests[0]->order_reserve,$aquantity);
     $lsStatment =
        "INSERT into wrh_transactions (`card_id`,`dc_flag`,`is_reserve`,`quantity`,`order_id`)
		 values ($lcard_id,1,1,$aqty,$aorder_id)";

     if(!$sql->query($lsStatment)){
      $err_message = "failed create transaction (db error: ".$sql->error().")";
	  $amsg = $err_message;
	  return -1;
     }	 
	 }
     $lsStatment =
        "INSERT into wrh_transactions (`card_id`,`dc_flag`,`is_reserve`,`quantity`,`order_id`)
		 values ($lcard_id,$adc_flag,$ais_reserve,$aquantity,$aorder_id)";

     if(!$sql->query($lsStatment)){
      $err_message = "failed create transaction (db error: ".$sql->error().")";
	  $amsg = $err_message;
	  return -1;
     }
     $atran_id=getLastID();   
   }
   
   if ($adc_flag == -1 && $ais_reserve == 1 && $rests[0]->order_reserve > 0) $aquantity = $aquantity-$rests[0]->order_reserve;
   if ($adc_flag == -1 && $ais_reserve == 0 && $rests[0]->order_reserve > 0)
    $newreserve = min($aquantity,$rests[0]->order_reserve);
   else
    $newreserve = ($aquantity)*$adc_flag*$ais_reserve;
   $newrest    = ($aquantity)*$adc_flag*(1-$ais_reserve);
   $lsStatment =
        "UPDATE wrh_card SET qty_rest = qty_rest + $newrest,
		        qty_reserved = qty_reserved - $newreserve
		  WHERE card_id = $lcard_id";

   if(!$sql->query($lsStatment)){
      $err_message = "failed update warehouse rests (db error: ".$sql->error().")";
	  $amsg = $err_message;
	  return -1;
   }
   
   return $atran_id;
}
function getDocuments($aDoctp_id){
   $lsmsg='';
   $lsStatment = "SELECT d.doc_id as id,d.*,concat(doc_number,' от ',doc_date) as doc_name from wrh_documents d WHERE doctp_id = $aDoctp_id order by doc_id desc";
   if (count($docs=execsql($lsStatment,$lsmsg,1))<=0) echo "$lsmsg";
   return $docs;
}
function getDocItems($aDoc_id){
   $lsmsg='';
   $lsStatment = "SELECT di.line_id as id, di.*,concat(n.alt_code,' - ',n.nmcl_name)as nmcl_name  from wrh_doc_items di join nomenclatures n using(nmcl_id) WHERE doc_id = $aDoc_id";
   if (count($docs=execsql($lsStatment,$lsmsg,1))<=0) echo "$lsmsg";
   return $docs;
}
function getItemsToProd($aDocID=0){
   $lid=(int)$aDocID;
   $lsmsg='';
   $lsStatment = "
   select 0 as line_id,od.*,c.ctg_name,concat(n.alt_code,' - ',n.nmcl_name)as nmcl_name from 
orders o join order_details od using(order_id)
join nomenclatures n using(nmcl_id)
left join categories c using(ctg_id)
where o.status in (0,1)
and( not exists (select null from wrh_card wc where wc.nmcl_id = od.nmcl_id and wc.ctg_id = od.ctg_id group by wc.nmcl_id having sum(qty_rest - qty_reserved) >= od.quantity)
or od.ctg_id = 0 or od.ctg_id is null
)
and not exists(select null from wrh_doc_items d where d.doc_id = $lid and d.nmcl_id = od.nmcl_id and d.ctg_id = d.ctg_id)
   ";
   if (count($docs=execsql($lsStatment,$lsmsg))<=0) echo "$lsmsg";
   return $docs;
}
//------------------------------------------------------------------------------
function getItemsToMove($aWrhID=0){
   $lid=(int)$aWrhID;
   $lsmsg='';
   $lsStatment = "
  SELECT 0 AS line_id, wc.* ,wc.qty_rest as quantity,null as order_id, c.ctg_name, concat( n.alt_code, ' - ', n.nmcl_name ) AS nmcl_name
from wrh_card wc 
join nomenclatures n using(nmcl_id)
LEFT JOIN categories c
USING ( ctg_id )
WHERE wrh_id = $lid and qty_rest > 0   ";
   if (count($docs=execsql($lsStatment,$lsmsg))<=0) echo "$lsmsg";
   return $docs;
}
//------------------------------------------------------------------------------
class Document{
    public $id;
	public $doc_id;
	public $doctp_id;
	public $doc_date;
	public $doc_number;
	public $doc_name;
	public $note;
	public $status;
	public $wrh_from;
	public $wrh_to;
	public $items = array();
	private $err_message;
  function __construct($aDocID) {
    if (isset($aDocID)&&$aDocID>0){
	  $lid = (int)$aDocID;
	  global $sql;
      if(!$sql->query("SELECT d.* FROM wrh_documents d where d.doc_id = $lid")){
	    $this->err_message = 'Ошибка BD:'.$sql->error();
		return;
	  }
      if (!$row = $sql->fetchObject()){
	    $this->err_message = 'Ошибка BD:'.$sql->error();
		return;
	  }
      $this->id=$row->doc_id; 
	  $this->doc_id=$row->doc_id; 
	  $this->doctp_id=$row->doctp_id;
	  $this->doc_date=$row->doc_date;
	  $this->doc_number=$row->doc_number;
	  $this->doc_name=$row->doc_number.' от '.$row->doc_date;
	  $this->note=$row->note;
	  $this->wrh_from=$row->wrh_from;
	  $this->wrh_to=$row->wrh_to;
	  $this->status=$row->status;
	  $this->items = getDocItems($this->id);
	} else {
	  $this->id = 0;
	  $this->doc_name = 'Новый документ';
	  $this->doc_date = date("Y-m-d");
	  $this->status = 1;
	}
  }
  function getMessage(){return $this->err_message;}
  function save(){
    global $sql;
	$result;
	$this->err_message = null;
	$status = (strlen($this->status)==0?'null':$this->status);
    $this->doc_name=$this->doc_number.' от '.$this->doc_date;

	if($this->id > 0)
    $lsStatment =
        "UPDATE wrh_documents ".
        " SET ".
				" doc_date = '".$this->doc_date."', ".
				" doc_number = '".$this->doc_number."', ".
				" status = $status, ".
				" note = '".str_replace("'","''",$this->note)."'".
        " WHERE doc_id = ".$this->id;
	else
    $lsStatment =
        "INSERT INTO wrh_documents (doctp_id,doc_date,status,doc_number,note) ".
        "VALUES ( ".$this->doctp_id.", ".
				" '".str_replace("'","''",$this->doc_date)."', ".
				 $status .
				", '".str_replace("'","''",$this->doc_number)."',".
				" '".str_replace("'","''",$this->note)."'".
        " )";

    if(!$sql->query($lsStatment)){
      $this->err_message = "failed change Document[id:$this->id](db error: ".$sql->error().")".$lsStatment;
	  $result = $sql->result;
    } else{
	  if($this->id == 0) $this->id = getLastID();
	  $result = $sql->result;
	  foreach ($this->items as $i=>$item){
	    $this->items[$i]->doc_id = $this->id;
	    $this->items[$i]->save();
		}
	}
	$this->doc_id = $this->id;
	return $result;
  }
  function remove(){
    global $sql;
	$this->err_message = null;
	if($this->id <= 0) {$this->err_message='Документ не идентифицирован'; return false;}
   /* $lsStatment = "select count(*) as cnt from order_details where ctg_id = $this->id";
    if(!$sql->query($lsStatment)){
      $this->err_message = "failed get order count (db error: ".$sql->error().")".$lsStatment;
	  return $sql->result;
    }
    $row = $sql->fetchObject(); 
	if ($row->cnt > 0) {$this->err_message='Невозможно удалить категорию, имеются подчиненые записи в таблице Заказы'; return false;}
    $lsStatment = "delete from nmcl_categories where ctg_id = $this->id";
    if(!$sql->query($lsStatment)){
      $this->err_message = "failed delete nmcl_categories (db error: ".$sql->error().")".$lsStatment;
	  return $sql->result;
    }
    $lsStatment = "delete from categories where ctg_id = $this->id";
    if(!$sql->query($lsStatment)){
      $this->err_message = "failed delete category [id:$this->id] (db error: ".$sql->error().")".$lsStatment;
	}*/
	return $sql->result;
  }
 }
 class DocItem{
    public $id;
	public $line_id;
	public $doc_id;
	public $order_id;
	public $nmcl_id;
	public $nmcl_name;
	public $ctg_id;
	public $ctg_name;
	public $quantity;
	public $status;
	private $err_message;
  function __construct($aLineID) {
    if (isset($aLineID)&&$aLineID>0){
	  $lid = (int)$aLineID;
	  global $sql;
      if(!$sql->query("SELECT d.* FROM wrh_doc_items d where d.line_id = $lid")){
	    $this->err_message = 'Ошибка BD:'.$sql->error();
		return;
	  }
      if (!$row = $sql->fetchObject()){
	    $this->err_message = 'Ошибка BD:'.$sql->error();
		return;
	  }
      $this->id=$row->line_id; 
	  $this->line_id=$row->doc_id; 
	  $this->doc_id=$row->doc_id;
	  $this->order_id=$row->order_id;
	  $this->nmcl_id=$row->nmcl_id;
	  $this->ctg_id=$row->ctg_id;
	  $this->quantity=$row->quantity;
	  $this->status=$row->status;
	} else {$this->id = 0;$this->line_id=0;}
  }
  function getMessage(){return $this->err_message;}
  function save(){
    global $sql;
	$this->err_message = null;
	
	$this->nmcl_id = (int)$this->nmcl_id;
	$this->ctg_id = (int)$this->ctg_id;
	$this->order_id = (int)$this->order_id;
    $this->quantity = (int)$this->quantity;
    $this->status = (int)$this->status;
    if ($this->nmcl_id <=0 ||$this->ctg_id<=0 ||$this->quantity <=0){
	  $this->err_message = 'Не все обязательные поля заполнены!!!';
	  return false;
	}

	if($this->id > 0)
    $lsStatment =
        "UPDATE wrh_doc_items ".
        " SET ".
				" nmcl_id = ".$this->nmcl_id.", ".
				" ctg_id = ".$this->ctg_id.", ".
				" order_id = $this->order_id, ".
				" quantity = $this->quantity".
        " WHERE line_id = ".$this->id;
	else
    $lsStatment =
        "INSERT INTO wrh_doc_items (doc_id,nmcl_id,ctg_id,order_id,quantity) ".
        "VALUES ( ".$this->doc_id.",".$this->nmcl_id.",".$this->ctg_id.",".$this->order_id.",".$this->quantity.
        " )";

    if(!$sql->query($lsStatment)){
      $this->err_message = "failed change Document Item[id:$this->id](db error: ".$sql->error().")".$lsStatment;
    } else
	if($this->id == 0) $this->id = getLastID();
	$this->line_id = $this->id;
	return $sql->result;
  }
}
//------------------------------------------------------------------------------
// Статистика
//------------------------------------------------------------------------------
function getNmclStat($aItemId, $aperiod = 1)
{
     // Returns an array of all item of group

     global $sql;

     $laRet = array();
     if (!isset($aItemId) || $aItemId <= 0) return $laRet;
		 
		 $lItemId = (int)$aItemId;

		 $lsSql = "
SELECT b.sid,a.mt, 
       min(if( a.mt = b.access_time, b.referer, NULL )) referer_link, 
       min(if( a.mt = b.access_time, b.page_title, NULL )) referer_title, 
	   if( min( b.access_time ) = b.access_time, ifnull(b.referer,b.uri), NULL ) sess_referer_link,
	   if( min( b.access_time ) = b.access_time, b.page_title, NULL ) sess_referer_title
FROM ( SELECT sid, min( access_time ) mt
       FROM access_log
       WHERE concat( request, '&' ) LIKE '%item_id=$lItemId&%'
       GROUP BY sid
     )a, access_log b
WHERE a.sid = b.sid
GROUP BY b.sid,a.mt
ORDER BY a.mt
		 ";
     if ($sql->query($lsSql)){
			 $pattern = '(=)([а-яА-Я]+[а-я А-Я]*)';
			 $replacement = '\\1<em>\\2</em>';
       while ($row = $sql->fetchObject()){ 
			  $row->sess_referer_link = iconv('UTF-8','cp1251',urldecode($row->sess_referer_link));
			  $row->referer_link = iconv('UTF-8','cp1251',urldecode($row->referer_link));
        //подсветим поисковый запрос
				$row->sess_referer_link = eregi_replace($pattern, $replacement, $row->sess_referer_link);			
				$row->referer_link = eregi_replace($pattern, $replacement, $row->referer_link);			
        $laRet[$row->sid] = $row;
			 }
     } else {
		   echo $sql->error();
		 }

    return $laRet;

}
//------------------------------------------------------------------------------
function getOrderStat($aItemId, $aperiod = 1)
{
     // Returns an array of all item of group

     global $sql;

     $laRet = array();
     if (!isset($aItemId) || $aItemId <= 0) return $laRet;
		 
		 $lItemId = (int)$aItemId;

		 $lsSql = "
select al.* from
access_log al,
orders o
where o.order_id = $lItemId
and o.sid = al.sid
order by access_time
		 ";
     if ($sql->query($lsSql)){
			 $pattern = '(=)([а-яА-Я]+[а-я А-Я]*)';
 			 $replacement = '\\1<em>\\2</em>';
       while ($row = $sql->fetchObject()) {
			  $row->referer = iconv('UTF-8','cp1251',urldecode($row->referer));
			  //подсветим поисковый запрос
				$row->referer = eregi_replace($pattern, $replacement, $row->referer);			
        $laRet[$row->log_id] = $row;
				}
     } else {
		   echo $sql->error();
		 }

    return $laRet;

}
//------------------------------------------------------------------------------
function getNmclStatCount($aItemId, $aperiod = 1)
{
     // Returns an array of all item of group

     global $sql;

     $laRet = array();
     if (!isset($aItemId) || $aItemId <= 0) return $laRet;
		 
		 $lItemId = (int)$aItemId;

		 $lsSql = "
SELECT count( DISTINCT sid ) AS csid, count( DISTINCT user_ip ) AS cip, count( * ) AS cview
FROM access_log
WHERE concat( request, '&' ) LIKE '%item_id=$aItemId&%'
		 ";
     if ($sql->query($lsSql)){
       if ($row = $sql->fetchObject()) {
        $laRet['Сессий'] = $row->csid;
        $laRet['Хостов'] = $row->cip;
        $laRet['Просмотров'] = $row->cview;
			}
     } else {
		   echo $sql->error();
		 }
		 $lsSql = "
SELECT count( DISTINCT o.order_id ) corder
FROM order_details od, orders o
WHERE od.nmcl_id=$aItemId  and o.order_id = od.order_id
		 ";
     if ($sql->query($lsSql)){
       if ($row = $sql->fetchObject()) {
        $laRet['Заказов'] = $row->corder;
			}
     } else {
		   echo $sql->error();
		 }

    return $laRet;

}
//------------------------------------------------------------------------------
function statrep01($aperiod = 1)
{
     // Returns an array of all item of group

     global $sql;

     $laRet = array();

		 $lsSql = "
select nl.*, od.csold,od.corder,100*od.corder/csid as conv from
(
SELECT nmcl_id,alt_code,nmcl_name,count( DISTINCT sid ) AS csid, count( DISTINCT user_ip ) AS cip, count( * ) AS cview
  FROM `access_log` l, nomenclatures n
WHERE concat( request, '&' ) LIKE concat('%item_id=',n.nmcl_id,'&%')
group by n.nmcl_id
) nl left outer join
(select nmcl_id, count(*) as csold, count(distinct od.order_id) as corder from order_details od, orders o 
 where o.order_id = od.order_id and order_date >='2011-03-02' and status<>6
 group by od.nmcl_id) od
 on (nl.nmcl_id = od.nmcl_id)
group by nl.nmcl_id
order by conv desc
		 ";
     if ($sql->query($lsSql)){
			 $pattern = '(=)([а-яА-Я]+[а-я А-Я]*)';
			 $replacement = '\\1<em>\\2</em>';
       while ($row = $sql->fetchObject()){ 
			  //$row->sess_referer_link = iconv('UTF-8','cp1251',urldecode($row->sess_referer_link));
			  //$row->referer_link = iconv('UTF-8','cp1251',urldecode($row->referer_link));
        //подсветим поисковый запрос
				//$row->sess_referer_link = eregi_replace($pattern, $replacement, $row->sess_referer_link);			
				//$row->referer_link = eregi_replace($pattern, $replacement, $row->referer_link);			
        $laRet[$row->nmcl_id] = $row;
			 }
     } else {
		   echo $sql->error();
		 }

    return $laRet;

}
function getReport($areport,$ps='',$pe='',$ls='',$ll='')
{
 global $sql;
 $laRet = array();
 switch ($areport){
 case 'NO_CATEGORY': $lsSql = 
 "SELECT n.* FROM nomenclatures n, nmcl_groups ng".
 " WHERE ng.grp_id = n.grp_id and ng.parent_id=200 and n.status != 1 and ng.status != 1".
 "   and not exists(select null from nmcl_categories nc join categories c using (ctg_id) join category_types ct using(ctg_type_id) ".
 "                   where ct.alt_code='WARDROBE' and n.nmcl_id = nc.nmcl_id )".
 " ORDER BY ifnull(nullif(trim(n.alt_code),''),'яяя'),n.alt_code,n.nmcl_name"
 ;break;
 case 'NO_COLLECTION': $lsSql = 
 "SELECT n.* FROM nomenclatures n, nmcl_groups ng".
 " WHERE ng.grp_id = n.grp_id and ng.parent_id=200 and n.status != 1 and ng.status != 1".
 "   and not exists(select null from nmcl_categories nc join categories c using (ctg_id) join category_types ct using(ctg_type_id) ".
 "                   where ct.alt_code='COMPLECT' and n.nmcl_id = nc.nmcl_id )".
 " ORDER BY ifnull(nullif(trim(n.alt_code),''),'яяя'),n.alt_code,n.nmcl_name"
 ;break;
 case 'NO_SIZE': $lsSql = 
 "SELECT n.* FROM nomenclatures n, nmcl_groups ng".
 " WHERE ng.grp_id = n.grp_id and ng.parent_id=200 and n.status != 1 and ng.status != 1".
 "   and not exists(select null from nmcl_categories nc join categories c using (ctg_id) join category_types ct using(ctg_type_id) ".
 "                   where ct.alt_code like'SIZE%' and n.nmcl_id = nc.nmcl_id )".
 " ORDER BY ifnull(nullif(trim(n.alt_code),''),'яяя'),n.alt_code,n.nmcl_name"
 ;break;
 case 'NO_DESCR': $lsSql = 
 "SELECT n.* FROM nomenclatures n, nmcl_groups ng".
 " WHERE ng.grp_id = n.grp_id and ng.parent_id=200 and n.status != 1 and ng.status != 1".
 "   and nullif(n.description,'') is null ".
 " ORDER BY ifnull(nullif(trim(n.alt_code),''),'яяя'),n.alt_code,n.nmcl_name"
 ;break;
 case 'MOST_WANTED': 
 $where = (strlen($ps)>0&&strlen($pe)>0?"where order_date between '%s' and '%s' ":'');
 $where = sprintf($where,$sql->escape_string($ps),$sql->escape_string($pe));
 $lsSql = 
 "select ifnull(n.nmcl_name,o.nmcl_name) name, n.*, sum(qty) orders_num 
from 
 (select order_id, order_date,nmcl_id,nmcl_name, sum(quantity) qty 
    from order_details join orders o using (order_id) 
   where o.status != 6 
   group by order_id,order_date,nmcl_id,nmcl_name) o 
left join nomenclatures n using (nmcl_id) 
group by nmcl_id, ifnull(n.nmcl_name,o.nmcl_name) 
order by orders_num desc"
 ;break;
 case 'TOGETHER_ORDERED': 
 $limit = (strlen($ls)>0&&strlen($ll)>0?" limit %d,%d ":'');
 $limit = sprintf($limit,$sql->escape_string($ls),$sql->escape_string($ll));
 $lsSql = 
 "select a.nmcl_id,a.alt_code,a.nmcl_name,a.foto_name,a.orders_num,nn.nmcl_id nn_id,nn.alt_code nn_code,nn.nmcl_name nn_name,nn.foto_name nn_foto
 from(
SELECT n. * , count( * ) orders_num
FROM nomenclatures n
JOIN (  SELECT order_id, order_date, nmcl_id
          FROM order_details
          JOIN orders USING ( order_id )
         GROUP BY order_id, order_date, nmcl_id
)o USING ( nmcl_id )
GROUP BY nmcl_id
ORDER BY orders_num DESC
".$limit."
) a left join
(
    select om.nmcl_id nmcl_m,od.nmcl_id nmcl_d from order_details om join order_details od on om.order_id=od.order_id and om.nmcl_id<>od.nmcl_id
	group by om.nmcl_id,od.nmcl_id
) b on a.nmcl_id = b.nmcl_m
left join nomenclatures nn on nmcl_d = nn.nmcl_id
ORDER BY orders_num DESC,a.nmcl_id,nn.nmcl_id"
 ;
 echo $lsSql;
 break; 
 
 
 }

 if ($sql->query($lsSql)){
   while ($row = $sql->fetchObject()){
    $tmp = explode('|',$row->foto_name);
	$img = $tmp[0];
	$row->foto_name = $img;
    $laRet[$row->nmcl_id] = $row;
   }
 } else echo $sql->error().':'.$lsSql;
 return $laRet;
}
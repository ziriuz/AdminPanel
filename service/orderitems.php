<?php 
require_once("../application/env.php");
include("../includes/prd_db.php");
 if (!$sql->query("SELECT od.item_id, od.order_id, od.nmcl_id, od.ctg_id, od.quantity, od.price, od.amount, od.prod,
 CASE WHEN length( od.nmcl_name ) =0
 OR isnull( od.nmcl_name )
 THEN n.nmcl_name
 ELSE od.nmcl_name
 END nmcl_name, od.deleted, od.last_modify, c.ctg_name
 FROM order_details od
 LEFT OUTER JOIN categories c ON ( od.ctg_id = c.ctg_id )
 LEFT JOIN nomenclatures n ON n.nmcl_id = od.nmcl_id"
     ))
  header('HTTP/1.0 500 Server error');
  else{
     header('Content-type: text/plain');
     while ($row = $sql->fetchObject()) {
	 echo  $row->item_id
	 .'||'.$row->order_id
	 .'||'.$row->nmcl_id
	 .'||'.$row->ctg_id
	 .'||'.$row->quantity
	 .'||'.$row->price
	 .'||'.$row->amount
	 .'||'.$row->prod
	 .'||'.$row->nmcl_name
	 .'||'.$row->deleted
	 .'||'.$row->last_modify
	 .'||'.$row->ctg_name
	 ."`"
	 ;
     }
  }
?>

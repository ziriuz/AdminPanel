<?php 
require_once("../application/env.php");
include("../includes/prd_db.php");
 if (!$sql->query("SELECT o.*, concat_ws(' ',o.last_name,o.first_name,o.middle_name) as name, ".
                 "  (select sum(amount) from order_details od where od.order_id = o.order_id and deleted=0) as amount ".
                 "  FROM orders o "
     ))
  header('HTTP/1.0 500 Server error');
  else{
     header('Content-type: text/plain');
     while ($row = $sql->fetchObject()) {
	 echo
	 $row->order_id
	 .'||'.$row->amount
	 .'||'.$row->sid
	 .'||'.$row->first_name
	 .'||'.$row->last_name
	 .'||'.$row->name
	 .'||'.$row->email
	 .'||'.$row->phone
	 .'||'.$row->address
	 .'||'.$row->order_date
	 .'||'.$row->delivery_date
	 .'||'.$row->status
	 .'||'.$row->remote_addr
	 .'||'.$row->note
	 .'||'.$row->note2
	 .'||'.$row->deliv_id
	 .'||'.$row->post_id
	 .'||'.$row->last_modify
	 ."`"
	 ;
     }
  }
?>
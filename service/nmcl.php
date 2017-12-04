<?php 
require_once("../application/env.php");
include("../includes/prd_db.php");
 if (!$sql->query("SELECT n.* FROM nomenclatures n, nmcl_groups ng".
                      " WHERE ng.grp_id = n.grp_id and ng.parent_id=200 "))
  header('HTTP/1.0 500 Server error');
  else{
     header('Content-type: text/plain');
     while ($row = $sql->fetchObject()) {
	 echo
	 $row->nmcl_id
	 .'||'.$row->alt_code
	 .'||'.$row->nmcl_name
	 .'||'.$row->title
	 .'||'.$row->grp_id
	 .'||'.$row->description
	 .'||'.$row->tech_description
	 .'||'.$row->foto_preview
	 .'||'.$row->foto_name
	 .'||'.$row->foto_alt
	 .'||'.$row->price
	 .'||'.$row->status
	 .'||'.$row->prod_term
	 .'||'.$row->create_date
	 .'||'.$row->last_modify
	 ."`"
	 ;
     }
  }
?>
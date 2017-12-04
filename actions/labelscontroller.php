<?php
	class LabelController{
	 function __construct(){
	  return null;
	 }
  public function getLabels($alt_code,$size,$quantity)
  {
     // Returns an array of all item in the order

     global $sql;
     $laRet = array();
	 
	 $lsStatment = sprintf("
	 SELECT n.nmcl_id,c.ctg_id size_id, n.nmcl_name, 
         replace(substring(c.ctg_name,1,ifnull(nullif(instr(c.ctg_name,' '),0),16)-1),',','.')
         size_name,
	        concat(lpad(n.alt_code,4,'0'),lpad(color_id,4,'0'),lpad(c.ctg_id,4,'0')) label_code,color_name,n.alt_code
	 FROM nomenclatures n
	 LEFT JOIN categories c ON (%d=c.ctg_id) 
     LEFT JOIN (select nmcl_id, min(ctg_id) as color_id,group_concat(ctg_name) as color_name from nmcl_categories join categories using(ctg_id) where ctg_type_id=3 group by nmcl_id ) cl
            ON n.nmcl_id = cl.nmcl_id     
     WHERE n.alt_code =  '%s'
	 ",$sql->escape_string($size),$sql->escape_string($alt_code));

     if($sql->query($lsStatment)){
     $i=0;
     while ($row = $sql->fetchObject()) {
		for ($j=0;$j<$quantity;$j++)
        $laRet[$i++] = $row;
     }
	 } else echo $sql->error();
     return $laRet;

  } // end getOrderItems()	 
}
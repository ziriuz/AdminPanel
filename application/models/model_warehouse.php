<?php

class Model_Warehouse extends Model
{
  public $message;
    function getDocumentItems(){
     global $sql;
     $laRet = array();
     $lsStatment = "SELECT n.nmcl_id,n.alt_code, n.nmcl_name
          ,c.ctg_id size_id,lpad(c.ctg_id,4,'0') size_code, c.short_name size_name, size
          ,lpad(ifnull(clr_id,color_id),4,'0') color_code, case when clr_id is null then color_name else clr.ctg_name end color_name
          ,qty,qty+ifnull(qty_add,0) qty_tot, qty_add
          ,concat(lpad(n.alt_code,4,'0'),lpad(ifnull(clr_id,color_id),4,'0'),lpad(c.ctg_id,4,'0')) label_code
          ,n.foto_name,n.price,n.price_min,n.price_mid,load_id
   FROM tt_wd 
   JOIN nomenclatures n on code = n.alt_code
   LEFT JOIN categories c ON (size_id=c.ctg_id) 
   LEFT JOIN (select nmcl_id, min(ctg_id) as color_id,group_concat(ctg_name) as color_name from nmcl_categories join categories using(ctg_id) where ctg_type_id=3 group by nmcl_id ) cl
        ON n.nmcl_id = cl.nmcl_id
   left join categories clr ON clr.ctg_id = clr_id
        order by load_id,n.alt_code, case when clr_id is null then color_name else clr.ctg_name end, cast(c.ctg_name as UNSIGNED),c.ctg_name";
     if($sql->query($lsStatment)){
     $i=0;
     while ($row = $sql->fetchObject()) {
        if (strlen($row->foto_name)>0){
            $laItemImages = explode('|',$row->foto_name);
        }
        else{
            $laItemImages[0] = 'no_image.gif';        
        }
        $row->foto_name = $laItemImages[0];          
        $laRet[$i++] = $row;
     }
     } else echo $sql->error();
     return $laRet;
    }
    function getPivotedItems(){
     global $sql;
     $laRet = array();
     $lsStatment = "select n.nmcl_id,n.alt_code, n.nmcl_name,
     lpad(ifnull(clr_id,color_id),4,'0') color_code,  case when clr_id is null then color_name else clr.ctg_name end color_name,
     n.foto_name,n.price,n.price_min,n.price_mid,
 sum(qty) qty,
 sum(case when c.code='42' and c.ctg_type_id=9 then qty else null end) as q42,
 sum(case when c.code='44' and c.ctg_type_id=9 then qty else null end) as q44,
 sum(case when c.code='46' and c.ctg_type_id=9 then qty else null end) as q46,
 sum(case when c.code='48' and c.ctg_type_id=9 then qty else null end) as q48,
 sum(case when c.code='50' and c.ctg_type_id=9 then qty else null end) as q50,
 sum(case when c.code='52' and c.ctg_type_id=9 then qty else null end) as q52,
 sum(case when c.code='54' and c.ctg_type_id=9 then qty else null end) as q54,
 sum(case when c.code='56' and c.ctg_type_id=9 then qty else null end) as q56,
 sum(case when c.code='58' and c.ctg_type_id=9 then qty else null end) as q58,
 sum(case when c.code='68' and c.ctg_type_id=12 then qty else null end) as q68,
 sum(case when c.code='74' and c.ctg_type_id=12 then qty else null end) as q74,
 sum(case when c.code='80' and c.ctg_type_id=12 then qty else null end) as q80,
 sum(case when c.code='86' and c.ctg_type_id=12 then qty else null end) as q86,
 sum(case when c.code='92' and c.ctg_type_id=12 then qty else null end) as q92,
 sum(case when c.code='98' and c.ctg_type_id=12 then qty else null end) as q98,
 sum(case when c.code='104' and c.ctg_type_id=12 then qty else null end) as q104,
 sum(case when c.code='110' and c.ctg_type_id=12 then qty else null end) as q110,
 sum(case when c.code='116' and c.ctg_type_id=12 then qty else null end) as q116,
 sum(case when c.code='122' and c.ctg_type_id=12 then qty else null end) as q122,
 sum(case when c.code='128' and c.ctg_type_id=12 then qty else null end) as q128,
 sum(case when c.code='134' and c.ctg_type_id=12 then qty else null end) as q134,
 sum(case when c.code='140' and c.ctg_type_id=12 then qty else null end) as q140
 FROM tt_wd 
   JOIN nomenclatures n on code = n.alt_code
   LEFT JOIN categories c ON (size_id=c.ctg_id) 
   LEFT JOIN (select nmcl_id, min(ctg_id) as color_id,group_concat(ctg_name) as color_name from nmcl_categories join categories using(ctg_id) where ctg_type_id=3 group by nmcl_id ) cl
        ON n.nmcl_id = cl.nmcl_id
   left join categories clr ON clr.ctg_id = clr_id        
 group by load_id,n.nmcl_id,clr_id
        order by load_id,n.alt_code";
     if($sql->query($lsStatment)){
     $i=0;
     while ($row = $sql->fetchObject()) {
        if (strlen($row->foto_name)>0){
            $laItemImages = explode('|',$row->foto_name);
        }
        else{
            $laItemImages[0] = 'no_image.gif';        
        }
        $row->foto_name = $laItemImages[0];          
        $laRet[$i++] = $row;
     }
     } else echo $sql->error();
     return $laRet;
    }    
    public function getLabels($alt_code,$size,$quantity)
  {
     // Returns an array of all item in the order

     global $sql;
     $laRet = array();
	 
	 $lsStatment = sprintf("
	 SELECT n.nmcl_id,c.ctg_id size_id, n.nmcl_name, 
         c.short_name         size_name,
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

  }
  public function get_data($fwrh_id=3)
  {
  global $_SESSION;
  $MESSAGE = '';
  $WRH = getRefElements('warehouse');
  //$ACTION_TEMPLATE = 'default';
  if(!isset($fwrh_id)) $WRH_ID=3; else $WRH_ID=$fwrh_id;
  $WRHNAME = $WRH[$WRH_ID];
  $RESTS_TOTAL = wrhrests_totals($WRH_ID);
  return array(
  //'ACTION_TEMPLATE' =>  $ACTION_TEMPLATE,
  'WRH_ID' =>  $WRH_ID,
  'WRHNAME' =>  $WRHNAME,
  'RESTS_TOTAL' =>  $RESTS_TOTAL,
	'WRH' =>  $WRH,
  );
  }
  public function clear_rests($flags){
      if (isset($flags)&&  is_array($flags)){
          foreach ($flags as $key => $value) {
              $item = new Item($value);
              if($item->getWrhRests()){
               foreach ($item->wrh_rests as $j => $card) {
                if  ($card->qty_rest>0)
                $item->createWrhTransaction($card->wrh_id,$card->goods_type_id,$card->ctg_id,$card->price,$card->um_id,-1,0,$card->qty_rest,'null');
               }
              }else{
                $this->message = $item->err_message;
                break;
              }
          }
      }    
  }  
}

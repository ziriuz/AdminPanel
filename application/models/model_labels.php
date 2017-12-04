<?php

class ModelLabels extends Model
{
function getDocumentItemsPivot($pDocId){
     $lsStatment = sprintf("select n.nmcl_id,n.alt_code, n.nmcl_name,
     lpad(cl.ctg_id,4,'0') color_code,  cl.ctg_name color_name,
     n.foto_name,di.price,di.price2 price_min,di.price2 price_mid,
 sum(quantity) qty,
 sum(case when c.code not in('40','42','44','46','48','50','52','54','56','58') and c.ctg_type_id=9 then quantity else null end) as q_others,
 sum(case when c.code='40' and c.ctg_type_id=9 then quantity else null end) as q40,
 sum(case when c.code='42' and c.ctg_type_id=9 then quantity else null end) as q42,
 sum(case when c.code='44' and c.ctg_type_id=9 then quantity else null end) as q44,
 sum(case when c.code='46' and c.ctg_type_id=9 then quantity else null end) as q46,
 sum(case when c.code='48' and c.ctg_type_id=9 then quantity else null end) as q48,
 sum(case when c.code='50' and c.ctg_type_id=9 then quantity else null end) as q50,
 sum(case when c.code='52' and c.ctg_type_id=9 then quantity else null end) as q52,
 sum(case when c.code='54' and c.ctg_type_id=9 then quantity else null end) as q54,
 sum(case when c.code='56' and c.ctg_type_id=9 then quantity else null end) as q56,
 sum(case when c.code='58' and c.ctg_type_id=9 then quantity else null end) as q58,
 sum(case when c.code='68' and c.ctg_type_id=12 then quantity else null end) as q68,
 sum(case when c.code='74' and c.ctg_type_id=12 then quantity else null end) as q74,
 sum(case when c.code='80' and c.ctg_type_id=12 then quantity else null end) as q80,
 sum(case when c.code='86' and c.ctg_type_id=12 then quantity else null end) as q86,
 sum(case when c.code='92' and c.ctg_type_id=12 then quantity else null end) as q92,
 sum(case when c.code='98' and c.ctg_type_id=12 then quantity else null end) as q98,
 sum(case when c.code='104' and c.ctg_type_id=12 then quantity else null end) as q104,
 sum(case when c.code='110' and c.ctg_type_id=12 then quantity else null end) as q110,
 sum(case when c.code='116' and c.ctg_type_id=12 then quantity else null end) as q116,
 sum(case when c.code='122' and c.ctg_type_id=12 then quantity else null end) as q122,
 sum(case when c.code='128' and c.ctg_type_id=12 then quantity else null end) as q128,
 sum(case when c.code='134' and c.ctg_type_id=12 then quantity else null end) as q134,
 sum(case when c.code='140' and c.ctg_type_id=12 then quantity else null end) as q140,
 sum(case when c.code='146' and c.ctg_type_id=12 then quantity else null end) as q146,
 sum(case when c.code='152' and c.ctg_type_id=12 then quantity else null end) as q152
 FROM wrh_doc_items di 
   JOIN nomenclatures n on (di.nmcl_id = n.nmcl_id)
   LEFT JOIN categories c ON (di.ctg_id=c.ctg_id) 
   LEFT JOIN categories cl ON di.ctg2_id = cl.ctg_id
where di.doc_id = %d   
 group by n.nmcl_id
        order by n.alt_code", $this->db->escape($pDocId));
     $query = $this->db->query($lsStatment,$mode='object');
     $i=0;
     foreach ($query->rows as $i=>$row) {
        if (strlen($row->foto_name)>0){
            $laItemImages = explode('|',$row->foto_name);
        }
        else{
            $laItemImages[0] = 'no_image.gif';        
        }
        $query->rows[$i]->foto_name = $laItemImages[0];
     return $query->rows;
    }
}
function getDraftItemsPivot(){
     $lsStatment = "select n.nmcl_id,n.alt_code, n.nmcl_name,
     lpad(ifnull(clr_id,color_id),4,'0') color_code,  case when clr_id is null then color_name else clr.ctg_name end color_name,
     n.foto_name,n.price,n.price_min,n.price_mid,
 sum(qty) qty,
 sum(case when c.code not in('40','42','44','46','48','50','52','54','56','58') and c.ctg_type_id=9 then qty else null end) as q_others,
 sum(case when c.code='40' and c.ctg_type_id=9 then qty else null end) as q40,
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
 sum(case when c.code='140' and c.ctg_type_id=12 then qty else null end) as q140,
 sum(case when c.code='146' and c.ctg_type_id=12 then qty else null end) as q146,
 sum(case when c.code='152' and c.ctg_type_id=12 then qty else null end) as q152
 FROM tt_wd 
   JOIN nomenclatures n on code = n.alt_code
   LEFT JOIN categories c ON (size_id=c.ctg_id) 
   LEFT JOIN (select nmcl_id, min(ctg_id) as color_id,group_concat(ctg_name) as color_name from nmcl_categories join categories using(ctg_id) where ctg_type_id=3 group by nmcl_id ) cl
        ON n.nmcl_id = cl.nmcl_id
   left join categories clr ON clr.ctg_id = clr_id        
 group by load_id,n.nmcl_id,clr_id
        order by load_id,n.alt_code";
     $query = $this->db->query($lsStatment,$mode='object');
     $i=0;
     foreach ($query->rows as $i=>$row) {
        if (strlen($row->foto_name)>0){
            $laItemImages = explode('|',$row->foto_name);
        }
        else{
            $laItemImages[0] = 'no_image.gif';        
        }
        $query->rows[$i]->foto_name = $laItemImages[0];
     return $query->rows;
    }
  }
  public function getLabels($lineId,$quantity)
  {
     // Returns an array of all item in the order
     $laRet = array();
     $date = new DateTime();
     $date->sub(new DateInterval('P1M'));
     $prodDate = $date->format('d.m.Y');
	 
	 $lsStatment = sprintf("
	 SELECT n.nmcl_id,c.ctg_id size_id, n.nmcl_name, n.description,thing,sex,ifnull(composition,'100%s хлопок') as composition,
          c.short_name size_name, c.ext_name age,
	        concat(lpad(n.alt_code,4,'0'),lpad(ifnull(clr.ctg_id,color_id),4,'0'),lpad(c.ctg_id,4,'0')) label_code,case when clr.ctg_id is null then color_name else clr.ctg_name end color_name,n.alt_code
	   FROM tt_wd wb 
     JOIN nomenclatures n ON n.alt_code = wb.code
     LEFT JOIN categories c ON (wb.size_id=c.ctg_id)
     LEFT JOIN categories clr ON wb.clr_id = clr.ctg_id 
     LEFT JOIN (select nmcl_id, min(ctg_id) as color_id,group_concat(ctg_name) as color_name from nmcl_categories join categories using(ctg_id) where ctg_type_id=3 group by nmcl_id ) cl
            ON n.nmcl_id = cl.nmcl_id     
     LEFT JOIN (select nmcl_id, min(ctg_id) as comp_id,group_concat(ctg_name) as composition from nmcl_categories join categories using(ctg_id) where ctg_type_id=20 group by nmcl_id ) comp
            ON n.nmcl_id = comp.nmcl_id
     LEFT JOIN (select nmcl_id, replace(replace(replace(replace(group_concat(c.short_name),'Мужской,Женский','Унисекс'),'Женский,Мужской','Унисекс'),'Мальчики,Девочки','Детский'),'Девочки,Мальчики','Детский') as sex from nmcl_categories join categories c using(ctg_id) where ctg_type_id=11 and c.code in('BOY','GIRL','MAN','WOMAN')  group by nmcl_id ) sx
            ON n.nmcl_id = sx.nmcl_id
     LEFT JOIN (select nmcl_id, group_concat(c.code) as thing,group_concat(ifnull(c.short_name,c.ctg_name)) thing_type from nmcl_categories join categories c using(ctg_id) where ctg_type_id=14 group by nmcl_id ) wad
            ON n.nmcl_id = wad.nmcl_id     
     WHERE wb.id = %d
	 ",'%',$this->db->escape($lineId));
     $query = $this->db->query($lsStatment,$mode='object');
     $i=0;
     foreach ( $query->rows as $i=>$row ) {
         $row->prod_date = $prodDate;
         for ($j=0;$j<$quantity;$j++){
             $laRet[$i++] = $row;
         }
     }
     return $laRet;
  }
  
  public function getDocLabels($pDocId,$lineId,$quantity){
     $laRet = array();
     $laRet = array();
     $date = new DateTime();
     $date->sub(new DateInterval('P1M'));
     $prodDate = $date->format('d.m.Y');
     $lsStatment = sprintf("
   SELECT n.nmcl_id,n.nmcl_name,n.alt_code,n.description,thing,sex,ifnull(composition,'100%s хлопок') as composition
          ,c.ctg_id size_id,c.short_name size_name,c.ext_name age
          ,di.barcode label_code,cl.ctg_name color_name
      FROM wrh_doc_items di
      JOIN nomenclatures n on di.nmcl_id=n.nmcl_id
      LEFT JOIN categories c ON (di.ctg_id=c.ctg_id) 
      LEFT JOIN categories cl ON di.ctg2_id = cl.ctg_id 
      LEFT JOIN (select nmcl_id, min(ctg_id) as comp_id,group_concat(ctg_name) as composition from nmcl_categories join categories using(ctg_id) where ctg_type_id=20 group by nmcl_id ) comp
            ON n.nmcl_id = comp.nmcl_id
      LEFT JOIN (select nmcl_id, replace(replace(replace(replace(group_concat(c.short_name),'Мужской,Женский','Унисекс'),'Женский,Мужской','Унисекс'),'Мальчики,Девочки','Детский'),'Девочки,Мальчики','Детский') as sex from nmcl_categories join categories c using(ctg_id) where ctg_type_id=11 and c.code in('BOY','GIRL','MAN','WOMAN')  group by nmcl_id ) sx
             ON n.nmcl_id = sx.nmcl_id
      LEFT JOIN (select nmcl_id, group_concat(c.code) as thing,group_concat(ifnull(c.short_name,c.ctg_name)) thing_type from nmcl_categories join categories c using(ctg_id) where ctg_type_id=14 group by nmcl_id ) wad
            ON n.nmcl_id = wad.nmcl_id     
         
     WHERE di.doc_id = %d and di.line_id = %f
	 ",'%',$this->db->escape($pDocId),$this->db->escape($lineId));
     $query = $this->db->query($lsStatment,$mode='object');
     $i=0;
     foreach ( $query->rows as $i=>$row ) {
         $row->prod_date = $prodDate;
         for ($j=0;$j<$quantity;$j++){
             $laRet[$i++] = $row;
         }
     }
     return $laRet;
  }
  public function getPrintData($flags, $print_qty, $pDocId) {
        if (!(isset($flags) && is_array($flags))) {
            throw new Exception("Нет данных для печати");
        }
        $labels = array();
        foreach ($flags as $key => $value) {
            $params = explode('|', $value);
            $line_id = $params[0];
            $qty = isset($print_qty[$key]) && $print_qty[$key] > 0 ? $print_qty[$key] : $params[1];
            if (isset($pDocId)) {
                $aLabels = $this->getDocLabels($pDocId, $line_id, $qty);
            } else {
                $aLabels = $this->getLabels($line_id, $qty);
            }
            if ($aLabels) {
                $labels = array_merge($labels, $aLabels);
            }
        }
        return $labels;
  }

    public function getPrintDataOld($flags,$print_qty,$pDocId){
      global $_SESSION;
      if (isset($flags)&&  is_array($flags)){
          $labels = array();
          foreach ($flags as $key => $value) {
              $params = explode('|', $value);
              if(isset($print_qty[$key])&&$print_qty[$key]>0) $params[2]=$print_qty[$key];
              if (isset($pDocId)){
                  $aLabels=$this->getDocLabels($pDocId, $params[3], $params[2]);
              } else{
                  $aLabels=$this->getLabels($params[0],$params[1],$params[2]);
              }
              if ($aLabels){
                $labels = array_merge($labels, $aLabels);
              }
          }
          return array('labels' =>$labels);
      }
      return array(
        'labels' =>(isset($_SESSION["barcodes"])?$_SESSION["barcodes"]:null)
      );      
  }
}
//------------------------------------------------------------------------------
//-- Old implementation, remove after release
//------------------------------------------------------------------------------
class Model_Labels extends Model
{   public $totalQty;
    public $totalAmount;
    public $totalAmountMin;
    function getDocuments(){
     global $sql;
     $laRet = array();
     $lsStatment = "select d.*,t.name type_name,s.name status_name from wrh_documents d join doc_type t using (doctp_id) join doc_status s on d.status = s.status_id order by doc_date desc";
     if($sql->query($lsStatment)){
     $i=0;
     while ($row = $sql->fetchObject()) {
        $laRet[$i++] = $row;
     }
     } else echo $sql->error();
     return $laRet;
    }
    function getDocumentItems($pDocId){
     global $sql;
     $laRet = array();
     $lsStatment = sprintf("SELECT n.nmcl_id,n.alt_code, n.nmcl_name,sex,thing,thing_type
          ,c.ctg_id size_id,lpad(c.ctg_id,4,'0') size_code, c.short_name size_name,c.short_name size, c.ext_name age
          ,lpad(cl.ctg_id,4,'0') color_code, cl.ctg_name color_name
          ,di.quantity qty
          ,di.barcode label_code
          ,n.foto_name,di.price,di.price2 price_min,di.status,replace(replace(n.description,'<br>',' '),'<br/>',' ') description ,n.foto_name,replace(replace(n.foto_name,concat(n.nmcl_id,'_'),''),'|',', ') foto_filename, n.wght,comp_id,ifnull(composition,'100%s хлопок') as composition, di.quantity*di.price amount,di.quantity*di.price2 amount_min
   FROM wrh_doc_items di 
   JOIN nomenclatures n on di.nmcl_id = n.nmcl_id
   LEFT JOIN categories c ON (di.ctg_id=c.ctg_id) 
   LEFT JOIN categories cl ON di.ctg2_id = cl.ctg_id 
   LEFT JOIN (select nmcl_id, replace(replace(replace(replace(group_concat(c.short_name),'Мужской,Женский','Унисекс'),'Женский,Мужской','Унисекс'),'Мальчики,Девочки','Детский'),'Девочки,Мальчики','Детский') as sex from nmcl_categories join categories c using(ctg_id) where ctg_type_id=11 and c.code in('BOY','GIRL','MAN','WOMAN')  group by nmcl_id ) sx
        ON n.nmcl_id = sx.nmcl_id
   LEFT JOIN (select nmcl_id, group_concat(c.code) as thing,group_concat(ifnull(c.short_name,c.ctg_name)) thing_type from nmcl_categories join categories c using(ctg_id) where ctg_type_id=14 group by nmcl_id ) wad
        ON n.nmcl_id = wad.nmcl_id
   LEFT JOIN (select nmcl_id, min(ctg_id) as comp_id,group_concat(ctg_name) as composition from nmcl_categories join categories using(ctg_id) where ctg_type_id=20 group by nmcl_id ) comp
        ON n.nmcl_id = comp.nmcl_id
   where di.doc_id = %d
   order by n.alt_code, cl.ctg_name, cast(c.ctg_name as UNSIGNED),c.ctg_name"
             ,"%", $sql->escape_string($pDocId));
     if($sql->query($lsStatment)){
     $i=0;
     $this->totalQty = 0;
     $this->totalAmount = 0;
     $this->totalAmountMin = 0;
     while ($row = $sql->fetchObject()) {
        if (strlen($row->foto_name)>0){
            $laItemImages = explode('|',$row->foto_name);
        }
        else{
            $laItemImages[0] = 'no_image.gif';        
        }
        $row->foto_name = $laItemImages[0];
        $row->description = strip_tags($row->description);
        $this->totalQty += $row->qty;
        $this->totalAmount += $row->amount;
        $this->totalAmountMin += $row->amount_min;
        $laRet[$i++] = $row;
     }
     } else echo $sql->error();
     return $laRet;
    }
    function getTtDocumentItems(){
     global $sql;
     $laRet = array();
     $lsStatment = "SELECT n.nmcl_id,n.alt_code, n.nmcl_name,sex,thing,thing_type
          ,c.ctg_id size_id,lpad(c.ctg_id,4,'0') size_code, c.short_name size_name, size, c.ext_name age
          ,lpad(ifnull(clr_id,color_id),4,'0') color_code, case when clr_id is null then color_name else clr.ctg_name end color_name
          ,qty,qty+ifnull(qty_add,0) qty_tot, qty_add
          ,concat(lpad(n.alt_code,4,'0'),lpad(ifnull(clr_id,color_id),4,'0'),lpad(c.ctg_id,4,'0')) label_code
          ,n.foto_name,n.price,n.price_min,n.price_mid,load_id,0 status, replace(replace(n.description,'<br>',' '),'<br/>',' ') description, n.foto_name,replace(replace(n.foto_name,concat(n.nmcl_id,'_'),''),'|',', ') foto_filename,n.wght,comp_id,ifnull(composition,'100% хлопок') as composition, qty*n.price amount,qty*n.price_min amount_min
   FROM tt_wd 
   JOIN nomenclatures n on code = n.alt_code
   LEFT JOIN categories c ON (size_id=c.ctg_id) 
   LEFT JOIN (select nmcl_id, min(ctg_id) as color_id,group_concat(ctg_name) as color_name from nmcl_categories join categories using(ctg_id) where ctg_type_id=3 group by nmcl_id ) cl
        ON n.nmcl_id = cl.nmcl_id
   LEFT JOIN (select nmcl_id, min(ctg_id) as comp_id,group_concat(ctg_name) as composition from nmcl_categories join categories using(ctg_id) where ctg_type_id=20 group by nmcl_id ) comp
        ON n.nmcl_id = comp.nmcl_id
   left join categories clr ON clr.ctg_id = clr_id
      LEFT JOIN (select nmcl_id, replace(replace(replace(replace(group_concat(c.short_name),'Мужской,Женский','Унисекс'),'Женский,Мужской','Унисекс'),'Мальчики,Девочки','Детский'),'Девочки,Мальчики','Детский') as sex from nmcl_categories join categories c using(ctg_id) where ctg_type_id=11 and c.code in('BOY','GIRL','MAN','WOMAN')  group by nmcl_id ) sx
        ON n.nmcl_id = sx.nmcl_id
   LEFT JOIN (select nmcl_id, group_concat(c.code) as thing,group_concat(ifnull(c.short_name,c.ctg_name)) thing_type from nmcl_categories join categories c using(ctg_id) where ctg_type_id=14 group by nmcl_id ) wad
        ON n.nmcl_id = wad.nmcl_id
        order by load_id,n.alt_code, case when clr_id is null then color_name else clr.ctg_name end, cast(c.ctg_name as UNSIGNED),c.ctg_name";
     if($sql->query($lsStatment)){
     $i=0;
     $this->totalQty = 0;
     $this->totalAmount = 0;
     $this->totalAmountMin = 0;
     while ($row = $sql->fetchObject()) {
        if (strlen($row->foto_name)>0){
            $laItemImages = explode('|',$row->foto_name);
        }
        else{
            $laItemImages[0] = 'no_image.gif';        
        }
        $row->foto_name = $laItemImages[0];
        $row->description = strip_tags($row->description);
        $this->totalQty += $row->qty;
        $this->totalAmount += $row->amount;
        $this->totalAmountMin += $row->amount_min;
        $laRet[$i++] = $row;
     }
     } else echo $sql->error();
     return $laRet;
    }      
function getPivotedItems($pDocId){
     global $sql;
     $laRet = array();
     $lsStatment = sprintf("select n.nmcl_id,n.alt_code, n.nmcl_name,
     lpad(cl.ctg_id,4,'0') color_code,  cl.ctg_name color_name,
     n.foto_name,di.price,di.price2 price_min,di.price2 price_mid,
 sum(quantity) qty,
 sum(case when c.code not in('40','42','44','46','48','50','52','54','56','58') and c.ctg_type_id=9 then quantity else null end) as q_others,
 sum(case when c.code='40' and c.ctg_type_id=9 then quantity else null end) as q40,
 sum(case when c.code='42' and c.ctg_type_id=9 then quantity else null end) as q42,
 sum(case when c.code='44' and c.ctg_type_id=9 then quantity else null end) as q44,
 sum(case when c.code='46' and c.ctg_type_id=9 then quantity else null end) as q46,
 sum(case when c.code='48' and c.ctg_type_id=9 then quantity else null end) as q48,
 sum(case when c.code='50' and c.ctg_type_id=9 then quantity else null end) as q50,
 sum(case when c.code='52' and c.ctg_type_id=9 then quantity else null end) as q52,
 sum(case when c.code='54' and c.ctg_type_id=9 then quantity else null end) as q54,
 sum(case when c.code='56' and c.ctg_type_id=9 then quantity else null end) as q56,
 sum(case when c.code='58' and c.ctg_type_id=9 then quantity else null end) as q58,
 sum(case when c.code='68' and c.ctg_type_id=12 then quantity else null end) as q68,
 sum(case when c.code='74' and c.ctg_type_id=12 then quantity else null end) as q74,
 sum(case when c.code='80' and c.ctg_type_id=12 then quantity else null end) as q80,
 sum(case when c.code='86' and c.ctg_type_id=12 then quantity else null end) as q86,
 sum(case when c.code='92' and c.ctg_type_id=12 then quantity else null end) as q92,
 sum(case when c.code='98' and c.ctg_type_id=12 then quantity else null end) as q98,
 sum(case when c.code='104' and c.ctg_type_id=12 then quantity else null end) as q104,
 sum(case when c.code='110' and c.ctg_type_id=12 then quantity else null end) as q110,
 sum(case when c.code='116' and c.ctg_type_id=12 then quantity else null end) as q116,
 sum(case when c.code='122' and c.ctg_type_id=12 then quantity else null end) as q122,
 sum(case when c.code='128' and c.ctg_type_id=12 then quantity else null end) as q128,
 sum(case when c.code='134' and c.ctg_type_id=12 then quantity else null end) as q134,
 sum(case when c.code='140' and c.ctg_type_id=12 then quantity else null end) as q140,
 sum(case when c.code='146' and c.ctg_type_id=12 then quantity else null end) as q146,
 sum(case when c.code='152' and c.ctg_type_id=12 then quantity else null end) as q152
 FROM wrh_doc_items di 
   JOIN nomenclatures n on (di.nmcl_id = n.nmcl_id)
   LEFT JOIN categories c ON (di.ctg_id=c.ctg_id) 
   LEFT JOIN categories cl ON di.ctg2_id = cl.ctg_id
where di.doc_id = %d   
 group by n.nmcl_id
        order by n.alt_code", $sql->escape_string($pDocId));
		
		
		
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
    function getTtPivotedItems(){
     global $sql;
     $laRet = array();
     $lsStatment = "select n.nmcl_id,n.alt_code, n.nmcl_name,
     lpad(ifnull(clr_id,color_id),4,'0') color_code,  case when clr_id is null then color_name else clr.ctg_name end color_name,
     n.foto_name,n.price,n.price_min,n.price_mid,
 sum(qty) qty,
 sum(case when c.code not in('40','42','44','46','48','50','52','54','56','58') and c.ctg_type_id=9 then qty else null end) as q_others,
 sum(case when c.code='40' and c.ctg_type_id=9 then qty else null end) as q40,
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
 sum(case when c.code='140' and c.ctg_type_id=12 then qty else null end) as q140,
 sum(case when c.code='146' and c.ctg_type_id=12 then qty else null end) as q146,
 sum(case when c.code='152' and c.ctg_type_id=12 then qty else null end) as q152
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
	 SELECT n.nmcl_id,c.ctg_id size_id, n.nmcl_name, n.description,thing,sex,ifnull(composition,'100%s хлопок') as composition,
          c.short_name size_name, c.ext_name age,
	        concat(lpad(n.alt_code,4,'0'),lpad(ifnull(clr.ctg_id,color_id),4,'0'),lpad(c.ctg_id,4,'0')) label_code,case when clr.ctg_id is null then color_name else clr.ctg_name end color_name,n.alt_code
	   FROM tt_wd wb 
     JOIN nomenclatures n ON n.alt_code = wb.code
     LEFT JOIN categories c ON (wb.size_id=c.ctg_id)
     LEFT JOIN categories clr ON wb.clr_id = clr.ctg_id 
     LEFT JOIN (select nmcl_id, min(ctg_id) as color_id,group_concat(ctg_name) as color_name from nmcl_categories join categories using(ctg_id) where ctg_type_id=3 group by nmcl_id ) cl
            ON n.nmcl_id = cl.nmcl_id     
     LEFT JOIN (select nmcl_id, min(ctg_id) as comp_id,group_concat(ctg_name) as composition from nmcl_categories join categories using(ctg_id) where ctg_type_id=20 group by nmcl_id ) comp
            ON n.nmcl_id = comp.nmcl_id
     LEFT JOIN (select nmcl_id, replace(replace(replace(replace(group_concat(c.short_name),'Мужской,Женский','Унисекс'),'Женский,Мужской','Унисекс'),'Мальчики,Девочки','Детский'),'Девочки,Мальчики','Детский') as sex from nmcl_categories join categories c using(ctg_id) where ctg_type_id=11 and c.code in('BOY','GIRL','MAN','WOMAN')  group by nmcl_id ) sx
            ON n.nmcl_id = sx.nmcl_id
     LEFT JOIN (select nmcl_id, group_concat(c.code) as thing,group_concat(ifnull(c.short_name,c.ctg_name)) thing_type from nmcl_categories join categories c using(ctg_id) where ctg_type_id=14 group by nmcl_id ) wad
            ON n.nmcl_id = wad.nmcl_id     
     WHERE wb.size_id = %d and wb.code =  '%s'
	 ",'%',$sql->escape_string($size),$sql->escape_string($alt_code));

     if($sql->query($lsStatment)){
     $i=0;
     while ($row = $sql->fetchObject()) {
		for ($j=0;$j<$quantity;$j++)
        $laRet[$i++] = $row;
     }
	 } else echo $sql->error();
     return $laRet;

  }
    public function getDocLabels($pDocId,$pBarcode,$quantity)
  {
     // Returns an array of all item in the order

     global $sql;
     $laRet = array();
	 
	 $lsStatment = sprintf("
   SELECT n.nmcl_id,n.nmcl_name,n.alt_code,n.description,thing,sex,ifnull(composition,'100%s хлопок') as composition
          ,c.ctg_id size_id,c.short_name size_name,c.ext_name age
          ,di.barcode label_code,cl.ctg_name color_name
      FROM wrh_doc_items di
      JOIN nomenclatures n on di.nmcl_id=n.nmcl_id
      LEFT JOIN categories c ON (di.ctg_id=c.ctg_id) 
      LEFT JOIN categories cl ON di.ctg2_id = cl.ctg_id 
      LEFT JOIN (select nmcl_id, min(ctg_id) as comp_id,group_concat(ctg_name) as composition from nmcl_categories join categories using(ctg_id) where ctg_type_id=20 group by nmcl_id ) comp
            ON n.nmcl_id = comp.nmcl_id
      LEFT JOIN (select nmcl_id, replace(replace(replace(replace(group_concat(c.short_name),'Мужской,Женский','Унисекс'),'Женский,Мужской','Унисекс'),'Мальчики,Девочки','Детский'),'Девочки,Мальчики','Детский') as sex from nmcl_categories join categories c using(ctg_id) where ctg_type_id=11 and c.code in('BOY','GIRL','MAN','WOMAN')  group by nmcl_id ) sx
             ON n.nmcl_id = sx.nmcl_id
      LEFT JOIN (select nmcl_id, group_concat(c.code) as thing,group_concat(ifnull(c.short_name,c.ctg_name)) thing_type from nmcl_categories join categories c using(ctg_id) where ctg_type_id=14 group by nmcl_id ) wad
            ON n.nmcl_id = wad.nmcl_id     
         
     WHERE di.doc_id = %d and di.barcode = '%s'
	 ",'%',$sql->escape_string($pDocId),$sql->escape_string($pBarcode));

     if($sql->query($lsStatment)){
     $i=0;
     while ($row = $sql->fetchObject()) {
		for ($j=0;$j<$quantity;$j++)
        $laRet[$i++] = $row;
     }
	 } else echo $sql->error();
     return $laRet;

  }  
  public function get_data($pDocId = null, $pivot=false)
  {
   global $_SESSION;
   $PRINTLABELS = false;
   $CATEGORY = getRefElements('wrh_size','SIZE');
   $action = (isset($_POST["action"])?$_POST["action"]:null);
   $labelList = array();
   $DEF_ID = 0;
   $alt_code = '';
   if (isset($action)){
     switch ($action){
       case 'ADD_ARTICUL':
         $quantity=$_POST["filter_quantity"];
         $size=$_POST["filter_size"];
         $alt_code=$_POST["filter_alt_code"];
         $label = array(
             "quantity"=>$quantity,
             "size"=>$size,
             "alt_code"=>$alt_code
             );
         if(isset($_SESSION["label_list"]))
             $labelList = $_SESSION["label_list"];
         $labelList[] = $label;
         $_SESSION["label_list"] = $labelList;
         $aLabels=$this->getLabels($alt_code,$size,$quantity);
         $labels = array();
         if (isset($_SESSION["barcodes"]))
             $labels = $_SESSION["barcodes"];
         $labels = array_merge($labels, $aLabels);
         $_SESSION["barcodes"] = $labels;
         break;
     case 'PRINT_LABELS':
         $PRINTLABELS = true;
         $labels = $_SESSION["barcodes"];
         break;
     default : break;
     }
  }
  else{
      unset($_SESSION["label_list"]);
      unset($_SESSION["barcodes"]);
  }
  if (isset($pDocId)) {
      $documentItems = ($pivot?$this->getPivotedItems($pDocId):$this->getDocumentItems($pDocId));
  }
  else{
      $documentItems = ($pivot?$this->getTtPivotedItems():$this->getTtDocumentItems());
  }
  return array(
        'PRINTLABELS' => $PRINTLABELS,
        'CATEGORY' =>  $CATEGORY,
        'DEF_ID' =>  $DEF_ID,
        'labelList' => $labelList,
       'val_alt_code' => $alt_code,
        'totalQty' => $this->totalQty,
        'totalAmount' => $this->totalAmount,
        'totalAmountMin' => $this->totalAmountMin,
        'documentItems'=> $documentItems,
      'supplyDocList'=>$this->getDocuments()
    );
  }
  public function getPrintData($flags,$print_qty,$pDocId){
      global $_SESSION;
      if (isset($flags)&&  is_array($flags)){
          $labels = array();
          foreach ($flags as $key => $value) {
              $params = explode('|', $value);
              if(isset($print_qty[$key])&&$print_qty[$key]>0) $params[2]=$print_qty[$key];
              if (isset($pDocId)){
                  $aLabels=$this->getDocLabels($pDocId, $params[3], $params[2]);
              } else{
                  $aLabels=$this->getLabels($params[0],$params[1],$params[2]);
              }
              if ($aLabels){
                $labels = array_merge($labels, $aLabels);
              }
          }
          return array('labels' =>$labels);
      }
      return array(
        'labels' =>(isset($_SESSION["barcodes"])?$_SESSION["barcodes"]:null)
      );      
  }
}

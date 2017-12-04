<?php

class ModelDocuments extends Model
{   public $totalQty;
    public $totalAmount;
    public $totalAmountMin;
    function getDocuments(){
        $lsStatment = "select d.*,t.name type_name,s.name status_name from wrh_documents d join doc_type t using (doctp_id) join doc_status s on d.status = s.status_id order by doc_date desc";
        $query = $this->db->query($lsStatment);
        return $query->rows;
    }
    function getCategories($ctgType) {
        // Returns an array of all item in the order

        $lsStatment = sprintf(
                "SELECT c.ctg_id as id,c.code,c.short_name,c.ctg_name as name, ct.alt_code " .
                "  FROM category_types ct, categories c " .
                " WHERE ct.alt_code = '%s' and c.ctg_type_id=ct.ctg_type_id and c.status=1".
                " ORDER BY cast(c.ctg_name as UNSIGNED),c.ctg_name"
                , $this->db->escape($ctgType)
        );
        $query = $this->db->query($lsStatment);
        return $query->rows;
    }
    function getCategory($ctgId) {
        // Returns an array of all item in the order

        $lsStatment = sprintf(
                "SELECT c.ctg_id as id,c.code,c.short_name,c.ctg_name as name, ct.alt_code " .
                "  FROM category_types ct, categories c " .
                " WHERE c.ctg_id = %d and c.ctg_type_id=ct.ctg_type_id and c.status=1".
                " ORDER BY cast(c.ctg_name as UNSIGNED),c.ctg_name"
                , $this->db->escape($ctgId)
        );
        $query = $this->db->query($lsStatment);
        if($query->num_rows<1){
            throw new NoDataFoundException("Category not found [$ctgId]");
        }
        return $query->row;
    }
    function getDocumentItems($pDocId){
     $lsStatment = sprintf("SELECT di.line_id, n.nmcl_id,n.alt_code, n.nmcl_name,sex,thing,thing_type
          ,c.ctg_id size_id,lpad(c.ctg_id,4,'0') size_code, c.short_name size_name,c.short_name size, c.ext_name age
          ,lpad(cl.ctg_id,4,'0') color_code, cl.ctg_name color_name
          ,di.quantity qty,di.ctg2_id color_id
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
             ,"%", $this->db->escape($pDocId));
        $query = $this->db->query($lsStatment,$mode='object');
     $i=0;
     $this->totalQty = 0;
     $this->totalAmount = 0;
     $this->totalAmountMin = 0;
     foreach($query->rows as $i=>$row) {
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
        $query->rows[$i] = $row;
     }
     return $query->rows;
    }
    function getDraftItems(){
     //global $sql;
     //$laRet = array();
        $lsStatment = "SELECT id as line_id, n.nmcl_id,n.alt_code, n.nmcl_name,sex,thing,thing_type
          ,c.ctg_id size_id,lpad(c.ctg_id,4,'0') size_code, c.short_name size_name, size, c.ext_name age
          ,lpad(ifnull(clr_id,color_id),4,'0') color_code, case when clr_id is null then color_name else clr.ctg_name end color_name
          ,qty,qty+ifnull(qty_add,0) qty_tot, qty_add,ifnull(clr_id,color_id) color_id
          ,ifnull(draft.barcode,concat(lpad(n.alt_code,4,'0'),lpad(ifnull(clr_id,color_id),4,'0'),lpad(c.ctg_id,4,'0'))) label_code
          ,n.foto_name,ifnull(draft.price,n.price) price
          ,ifnull(draft.price_min,n.price_min) price_min
          ,n.price_mid,load_id,0 status, replace(replace(n.description,'<br>',' '),'<br/>',' ') description, n.foto_name,replace(replace(n.foto_name,concat(n.nmcl_id,'_'),''),'|',', ') foto_filename,n.wght,comp_id,ifnull(composition,'100% хлопок') as composition, qty*ifnull(draft.price,n.price) amount,qty*ifnull(draft.price_min,n.price_min) amount_min
   FROM tt_wd draft
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
        $query = $this->db->query($lsStatment,$mode='object');
 
     $i=0;
     $this->totalQty = 0;
     $this->totalAmount = 0;
     $this->totalAmountMin = 0;
     foreach($query->rows as $i=>$row) {
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
        $query->rows[$i] = $row;
     }
     return $query->rows;
    }

    function getDraftItem($line_id) {
        $lsStatment = sprintf("SELECT id as line_id, code as alt_code, size_id as size_id, "
                . "clr_id as color_id, qty as qty, price_min as price_min, "
                . "price as price, barcode as label_code"
                . " FROM tt_wd WHERE id=%d", $this->db->escape($line_id));
        $query = $this->db->query($lsStatment);
        if ($query->num_rows < 1) {
            throw new NoDataFoundException("Item not found", $line_id);
        }
        return $query->row;
    }
    function getDraftItemDetailed($line_id){
     //global $sql;
     //$laRet = array();
        $lsStatment = "SELECT id as line_id, n.nmcl_id,n.alt_code, n.nmcl_name,sex,thing,thing_type
          ,c.ctg_id size_id,lpad(c.ctg_id,4,'0') size_code, c.short_name size_name, size, c.ext_name age
          ,lpad(ifnull(clr_id,color_id),4,'0') color_code, case when clr_id is null then color_name else clr.ctg_name end color_name
          ,qty,qty+ifnull(qty_add,0) qty_tot, qty_add,ifnull(clr_id,color_id) color_id
          ,ifnull(draft.barcode,concat(lpad(n.alt_code,4,'0'),lpad(ifnull(clr_id,color_id),4,'0'),lpad(c.ctg_id,4,'0'))) label_code
          ,n.foto_name,ifnull(draft.price,n.price) price
          ,ifnull(draft.price_min,n.price_min) price_min
          ,n.price_mid,load_id,0 status, replace(replace(n.description,'<br>',' '),'<br/>',' ') description, n.foto_name,replace(replace(n.foto_name,concat(n.nmcl_id,'_'),''),'|',', ') foto_filename,n.wght,comp_id,ifnull(composition,'100% хлопок') as composition, qty*ifnull(draft.price,n.price) amount,qty*ifnull(draft.price_min,n.price_min) amount_min
   FROM tt_wd draft
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
        WHERE draft.id=".$this->db->escape($line_id);
        $query = $this->db->query($lsStatment);
 
        if (strlen($query->row['foto_name'])>0){
            $laItemImages = explode('|',$query->row['foto_name']);
        }
        else{
            $laItemImages[0] = 'no_image.gif';        
        }
        $query->row['foto_name'] = $laItemImages[0];
        $query->row['description'] = strip_tags($query->row['description']);

     return $query->row;
    }
    function saveDraftItem(&$details){
      $res = true;
      $sizeInfo = $this->getCategory($details['size_id']);
      if ($details){
          if ($details["line_id"] != null) {
            $stmt = sprintf("UPDATE tt_wd set ".
            "code = %s".
            ",size_id = %s".
            ",size = %s".
            ",clr_id = %s".
            ",qty = %s".
            ",price_min = %s".
            ",price = %s".
            ",barcode= %s".
            " WHERE id = %f",
            $this->formatStringValue($details['alt_code']),
            $this->formatStringValue($details['size_id']),
            $this->formatStringValue($sizeInfo['short_name']),
            $this->formatStringValue($details['color_id']),
            $this->formatStringValue($details['qty']),
            $this->formatStringValue($details['price_min']),
            $this->formatStringValue($details['price']),
            $this->formatStringValue($details['label_code']),         
            $this->db->escape($details['line_id'])
            );            
          } else {
            $stmt = sprintf(
                 "INSERT INTO tt_wd (code,size_id,size,clr_id,qty,price_min,price,barcode) "
                 ."VALUES (%s,%s,%s,%s,%s,%s,%s,%s)",
                    $this->formatStringValue($details['alt_code']),
                    $this->formatStringValue($details['size_id']),
                    $this->formatStringValue($sizeInfo['short_name']),
                    $this->formatStringValue($details['color_id']),
                    $this->formatStringValue($details['qty']),
                    $this->formatStringValue($details['price_min']),
                    $this->formatStringValue($details['price']),
                    $this->formatStringValue($details['label_code'])
                 );
          }
          $query = $this->db->query($stmt);
          if ($details["line_id"] == null){
             $details["line_id"] = $this->db->getLastId();
          }
          if ($this->db->countAffected()>0){
             $details["message"] = "Success";
          } else {
             $details["message"] = "Document item is not modified (line_id:[{$details['line_id']}])";
          }
      } else {
          throw new Exception("Invalid parameters");
      } 
     return $res;
  }
  function createFromDraft(&$document) {
        $stmt = sprintf(
                "INSERT INTO wrh_documents (doctp_id,doc_number,doc_date) "
                . "VALUES (1,%s,%s)", $this->formatStringValue($document['doc_number']), $this->formatStringValue($document['doc_date'])
        );
        $this->db->query($stmt);
        $document['doc_id']=$this->db->getLastId();
        $stmt = sprintf("insert into wrh_doc_items(doc_id,nmcl_id,ctg_id,ctg2_id,quantity,price,price2,barcode)
SELECT       
 %s as doc_id 	,
n.nmcl_id nmcl_id ,
c.ctg_id ctg_id 	,
ifnull(clr_id,color_id) ctg2_id ,
qty+ifnull(qty_add,0) quantity,
ifnull(draft.price,n.price) price 	,
ifnull(draft.price_min,n.price_min) price2 	,
ifnull(barcode,concat(lpad(n.alt_code,4,'0'),lpad(ifnull(clr_id,color_id),4,'0'),lpad(c.ctg_id,4,'0')))  barcode
   FROM tt_wd draft
   JOIN nomenclatures n on code = n.alt_code
   LEFT JOIN categories c ON (size_id=c.ctg_id) 
   LEFT JOIN (select nmcl_id, min(ctg_id) as color_id,group_concat(ctg_name) as color_name from nmcl_categories join categories using(ctg_id) where ctg_type_id=3 group by nmcl_id ) cl
        ON n.nmcl_id = cl.nmcl_id
        where draft.qty>0
        order by n.alt_code, color_name, cast(c.ctg_name as UNSIGNED),c.ctg_name",
                $document['doc_id']);
        try{
            $this->db->query($stmt);
        }
        catch (SqlException $ex){
            //rollback insert
            $this->db->query("delete from wrh_documents where doc_id = ".$document['doc_id']);
            throw $ex;
        }
        return true;
    }
    function clearDraft() {
        //make backup
        $this->db->query('truncate table doc_draft_backup');
        $this->db->query('insert into doc_draft_backup select * from tt_wd');
        //clear        
        $stmt = "DELETE FROM tt_wd";
        $this->db->query($stmt);
    }
    function restoreDraft() {
        $this->db->query('insert into tt_wd select * from doc_draft_backup');
    }
    function deleteDocument($docId) {
        if (!$docId) throw new Exception("Не выбран документ");
        $this->db->query(sprintf('delete from wrh_doc_items where doc_id = %s',$this->db->escape($docId)));
        $this->db->query(sprintf('delete from wrh_documents where doc_id = %s',$this->db->escape($docId)));
    }
}

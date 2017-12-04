<?php

class Model_WbOrder extends Model
{
  function addOrderItem($articul,$sizeId,$size,$qty){
     global $sql;
     $lsStatment = sprintf(
        "INSERT INTO tt_wd (code,size_id,size ,qty)".
        " VALUES( '%s',%d,'%s',%d)",
				$sql->escape_string($articul),
				$sql->escape_string($sizeId),
				$sql->escape_string($size),
				$sql->escape_string($qty));
    if(!$sql->query($lsStatment)){
      $this->err_message = "failed create nomenclature(db error: ".$sql->error().")";
	  return $sql->error();
    }
	return 'OK';
  }
  function checkArticul($articul){
     global $sql;
     $laRet = array();
     $lsStatment = sprintf("select nmcl_id from nomenclatures where alt_code = '%s'",$sql->escape_string($articul));
     if($sql->query($lsStatment)){
       $i=0;
	   $ids='';
       while ($row = $sql->fetchObject()) {
          $i++;
		  $ids=$ids . $row->nmcl_id . ',';
       }
	   $s='OK';
	   if ($i>1)$s = "Найдено более одного артикула [$ids]";
	   if ($i<1)$s = "Артикул не найден [$articul]";
	   return $s;
     } else return $sql->error();
    }
  public function get_data($articul,$sizeId,$size,$qty)
  { 
     $s='OK';
	 if (!isset($articul)||strlen($articul)<1||!isset($sizeId)||strlen($sizeId)<1||!isset($qty)||strlen($qty)<1)
	 {
		$s="Укажите артикул, id размера, количество";
	 }
	 $articul=str_pad($articul,4,'0',STR_PAD_LEFT);
	 if ($s=='OK') $s = $this->checkArticul($articul);
	 if ($s=='OK') $s = $this->addOrderItem($articul,$sizeId,$size,$qty);
     return array(
        'success' => $s=='OK',
        'message' => $s,
    );
  }  
}

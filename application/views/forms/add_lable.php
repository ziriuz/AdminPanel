 <?php require(BLOCKS_DIR."/item_search.php");?>
 <form method=post >
     <input type ="hidden" name="action" value="ADD_ARTICUL">
  <label for=filter_alt_code>Артикул</label><input id="filter_alt_code" type=text name="filter_alt_code" value="<?=$val_alt_code?>"/>
  <label for=filter_quantity>Количество</label><input id="filter_quantity" type=text name="filter_quantity"/>
  <label for=filter_size>Размер</label>
  <select name="filter_size">	  
   <option value=''> </option>
   <?php foreach($sizeList as $i=>$item):?>
   <option value=<?=$item['id']?>><?=$item['name']?></option>
   <?php endforeach;?>
  </select>
  <button type=submit value="add" class="btn  btn-sm">Add</button>
 </form>
 <form method=post action="labels/print" target="_blank">
  <input type=hidden name="filter_alt_code[]"/>
  <input type=hidden name="filter_quantity[]"/>
  <input type=hidden name="filter_size[]"/>
  <?php foreach($labelList as $i=>$label1):?>
  <span class="label label-primary"><?=$label1["alt_code"]?> (<?=$label1["size"]?>)<span class="badge"><?=$label1["quantity"]?></span></span>
  <?php endforeach;?>
  <button type=submit value="print" class="btn btn-sm navbar-btn" >Print</button>
  </form>
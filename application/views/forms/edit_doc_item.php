<?php
$entity='doc_item';
$windowId="edit_${entity}_dialog";
$formId="edit_${entity}_form";
$fields=array(
'line_id'=>array('label'=>'Код строки','list'=>null),
'alt_code'=>array('label'=>'Артикул','list'=>null),
'size_id'=>array('label'=>'Размер','list'=>&$sizeList),
'color_id'=>array('label'=>'Цвет','list'=>&$colorList),
'qty'=>array('label'=>'Количество','list'=>null),
'price_min'=>array('label'=>'Цена опт.','list'=>null),
'price'=>array('label'=>'Цена','list'=>null),
'label_code'=>array('label'=>'Штрихкод','list'=>null)
);
$fieldsDetailed=array(
 "alt_code" => "alt_code",
 "nmcl_id" => "nmcl_id",
 "size" => "size",
 "color_name" => "color_name",
 "thing_type" => "thing_type",
 "sex" => "sex",
 "qty" => "qty",
 "price" => "price",
 "price_min" => "price_min",
 "nmcl_name" => "nmcl_name",
 "thing" => "thing",
 "label_code" => "label_code",
 "color_code" => "color_code",
 "amount" => "amount",
 "amount_min" => "amount_min"
);

$idField = 'line_id';
?>
<!-- dialogue window -->
<div id="mask" class="mask mask_hide"></div>
<div class="dialog_window mask_hide" id="<?=$windowId?>">
<div class="form-container">
<a class="closeLink"  id="close_dialog"><span class="visuallyhidden">Close Notification</span></a>
<form method="post" id="<?=$formId?>">
<input type="hidden" name="action" value="save">
<h2>Изменение строки документа</h2>
<div><em id="ajaxmessage_box" style="color:red;font-size:1.6em;line-height:16px"></em></div>
<?php foreach($fields as $key=>$field):?>
<div class="input-group" style="margin-top: 10px">
 <?php if($key==$idField):?>
  <input type="hidden" id="<?=$entity.'_'.$key?>" name="<?=$entity.'_'.$key?>">
 <?php elseif($field['list']!==null):?>
  <span class="input-group-addon" style="min-width:100px" id="addon_<?=$key?>"><?=$field['label']?></span>
  <select class="form-control" id="<?=$entity.'_'.$key?>" name="<?=$entity.'_'.$key?>" aria-describedby="addon_<?=$key?>" >	  
   <option value=''> </option>
   <?php foreach($field['list'] as $i=>$item):?>
   <option value=<?=$item['id']?>><?=$item['name']?></option>
   <?php endforeach;?>
  </select>
 <?php else:?>
  <span class="input-group-addon" style="min-width:100px" id="addon_<?=$key?>"><?=$field['label']?></span>
  <input type="text" class="form-control" id="<?=$entity.'_'.$key?>" name="<?=$entity.'_'.$key?>" aria-describedby="addon_<?=$key?>" placeholder="">
 <?php endif;?>
</div>
<?php endforeach;?>

<div class="submit-container">
<div class="form-info">
<button id="submit_<?=$formId?>" class="btn btn-primary btn-sm" type="submit" value="save_<?=$entity?>" name="submit_<?=$formId?>">Сохранить</button>
<button id="close_<?=$formId?>" class="btn btn-default btn-sm" value="close_<?=$entity?>" name="close_<?=$formId?>">Отмена</button>
</div>
</div>
</form>
</div>
</div>
<script language="JavaScript">
 document.getElementById('close_dialog').onclick=function(){close_dialog("<?=$windowId?>");return false;};
 document.getElementById('close_<?=$formId?>').onclick=function(){close_dialog("<?=$windowId?>");return false;};
 document.getElementById('submit_<?=$formId?>').onclick=function(){
   //var fldName = document.getElementById("name_field");
   //var fldContact = document.getElementById("cotact_field");
   var err = false;
   //if (fldName.value.length==0){ addClass(fldName,"form-field-req");err=true;}
   //else removeClass(fldName,"form-field-req")
   //if (fldContact.value.length==0){ addClass(fldContact,"form-field-req");err=true;}
   //else removeClass(fldContact,"form-field-req")
   if (!err)
   postForm(
     {
      token:"save_<?=$entity?>",
      formId:"<?=$formId?>",
      tipsBoxId:"ajaxmessage_box",
      onSuccess:function(result){
          close_dialog("<?=$windowId?>");
          var itemId = result.<?=$idField?>;
          if(result.action=="create"){ 
              addRow("<?=$entity?>",itemId);
          }
          <?php foreach($fields as $key=>$field):?>
              setElementValue("view_<?=$key?>_"+itemId,getElementValue("<?=$entity.'_'.$key?>"));
          <?php endforeach;?>
          <?php foreach($fieldsDetailed as $key=>$field):?>
              setElementText("<?=$entity.'_'.$key?>_"+itemId,result.<?=$key?>);
          <?php endforeach;?>
          // Customization
          setElementValue("chk_"+itemId,itemId+'|'+result.qty);
          setElementText("<?=$entity?>_chk_cell_"+itemId,
             '<input type="checkbox" id="chk_'+itemId+'" onclick="javascript:show_edit(\''+itemId+'\')" name="flag[]" value="'+itemId+'|'+result.qty+'">');
          setElementText("<?=$entity?>_img_"+itemId,
             '<img src="../prd_lib/images/96/'+result.foto_name+'" width="30px">');
          setElementText("<?=$entity?>_alt_code_"+itemId,
             '<h4><span class="label label-primary">'+result.alt_code+'</span></h4>');
          setElementText("<?=$entity?>_nmcl_id_"+itemId,
             '<a href="index.php?fitem_id='+result.nmcl_id+'" target="blank">'+result.alt_code+'</a>');
          setElementText("<?=$entity?>_qty_cell_"+itemId,
             '<span id="<?=$entity?>_qty_'+itemId+'">'+result.qty+'</span><span id="qty_'+itemId+'"></span>');

          //-------------
          var noti = new Notification({elem:document.getElementById('noti_bar'),message:result.message,autoClose:3});
          noti.show();
      }
     }
   );
   return false;
 };
 function openItemEditForm(itemId){
  <?php foreach($fields as $key=>$field):?>
  setElementValue("<?=$entity.'_'.$key?>",getElementValue("view_<?=$key?>_"+itemId));
  <?php endforeach;?>
  setElementText("ajaxmessage_box","");
  open_dialog("<?=$windowId?>");
 /* getForm(
    {
      token:"LoadDocumentItem",
      formId:"doc_item_"+itemId,
      onSuccess:function(result){
        open_dialog();
        setElemenText("order_name",result.order_number);
        setElementValue("order_id",result.order_id);
        setElementValue("shopify_id",result.shopify_id);
        setElementValue("delivery_dt",result.delivery_dt);
        setElementValue("delivery_address",result.delivery_address);
        setElementValue("status",result.status);
        setElementValue("status_dt",result.status_dt);
        setElementValue("payment_status",result.payment_status);
        setElementValue("payment_dt",result.payment_dt);
        setElementValue("delivery_total",result.delivery_total);
        setElementValue("cash_on_delivery",result.cash_on_delivery);
        setElementValue("comments",result.comments);
      }
    }
  );*/
 }
 function addRow(entity,itemId) {
   var tableId = entity+"_table";
   var table = document.getElementById(tableId);
   var rowCount = table.rows.length;
   var row = table.insertRow(rowCount);
   var fields = new Array("cell0","chk_cell","img","alt_code","nmcl_id","size","color_name","thing_type","sex","qty_cell","cell2","price","cell3","cell4","price_min","nmcl_name",
   "thing","label_code","color_code","amount","amount_min");
   for(var i = 0; i < fields.length; i++) {
       row.insertCell(i).id=entity+"_"+fields[i]+"_"+itemId;
   }
 }
      /*var element1 = document.createElement("input");
   element1.type = "checkbox";
   element1.name="flag[]";
   element1.id="chk_"+itemId;
   element1.onclick=function(){show_edit(itemId)};   
			var cell3 = row.insertCell(2);
			var element2 = document.createElement("input");
			element2.type = "text";
			element2.name = "txtbox[]";
			cell3.appendChild(element2);*/
</script>
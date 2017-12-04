<div class="container-fluid">
<div class="row-fluid">
<div id="document_list" class="col-xs-2" style="display:block;height:500px; overflow-y: auto;">
 <!--Sidebar content-->
 <ul class="nav nav-pills nav-stacked">
 <?php foreach($supplyDocList as $i=>$doc):?>
 <li class="<?=(isset($active_doc)&&$active_doc==$doc['doc_id']?'active':'')?>"><a href="?doc_id=<?=$doc['doc_id']?>"><?=$doc['type_name']?> <?=$doc['doc_number']?> от <?=$doc['doc_date']?></a></li>
 <?php endforeach;?>        
 </ul>
</div>
<div class="col-xs-10">
 <!--Body content-->
<div>
<?php if($message):?><div class="alert alert-danger" role="alert"><?=$message?></div><?php endif;?>
<?php if($draft):?>
 <nav class="navbar navbar-default panel-body">
  <form action="" class="form-inline" method="POST" id="document_form">
  <div class="form-group form-group-sm">
    <div class="input-group">
      <div class="input-group-addon">№</div>
      <input type="text" class="form-control" name="document_doc_number" id="document_doc_number" placeholder="Номер накладной"/>
    </div>
    <div class="input-group">
      <div class="input-group-addon">Дата</div>
      <input type="date" class="form-control" name="document_doc_date" placeholder="yyyy-mm-dd"/>
    </div>
  </div>
  <button name="token" value="create_document" id="submit_document_form" type="submit" class="btn btn-primary btn-sm">Создать документ</button>
  <button name="token" value="clear_draft" id="submit_clear_draft_form" type="submit" class="btn btn-primary btn-sm" onclick="javascript:return confirm('Текущий документ будет очищен. Продолжить?');">Очистить черновик</button>
  </form> 
 </nav>
<?php endif;?>
</div>
<div>
<table><tr>
<td><form method=post action="labels/barcodes<?=(isset($active_doc)?"?doc_id=$active_doc":'')?>" target="_blank">
    <input type=submit name = "btn_barcodes" value="barcodes">
</form></td>
<td><form method=post action="labels/specification<?=(isset($active_doc)?"?doc_id=$active_doc":'')?>" target="_blank">
    <input type=submit name = "btn_specification" value="specification">
</form></td>
<td><form method=post action="labels/torg12<?=(isset($active_doc)?"?doc_id=$active_doc":'')?>" target="_blank">
    <input type=submit name = "btn_torg12" value="torg12">
</form></td>
<td><a href="labels/pivot<?=(isset($active_doc)?"?doc_id=$active_doc":'')?>" target="_blank">Отчет с картинками</a> </td>
</tr></table>
    <form method=post action="labels/print<?=(isset($active_doc)?"?doc_id=$active_doc":'')?>" target="_blank">
        <button name = "token" value="label">Этикетки</button>
        <button name = "token" value="detail_label">Этикетки mamsy</button>
        <button name = "token" value="inner_label">Этикетки картон</button>
        <input type="checkbox" onClick="select_all(this)" />Выделить все
<div id=itemtable style="display:block;height:500px; overflow-y: auto;margin-top: 10px">     
<table class="table label-list" id="doc_item_table">
    <thead><tr>
    <td><?php if($draft):?><img id="edit_btn_0" onclick="javascript:openItemEditForm('0');return false;" src="images/btn-add.jpg" alt="edit"/>
    <form id="doc_item_0">
     <input type="hidden" id="view_line_id_0" name="line_id" value="0"/>
     <input type="hidden" id="view_alt_code_0" name="alt_code" value=""/>
     <input type="hidden" id="view_size_id_0" name="size_id" value=""/>
     <input type="hidden" id="view_color_id_0" name="color_id" value=""/>
     <input type="hidden" id="view_qty_0" name="qty" value=""/>
     <input type="hidden" id="view_price_min_0" name="price_min" value=""/>
     <input type="hidden" id="view_price_0" name="price" value=""/>
     <input type="hidden" id="view_label_code_0" name="label_code" value=""/>
    </form><?php endif;?></td>
    <td></td><td></td>
    <td>Артикул</td><td>Артикул цв.</td>
    <td>Размер</td><td>Цвет</td><td>Предмет</td><td>Пол</td><td>Кол-во <b><?=$totalQty?></b></td><td>МК</td><td>Цена</td><td>by</td><td>kz</td><td>Цена опт</td>
    <td>Название</td><td>Вещь</td><td>barcode</td><td>Код цвета</td><td>Сумма <b><?=$totalAmount?></b></td><td>Сумма опт <b><?=$totalAmountMin?></b></td>
    </tr></thead>
 <?php foreach($documentItems as $i=>$docItem):?>
        <tr>
            <td><?php if($draft):?><img id="edit_btn_<?= $docItem->line_id ?>" onclick="javascript:openItemEditForm('<?= $docItem->line_id ?>');return false;" src="images/btn-edit.jpg" alt="edit"/>
                <form id="doc_item_<?= $docItem->line_id ?>">
                    <input type="hidden" id="view_line_id_<?= $docItem->line_id ?>" name="line_id" value="<?= $docItem->line_id ?>"/>
                    <input type="hidden" id="view_alt_code_<?= $docItem->line_id ?>" name="alt_code" value="<?= $docItem->alt_code ?>"/>
                    <input type="hidden" id="view_size_id_<?= $docItem->line_id ?>" name="size_id" value="<?= $docItem->size_id ?>"/>
                    <input type="hidden" id="view_color_id_<?= $docItem->line_id ?>" name="color_id" value="<?= $docItem->color_id ?>"/>
                    <input type="hidden" id="view_qty_<?= $docItem->line_id ?>" name="qty" value="<?= $docItem->qty ?>"/>
                    <input type="hidden" id="view_price_min_<?= $docItem->line_id ?>" name="price_min" value="<?= $docItem->price_min ?>"/>
                    <input type="hidden" id="view_price_<?= $docItem->line_id ?>" name="price" value="<?= $docItem->price ?>"/>
                    <input type="hidden" id="view_label_code_<?= $docItem->line_id ?>" name="label_code" value="<?= $docItem->label_code ?>"/>
                </form><?php endif;?>
            </td>
            <td><input type="checkbox" id="chk_<?= $docItem->line_id ?>" onclick="javascript:show_edit('<?= $docItem->line_id ?>')" name="flag[]" value="<?= $docItem->line_id ?>|<?= $docItem->qty ?>"></td>
            <td id="doc_item_img_<?= $docItem->line_id ?>"><img src="../prd_lib/images/96/<?= $docItem->foto_name ?>" width="30px"></td>
            <td id="doc_item_alt_code_<?= $docItem->line_id ?>"><h4><span class="label <?= ($docItem->status == 1 ? 'label-success' : ($docItem->status == 2 ? 'label-primary' : 'label-default')) ?>"><?= $docItem->alt_code ?></span></h4></td>
            <td id="doc_item_nmcl_id_<?= $docItem->line_id ?>"><a href="index.php?fitem_id=<?= $docItem->nmcl_id ?>" target="blank"><?= $docItem->alt_code ?></a></td>
            <td id="doc_item_size_<?= $docItem->line_id ?>"><?= $docItem->size ?></td>
            <td id="doc_item_color_name_<?= $docItem->line_id ?>"><?= $docItem->color_name ?></td>
            <td id="doc_item_thing_type_<?= $docItem->line_id ?>"><?= $docItem->thing_type ?></td>
            <td id="doc_item_sex_<?= $docItem->line_id ?>"><?= $docItem->sex ?></td>
            <td><span id="doc_item_qty_<?= $docItem->line_id ?>"><?= $docItem->qty ?></span><span id="qty_<?= $docItem->line_id ?>"></span></td>
            <td></td>
            <td id="doc_item_price_<?=$docItem->line_id?>"><?= $docItem->price ?></td>
            <td></td>
            <td></td>
            <td id="doc_item_price_min_<?= $docItem->line_id ?>"><?= $docItem->price_min ?></td>
            <td id="doc_item_nmcl_name_<?= $docItem->line_id ?>"><?= $docItem->nmcl_name ?></td>
            <td id="doc_item_thing_<?= $docItem->line_id ?>"><?= $docItem->thing ?></td>      
            <td id="doc_item_label_code_<?= $docItem->line_id ?>"><?= $docItem->label_code ?></td>
            <td id="doc_item_color_code_<?= $docItem->line_id ?>"><?= $docItem->color_code ?></td>
            <td id="doc_item_amount_<?= $docItem->line_id ?>"><?= $docItem->amount ?></td>
            <td id="doc_item_amount_min_<?= $docItem->line_id ?>"><?= $docItem->amount_min ?></td>
        </tr>
 <?php endforeach;?>        
 </table>
</div>
</form>

    

</div>
<script>
function show_edit(elem_id){
    if (document.getElementById('chk_'+elem_id).checked){
        document.getElementById('qty_'+elem_id).innerHTML='<input type="text" size="4" name="print_qty[]"/>';
    } else {
        document.getElementById('qty_'+elem_id).innerHTML="";
    }
}
var elem = document.getElementById('itemtable');
elem.style.height=(document.documentElement.clientHeight - getCoords(elem).top) + "px";
elem = document.getElementById('document_list');
elem.style.height=(document.documentElement.clientHeight - getCoords(elem).top) + "px";
function select_all(source){
  checkboxes = document.getElementsByName('flag[]');
  for(var i=0, n=checkboxes.length;i<n;i++) {
    checkboxes[i].checked = source.checked;
  }
}
</script>
</div>
</div>
</div>
<?php require(FORMS_DIR."/edit_doc_item.php");?>
<?php require(BLOCKS_DIR."/notification_bar.php");?>
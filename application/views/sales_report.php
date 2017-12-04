<div class="container-fluid">
<div class="row-fluid">
<div id="document_list" class="col-xs-2" style="display:block;height:500px; overflow-y: auto;">
    <!--Sidebar content-->
 <ul class="nav nav-pills nav-stacked">
 <li class="<?=($report=='SALES'?'active':'')?>"><a href="reports">Продажи</a></li>
 <li class="<?=($report=='ORDERS'?'active':'')?>"><a href="reports/orders">Заказы</a></li>
 <br>
 </ul>
            <form method=post>
                <label><u>Фильтр списка заказов</u></label>
                <br><img src="images/status0.gif" ><input type="checkbox" value="0" name="f_status[]" <?= $g_status[0] ?>> Новый
                <br><img src="images/status1.gif" ><input type="checkbox" value="1" name="f_status[]" <?= $g_status[1] ?>> Согласовывается
                <br><img src="images/status2.gif" ><input type="checkbox" value="2" name="f_status[]" <?= $g_status[2] ?>> Подтвержден
                <br><img src="images/status3.gif" ><input type="checkbox" value="3" name="f_status[]" <?= $g_status[3] ?>> Создан заказ в крурьерской службе
                <br><img src="images/status4.gif" ><input type="checkbox" value="4" name="f_status[]" <?= $g_status[4] ?>> Отправлен
                <br><img src="images/status5.gif" ><input type="checkbox" value="5" name="f_status[]" <?= $g_status[5] ?>> Получен клиентом
                <br><img src="images/status6.gif" ><input type="checkbox" value="6" name="f_status[]" <?= $g_status[6] ?>> Отменен
                <input type=hidden name=actoin value="APPLY_FILTER">
                <br><input type=submit value="Применить">
            </form>			
</div>
<div class="col-xs-10">
 <!--Body content-->
<div>
<?php if($exception):?><div class="alert alert-danger" role="alert"><?=$message?></div><?php endif;?>
 <nav class="navbar navbar-default panel-body">
  <form action="" class="form-inline" method="POST" id="filter_form">
  <div class="form-group form-group-sm">
    <div class="input-group">
      <div class="input-group-addon">Начало</div>
      <input type="text" class="form-control" name="start_date" id="start_date" placeholder="yyyy-mm-dd" value="<?=$start_date?>"/>
    </div>
    <div class="input-group">
      <div class="input-group-addon">Конец</div>
      <input type="date" class="form-control" name="end_date" id="end_date" placeholder="yyyy-mm-dd" value="<?=$end_date?>"/>
    </div>
  </div>
  <button name="token" value="build_report" id="build_report" type="submit" class="btn btn-primary btn-sm">Применить</button>
  <br/>
  <div class="form-group form-group-sm">
      <div class="input-group">
      <div class="input-group-addon">Способ доставки:</div>
      <input type="checkbox" name="delivery_type[]" value="GE" <?=$delivery_type['GE']?>/>GE 
      <input type="checkbox" name="delivery_type[]" value="SDEK" <?=$delivery_type['SDEK']?>/>СДЭК 
      <input type="checkbox" name="delivery_type[]" value="POST" <?=$delivery_type['POST']?>/>Почта
      <input type="checkbox" name="delivery_type[]" value="ALL" <?=$delivery_type['ALL']?>/>Все
      </div>
      <div class="input-group">
      <div class="input-group-addon">Статус оплаты:</div>
      <input type="checkbox" name="payment_status[]" value="Y" <?=$payment_status['Y']?>/>Оплачено 
      <input type="checkbox" name="payment_status[]" value="N" <?=$payment_status['N']?>/>Не оплачено
      </div>
  </div>
  </form> 
 </nav>
</div>
<div>
<h3><?=$REPTITLE?></h3>
<?php if(!$exception):?>
<div id=itemtable style="display:block;height:500px; overflow-y: auto;margin-top: 10px">     
<table class="table label-list" id="doc_item_table">
<thead>
<tr align=center>
<?php foreach($columns as $i=> $column):?>
 <td><?=$column['label']?></td>
<?php endforeach;?>
</tr>
</thead>
<?php foreach($REPORTROWS as $i=> $ROW):?>
<tr>
<?php foreach($columns as $colName=> $column):?>
 <?php if ($column['type']=='link'):?>
    <td><a href=<?=str_replace('#VALUE#',$ROW->$colName,$column['href'])?>><?=$ROW->$colName?></a></td>
 <?php elseif ($column['type']=='image'):?>
  <td><img width=96 src="<?=$ROW->$colName?>"></td>
 <?php else:?>
  <td><?=$ROW->$colName?></td>
 <?php endif;?>
<?php endforeach;?>
</tr>
<?php endforeach;?>
</table>
</div>
<?php endif;?>
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
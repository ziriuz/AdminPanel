    <form method=post action="warehouse/clear_rests">
        <input type=submit name = "btn_print" value="Обнулить остатки">     
<div class="statreport" id="wrhrests">
<h3>Остатки</h3>
<table>
<thead>
<tr align=center>
<td>фото</td>
<td>id</td>
<td>Артикул</td>
<td>Наименование</td>
<td>Размер</td>
<td>Остаток</td>
<td>Резерв</td>
<td>Ед.Изм.</td>
<td>Цена опт</td>
<td>Цена мелк. опт</td>
<td>Цена</td>
</tr>
</thead>
<?php $c = -1;?>
<?php foreach(wrhrests($WRH_ID) as $i=> $ROW):?>
<?php
if($c != $ROW->nmcl_id){
  $item = new Item($ROW->nmcl_id);
  $img = '<img src="../prd_lib/images/96/'.$item->foto_name[0].'">';
  $flag = "<input type=\"checkbox\" id=\"chk_$ROW->nmcl_id\" name=\"flag[]\" value=\"$ROW->nmcl_id\">";
  $c = $ROW->nmcl_id;
} else{ $img = "";$flag = "";}
?>
<tr>
<td><?=$img?></td>
<td>
<a href=index.php?fitem_id=<?=$ROW->nmcl_id?>><?=$ROW->nmcl_id?></a>
</td>
<td>
<?=$ROW->alt_code?>
<?=$flag?>
</td>
<td>
<?=$ROW->nmcl_name?>
</td>
<td>
<?=$ROW->size?>
</td>
<td>
<?=$ROW->qty_rest?>
</td>
<td>
<?=$ROW->qty_reserved?>
</td>
<td>
<?=$ROW->um_name?>
</td>
<td>
<?=$ROW->price_min?>
</td>
<td>
<?=$ROW->price_mid?>
</td>
<td>
<?=$ROW->price?>
</td>
</tr>
<?php endforeach;?>
</table>
</div>
</form>
<style>
.statreport{
  display:block;height:500px; overflow-y: auto;
}
</style>
<script>
  var elem = document.getElementById('wrhrests');
  elem.style.height=(document.documentElement.clientHeight - getCoords(elem).top) + "px";
</script>
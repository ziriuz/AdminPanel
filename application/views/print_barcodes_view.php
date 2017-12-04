<link rel="stylesheet" href="label.css"/>
<table>
<tr>
     <td>Штрихкод</td>
     <td>barcode</td>
     <td>Артикул</td>
     <td>Название</td>
     <td>Размер</td>
     <td>Цвет</td>
     <td>Количество</td>
	 <td>Цена опт</td>
</tr>
 <?php foreach($documentItems as $i=>$docItem):?>
 <tr><td><img src="lib/code128/ex.php?code=<?=$docItem->label_code?>"></td>
     <td><?=$docItem->label_code?></td>
     <td><?=$docItem->alt_code?></td>
     <td><?=$docItem->thing?></td>
     <td><?=$docItem->size?></td>
     <td><?=$docItem->color_name?></td>
     <td><?=$docItem->qty?></td>
	 <td><?=$docItem->price_min?></td>
 </tr>
 <?php endforeach;?>


</table>
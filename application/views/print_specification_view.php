<link rel="stylesheet" href="label.css"/>
<table>
<tr>
<td>Марка</td><td>Артикул поставщика</td><td>Цвет поставщика</td><td>Наименование</td><td>Баркод</td><td>Предмет</td><td>Пол</td><td>Цвет</td><td>Состав</td><td>Материал подкладки</td><td>Утеплитель</td><td>Материал подошвы</td><td>Материал стельки</td><td>Коллекция</td><td>Сезон</td><td>Направление</td><td>Стиль</td><td>НДС</td><td>Страна производитель</td><td>Описание</td><td>Комплектация</td><td>Технология</td><td>Возрастные ограничения</td><td>Уход за вещью</td><td>Размер тех</td><td>Российский размер</td><td>Обхват груди, в см</td><td>Обхват талии, в см</td><td>Обхват бедер, в см</td><td>Обхват под грудью, в см</td><td>Длина стопы, в см</td><td>Рост, в см</td>
<td/><td/><td/><td/><td/><td/><td/>
<td>Длина изделия по спинке, в см</td>
<td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/>
<td>Длина рукава</td><td>Название</td><td>Описание</td><td>Фото</td><td>Цена опт</td><td>Цена</td><td>Количество</td><td>Возраст</td><td>Вес</td>
</tr>
 <?php foreach($documentItems as $i=>$docItem):?>
 <tr>
<td>Мы команда</td><td></td><td><?=$docItem->alt_code?></td><td><?=$docItem->thing?></td><td><?=$docItem->label_code?></td><td><?=$docItem->thing_type?></td><td><?=$docItem->sex?></td><td><?=$docItem->color_name?></td><td><?=$docItem->composition?></td><td></td><td></td><td></td><td></td><td>Базовая коллекция</td><td>круглогодичный</td><td></td><td>Casual</td><td>0</td><td>Россия</td><td></td><td><?=$docItem->thing?></td><td></td><td></td><td></td><td><?=$docItem->size?></td><td><?=($docItem->sex=='Детский'||$docItem->sex=='Девочки'||$docItem->sex=='Мальчики'?(strpos($docItem->size,'-')===false?$docItem->size.'-'.($docItem->size+6):$docItem->size):$docItem->size)?></td><td><?=($docItem->sex=='Детский'||$docItem->sex=='Девочки'||$docItem->sex=='Мальчики'?'':($docItem->sex=='Женский'?$docItem->size*2:($docItem->size+1)*2))?></td><td><?=($docItem->sex=='Детский'||$docItem->sex=='Девочки'||$docItem->sex=='Мальчики'?'':($docItem->sex=='Женский'?($docItem->size-9)*2:($docItem->size-5)*2))?></td><td><?=($docItem->sex=='Детский'||$docItem->sex=='Девочки'||$docItem->sex=='Мальчики'?'':($docItem->sex=='Женский'?($docItem->size+3)*2:($docItem->size+3)*2))?></td><td></td><td></td><td><?=($docItem->sex=='Детский'||$docItem->sex=='Девочки'||$docItem->sex=='Мальчики'?$docItem->size:'')?></td>
<td/><td/><td/><td/><td/><td/><td/>
<td><?=($docItem->thing_type=='Футболки'?($docItem->sex=='Детский'||$docItem->sex=='Девочки'||$docItem->sex=='Мальчики'?'37':($docItem->sex=='Женский'?'58':'73')):'')?></td>
<td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/><td/>
<td><?=($docItem->thing_type=='Футболки'?'Короткие':'')?></td><td><?=$docItem->nmcl_name?></td><td><?=$docItem->description?></td><td><?=$docItem->foto_filename?></td><td><?=$docItem->price_min?></td><td><?=$docItem->price?></td><td><?=$docItem->qty?></td><td><?=$docItem->age?></td><td><?=$docItem->wght?></td>
 </tr>
 <?php endforeach;?>


</table>
<link rel="stylesheet" href="label.css"/>
<table class="table"><tr><td>title</td>
<td>sku</td>
<td>inventory_quantity</td>
<td>price</td>
<td>barcode</td>
<td>weight</td> 
<td>weight_unit</td>
<td>image</td><td>image uri</td><td>tags</td><td  width="20%">describtion</td>
<td>id</td>
<td>vendor</td>
<td>product_type</td>
<td>created_at</td>
<td>handle</td>
<td>updated_at</td>
<td>published_at</td>
<td>published_scope</td>
<td>load message</td>
</tr>
<?php foreach($products as $key => $product):?>
<tr>
 <td><?=$product["title"]?></td>	
 <td><?=$product["variants"][0]["sku"]?></td>
 <td><?=$product["variants"][0]["inventory_quantity"]?></td>
 <td><?=$product["variants"][0]["price"]?></td>
 <td><?=$product["variants"][0]["barcode"]?></td>
 <td><?=$product["variants"][0]["weight"]?></td>
 <td><?=$product["variants"][0]["weight_unit"]?></td>        
 <td><img width=100 src="<?=$product["image"]["src"]?>"></td>
 <td><?=$product["image"]["src"]?></td>
 <td><?=$product["tags"]?></td>
 <td width="20%"><div style="width:400px;height:100px;overflow:auto"><?=$product["body_html"]?></div></td>
 <td><?=$product["id"]?></td>
 <td><?=$product["vendor"]?></td>
 <td><?=$product["product_type"]?></td>
 <td><?=$product["created_at"]?></td>
 <td><?=$product["handle"]?></td>
 <td><?=$product["updated_at"]?></td>
 <td><?=$product["published_at"]?></td>
 <td><?=$product["published_scope"]?></td>
 <td><?=isset($product["message"])?$product["message"]:""?></td>
 </tr>
 <?php endforeach;?>
</table>
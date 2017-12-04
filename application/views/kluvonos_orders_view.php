<?php require(FORMS_DIR."/edit_order.php");?>
<?php require(BLOCKS_DIR."/notification_bar.php");?>
<link rel="stylesheet" href="label.css"/>
<table class="table table-hover"><thead><tr>
 <th>TOOLS</th>
 <th>STATUS</th>
 <th>ID</th>
 <th>title</th>
 <th>created_at</th>
 <th>subtotal_price</th>
 <th>total_price</th>
 <th>financial_status</th>
 <th>confirmed</th>
 <th>shipping_title</th>
 <th>shipping_price</th>
 <th>name</th>
 <th>phone</th>
 <th>email</th>
 <th>note</th>
 </tr></thead>
    <tbody>
<?php foreach($orders as $key => $order):?>
<tr>
 <td><button id="edit_order_<?=$order['order_id']?>" onclick="loadOrder('<?=$order["order_id"]?>');return false;">edit</button><form id="order_<?=$order['order_id']?>"><input type="hidden" name="order_id" value="<?=$order['order_id']?>"/></form></td>
 <td><?=$order["status"]?></td>
 <td><a href="orders.php?forder_id=<?=$order["order_id"]?>"><?=$order["order_id"]?></a></td>
 <td><?=$order["order_number"]?></td>	
 <td><?=$order["created_at"]?></td>
 <td><?=$order["subtotal_price"]?></td>
 <td><?=$order["total_price"]?></td>
 <td><?=$order["financial_status"]?></td>
 <td><?=$order["confirmed"]?></td>
 <td><?=$order["shipping_type"]?></td>
 <td><?=$order["shipping_price"]?></td>
 <td><?=$order["customer_name"]?></td>
 <td><?=$order["phone"]?></td>
 <td><?=$order["email"]?></td>
 <td><?=$order["note"]?></td>
 </tr>
<!--script language="JavaScript">document.getElementById('edit_order_<?=$order["order_id"]?>').onclick=loadOrder('<?=$order["order_id"]?>');</script--> 
 <?php endforeach;?>
    </tbody>
</table>
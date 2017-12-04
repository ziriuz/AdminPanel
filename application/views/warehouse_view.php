<table border="0" cellspacing="0" cellpadding="0"  width=100%>
 <tr>
  <!-- .......................... Левое меню .......................... -->
  <td valign=top width=170px>
    <div class=nav>
    <h3>Склад</h3>
    <div class=navItem>
	  <?php foreach($WRH as $id=>$wrh): ?>
	  <?=$wrh?><br>
	  <form method=get>
	    <input type=hidden name=action value=wrh_in>
		<input type=hidden name=fwrh_id value=<?=$id?>>
		<input type=submit value="Приход">				
	  </form>
	  <form method=get>
	    <input type=hidden name=action value=wrh_out>
		<input type=hidden name=fwrh_id value=<?=$id?>>
		<input type=submit value="Расход">				
	  </form>
	  <form method=get>
	    <input type=hidden name=action value=wrh_reserve>
		<input type=hidden name=fwrh_id value=<?=$id?>>
		<input type=submit value="Резерв">				
	  </form>
	  <form method=get>
	    <input type=hidden name=action value=wrh_unreserve>
		<input type=hidden name=fwrh_id value=<?=$id?>>
		<input type=submit value="Отменить Резерв">				
	  </form>
 	  <br><br>
	  <?php endforeach?>
    </div>
   </div>
  </td>
  <!-- .......................... Содержимое .......................... -->
  <td valign=top>
   <div id=catContent1>
   <div class=editform>
   <form  method="get" name=wrhrest>
    <input type="hidden" name="action" value="view_wrh"/>
    <div class=blockedit>
    <p><label>Остатки на складе</label>
    <select name=fwrh_id>
	<?php foreach($WRH as $id=>$wrh): ?>
	<option <?=($id==$WRH_ID?'selected':'')?> value=<?=$id?>><?=$wrh?></option>	  
	<?php endforeach?>
    </select>
	<input type=submit value="Применить">
	<label>Сумма</label> <?=$RESTS_TOTAL->amount?> <label>Сумма мелк. опт</label> <?=$RESTS_TOTAL->amount_mid?> <label>Сумма опт</label> <?=$RESTS_TOTAL->amount_min?>
    </p>
    </div>
   </form>
   </div>
   <br>
   <?php require(BLOCKS_DIR."/wrhrests.php");?>
   </div>
  </td>
 </tr>
</table>
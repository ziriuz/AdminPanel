<table border="0" cellspacing="0" cellpadding="0"  width=100%>
 <tr>
  <!-- .......................... ����� ���� .......................... -->
  <td valign=top width=170px>
    <div class=nav>
    <h3>�����</h3>
    <div class=navItem>
	  <?php foreach($WRH as $id=>$wrh): ?>
	  <?=$wrh?><br>
	  <form method=get>
	    <input type=hidden name=action value=wrh_in>
		<input type=hidden name=fwrh_id value=<?=$id?>>
		<input type=submit value="������">				
	  </form>
	  <form method=get>
	    <input type=hidden name=action value=wrh_out>
		<input type=hidden name=fwrh_id value=<?=$id?>>
		<input type=submit value="������">				
	  </form>
	  <form method=get>
	    <input type=hidden name=action value=wrh_reserve>
		<input type=hidden name=fwrh_id value=<?=$id?>>
		<input type=submit value="������">				
	  </form>
	  <form method=get>
	    <input type=hidden name=action value=wrh_unreserve>
		<input type=hidden name=fwrh_id value=<?=$id?>>
		<input type=submit value="�������� ������">				
	  </form>
 	  <br><br>
	  <?php endforeach?>
    </div>
   </div>
  </td>
  <!-- .......................... ���������� .......................... -->
  <td valign=top>
   <div id=catContent1>
   <div class=editform>
   <form  method="get" name=wrhrest>
    <input type="hidden" name="action" value="view_wrh"/>
    <div class=blockedit>
    <p><label>������� �� ������</label>
    <select name=fwrh_id>
	<?php foreach($WRH as $id=>$wrh): ?>
	<option <?=($id==$WRH_ID?'selected':'')?> value=<?=$id?>><?=$wrh?></option>	  
	<?php endforeach?>
    </select>
	<input type=submit value="���������">
	<label>�����</label> <?=$RESTS_TOTAL->amount?> <label>����� ����. ���</label> <?=$RESTS_TOTAL->amount_mid?> <label>����� ���</label> <?=$RESTS_TOTAL->amount_min?>
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
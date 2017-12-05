<?php
  session_start();
	include("includes/functions.php");
	include("includes/discount.php");


  if(!$USER=displayLogin()) exit;

  $MESSAGES = array();
  $MESSAGE = ''; 
  $TITLE = '������ ���������� ���������';
  $DISCOUNTS = getDiscounts();
  header('Content-type: text/html; charset=windows-1251');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//RU" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML lang="ru,en">
<HEAD>
    <TITLE><?=$TITLE?></TITLE>
    <META content="text/html"; charset="windows-1251" http-equiv="Content-Type" />
    <link rel="stylesheet" href="prd_main.css"/>
	<link rel="stylesheet" href="comment.css"/>
    <!--[if IE7]><link rel="stylesheet" type="text/css" href="prd_main_ie.css" media="all" /><![endif]-->
    <script language="JavaScript" src="ajax.js"></script>
</HEAD>
<BODY>
<?php if($PRINTORDER):?>
<?php require("templates/printorder.htm"); require("templates/printorder.htm"); exit(); ?>
<?php elseif($PRINTPOSTCHECK):?>
<?php require("templates/printpostcheck.htm"); exit(); ?>
<?php elseif($PRINTPOST):?>
<?php require("templates/printpost.htm"); exit(); ?>
<?php elseif($IMPORTORDER):?>
<?php require("templates/importorder.htm"); exit(); ?>
</BODY>
</HTML>
<?php endif ?>
<!-- .......................... ��������� .......................... -->
<?php require("templates/menu.htm");?>
<div id=contMain>
<table border="0" cellspacing="0" cellpadding="0"  width=100%>
 <tr>
  <!-- .......................... ����� ���� .......................... -->
  <td valign=top width=170px>
    <div class=nav>
    <h3>������</h3>
    <div class=navItem>
      <img src="images/status0.gif" > �����
      <br><img src="images/status2.gif" > email �������������
      <br><img src="images/status1.gif" > �����������
      <br><img src="images/status4.gif" > ��������� �� �����
      <br><img src="images/status5.gif" > email � ������� �����
      <br><img src="images/status6.gif" > �������
    </div>
		  
    <div class=navItem>
		  <form method=post>
      <label><u>������ ������ �������</u></label>
			<br><input type="checkbox" value="0" name="f_status[]" <?=$g_status[0]?>> �����
      <br><input type="checkbox" value="2" name="f_status[]" <?=$g_status[2]?>> email �������������
      <br><input type="checkbox" value="1" name="f_status[]" <?=$g_status[1]?>> �����������
      <br><input type="checkbox" value="4" name="f_status[]" <?=$g_status[4]?>> ��������� �� �����
      <br><input type="checkbox" value="5" name="f_status[]" <?=$g_status[5]?>> email � ������� �����
      <br><input type="checkbox" value="6" name="f_status[]" <?=$g_status[6]?>> �������
			<input type=hidden name=f_actoin value="APPLY_FILTER">
			<br><input type=submit value="���������">
			</form>			
    </div>
   </div>
  </td>
  <!-- .......................... ���������� .......................... -->
  <td valign=top>
    <div id=catContent>
     <table>
      <tr valign=top>
      <td><b></b></td><td><b>ID</b></td><td><b></b></td><td nowrap><b>���</b></td><td><b>���</b></td><td><b>��������</b></td>
      <td><b>������������</b></td><td><b>�������� ���</b></td><td><b>����������</b></td>
     </tr>

     <?php foreach($DISCOUNTS as $i => $discount):?>
     <?php //$ITEM = new Item($item_id);?>
     <tr valign=top>
      <td>
        <input type=checkbox name=f_order_id[] value=<?=$reg_id?>>
		<input type=hidden name=f_id[] value=<?=$reg_id?>>
      </td>
      <td><a href="subscribe.php?forder_id=<?=$reg_id?>"><?=$reg_id?></a></td>
      <td><img src="images/status<?=$REG->status?>.gif"></td>
      <td nowrap><?=$discount->code?></td>
      <td><?=$discount->distype_id?>  <?=$REG->lastName?></td>
      <td><?=$discount->description?></td>
      <td><?=$discount->published?></td>
      <td><input type=text size=15 value="<?=$REG->postId?>" name = f_postid[]></td>
      <td><input type=text size=15 value="<?=$REG->dateSend?>" name = f_datesend[]></td>
     </tr>
     <?php endforeach;?>
     </table>
     </form>
     </div>
	 <div>
	 <?php include("templates/discount.htm"); ?>
	 </div>
	
  </td>
 </tr>
</table>
</div>

<!-- .......................... ������ .......................... -->
<div id="FooterBar">
</div>
</BODY>
</HTML>


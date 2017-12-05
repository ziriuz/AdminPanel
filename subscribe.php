<?php
  session_start();
  include("includes/functions.php");
  include("includes/registry.php");
  include("includes/subscribe.php");
  if(!$USER=displayLogin()) exit;

  $gorder_id = '';
  $MESSAGE = ''; 
  $TITLE = '������ ���������� ���������';
  $g_status = array('checked','checked','checked','checked','checked','','');
  $SUBSCRIBES = getSubscribes();  
  $filter = '';
  if (isset($_GET['code'])) $filter=$_GET['code'];  
  $REGS = getRegs($filter);

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
 <script language="JavaScript" src="ajax2.js"></script>
</HEAD>
<BODY>
<!-- .......................... ��������� .......................... -->
<?php require("templates/menu.htm");?>
<div id=contMain>
<table border="0" cellspacing="0" cellpadding="0"  width=100%>
 <tr>
  <!-- .......................... ����� ���� .......................... -->
  <td valign=top width=170px>
    <div class=nav style="background-color:#DDEEAA">
	<!--
    <h3>��������</h3>
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
	-->
	
<link rel="stylesheet" href="jquery/themes/base/jquery-ui.css">
<style>
input.text { margin-bottom:6px; width:90%; padding: .4em; }
fieldset { padding:0; border:0; margin-top:10px; }
h1 { font-size: 1.2em; margin: .6em 0; }
div#discounts-list { width: 186px; margin: 20px 0; }
div#discounts-list table { margin: 1em 0; border-collapse: collapse; width: 100%; }
div#discounts-list table td, div#discounts-list table th { border: 1px solid #eeeeee; padding: .6em 10px; text-align: left; }
.ui-dialog .ui-state-error { padding: .3em; }
.validateTips { border: 1px solid transparent; padding: 0.3em; }
.activeSubscr {color:blue;}
</style>

<div id="discounts-list" class="ui-widget">
<form name="setactive_subscribe">
<input type="hidden" name="action" value="setactive_subscribe">
<table id="subscribe-grid" class="ui-widget ui-widget-content">
<thead>
<tr class="ui-widget-header ">
 <th>������ ��������</th>
</tr>
</thead>
<tbody>
<?php foreach($SUBSCRIBES as $c => $subscr):?>
<tr>
 <td><input type=radio name="active_subscribe" value="<?=$subscr->code?>" <?=($subscr->active==1?'checked':'')?>/>
 <span class="<?=($subscr->active==1?'activeSubscr':'')?>" id="<?=$subscr->code?>" > [<?=$subscr->code?>] <a href="subscribe.php?code=<?=$subscr->code?>"><?=$subscr->name?></a></span>
 </td>
</tr>
<?php endforeach;?>
</tbody>
</table>
</form>
</div>
<button id="set-active" onclick="setActiveSubcribe()">������������ ��������</button>	
<div id="dialog-form" title="Create new subscribe">
<p class="validateTips"></p>
<form name=create_subscribe>
<em id="ajaxmescreate_subscribe"></em>
<input type="hidden" name="action" value="subscribe">
<fieldset>
<label for="code">���</label><input type="text" name="subscribe_code" id="subscribe_code" class="text ui-widget-content ui-corner-all">
<label for="name">��������</label><input type="text" name="subscribe_name" id="subscribe_name" value="" class="text ui-widget-content ui-corner-all">
<label for="description">��������</label><textarea name="subscribe_description" id="subscribe_description" cols=30 rows=4 class="text ui-widget-content ui-corner-all"></textarea>
</fieldset>
</form>
<button id="create-subscribe" onclick="sendSubcribe()">�������� ��������</button>
</div>	
   </div>
  </td>
  <!-- .......................... ���������� .......................... -->
  <td valign=top>
    <div id=catContent>
     <div class=searchform>
     <form>
      <label>ID ������</label>
      <input type=text name=forder_id value="<?=$gorder_id?>">
      <input type=submit value="�����">
      <em><?=$MESSAGE?></em>
     </form>
     </div>
     <br>
     <h3>����������</h3>
     <br>
     <div class=orderlist>
     <form method=post>
     <input type=hidden name=f_action value = "UPDATE_POSTID">
      <!--label>�������� ������ ��� ���������� ��</label>
      <select name=f_status>
       <option value=0><img src="images/status0.gif" > ����� </option>
       <option value=1><img src="images/status1.gif" > ����������� </option>
       <option value=2><img src="images/status2.gif" > ��������������� � ���� </option>
       <option value=3><img src="images/status3.gif" > ������� �������</option>
       <option value=4><img src="images/status4.gif" > ��������� �� ����� </option>
       <option value=5><img src="images/status5.gif" > ��������</option>
       <option value=6><img src="images/status6.gif" > �������</option>
      </select-->
      <input type=submit name = "f_send_conf" value="��������� ������������� email">
      <input type=submit name = "f_save" value="���������">
     <table>
      <tr valign=top>
      <td><b></b></td><td><b>��������</b></td><td><b></b></td><td nowrap><b>���� ��������</b></td><td><b>���</b></td><td width="100px"><b>email</b></td>
      <td><b>�������</b></td><td><b>������</b></td><td><b>�����</b></td><td><b>���.</b></td><td><b>���.</b></td><td><b>�������� ���</b></td><td><b>����������</b></td>
     </tr>

     <?php foreach($REGS as $reg_id => $REG):?>
     <?php //$ITEM = new Item($item_id);?>
     <tr valign=top>
      <td>
        <input type=checkbox name=f_order_id[] value=<?=$reg_id?>>
    <input type=hidden name=f_id[] value=<?=$reg_id?>>
      </td>
      <td nowrap><?=$REG->subscr_code?></td>
      <td><img src="images/status<?=$REG->status?>.gif"></td>
      <td nowrap><?=$REG->dateReg?></td>
      <td><?=$REG->firstName?>  <?=$REG->lastName?></td>
      <td><?=$REG->email?></td>
      <td><?=$REG->phone?></td>
      <td><?=$REG->postCode?></td>
      <td><?=$REG->address?></td>
      <td nowrap><?=$REG->kidSize?></td>
      <td><?=$REG->bigSize?></td>
      <td><input type=text size=15 value="<?=$REG->postId?>" name = f_postid[]></td>
      <td><input type=text size=15 value="<?=$REG->dateSend?>" name = f_datesend[]></td>
     </tr>
     <?php endforeach;?>
     </table>
     </form>
     </div>
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


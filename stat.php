<?php
  session_start();
  include("includes/functions.php");


  if(!$USER=displayLogin()) exit;

  $TITLE = 'Панель управления магазином';

  $SHOWREPORT=false;
  $MESSAGE = '';

  if (!isset($freport))$freport = 'NO_CATEGORY';
  if (!isset($_POST['action'])) {
   $period_from = '';
   $period_to = '';
   $show_foto = 'checked';
   $limit_from = 0;
   $limit_len = 10;
  } else {
   $period_from = $_POST['period_from'];
   $period_to = $_POST['period_to'];
   $show_foto = (isset($_POST['show_foto'])?$_POST['show_foto']:'');
   $limit_from = $_POST['limit_from'];
   $limit_len = $_POST['limit_len'];
  }
  if ($freport == 'STAT01')
  $REPORTROWS = statrep01(1);
  else{
  $REPORTROWS = getReport($freport,$period_from,$period_to,$limit_from, $limit_len);
  $SHOWREPORT=true;
  }
  $REPTITLE = $freport;
  
    header('Content-type: text/html; charset=utf-8');
  
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//RU" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML lang="ru,en">
<HEAD>
    <TITLE><?=$TITLE?></TITLE>
    <META content="text/html"; charset="windows-1251" http-equiv="Content-Type" />
    <link rel="stylesheet" href="prd_main.css"/>
    <!--[if IE7]><link rel="stylesheet" type="text/css" href="prd_main_ie.css" media="all" /><![endif]-->
    <script language="JavaScript" src="ajax.js"></script>
</HEAD>
<BODY>
<!-- .......................... Заголовок .......................... -->
<?php require("templates/menu.htm");?>
<div id=contMain>
<table border="0" cellspacing="0" cellpadding="0"  width=100%>
 <tr>
  <!-- .......................... Левое меню .......................... -->
  <td valign=top width=170px>
    <div class=nav>
    <h3>Отчеты</h3>
    <div class=navItem>
      <img src="images/status0.gif" > <a href="stat.php?freport=STAT01">Статистика</a>
      <br><img src="images/status1.gif" > <a href="stat.php?freport=NO_CATEGORY">Нет категории</a>
      <br><img src="images/status2.gif" > <a href="stat.php?freport=NO_COLLECTION">Нет коллекции</a>
      <br><img src="images/status3.gif" > <a href="stat.php?freport=NO_SIZE">Нет размера</a>
      <br><img src="images/status4.gif" > <a href="stat.php?freport=NO_DESCR">Нет описания</a>
      <br><img src="images/status5.gif" > <a href="stat.php?freport=MOST_WANTED">Самые заказываемые</a>
	  <br><img src="images/status5.gif" > <a href="stat.php?freport=TOGETHER_ORDERED">Совместно заказываемые</a>	  
	  <br><img src="images/status5.gif" > <a href="stat.php?freport=TOGETHER_ORDERED2">Совместно заказываемые 2</a>	  
    </div>
   </div>
  </td>
  <!-- .......................... Содержимое .......................... -->
  <td valign=top>
   <div id=catContent> 
   <div class=editform>
   <form  method="post" name=statrep01>
    <input type="hidden" name="freport" value="<?=$freport?>"/>
    <input type="hidden" name="action" value="getreport"/>
    <div class=blockedit>
    <p><label>Период</label>
    <select name=fperiod>
		  <option selected value=1>За день</option>
		  <option value=7>За неделю</option>
		  <option value=30>За месяц</option>
		  <option value=365>За год</option>
    </select>
	<label>C</label> <input name="period_from" type="text" value="<?=$period_from?>"> 
	<label> По </label> <input name="period_to" type="text" value="<?=$period_to?>">
	<br>
	<label>Показать фото</label> <input type = "checkbox" value="checked" name = "show_foto" <?=$show_foto?>>
    <label>Начать с</label> <input name="limit_from" type="text" value="<?=$limit_from?>"> 
	<label>Количество</label> <input name="limit_len" type="text" value="<?=$limit_len?>">
	<input type ="submit" value="Выполнить" name = "submit">
    </p>
    </div>
    </form>
    </div>
	<?php if($SHOWREPORT) require('templates/reporttable.htm');
          else require('templates/statrep01.htm');?>
    </div>
  </td>
 </tr>
</table>
</div>

<!-- .......................... Подвал .......................... -->
<div id="FooterBar">
</div>
</BODY>
</HTML>


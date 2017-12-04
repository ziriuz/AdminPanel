<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//RU" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML lang="ru,en">
<HEAD>
    <BASE href="<?=BASE?>"/>
    <TITLE><?=$TITLE?></TITLE>
    <META content="text/html" charset="utf-8" http-equiv="Content-Type" />
    <!-- Bootstrap core CSS -->
    <link href="application/views/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="application/views/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link rel="stylesheet" href="application/views/css/common.css"/>
    <link rel="stylesheet" href="comment.css"/>
    <link rel="stylesheet" href="application/views/css/notification.css"/>
    <link rel="stylesheet" href="application/views/css/form.css"/>    
    <!--[if IE7]><link rel="stylesheet" type="text/css" href="prd_main_ie.css" media="all" /><![endif]-->
    <script language="JavaScript" src="ajax.js"></script>
    <script language="JavaScript" src="application/views/js/ajax2.js"></script>
    <script language="JavaScript" src="application/views/js/ui.js"></script>
</HEAD>
<BODY>
<!-- .......................... Заголовок .......................... -->
<?php require(BLOCKS_DIR."/menu.php");?>
<div id=contMain>
<?php include VEIW_IM_PATH.'/'.$content_view; ?>
</div>
<!-- .......................... Подвал .......................... -->
<!--div id="FooterBar">
</div-->
    <!-- Placed at the end of the document so the pages load faster -->
<!--    <script src="application/views/jquery/jquery.min.js"></script>
    <script src="application/views/bootstrap/js/bootstrap.min.js"></script>
    <script src="application/views/bootstrap/js/docs.min.js"></script>-->
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <!--script src="application/views/bootstrap/js/ie10-viewport-bug-workaround.js"></script-->
</BODY>
</HTML>
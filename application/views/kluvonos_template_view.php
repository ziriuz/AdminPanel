<!DOCTYPE html>
<HTML lang="ru,en">
<HEAD>
    <BASE href="<?=BASE?>"/>
    <TITLE><?=$TITLE?></TITLE>
    <META content="text/html" charset="utf-8" http-equiv="Content-Type" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <!-- Bootstrap core CSS -->
    <link href="application/views/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
    <!-- Bootstrap theme -->
    <!--link href="application/views/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet"/-->
    <!-- Custom styles for this template -->
    <link rel="stylesheet" href="application/views/css/common.css"/>
    <link rel="stylesheet" href="comment.css"/>
    <link rel="stylesheet" href="application/views/css/notification.css"/>
    <link rel="stylesheet" href="application/views/css/form.css"/>
    <script language="JavaScript" src="application/views/js/ajax2.js"></script>
    <script language="JavaScript" src="application/views/js/ui.js"></script>  
</HEAD>
<BODY>
<!-- .......................... Заголовок .......................... -->
<?php require("templates/menu.htm");?>
<div id=contMain>
<?php include 'application/views/'.$content_view; ?>
</div>
<!-- .......................... Подвал .......................... -->
<!--div id="FooterBar">
</div-->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="application/views/jquery/jquery.min.js"></script>
    <script src="application/views/bootstrap/js/bootstrap.min.js"></script>
    <script src="application/views/bootstrap/js/docs.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="application/views/bootstrap/js/ie10-viewport-bug-workaround.js"></script>
<!-- Script section -->
</BODY>
</HTML>
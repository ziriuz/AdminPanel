<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//RU" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML lang="ru,en">
<HEAD>
    <BASE href="<?=BASE?>"/>
    <TITLE><?=$TITLE?></TITLE>
    <META content="text/html" charset="utf-8" http-equiv="Content-Type" />
    <!-- Custom styles for this template -->
    <link rel="stylesheet" href="application/views/css/common.css"/>
</HEAD>
<BODY>
<?php if ($exception): ?>
 <link href="application/views/bootstrap/css/bootstrap.min.css" rel="stylesheet">
 <div class="alert alert-danger" role="alert"><?= $message ?></div>
<?php else: ?>
 <?php include 'application/views/' . $content_view; ?>
<?php endif; ?>
</BODY>
</HTML>
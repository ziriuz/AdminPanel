<?php
  session_start();
	include("includes/functions.php");
	include("includes/articles.php");

  if(!$USER=displayLogin()){ if(isset($sql)) $sql->close(); exit;}

  //----------------------------------------------------------------------------

  $SHOWARTICLE = false;
  $SHOWSECTION = false;
  $SHOWDROPCONF = false;
  $ARTSECTIONS = getSections();
  $MESSAGE = '';
  if (isset($section)){
    $SECTION = new section($section);
    $SHOWSECTION = true;
    $ARTICLES = $SECTION->getSectionArticlesXML();
    if (isset($article)){
      if (isset($ARTICLES[$article])){
        $ARTICLE = $ARTICLES[$article];
        if ($ARTICLE->exists){
				  $SHOWARTICLE = true;
					$ARTICLE->loadComments();
		}
      }

     	if (isset($action) && $action == 'modify'){
        //�������� ���������
        if (!isset($ARTICLE)){
          $ARTICLE = new article($section,$fname,true);
          $article = $fname;
        }
        $ARTICLE->name = $fname;
        $ARTICLE->title = $ftitle;
        $ARTICLE->date = $fdate;
        $ARTICLE->author = $fauthor;
        $ARTICLE->keywords = $fkeywords;
        $ARTICLE->description = $fdescription;
        $ARTICLE->mainpage = (isset($fmainpage)&&'main'==$fmainpage?'main':null);
        $ARTICLE->width = $fwidth;
        $ARTICLE->orderpage = (isset($forderpage)&&'yes'==$forderpage?'yes':'no');
        

        preg_match('/^([a-z]|[0-9]|_|-)*/', $ARTICLE->name, $matches);
        if ($ARTICLE->name == 'noname' || $matches[0] != $ARTICLE->name){ $MESSAGE = '�������� ID ������';}
        else{
          preg_match('/^([a-z]|[A-Z]|[�-�]|[�-�]|[0-9]|[-_ .,?!:;\/@#$%*()+=�"\'])*/', $ARTICLE->title, $matches);
          if ($matches[0] != $ARTICLE->title){ $MESSAGE = '������������ ������� � ��������� ������.';}
          preg_match('/^([a-z]|[A-Z]|[�-�]|[�-�]|[0-9]|[-_ .,?!:;\/@#$%*()+=�"\'])*/', $ARTICLE->keywords, $matches);
          if ($matches[0] != $ARTICLE->keywords){ $MESSAGE .= ' ������������ ������� � ���� keywords.';}
          preg_match('/^([a-z]|[A-Z]|[�-�]|[�-�]|[0-9]|[-_ .,?!:;\/@#$%*()+=�"\'])*/', $ARTICLE->description, $matches);
          if ($matches[0] != $ARTICLE->description){ $MESSAGE .= ' ������������ ������� � ���� description.';}
          preg_match('/^([a-z]|[A-Z]|[�-�]|[�-�]|[0-9]|[-_ .,?!:;\/@#$%*()+=�"\'])*/', $ARTICLE->author, $matches);
          if ($matches[0] != $ARTICLE->author){ $MESSAGE .= ' ������������ ������� � ���� author.';}
          preg_match('/^([a-z]|[A-Z]|[�-�]|[�-�]|[0-9]|[. -])*/', $ARTICLE->date, $matches);
          if ($matches[0] != $ARTICLE->date){ $MESSAGE .= ' ������������ ������� � ����.';}
        }
        if (strlen($MESSAGE) == 0) $ARTICLE->save();
        //if ($ARTICLE->exists) $SHOWARTICLE = true;
        $SHOWARTICLE = true;
     	}
		if (isset($action) && $action == 'modifytext'){
          //�������� ���������
          $ARTICLE->saveContent($fartcontent);
     	}
		if (isset($action) && $action == 'drop_article'){
          //�������� ���������
		  $SHOWARTICLE = false;
		  if(isset($ARTICLE)){
		    if(isset($conform) && $conform='yes'){if($ARTICLE->drop()) unset($ARTICLES[$article]);}
			else $SHOWDROPCONF=true;			
		  }
     	}
    }elseif (isset($action) && $action == 'savesection'){
      //�������� ��� � XML
      $ARTICLES = $SECTION->getSectionArticlesINI();
      foreach($ARTICLES as $art) $art->save(false);
      $ARTICLES = $SECTION->getSectionArticlesXML();
    }elseif (isset($action) && $action == 'newarticle'){
      //������� ����� ������
      $ARTICLE = new article($SECTION->name,'noname',true);
      $SHOWARTICLE = true;
    }
    

  } else {
    $ORDER_ARTICLES = getOrderArticles();
  }
    header('Content-type: text/html; charset=windows-1251');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//RU" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML lang="ru,en">
<HEAD>
    <TITLE>������ ���������� ���������</TITLE>
    <META content="text/html"; charset="windows-1251" http-equiv="Content-Type" />
    <link rel="stylesheet" href="prd_main.css"/>
    <!--[if IE7]><link rel="stylesheet" type="text/css" href="prd_main_ie.css" media="all" /><![endif]-->
    <script language="JavaScript" src="ajax.js"></script>
</HEAD>
<BODY>
<!-- .......................... ��������� .......................... -->
<?php require("templates/menu.htm");?>
<div id=contMain>
<table border="0" cellspacing="0" cellpadding="0"  width=100%>
 <tr>
  <!-- .......................... ����� ���� .......................... -->
  <td valign=top width=170px>
    <div class=nav>
    <h3>������� ������</h3>
    <?php foreach($ARTSECTIONS as $SNAME => $STITLE):?>
    <div class=navItem>
      <a href=articles.php?section=<?=$SNAME?>><?=$STITLE?></a>
    </div>
    <?php endforeach ?>
   </div>
  </td>
  <!-- .......................... ���������� .......................... -->
  <td valign=top>
    <div id=catContent>
     <br>
	 <?php if($SHOWDROPCONF):?> 
	    <b>
	    <font color=red>�� ������������� ������ ������� ������?</font>
		<br><font color=blue>[<?=$ARTICLE->name?>]</font>
		<br><font color=black><?=$ARTICLE->title?></font>
		<br>
		</b>
		<br>
		<a href=articles.php?section=<?=$SECTION->name?>&article=<?=$ARTICLE->name?>&action=drop_article&conform=yes>�������</a>
		<a href=articles.php?section=<?=$SECTION->name?>>������</a>
     <?php elseif($SHOWARTICLE):?>
       <form action=articles.php?section=<?=$SECTION->name?>&article=<?=$ARTICLE->name?> method=post>
       <input type=hidden name=action value="modify">
       <div class=blockhead>���������</div>
       <div class=blockedit>
         <p><font color=red><?=$MESSAGE?></font></p>
         <p><label>ID</label><input type=text name=fname size=60 value="<?=$ARTICLE->name?>">
           <input type="checkbox" name="fmainpage" value="main" <?=('main'==$ARTICLE->mainpage?'checked':'')?>><label>���������� �� �������</label>
					 <input type=text name=fwidth size=10 value="<?=$ARTICLE->width?>">
         </p>
         <p><label>���������</label><input type=text name=ftitle size=100 value="<?=$ARTICLE->title?>"></p>
         <p><label>����</label><input type=text name=fdate size=20 value="<?=$ARTICLE->date?>">
         <label>�����</label><input type=text name=fauthor size=50 value="<?=$ARTICLE->author?>"> </p>
         <p><input type="checkbox" name="forderpage" value="yes" <?=('yes'==$ARTICLE->orderpage?'checked':'')?>><label>���������� �� �������� ������������ ������</label></p>
         <p><label>META.keywords</label><br><input type=text name=fkeywords size=100 value="<?=$ARTICLE->keywords?>"></p>
         <p><label>META.description</label><br><textarea name=fdescription cols=100 rows=5><?=$ARTICLE->description?></textarea></p>
         <p><input type=submit name="btnsave" value="��������� ���������"></p>
       </div>
       </form>
       <div class=blockhead>����������</div>
       <div class=blockedit>
         <form action="articles.php?section=<?=$SECTION->name?>&article=<?=$ARTICLE->name?>" method=post>
          <input type=hidden name=action value="modifytext">
          <p>
           <input type=submit name="btnsave" value="��������� ������"> <a target=previewarticle href=<?=$ARTICLE->preview_link?>>��������</a>
          </p>
          <p>
          <textarea id=article_content cols=150 rows=30 name=fartcontent><?php $ARTICLE->getContent(); ?></textarea>
          </p>
          <p>
           <input type=submit name="btnsave" value="��������� ������">
          </p>		  
          </form>
		  <div>
		   <form  action="index.php" method="post" name=load_file<?=$ARTICLE->name?>>
           <input type="hidden" name = "article" value="../articles/<?=$ARTICLE->section?>"/>
		   <input type="hidden" name = "action" value="load_photo"/>
           <label>��������� ����</label><input name="itemPhoto" type="file" size="40"/>
           <a href="javascript:loadFile('<?=$ARTICLE->name?>')"><img src="images/btn-save.jpg" alt="��������� ���������"></a>
           <em id="ajaxmes_load_file<?=$ARTICLE->name?>"></em>
           </form>
          </div>
       </div>
       <div class=blockhead>�����������</div>
        <div class=blockedit>
         <form action="articles.php?section=<?=$SECTION->name?>&article=<?=$ARTICLE->name?>" method=post>
          <input type=hidden name=action value="modifycomments">
          <div class=articlelist>
          <?php foreach($ARTICLE->comments as $comment):?>
					<div id=comment<?=$comment->id?> class="article comment<?=($comment->status==1?' good_comment':'')?>">
          <p> 
					 <?=$comment->id?> <strong><?=$comment->username?></strong> | <?=$comment->add_time?> | <?=$comment->email?> 
				   <br><span><?=$comment->subject?></span> |
					 <span id=message<?=$comment->id?>>
					  <?php if($comment->status==0): ?>
					   <a href="javascript:modifyComment('<?=$ARTICLE->section?>','<?=$ARTICLE->name?>',<?=$comment->id?>,1)">���������</a>
					  <?php else:?>						 					  
					   <a href="javascript:modifyComment('<?=$ARTICLE->section?>','<?=$ARTICLE->name?>',<?=$comment->id?>,0)">��������</a>
					  <?php endif?>						 
					 </span>
           <br><?=$comment->text?>					
					</p>
					</div>
          <?php endforeach?>
          </div>
         </form>
        </div>
     <?php elseif ($SHOWSECTION):?>
     <h3> <?=$SECTION->title?> </h3>
     <br>
     <form action=articles.php?section=<?=$SECTION->name?> method=post>
       <input type=hidden name=action value="savesection">
       <input type=submit name="btnsave" value="��������� �� INI">
     </form>
     <form action=articles.php?section=<?=$SECTION->name?> method=post>
       <input type=hidden name=action value="newarticle">
       <input type=submit name="btnsave" value="�������">
     </form>
     <br>
     <div class=articlelist>
      <?php foreach($ARTICLES as $ANAME => $ARTICLE):?>
      <div class="article<?=(!$ARTICLE->text_exists?' no_text':'');?><?=('main'==$ARTICLE->mainpage?' on_main':'');?>">
		<a href=articles.php?section=<?=$SECTION->name?>&article=<?=$ARTICLE->name?> ><strong><?=$ARTICLE->title?> <?php if($ARTICLE->orderpage=='yes'):?><img border="0" src="images/ord.jpg"/><?php endif;?></strong></a>
        <a href=articles.php?section=<?=$SECTION->name?>&article=<?=$ARTICLE->name?>&action=drop_article>�������</a>
        <p><span><?=$ARTICLE->author?></span> | <?=$ARTICLE->date?> 
           <a href=articles.php?section=<?=$SECTION->name?>&article=<?=$ARTICLE->name?> ><img border="0" src="images/btn_more.gif" align="absmiddle" width="11" height="11" /></a>
        </p>
      </div>
      <?php endforeach;?>
     </div>
     <?php else:?>
      <h3>��� ������ ����� �������� ���������� ����� ���������� ������</h3>
	  <br>
	  <div class=blockedit><form>���������� <input type=text value=3 size=5> ������, ��������� � ��������� ������� </form></div>
	  <br>
      <?php foreach($ORDER_ARTICLES as $ANAME => $ARTICLE):?>
	  <div class="article<?=(!$ARTICLE->text_exists?' no_text':'');?><?=('main'==$ARTICLE->mainpage?' on_main':'');?>">
        <a href=articles.php?section=<?=$ARTICLE->section?>&article=<?=$ARTICLE->name?> ><strong><?=$ARTICLE->title?></strong></a>
        <p><span><?=$ARTICLE->author?></span> | <?=$ARTICLE->date?>
           <a href=articles.php?section=<?=$ARTICLE->section?>&article=<?=$ARTICLE->name?> ><img border="0" src="images/btn_more.gif" align="absmiddle" width="11" height="11" /></a>
        </p>
		<p>
		  <?=$ARTICLE->intro?>
		</p>
      </div>
      <?php endforeach;?>
     <?php endif;?>
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
<?php if(isset($sql)) $sql->close();?>
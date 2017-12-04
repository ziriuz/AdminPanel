<?php

$gsSitePath = '..';
$gsArticlesPath = $gsSitePath.'/articles';
$months = array('января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');
class xmlDoc{  
  protected $docSection;
  protected $siteCode = "WINDOWS-1251";
  protected $xmlCode = "UTF-8";

  //------------------------------
  protected function getChildNodeByAttr($node,$childName,$attrName,$attrValue){
   $nodes = $node->getElementsByTagName($childName);
   $retNode = null;
   foreach ($nodes as $item)
     if ($item->getAttribute($attrName) == $attrValue){
       $retNode = $item;
       break;
   }
   return $retNode;
  }
	//------------------------------
  protected function getChildNode($node,$childName){
   $nodes = $node->getElementsByTagName($childName);
   $retNode = null;
   if ($nodes->length == 1) $retNode = $nodes->item(0);
   return $retNode;
  }
  protected function getAttribute($xmlNode,$attr){
    return iconv($this->xmlCode,$this->siteCode,$xmlNode->getAttribute($attr));
  }
  protected function getChildValue($xmlNode,$attr){
    return iconv($this->xmlCode,$this->siteCode,$this->getChildNode($xmlNode,$attr)->nodeValue);
  }
  protected function setAttribute($xmlNode,$attr,$value){
     if (!$xmlNode->hasAttribute($attr)){
	   $attribute = $this->docSection->createAttribute($attr);
         $text = $this->docSection->createTextNode(iconv($this->siteCode,$this->xmlCode,$value));
         $text = $attribute->appendChild($text);
       $attribute = $xmlNode->appendChild($attribute);
	 }else
	   $xmlNode->setAttribute($attr,iconv($this->siteCode,$this->xmlCode,$value));
  }
  protected function setChildValue($xmlNode,$attr,$value){
     if ($xmlNode->getElementsByTagName($attr)->length != 1){
       $childnode = $this->docSection->createElement($attr);
         $text = $this->docSection->createTextNode(iconv($this->siteCode,$this->xmlCode,$value));
         $text = $childnode->appendChild($text);
       $childnode = $xmlNode->appendChild($childnode);
     }else
       $this->getChildNode($xmlNode,$attr)->nodeValue = iconv($this->siteCode,$this->xmlCode,$value);
  }
}
//------------------------------------------------------------------------------
class comment extends xmlDoc{
  public $id;
	public $username;
	public $email;
	public $subject;
	public $text;
	public $add_time;
	public $status;
	//------------------------------
  function __construct($xmlComment) {
    if (!isset($xmlComment)) return null;
    $siteCode = "WINDOWS-1251";
    $xmlCode = "UTF-8";

    $this->id  = $this->getAttribute($xmlComment,'id');
    $this->username = $this->getAttribute($xmlComment,'username');
    $this->email = $this->getAttribute($xmlComment,'email');
		$dt = (int)$xmlComment->getAttribute('add_time');
		$this->add_time = date('D d.m.Y G:i',$dt);
    $this->status = (int) $xmlComment->getAttribute('status');
    $this->subject = $this->getChildValue($xmlComment,'SUBJECT');
    $this->text = nl2br($this->getChildValue($xmlComment,'TEXT'));
    // = iconv($xmlCode,$siteCode,$this->keywords);
  }
	//------------------------------
}
//------------------------------------------------------------------------------
class article extends xmlDoc{
    public $name;
	public $title;
	public $date;
	public $author;
	public $mainpage; // для новостей
	public $width;    // для новостей
	public $orderpage; // показывать на странице заказа
	public $section;
	public $intro;
	public $exists = false;
	public $text_exists = false;
	public $link;
	public $preview_link;
	public $keywords;
	public $description;
	private $xmlSectionFile;
	private $iniSectionFile;
	public $comments = array();
	public $lastComment;
	public $newCommentStatus = 0;

	//------------------------------
  function loadIni($section, $name) {
    global $gsArticlesPath;
    global $gsSitePath;

    if (!isset($section)||!isset($name)) return null;
    $this->section = $section;
    $this->name = $name;
    $this->link = "$gsArticlesPath/$section/$name".'.htm';
    $this->xmlSectionFile = "$gsArticlesPath/$section/$section".'.xml';
    $this->iniSectionFile = "$gsArticlesPath/$section/$section".'.ini';

    if(file_exists($this->link)){
      $this->exists = true;
      $this->intro = $this->getArticlePart();
    }else return null;

    $this->preview_link = $gsSitePath."/article.php?section=$section&article=$name";
    $lProps = explode('|',$this->getIniArticle());
    if (isset($lProps[0])) $this->title = $lProps[0];
    if (isset($lProps[1])) $this->date  = $lProps[1];
    if (isset($lProps[2])) $this->author = $lProps[2];
    if (isset($lProps[3])) $this->mainpage  = $lProps[3];
    if (isset($lProps[4])) $this->width = $lProps[4];
  }
  
  
	//------------------------------
  public function getIniArticle(){

    $lsProp = "";
    if (!file_exists($this->iniSectionFile)) return $lsProp;
    if($f=fopen("$this->iniSectionFile","r")){
      while (!feof ($f)) {
        $buffer = fgets($f, 256);
        $sname = trim(substr($buffer,0,strpos($buffer,"=")));
        $stitle = trim(substr($buffer,strpos($buffer,"=")+1,256));
        if ($stitle != '' && $sname == $this->name){
          $lsProp = $stitle;
          break;
        }
      }
      fclose($f);
	  }
    return $lsProp;
  }  
  function __construct($section, $name,$isAdmin=false) {
    global $gsArticlesPath;
    global $gsSitePath;
    global $months;
    if (!isset($section)||!isset($name)) return null;
    $this->section = $section;
    $this->name = $name;
    $this->link = "$gsArticlesPath/$section/$name".'.htm';
    $this->xmlSectionFile = "$gsArticlesPath/$section/$section".'.xml';
    $dt = getdate();
    $this->date = $dt['mday'].' '.$months[$dt['mon']-1].' '.$dt['year'];

    if(file_exists($this->link)){
      $this->text_exists = true;
      $this->intro = $this->getArticlePart();
    }//else return null;

    $this->preview_link = $gsSitePath."/article.php?section=$section&article=$name";

    $siteCode = "WINDOWS-1251";
    $xmlCode = "UTF-8";
    $this->docSection = new DOMDocument('1.0');
    $docSection = $this->docSection;
    
	if (file_exists($this->xmlSectionFile)){
      $docSection->validateOnParse = true;
      $docSection->load($this->xmlSectionFile);
      $root = $this->getChildNode($docSection,'SECTION');
      if ($root != null){
        $article = $this->getChildNodeByAttr($root,'ARTICLE','name',$this->name);
        if ($article != null){
          $this->date  = $this->getAttribute($article,'date');
          $this->author = $this->getAttribute($article,'author');
          $this->mainpage = ('yes'==$this->getAttribute($article,'mainpage')?'main':'');
          $this->width = $this->getAttribute($article,'width');
          $this->orderpage = $this->getAttribute($article,'orderpage');
          $this->title = $this->getChildValue($article,'TITLE');
          $this->description = $this->getChildValue($article,'DESCRIPTION');
          $this->keywords = $this->getChildValue($article,'KEYWORDS');
          $this->exists = true;
          // = iconv($xmlCode,$siteCode,$this->keywords);
        }
      }
    } elseif (!$isAdmin) $this->loadIni($section, $name);
  }
	//------------------------------
  private function getArticlePart(){
  $lsText = '';
	$lbStart = false;
	$lbEnd = false;
	if($f=fopen($this->link,"r")){
			while (!feof ($f)) {
				$buffer = fread($f, 256);
				if (!$lbStart){
				  //ищем начало краткого содержания статьи
					$pos = strpos ($buffer, "<intro>");
        	if ($pos === false);
					else {
				    $buffer = substr($buffer,$pos+7); // обрежем все что идет до начала
					  $lbStart = true;
					}
				}
				if($lbStart){
				  //ищем конец краткого содержания статьи
        	$pos = strpos ($buffer, "</intro>");
        	if ($pos === false);
					else {
					  $buffer = substr($buffer,0,$pos);
						$lbEnd = true;
					}
					$lsText = $lsText.$buffer;
				}
				if($lbEnd) break;
				/*if (strlen($lsText) >= 256) {
					$buffer = substr($buffer,0,strpos($buffer,' ',1))." ...";
					$lsText = $lsText.$buffer;
					break;
				}else {$lsText = $lsText.$buffer;}*/
			}
		fclose($f);
  }
	return $lsText;
  }
  //------------------------------
  public function save($insertFirst = true){
   $siteCode = "WINDOWS-1251";
   $xmlCode = "UTF-8";
   $docSection = $this->docSection;

   if (file_exists($this->xmlSectionFile)){
       $docSection->validateOnParse = true;
       $docSection->load($this->xmlSectionFile);
   }
   $list = $docSection->getElementsByTagName("SECTION");
   if ($list->length > 0){
    $root = $list->item(0);
   }else{
    $root = $docSection->createElement("SECTION");
    $root = $docSection->appendChild($root);
      $attribute = $docSection->createAttribute('name');
      $attribute = $root->appendChild($attribute);
      $text = $docSection->createTextNode(iconv($siteCode,$xmlCode,$this->section));
      $text = $attribute->appendChild($text);
   }

   $article = $this->getChildNodeByAttr($root,'ARTICLE','name',$this->name);
   if(null == $article){
     $article = $docSection->createElement("ARTICLE");
     if ($insertFirst){
      $list = $root->getElementsByTagName("ARTICLE");
      $article = $root->insertBefore($article,$list->item(0));
     }
     else $article = $root->appendChild($article);
   }   
   $this->setAttribute($article,'name',$this->name);
   $this->setAttribute($article,'date',$this->date);
   $this->setAttribute($article,'author',$this->author);
   $this->setAttribute($article,'mainpage',('main'==$this->mainpage?'yes':'no'));
   $this->setAttribute($article,'width',$this->width);
   $this->setAttribute($article,'orderpage',$this->orderpage);
   $this->setChildValue($article,'TITLE',$this->title);
   $this->setChildValue($article,'DESCRIPTION',$this->description);
   $this->setChildValue($article,'KEYWORDS',$this->keywords);
   
   //$docSection->dump_file($this->xmlSectionFile, false, true);
   $docSection->saveHTMLFile($this->xmlSectionFile);
   $this->exists = true;
    
   if(!file_exists($this->link)){
      if($f=fopen($this->link,"w")){
        fwrite($f,"<intro></intro>");
        fclose($f);
        $this->text_exists = true;
      }
   }
   global $gsArticlesPath;
   global $gsSitePath;
   $this->link = "$gsArticlesPath/$this->section/$this->name".'.htm';
   $this->intro = $this->getArticlePart();
  }
 	//------------------------------
  public function saveContent($content){
   $siteCode = "WINDOWS-1251";
   $xmlCode = "UTF-8";
   if($f=fopen($this->link,"w")){
			fwrite($f, $content);
   		fclose($f);
   }
  }
 	//------------------------------
  public function getContent(){
   if(file_exists($this->link)){
     include($this->link);
   }
  }
 	//------------------------------
  public function drop(){
   if (file_exists($this->xmlSectionFile)){
       $this->docSection->validateOnParse = true;
       $this->docSection->load($this->xmlSectionFile);
	   if (null != $root = $this->getchildNode($this->docSection,'SECTION')){
	     if(null != $article = $this->getChildNodeByAttr($root,'ARTICLE','name',$this->name)){
		   $root->removeChild($article);
		   $this->docSection->saveHTMLFile($this->xmlSectionFile);		   
		   if(file_exists($this->link)) unlink($this->link);
		   return true;
		 } 
	   }
   }
   return false;   
  }

	//------------------------------
  public function loadComments($aStatus = 0){
    $this->comments = array();
		$this->lastComment = 0;
		global $gsArticlesPath;
    if (!$this->exists) return null;
    $xmlCommentsFile = "$gsArticlesPath/$this->section/$this->name".'.xml';
    $docComments = new DOMDocument('1.0');
    if (file_exists($xmlCommentsFile)){
      $docComments->validateOnParse = true;
      $docComments->load($xmlCommentsFile);
      $root = $this->getChildNode($docComments,'COMMENTS');
      if ($root!=null){
        $comments = $root->getElementsByTagName("COMMENT");
        foreach($comments as $item){
          $id = $item->getAttribute('id');
					$status = $item->getAttribute('status');
					if($id!=null){
						$this->lastComment = max($id,$this->lastComment);
            if ($aStatus == 0 || $aStatus == $status)
              $this->comments[$id] = new comment($item);
          }
        }
      }
    }
  }
	//------------------------------
  public function addComment($aUsername,$aEmail,$aSubject,$aText,$insertFirst = true){

   $aUsername = str_replace('"',"",strip_tags($aUsername));
   $aEmail = str_replace('"',"",strip_tags($aEmail));
   $aSubject = strip_tags($aSubject);
   $aText = strip_tags($aText);

   $siteCode = "WINDOWS-1251";
   $xmlCode = "UTF-8";
   $docSection = new DOMDocument('1.0');
	 global $gsArticlesPath;
	 $xmlCommentsFile = "$gsArticlesPath/$this->section/$this->name".'.xml';
   if (file_exists($xmlCommentsFile)){
       $docSection->validateOnParse = true;
       $docSection->load($xmlCommentsFile);
   }
   $root = $this->getChildNode($docSection,'COMMENTS');
   if ($root==null){
    $root = $docSection->createElement("COMMENTS");
    $root = $docSection->appendChild($root);
   }

   $comment = $docSection->createElement("COMMENT");
   $id = $this->lastComment + 1;
   if ($insertFirst){
      $list = $root->getElementsByTagName("COMMENT");
      $comment = $root->insertBefore($comment,$list->item(0));
   }else $comment = $root->appendChild($comment);
   $attribute = $docSection->createAttribute('id');
     $text = $docSection->createTextNode(iconv($siteCode,$xmlCode,$id));
     $text = $attribute->appendChild($text);
   $attribute = $comment->appendChild($attribute);
   $attribute = $docSection->createAttribute('username');
     $text = $docSection->createTextNode(iconv($siteCode,$xmlCode,$aUsername));
     $text = $attribute->appendChild($text);
   $attribute = $comment->appendChild($attribute);
   $attribute = $docSection->createAttribute('email');
     $text = $docSection->createTextNode(iconv($siteCode,$xmlCode,$aEmail));
     $text = $attribute->appendChild($text);
   $attribute = $comment->appendChild($attribute);
   $attribute = $docSection->createAttribute('add_time');
     $text = $docSection->createTextNode(time());
     $text = $attribute->appendChild($text);
   $attribute = $comment->appendChild($attribute);
   $attribute = $docSection->createAttribute('status');
     $text = $docSection->createTextNode($this->newCommentStatus);
     $text = $attribute->appendChild($text);
   $attribute = $comment->appendChild($attribute);
   $element = $docSection->createElement("SUBJECT");
     $text = $docSection->createTextNode(iconv($siteCode,$xmlCode,$aSubject));
     $text = $element->appendChild($text);
   $element = $comment->appendChild($element);
   $element = $docSection->createElement("TEXT");
      $text = $docSection->createTextNode(iconv($siteCode,$xmlCode,$aText));
      $text = $element->appendChild($text);
   $element = $comment->appendChild($element);

     //$docSection->dump_file($this->xmlSectionFile, false, true);
    $docSection->saveHTMLFile($xmlCommentsFile);
  }
	//------------------------------
  function modifyComment($commid,$status){
   if (!isset($commid)||!isset($status)||!($status==1||$status==0)) return false;
   $docSection = new DOMDocument('1.0');
	 global $gsArticlesPath;
	 $xmlCommentsFile = "$gsArticlesPath/$this->section/$this->name".'.xml';
   if (file_exists($xmlCommentsFile)){
       $docSection->validateOnParse = true;
       $docSection->load($xmlCommentsFile);
   } else return false;
	 	 
   $root = $this->getChildNode($docSection,'COMMENTS');
   if ($root==null) return false;
   $comment = $this->getChildNodeByAttr($root,'COMMENT','id',$commid);
	 if ($comment==null) return false;
   $comment->setAttribute('status',$status);
   $docSection->saveHTMLFile($xmlCommentsFile);
	 return true;
	}
}
//------------------------------------------------------------------------------
class section  extends xmlDoc{
  public $name;
	public $title;
	public $art_count;
	public $link;
	public $preview_link;
	public $desc_link;
	public $exists = false;
	private $xmlSectionFile;
	//------------------------------
  function __construct($name) {
    global $gsArticlesPath;
    global $gsSitePath;

    if (!isset($name)) return null;
		if ($name=='news') $sections[$name] = 'Новости';
		else $sections = getSections();
		if (!isset($sections[$name])) return null;
    $this->name = $name;
    $this->title = $sections[$name];
    $this->link = "$gsArticlesPath/$name/$name.ini";
    $this->desc_link = "$gsArticlesPath/$name/$name.htm";
    $this->xmlSectionFile = "$gsArticlesPath/$name/$name".'.xml';
    
    if(file_exists($this->link) || file_exists($this->xmlSectionFile)) $this->exists = true;
    else return null;

    $this->art_count = $this->getArticlesCount();
    $this->preview_link = $gsSitePath."/article.php?section=$name";
  }
 	//------------------------------
  public function getDescription(){
   if(file_exists($this->desc_link)){
     include($this->desc_link);
   }
	 return '';
  }
  //------------------------------
  private function getArticlesCount(){
  $count=0;
  if(!file_exists($this->link)) return $count;
	$f=fopen($this->link,"r");
  while (!feof ($f)) {
    $buffer = fgets($f, 256);
    $sname = trim(substr($buffer,0,strpos($buffer,"=")));
    $stitle = trim(substr($buffer,strpos($buffer,"=")+1,256));
    if ($stitle != '') $count++;
  }
  fclose($f);
	return  $count;
  }
	//------------------------------
  public function getSectionArticlesINI(){

    $this->art_count = 0;
    $laArticles = array();
    if (!$this->exists) return $laArticles;
    if($f=fopen("$this->link","r")){
      $i=0;
      while (!feof ($f)) {
        $buffer = fgets($f, 256);
        $sname = trim(substr($buffer,0,strpos($buffer,"=")));
        $stitle = trim(substr($buffer,strpos($buffer,"=")+1,256));
        if ($stitle != ''){
          $laArticles[$sname] = new article($this->name,$sname);
          $laArticles[$sname]->loadIni($this->name,$sname);
          $this->art_count++;
        }
      }
      fclose($f);
	  }
    return $laArticles;
  }
	//------------------------------
  public function getSectionArticlesXML(){

    $laArticles = array();
    if (!$this->exists) return $laArticles;
    
    $docSection = new DOMDocument('1.0');
    if (file_exists($this->xmlSectionFile)){
      $docSection->validateOnParse = true;
      $docSection->load($this->xmlSectionFile);
      $list = $docSection->getElementsByTagName("SECTION");
      if ($list->length > 0){
        $root = $list->item(0);
        $articles = $root->getElementsByTagName("ARTICLE");
        $this->art_count = 0;
        foreach($articles as $article){
          $n = $article->getAttribute('name');
          if ($n != null){
            $laArticles[$n] = new article($this->name,$n,'');
            $this->art_count++;
          }
        }
      }
    }
    return $laArticles;
  }
	//------------------------------
  public function getSectionArticles(){
    if (file_exists($this->xmlSectionFile)) $laArticles = $this->getSectionArticlesXML();
		else $laArticles = $this->getSectionArticlesINI();
    return $laArticles;
  }
}
//------------------------------------------------------------------------------
function getSections(){
 global $gsArticlesPath;
 $laSections = array();
 $f=fopen("$gsArticlesPath/sections.ini","r");
 while (!feof ($f)) {
  $buffer = fgets($f, 256);
  $sname = trim(substr($buffer,0,strpos($buffer,"=")));
  $stitle = trim(substr($buffer,strpos($buffer,"=")+1,256));
  if ($stitle != '') $laSections[$sname] = $stitle;
 }
 fclose($f);
 
 $laSections['news'] = 'Новости';
 
 return $laSections;
}
//------------------------------------------------------------------------------
function getNmclArticles($aNmclID){
  $laRet = array();
  return $laRet; //Временно отключаем, нужна оптимизация (сделать таблицу связки статьи и номенклатуры, заполнять при сохранении статьи)
  if(!isset($aNmclID) || $aNmclID <= 0) return $laRet;
  $item_id = (int)$aNmclID;
  $sections = getSections();
  foreach($sections as $sname => $stitle){
	  $section = new section($sname);
	  $articles = $section->getSectionArticlesXML();
		foreach($articles as $ART){	
		 if(file_exists($ART->link))	  
		 if($f=fopen($ART->link,"r")){
           while (!feof ($f)) {
		    $buffer = fgets($f, 2000);
            if (strpos($buffer,"list.php?")!==false && strpos($buffer,"item_id=".$item_id)!==false){
			  $laRet[$ART->preview_link] = $ART->title; 
			  break;
	        }
           }
           fclose($f);
		 }
		}
  }
  return $laRet;
}
function getCategoryArticles($aCtgID){
  $laRet = array();
  return $laRet; //Временно отключаем, нужна оптимизация (сделать таблицу связки статьи и номенклатуры, заполнять при сохранении статьи)  
  if(!isset($aCtgID) || $aCtgID <= 0) return $laRet;
  $item_id = (int)$aCtgID;
  $sections = getSections();
  foreach($sections as $sname => $stitle){
	  $section = new section($sname);
	  $articles = $section->getSectionArticlesXML();
		foreach($articles as $ART){	
		 if(file_exists($ART->link))	  
		 if($f=fopen($ART->link,"r")){
           while (!feof ($f)) {
		    $buffer = fgets($f, 2000);
            if (strpos($buffer,"list.php?")!==false && strpos($buffer,"ctg_id=".$item_id)!==false){
			  $laRet[$ART->preview_link] = $ART->title; 
			  break;
	        }
           }
           fclose($f);
		 }
		}
  }
  return $laRet;
}
function getOrderArticles(){
  $laRet = array();
  $sections = getSections();
  foreach($sections as $sname => $stitle){
	  $section = new section($sname);
	  $articles = $section->getSectionArticlesXML();
	  foreach($articles as $ART)	
	   if($ART->text_exists && $ART->orderpage == 'yes')
	    $laRet["$sname/$ART->name"] = $ART;
  }
  return $laRet;
}
?>
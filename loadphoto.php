<?php 
  session_start();
  include("includes/functions.php");
  header('Content-type: text/html; charset=utf-8');
  //if(!$USER=displayLogin()){if(isset($sql)) $sql->close(); exit;}
  $ISAJAX = true;
   $gMessage='';
   $gsFldr = '../prd_lib/images';
   $target = 'ITEM';
   if (isset($_POST["article"])){
     $gsFldr=$_POST["article"];
	 $target = 'ARTICLE';
   }
   $fileName = '';
   $fileFullName = '';
   $filePreview = '';
   $filePreview96 = '';
   $res ='FAILED'; 
   $item = $_POST["fitem_id"];
   function copyimage($asSrc,$asDest,$aiSize){
    $size = GetImageSize($asSrc);
    if ($size[0] > $aiSize || $size[1] > $aiSize){
      if (!$im = @ImageCreateFromJPEG ($asSrc)) return false;
      if ($size[0] > $size[1]){
        $w = $aiSize;
        $h = round($aiSize * $size[1] /  $size[0],0);
      } else {
        $h = $aiSize;
        $w = round($aiSize * $size[0] /  $size[1],0);
      }
      if (!$im_d = @ImageCreateTrueColor ($w, $h)) return false;
      ImageCopyResampled($im_d, $im,0,0,0,0, $w, $h, $size[0], $size[1]);
      imagejpeg($im_d,$asDest,100);
    }else return copy($asSrc,$asDest);
    return true;
   }
 if (!isset($item)) $gMessage='ID nomenclature not defined';
 elseif(isset($_POST["action"])&&$_POST["action"]=='load_photo'){
   $gsSType=strtolower(substr($_FILES['itemPhoto']['name'],strrpos($_FILES['itemPhoto']['name'],'.')+1));
   if (strpos('jpeg jpg jpe',$gsSType)===false) $gMessage = "Указан неверный формат файла";
   else{
    $fileName = $item.'_'.$_FILES['itemPhoto']['name'];
    $fileFullName=$gsFldr.'/'.$fileName;
    $filePreview=$gsFldr.'/140/'.$fileName;
    $filePreview96=$gsFldr.'/96/'.$fileName;
	if($target == 'ITEM'){
     if (!copyimage($_FILES['itemPhoto']['tmp_name'],$fileFullName,800))
      $gMessage = "Не удалось загрузить фото";
     elseif(!copyimage($_FILES['itemPhoto']['tmp_name'],$filePreview,140))
      $gMessage = "Не удалось загрузить фото preview";
     else {
	  copyimage($_FILES['itemPhoto']['tmp_name'],$filePreview96,96);
      // обновить в базе инфо о фото
      $ITEM = new Item($item);
      $ITEM->foto_name_src = (strlen($ITEM->foto_name_src)==0?$fileName:$ITEM->foto_name_src.'|'.$fileName);
        if (!$ITEM->updatePhotoName()) $gMessage=$ITEM->getMessage();
    }
   } else{
     if (!copyimage($_FILES['itemPhoto']['tmp_name'],$fileFullName,800))
      $gMessage = "Не удалось загрузить фото";
   }
   }
   if (strlen($gMessage)==0){
     $res='OK'; 
	 if($target == 'ITEM')  $gMessage = "<a href=\"$fileFullName\"><img id=\"itemimg$item\" src=\"$filePreview\"></a>";
	 else $gMessage = "<img id=\"itemimg$item\" src=\"$fileFullName\">";
   };
 }
?>
load_photo<?=($target == 'ARTICLE'?'_article':'')?>|<?=$res?>|<?=$item?>|<?=$fileFullName?>:::<?=$gMessage?>

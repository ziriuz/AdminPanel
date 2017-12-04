<form method=post class="form-inline">
  <input id=fnmcl_id<?=$DEF_ID?> type=hidden name=fnmcl_id  size="20" value="">
  <div class="form-group form-group-sm">
    <label class="sr-only" for="item<?=$DEF_ID?>">Поиск по ключу</label>
    <div class="input-group">
      <input placeholder="Поиск номенклатуры" autocomplete="off" id=item<?=$DEF_ID?> type=text name=fnmcl_name class="form-control" size="20" value="" onkeyupp = "changeEditMode(<?=$DEF_ID?>,'findnmcl','search','item<?=$DEF_ID?>')">
      <div class="input-group-addon">
        <a href="javascript:changeEditMode(<?=$DEF_ID?>,'findnmcl','search','item<?=$DEF_ID?>')">
	<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
        <!--img src="images/btn-find.jpg" alt="Найти товар"-->
	</a>      
      </div>
    </div>
    <span id=ajaxmesfindnmcl<?=$DEF_ID?>></span>
  </div>
</form>
<div id=findnmcl<?=$DEF_ID?> class=searchlist></div>        


	//============
	//
	// ShowImage
	//
	//============
function ShowImage(id, xpos, ypos, w, h){ 
  		window.open(id,'','top=' + ypos + ',left=' + xpos + ',width=' + w + ',height=' + h + ',scrollbars=yes,location=no,toolbar=no,directories=no,status=yes,menubar=no,resizable=yes');
	}

function getHTTPRequestObject() {
  var xmlHttpRequest;
  /*@cc_on
  @if (@_jscript_version >= 5)
  try {
    xmlHttpRequest = new ActiveXObject("Msxml2.XMLHTTP");
  } catch (exception1) {
    try {
      xmlHttpRequest = new ActiveXObject("Microsoft.XMLHTTP");
    } catch (exception2) {
      xmlHttpRequest = false;
    }
  }
  @else
    xmlhttpRequest = false;
  @end @*/

  if (!xmlHttpRequest && typeof XMLHttpRequest != 'undefined') {
    try {
      xmlHttpRequest = new XMLHttpRequest();
    } catch (exception) {
      xmlHttpRequest = false;
    }
  }
  return xmlHttpRequest;
}
//------------------------------------------------------------------
var httpRequester = getHTTPRequestObject(); /* Когда страница
                            загрузилась, создаем xml http объект */
var couldProcess = false;
function modifyComment(section,article,commID,status) {
	//var qty = document.getElementById("qty"+itemID).value;
  serverSideURL = "ajax.php?action=modify_comment&section="+section+"&article="+article+"&commid=" + commID + "&status=" + status;
  //document.getElementById('messageBody').value = "test";//
  if (!couldProcess && httpRequester) {
    httpRequester.open("GET", serverSideURL, true);
    httpRequester.setRequestHeader('Content-Length', '10');

    httpRequester.onreadystatechange = processResponse;
    couldProcess = true;
    httpRequester.send(null);
  }
}
function changeEditMode(itemID, token, mode, exparam) {
	//var qty = document.getElementById("qty"+itemID).value;
  serverSideURL = "ajax.php?action=change_edit_mode&fitem_id="+itemID+"&ftoken=" +token+"&fmode=" + mode;
  if (exparam!=undefined){
    if(document.getElementById(exparam)!= null)
     expar = document.getElementById(exparam).value;
	else expar=exparam;
	
	serverSideURL += "&exparam="+expar;
  }
  //document.getElementById('messageBody').value = "test";//
  if (!couldProcess && httpRequester) {
	document.getElementById("ajaxmes"+token+itemID).innerHTML = '<img src=images/busy.gif>';
    httpRequester.open("GET", serverSideURL, true);
    //httpRequester.setRequestHeader('Content-Length', '10');

    httpRequester.onreadystatechange = processResponse;
    couldProcess = true;
    httpRequester.send(null);
  }
}
function toUtf8(str){
var uni = "";
for (var i=0; i<str.length; i++)
{
	uni += '&#'+str.charCodeAt(i)+';';
}
return uni;
}
function getRequestBody(oForm) {
  var aParams = new Array();
  for(var i = 0; i < oForm.elements.length; i++) {
    if (oForm.elements[i].type == 'radio'){
	  if (!oForm.elements[i].checked) continue;
	}
    var sParam = encodeURIComponent(oForm.elements[i].name);
    sParam += "=";
	if (oForm.elements[i].type == 'checkbox'){
	  if (oForm.elements[i].checked) sParam += encodeURIComponent(1);
	  else sParam += encodeURIComponent(0);
	}else
    sParam += encodeURIComponent(oForm.elements[i].value);
    aParams.push(sParam);
  }
  return aParams.join("&");
}
function modifyItem(itemID) {
	//var qty = document.getElementById("qty"+itemID).value;
  serverSideURL = "ajax.php?action=modify_item&fitem_id="+itemID;
	var oForm = document.forms["modifyitem"+itemID];
	if (oForm == undefined){
	  document.getElementById("ajaxmesitem"+itemID).innerHTML = 'Переключитесь в режим редактирования';
	  return;
	}
  var sBody = getRequestBody(oForm);
  //document.getElementById('messageBody').value = "test";//
  if (!couldProcess && httpRequester) {
    document.getElementById("ajaxmesitem"+itemID).innerHTML = '<img src=images/busy.gif>';
    httpRequester.open("POST", serverSideURL, true);
		httpRequester.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		httpRequester.setRequestHeader("Content-length", sBody.length);
		httpRequester.setRequestHeader("Connection", "close");
		httpRequester.send(sBody);

		httpRequester.onreadystatechange = processResponse;
    couldProcess = true;
  }
}
//------------------------------------------------------------------
function addTransaction(itemID) {
	//var qty = document.getElementById("qty"+itemID).value;
  serverSideURL = "ajax.php?action=add_transaction&fitem_id="+itemID;
	var oForm = document.forms["addwrhtrans"+itemID];
	if (oForm == undefined){
	  document.getElementById("ajaxmesnewtran"+itemID).innerHTML = 'Переключитесь в режим редактирования';
	  return;
	}
  var sBody = getRequestBody(oForm);
  //document.getElementById('messageBody').value = "test";//
  if (!couldProcess && httpRequester) {
    document.getElementById("ajaxmesnewtran"+itemID).innerHTML = '<img src=images/busy.gif>';
    httpRequester.open("POST", serverSideURL, true);
		httpRequester.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		httpRequester.setRequestHeader("Content-length", sBody.length);
		httpRequester.setRequestHeader("Connection", "close");
		httpRequester.send(sBody);

		httpRequester.onreadystatechange = processResponse;
    couldProcess = true;
  }
}
//------------------------------------------------------------------
var is_file_uploaded = false;
function on_file_load() {
if ( ! is_file_uploaded ) {
is_file_uploaded = true;
return;
}
// этот код выполнится после полной загрузки файла
is_file_uploaded = false;
alert("Файл загружен!");
}
//-----------------------------------------------------------------
function getCaret(el) {
  if (el.selectionStart) {
    return el.selectionStart;
  } else if (document.selection) {
    el.focus();
 
    var r = document.selection.createRange();
    if (r == null) {
      return 0;
    }
 
    var re = el.createTextRange(),
        rc = re.duplicate();
    re.moveToBookmark(r.getBookmark());
    rc.setEndPoint('EndToStart', re);
 
    return rc.text.length;
  } 
  return 0;
}
function setSelectionRange(input, selectionStart, selectionEnd) {
  if (input.setSelectionRange) {
    input.focus();
    input.setSelectionRange(selectionStart, selectionEnd);
  }
  else if (input.createTextRange) {
    var range = input.createTextRange();
    range.collapse(true);
    range.moveEnd('character', selectionEnd);
    range.moveStart('character', selectionStart);
    range.select();
  }
}
 
function setCaretToPos (input, pos) {
  setSelectionRange(input, pos, pos);
}
function insertAtCaret(myValue){
            if (document.selection) {
                // Для браузеров типа Internet Explorer
                this.focus();
                var sel = document.selection.createRange();
                sel.text = myValue;
                this.focus();
            }
            else if (this.selectionStart || this.selectionStart == '0') {
                // Для браузеров типа Firefox и других Webkit-ов
                var startPos = this.selectionStart;
                var endPos = this.selectionEnd;
                var scrollTop = this.scrollTop;
                this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
                this.focus();
                this.selectionStart = startPos + myValue.length;
                this.selectionEnd = startPos + myValue.length;
                this.scrollTop = scrollTop;
            } else {
                this.value += myValue;
                this.focus();
            }
        }
var article_pos = 0;
//------------------------------------------------------------------
function loadFile(itemID) {
//http://habrahabr.ru/sandbox/28097/
	//var qty = document.getElementById("qty"+itemID).value;
  serverSideURL = "loadphoto.php";
	var oForm = document.forms["load_file"+itemID];
	if (oForm == undefined){
	  document.getElementById("ajaxmes_load_file"+itemID).innerHTML = 'Переключитесь в режим редактирования';
	  return;
	}
   var formData = new FormData(oForm); 
   formData.append("fitem_id", itemID);
   var sBody = getRequestBody(oForm);
  //document.getElementById('messageBody').value = "test";//
  if (!couldProcess && httpRequester) {
    document.getElementById("ajaxmes_load_file"+itemID).innerHTML = '<img src=images/busy.gif>';
    httpRequester.open("POST", serverSideURL, true);
	httpRequester.send(formData);
/*
	  var boundary = "AJAX--------------" + (new Date).getTime();
      var contentType = "multipart/form-data; boundary=" + boundary;
		httpRequester.setRequestHeader("Content-Type", contentType);
		httpRequester.setRequestHeader("Content-length", sBody.length);
		httpRequester.setRequestHeader("Connection", "close");
		httpRequester.sendAsBinary(data);
		httpRequester.send(sBody);
*/
		httpRequester.onreadystatechange = processResponse;
    couldProcess = true;
  }
}
//------------------------------------------------------------------
function postForm(token,itemID) {
  serverSideURL = "ajax.php";
  var oForm = document.forms[token+itemID];
  if (oForm == undefined){
	document.getElementById("ajaxmes"+token+itemID).innerHTML = 'Переключитесь в режим редактирования';
	return;
  }
  var formData = new FormData(oForm); 
  var sBody = getRequestBody(oForm);
  if (!couldProcess && httpRequester) {
    document.getElementById("ajaxmes"+token+itemID).innerHTML = '<img src=images/busy.gif>';
    httpRequester.open("POST", serverSideURL, true);
	httpRequester.send(formData);
    httpRequester.onreadystatechange = processResponse;
    couldProcess = true;
  }
}
//------------------------------------------------------------------
function postAction(a_Action,token,itemID) {
	//var qty = document.getElementById("qty"+itemID).value;
  serverSideURL = "ajax.php?action="+a_Action+"&fitem_id="+itemID;
	var oForm = document.forms[a_Action+itemID];
	if (oForm == undefined){
	  document.getElementById("ajaxmes"+token+itemID).innerHTML = 'Переключитесь в режим редактирования';
	  return;
	}
  var sBody = getRequestBody(oForm);
  //document.getElementById('messageBody').value = "test";//
  if (!couldProcess && httpRequester) {
    document.getElementById("ajaxmes"+token+itemID).innerHTML = '<img src=images/busy.gif>';
    httpRequester.open("POST", serverSideURL, true);
		httpRequester.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		httpRequester.setRequestHeader("Content-length", sBody.length);
		httpRequester.setRequestHeader("Connection", "close");
		httpRequester.send(sBody);

		httpRequester.onreadystatechange = processResponse;
    couldProcess = true;
  }
}
//------------------------------------------------------------------
function processResponse() {
  if ( httpRequester.readyState == 4/*COMPLETE*/ ) {//это константа, объявлена локально,  ее значение равно 4
   // если статус равен 200 (OK)
   if ( httpRequester.status == 200) {
     // ... результаты выполнения...
     if ( httpRequester.responseText.indexOf('invalid') == -1 ) {
			var respText = httpRequester.responseText.split(":::");
      var values = respText[0].split("|"); //анализируем ответ сервера
      var respContent = respText[1];
			switch (values[0]){
      case 'modify_comment':
        if(values[1]=="OK"){
		 if (values[3] =="0")  document.getElementById("message"+values[2]).innerHTML  = "забанено";
		 else document.getElementById("message"+values[2]).innerHTML  = "утверждено";
         switchClass(document.getElementById("comment"+values[2]),'good_comment');
		} else alert(httpRequester.responseText);
		break;
	  case 'change_edit_mode':
		document.getElementById("ajaxmes"+values[3]+values[2]).innerHTML = '';
        if(values[1]=="OK"){
          document.getElementById(values[3]+values[2]).innerHTML  = respContent;
		}else{
		  document.getElementById("ajaxmes"+values[3]+values[2]).innerHTML  = respContent;
		  alert(httpRequester.responseText);
		}
		break;
	  case 'set_tran_nmcl':
		document.getElementById("ajaxmes"+values[3]+values[2]).innerHTML = '';
        if(values[1]=="OK"){
          document.getElementById(values[3]+values[2]).innerHTML  = '';
          document.getElementById('item'+values[2]).value  = respContent;
		  document.getElementById('fnmcl_id'+values[2]).value  = values[4];
		  document.getElementById('price'+values[2]).value  = values[5];
		}else{
		  document.getElementById("ajaxmes"+values[3]+values[2]).innerHTML  = respContent;
		  alert(httpRequester.responseText);
		}
		break;
	  case 'modify_item':
		document.getElementById("ajaxmesitem"+values[2]).innerHTML = '';
        if(values[1]=="OK"){
          document.getElementById("item"+values[2]).innerHTML  = respContent;
          document.getElementById("itemname"+values[2]).innerHTML  = values[3];
          document.getElementById("itemprice"+values[2]).innerHTML  = values[4];
          document.getElementById("itemimg"+values[2]).alt = values[5];
		  document.getElementById("itemstatusimg"+values[2]).src = 'images/itemstatus'+values[6]+'.jpg';
		} else {
		  document.getElementById("ajaxmesitem"+values[2]).innerHTML  = respContent;
		  alert(httpRequester.responseText);
		}
		break;
	  case 'load_photo':
	    //load_photo|OK|itemId|file_name:::message
		document.getElementById("ajaxmes_load_file"+values[2]).innerHTML = '';
        if(values[1]=="OK"){
  		  document.getElementById("ajaxmes_load_file"+values[2]).innerHTML = 'Файл загружен';
		  var imgList = document.getElementById("imgList"+values[2]).innerHTML;
          document.getElementById("imgList"+values[2]).innerHTML = imgList+respContent;
   	      //document.getElementById("itemstatusimg"+values[2]).src = 'images/itemstatus'+values[6]+'.jpg';
		} else {
		  alert(httpRequester.responseText);
		}
		break;
	  case 'load_photo_article':
	    //load_photo|OK|itemId|file_name:::message
		article_pos = getCaret(document.getElementById('article_content'));
		document.getElementById("ajaxmes_load_file"+values[2]).innerHTML = '';
        if(values[1]=="OK"){
  		  document.getElementById("ajaxmes_load_file"+values[2]).innerHTML = respContent;
		  setCaretToPos(document.getElementById("article_content"), article_pos);
		  insertAtCaret(respContent);
		} else {
		  alert(httpRequester.responseText);
		}
		break;
	  case 'add_transaction':
		document.getElementById("ajaxmesnewtran"+values[2]).innerHTML = '';
        if(values[1]=="OK"){
		  document.getElementById("newtran"+values[2]).innerHTML  = '';
  		  document.getElementById("createdtran"+values[2]).innerHTML  = respContent;

		} else {
		  document.getElementById("ajaxmesnewtran"+values[2]).innerHTML  = respContent;
		  alert(httpRequester.responseText);
		}
		break;
	  case 'edit_category':
		document.getElementById("ajaxmescategory"+values[2]).innerHTML = '';
        if(values[1]=="OK"){
		  //document.getElementById("category"+values[2]).innerHTML  = '';
  		  //document.getElementById("category"+values[2]).innerHTML  = respContent;
		  if(values[2].substr(0,1)=='0') {
		  document.getElementById("category"+values[2]).innerHTML  = 'Добавить элемент';
		  document.getElementById("newcategory"+values[2]).innerHTML  = respContent + document.getElementById("newcategory"+values[2]).innerHTML;
		  }
		  else document.getElementById("category"+values[2]).innerHTML  = respContent;
		} else {
		  document.getElementById("ajaxmescategory"+values[2]).innerHTML  = respContent;
		  alert(httpRequester.responseText);
		}
		break;
	  case 'edit_document':
		document.getElementById("ajaxmesdocument"+values[2]).innerHTML = '';
        if(values[1]=="OK"){
		  //document.getElementById("category"+values[2]).innerHTML  = '';
  		  //document.getElementById("category"+values[2]).innerHTML  = respContent;
		  if(values[2].substr(0,1)=='0') {
		  document.getElementById("document"+values[2]).innerHTML  = 'Новый документ';
		  if(values[4]!='0' && document.getElementById("fid").value =='0' ){
		    document.getElementById("newdocument"+values[2]).innerHTML  = respContent + document.getElementById("newdocument"+values[2]).innerHTML;
		    document.getElementById("fid").value = values[4];
		  }
		  }
		  else document.getElementById("document"+values[2]).innerHTML  = respContent;
		  var messages = values[3].split(";;"); //сообщения об ошибках при сохранении строк	  
		  if (messages[0].length>0)
		  for(var i = 0; i < messages.length; i++) {		  
		   var msg = messages[i].split('--');
		   document.getElementById("ajaxmeseditdoc"+msg[0]).innerHTML = msg[2];		   
		   document.getElementById("findnmcl"+msg[0]).innerHTML = '';		   
		   document.getElementById("line_id"+msg[0]).value = msg[1];
		  }  
		} else {
		  document.getElementById("ajaxmesdocument"+values[2]).innerHTML  = respContent;
		  alert(httpRequester.responseText);
		}
		break;
	  case 'add_docitem':
		document.getElementById("ajaxmes"+values[3]+values[2]).innerHTML = '';
        if(values[1]=="OK"){
		  //document.getElementById("category"+values[2]).innerHTML  = '';
  		  //document.getElementById("category"+values[2]).innerHTML  = respContent;
		  //document.getElementById("addwrhtable").innerHTML  += respContent;
		  		  
		  var tbody = document.getElementById("adddocitemtable");
          var row = document.createElement("div");
		  row.innerHTML = respContent;
		  tbody.appendChild(row);
		  
		} else {
		  document.getElementById("ajaxmes"+values[3]+values[2]).innerHTML  = respContent;
		  alert(httpRequester.responseText);
		}
		break;
	  case 'add_orderitem':
		document.getElementById("ajaxmes"+values[3]+values[2]).innerHTML = '';
        if(values[1]=="OK"){
		  //document.getElementById("category"+values[2]).innerHTML  = '';
  		  //document.getElementById("category"+values[2]).innerHTML  = respContent;
		  //document.getElementById("addwrhtable").innerHTML  += respContent;
		  		  
		  var tbody = document.getElementById("addorderitemtable");
          var row = document.createElement("div");
		  row.innerHTML = respContent;
		  tbody.appendChild(row);
		  
		} else {
		  document.getElementById("ajaxmes"+values[3]+values[2]).innerHTML  = respContent;
		  alert(httpRequester.responseText);
		}
		break;
	  case 'edit_order':
		document.getElementById("ajaxmesorder"+values[2]).innerHTML = '';
        if(values[1]=="OK"){
		  document.getElementById("order"+values[2]).innerHTML  = respContent; 
		} else {
		  document.getElementById("ajaxmesorder"+values[2]).innerHTML  = respContent;
		  alert(httpRequester.responseText);
		  var messages = values[3].split(";;"); //сообщения об ошибках при сохранении строк	  
		  if (messages[0].length>0)
		  for(var i = 0; i < messages.length; i++) {		  
		   var msg = messages[i].split('--');
		   document.getElementById("ajaxmesedititem"+msg[0]).innerHTML = msg[2];		   
		   document.getElementById("findnmcl"+msg[0]).innerHTML = '';		   
		   document.getElementById("item_id"+msg[0]).value = msg[1];
		  } 
		}
		break;		
	  case 'create_pickpoint':
		document.getElementById("ajaxmescreate_pickpoint"+values[2]).innerHTML = "";
        if(values[1]=="OK"){
		  document.getElementById("ajaxmescreate_pickpoint"+values[2]).innerHTML  = "OK:"+respContent;
		  document.getElementById("pp_invoicenumber"+values[2]).value = values[3];
		  document.getElementById("pp_barcode"+values[2]).value = values[4];
		  document.getElementById("pp_label"+values[2]).value = values[5];
		  document.getElementById("invoicenumber"+values[2]).innerHTML = values[3];
		  document.getElementById("barcode"+values[2]).innerHTML = values[4];
		  document.getElementById("label"+values[2]).innerHTML = "<a href=\""+values[5]+"\">"+values[5]+"</a>";
		} else {
		  document.getElementById("ajaxmescreate_pickpoint"+values[2]).innerHTML  = "FAILED"+respContent;
		  alert(httpRequester.responseText);
		}
		break;		
	  case 'del_orderitem':
		document.getElementById("ajaxmesedititem"+values[2]).innerHTML = '';
 	    document.getElementById("findnmcl"+values[2]).innerHTML = '';		   
        if(values[1]=="OK"){	      
		  if(values[3]==0)
		   switchClass(document.getElementById("orderitem"+values[2]), 'hide_line');
		  else if (document.getElementById("deleted"+values[2]).value != values[4]){
		   switchClass(document.getElementById("orderitem"+values[2]), 'del_line');
		   switchClass(document.getElementById("btndel"+values[2]), 'hidden');
		   switchClass(document.getElementById("btnundo"+values[2]), 'hidden');
		  }
		  document.getElementById("deleted"+values[2]).value = values[4];
		} else {
		  document.getElementById("ajaxmesedititem"+values[2]).innerHTML  = respContent;		 
		  alert(httpRequester.responseText);
		} 
		break;	
	  case 'add_wrhrow':
		document.getElementById("ajaxmes"+values[3]+values[2]).innerHTML = '';
        if(values[1]=="OK"){
		  //document.getElementById("category"+values[2]).innerHTML  = '';
  		  //document.getElementById("category"+values[2]).innerHTML  = respContent;
		  //document.getElementById("addwrhtable").innerHTML  += respContent;
		  		  
		  var tbody = document.getElementById("addwrhtable");
          var row = document.createElement("div");
		  row.innerHTML = respContent;
		  tbody.appendChild(row);
		  
		} else {
		  document.getElementById("ajaxmes"+values[3]+values[2]).innerHTML  = respContent;
		  alert(httpRequester.responseText);
		}
		break;
	  case 'del_category':
		document.getElementById("ajaxmescategory"+values[2]).innerHTML = '';
        if(values[1]=="OK"){
		  document.getElementById("categorycontent"+values[2]).innerHTML  = respContent;
		} else {
		  document.getElementById("ajaxmescategory"+values[2]).innerHTML  = respContent;
		  alert(httpRequester.responseText);
		}
		break;
	  }
      couldProcess = false;
     }
   } else {
     // ... здесь обрабатываем ошибки...
     alert('Error. Code: ' + httpRequester.status);
   }
  }
}
function 	changeImage(imgID,sText){
	 document.getElementById(imgID).innerHTML = sText;
}

function switchClass(E,D){
  var C=E.className.split(/\s+/);
  var A=[];
  for(var B=0;B<C.length;B++){
    if(C[B]!=D){A.push(C[B])}
  }
  if(C.length==A.length){
    E.className+=" "+D
  }else{E.className=A.join(" ")}
}

function switchTab(tabpage,tab,index,count){
  for(var i=1;i<=count;i++){
    if (i==index) {
      document.getElementById(tabpage+i).className = 'tab_page';
      document.getElementById(tab+i).className = 'tab selected';
    }
    else {
      document.getElementById(tabpage+i).className = 'tab_page_hide';
      document.getElementById(tab+i).className = 'tab';
    }
  }
}
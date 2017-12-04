//------------------------------------------------------------------
function getRequestBody(oForm) {
  var aParams = new Array();
  for(var i = 0; i < oForm.elements.length; i++) {
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
var currentToken = '';
var tipsField = '';
var COMPLETE = 4;
var responseActions = [];
//------------------------------------------------------------------
function postForm(options) {
    var oForm = document.forms[options.formId];
    var tipField = document.getElementById(options.tipsBoxId);
    tipsField = options.tipsBoxId;
    if (oForm == undefined) {
        if (tipField == undefined)
            alert('Ошибочка вышла!');
        else
            tipField.innerHTML = 'Ошибочка вышла!';
        return;
    }
    if (!couldProcess && httpRequester) {
        if (tipField != undefined)
            tipField.innerHTML = '<img src=images/busy.gif>';
        currentToken = options.token;
        responseActions[currentToken] = options.onSuccess;
        serverSideURL = "kluvonos/service?token=" + options.token;
        httpRequester.open("POST", serverSideURL, true);
        //-- send data ----------------------
        //var formData = new FormData(oForm); 
        //httpRequester.send(formData);
        //-- send form ----------------------
        var sBody = getRequestBody(oForm);
        httpRequester.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        //httpRequester.setRequestHeader("Content-length", sBody.length);
        //httpRequester.setRequestHeader("Connection", "close");
        httpRequester.send(sBody);
        //----------------------------------
        httpRequester.onreadystatechange = postFormResponse;
        couldProcess = true;
        currentToken = options.token;
    }
}
function getForm(options) {
  var oForm = document.forms[options.formId];
  tipsField = options.tipsBoxId;
  if (oForm == undefined){
     var noti = new Notification({elem:document.getElementById('noti_bar')});
     noti.show();
     alert('Ошибочка вышла!');
 	 return;
  }
  if (!couldProcess && httpRequester) {
	currentToken = options.token;
	responseActions[currentToken] = options.onSuccess;
    var sBody = getRequestBody(oForm);
    var serverSideURL = "kluvonos/service?token="+options.token+"&"+sBody;
    httpRequester.open("GET", serverSideURL, true);
	//-- send data ----------------------
	//var formData = new FormData(oForm); 
	//httpRequester.send(formData);
	//-- send request ----------------------
	
	//httpRequester.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	//httpRequester.setRequestHeader("Content-length", sBody.length);
	//httpRequester.setRequestHeader("Connection", "close");
	//httpRequester.send(sBody);
	//----------------------------------
    httpRequester.onreadystatechange = postFormResponse;
    couldProcess = true;
    httpRequester.send(null);
  }
}
function trim(str, chars) {
    return ltrim(rtrim(str, chars), chars);
}
function ltrim(str, chars) {
    chars = chars || "\\s";
    return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}
function rtrim(str, chars) {
    chars = chars || "\\s";
    return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}
//------------------------------------------------------------------
function postFormResponse() {
  if ( httpRequester.readyState == COMPLETE ) {
   couldProcess = false;
   var token = currentToken;
   var tipField = document.getElementById(tipsField);
   tipsField = '';
   currentToken = '';
   if ( httpRequester.status == 200) {
     var respText = httpRequester.responseText;
     var respResult;
     try{
       try{ respResult = JSON.parse(respText);
       } catch (e) {
        console.error("Parsing error:", e);
        respResult = eval('(' + respText + ')');
       }
       if (!respResult.success){         
         if (tipField!=undefined) tipField.innerHTML = respResult.message;
         else alert(respResult.message);
       } else {
         token = respResult.token;
         if (tipField!=undefined) tipField.innerHTML = respResult.message; 
         responseActions[token](respResult.data);
       }
     } catch (e) {
       console.error("Parsing error:", e);
       if (tipField!=undefined) tipField.innerHTML = 'Serverside error: invalid response!';
     }
   } else {
     alert('Error. Code: ' + httpRequester.status);
     if (tipField!=undefined) tipField.innerHTML = '';
   }
  }
}

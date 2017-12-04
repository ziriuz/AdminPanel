var currentToken = '';
var tipsField = '';
const COMPLETE = 4;
//------------------------------------------------------------------
function postForm(token) {
  serverSideURL = "actions/"+token+".php";
  var oForm = document.forms[token];
  var tipField = document.getElementById(tipsField);
  if (oForm == undefined){
    if (tipField == undefined)
	 alert('ќшибочка вышла!');
	else
	 tipField.innerHTML = 'ќшибочка вышла!';
	return;
  }
  if (!couldProcess && httpRequester) {
    if (tipField != undefined)
      tipField.innerHTML = '<img src=images/busy.gif>';
    httpRequester.open("POST", serverSideURL, true);
	//-- send data ----------------------
	//var formData = new FormData(oForm); 
	//httpRequester.send(formData);
	//-- send form ----------------------
	var sBody = getRequestBody(oForm);
	httpRequester.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	httpRequester.setRequestHeader("Content-length", sBody.length);
	httpRequester.setRequestHeader("Connection", "close");
	httpRequester.send(sBody);
	//----------------------------------
    httpRequester.onreadystatechange = postFormResponse;
    couldProcess = true;
	currentToken = token;
  }
}
function sendSubcribe(){
 tipsField = "ajaxmescreate_subscribe";
 postForm("create_subscribe");
}
function setActiveSubcribe(){
 tipsField = "ajaxmescreate_subscribe";
 postForm("setactive_subscribe");
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
function addTabRow(tabId,cellContent){      
  var tab = document.getElementById(tabId);
  var row = tab.insertRow(tab.rows.length);
  var newCell = row.insertCell(0);
  newCell.innerHTML = cellContent;
  /*
   rowValues = ['first cell','cell 2','another cell'];
   for (var i = 0, ii = rowValues.length; i < ii; i++) {
      newCell = row.insertCell(i);
	  newCell.innerHTML = rowValues[i];
	  //or
      newCell.appendChild(document.createTextNode(rowValues[i]));
   }
   */
}
//------------------------------------------------------------------
function postFormResponse() {
  if ( httpRequester.readyState == COMPLETE ) {
   couldProcess = false;
   var token = currentToken;
   var tipField = document.getElementById(tipsField);
   currentToken = '';
   if ( httpRequester.status == 200) {
     var respText = httpRequester.responseText;
     var respResult;
     try{
       respResult = JSON.parse(respText);
       if (!respResult.success){
         alert(respResult.message);
         if (tipField!=undefined) tipField.innerHTML = respResult.message;
       } else {
         token = respResult.token;
         switch (token){
         case 'create_subscribe': if (tipField!=undefined) tipField.innerHTML = respResult.message; 
          addTabRow("subscribe-grid","<input type=radio name=\"active_subscribe\" id=\""+respResult.code+"\" value=\""+respResult.code+"\"/><span>["+respResult.code+"] "+respResult.name+"</span>");
          break;
         case 'setactive_subscribe': if (tipField!=undefined) tipField.innerHTML = respResult.message;
          items = document.getElementsByTagName('span');
          for (var i = 0, ii = items.length; i < ii; i++) {
           if (ltrim(items[i].className,' ') == 'activeSubscr') switchClass(items[i], 'activeSubscr');
          }
          switchClass(document.getElementById(respResult.code), 'activeSubscr');
          break;
         default: alert(token+"-"+respResult.message);
         }
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

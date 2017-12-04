<?php
class PickpointApi {
 private $loginReq = '{"Login":"%s","Password":"%s"}';
 private $logoutReq = '{"SessionId":"%s"}';
 private $createSendingReq='{"SessionId":"%s",
   "Sendings":[{
   "EDTN":"%s",
   "IKN":"%s",
   "Invoice":{
     "SenderCode":"%s",
     "Description":"%s",
     "RecipientName":"%s",
     "PostamatNumber":"%s",
     "MobilePhone":"%s",
     "Email":"%s",
     "PostageType":%d,
     "GettingType":%d,
     "PayType":1,
     "Sum":%d,
     "InsuareValue":%d,
     "Width":%d,
     "Height":%d,
     "Depth":%d
    }
  }]}';
 private $makeLabelReq = '{"SessionId":"%s","Invoices":["%s"]}';
 private $login;
 private $pwd;
 private $ikn;
 private $apiUrl;
 private $apiRoot;
 private $message;
 private $request;
 private $response;
 public $session;
 public $createdSendings;
 public $labelFile;
 private $dataDirectory = 'data/pickpoint';
 function  __construct($test=true) {
   $this->apiUrl = "e-solution.pickpoint.ru";
   $this->apiRoot=PP_API_ROOT;
   $this->login=PP_API_KEY;
   $this->pwd=PP_API_PASS;
   $this->ikn=PP_API_IKN;
 }
 function getMessage(){
    return $this->message.".\nrequest:".$this->request.".\nresponse:".$this->response;
 } 
 private function postRequest($method,$body,$toFile=false){
  $sock = fsockopen($this->apiUrl, 80, $errno, $errstr, 30);
   if (!$sock){
     $this->message = "Pickpoint connection error:[$errstr ($errno)]";
	 return false;
   }
   fputs($sock, sprintf("POST %s/%s HTTP/1.1\r\n",$this->apiRoot,$method));
   fputs($sock, sprintf("Host: %s\r\n",$this->apiUrl));
   fputs($sock, "Accept: */*\r\n");
   fputs($sock, "Content-Type: application/json \r\n");
   fputs($sock, "Content-Length: ".strlen($body)."\r\n");
   fputs($sock, "Connection: close\r\n\r\n");
   fputs($sock, $body);
   //split header
   //echo $body;
   $this->request = $body;
   while ($str = trim(fgets($sock, 4096)));
   $response = "";
   //body starts hear
   if (!$toFile) 
     while (!feof($sock)) $response.= fgets($sock, 4096);
   else{
     $i=0; $isError=false;
	 $filename = $this->dataDirectory.'/'.$toFile;
     while (!feof($sock)){
	   $str= fgets($sock, 4096);
	   if ($i==0){
	     if(strpos($str,'Error')===false){
		  if (!$handle = fopen($filename, 'wb')) { 
			$isError=true;
            $this->message = "Cannot open file ($filename)";
            exit;
		  }
		 }
	     else $isError=true;
	   } 	   
	   if (!$isError){
        if (fwrite($handle, $str) === FALSE) {
         $this->message = "Cannot write to file ($filename)";
         exit;
		}
	   } else $this->message .= $str;
       $i++;
	 }
	 if($handle) fclose($handle);
	 if($isError)  $response=false;
	 else  $response = $filename;
   }
   fclose($sock);
   //echo $response;
   $this->response = $response;
   return $response;
 } 
 function login(){
   $method = 'login';
   $body = sprintf($this->loginReq,$this->login,$this->pwd);
   if (!$response = $this->postRequest($method,$body)) return false;
   $pickObject = json_decode($response);
   if(!$pickObject) {
     $this->message = "Invalid $method:[$response]";
	 return false;
   } elseif ($pickObject->ErrorMessage!=null){
     $this->message = "Error $method:[$pickObject->ErrorMessage]";
	 return false;
   } elseif (strlen($pickObject->SessionId)==0){
     $this->message = "Error $method: SessionId not defined";
	 return false;
   }	 
   else
     $this->session = $pickObject->SessionId;
   return true;
 }
function logout(){
   $method = 'logout';
   $body = sprintf($this->logoutReq,$this->session);
   if (!$response = $this->postRequest($method,$body)) return false;
   $pickObject = json_decode($response);
   if(!$pickObject) {
     $this->message = "Invalid $method:[$response]";
	 return false;
   } elseif ($pickObject->Success === false){
     $this->message = "Error $method";
	 return false;
   } else
     $this->session = null;
   return true;
 }
function makeLabel($sendingNumber){
   $method = 'makelabel';
   $body = sprintf($this->makeLabelReq,$this->session,$sendingNumber);
   if (!$response = $this->postRequest($method,$body,$sendingNumber.'.pdf')) return false;
   $this->labelFile = $response;
   return true;
 }
function createSending($orderId,$RecipientName,$postamatNumber,
     $mobilePhone,
     $email,
     $postageType,
     $gettingType,
     $sum,
     $insuareValue){
     $method = 'createsending';
     $body = sprintf($this->createSendingReq,$this->session,$orderId,$this->ikn,
     $orderId,
     'Одежда',
     $RecipientName,
     $postamatNumber,
     $mobilePhone,
     $email,
     $postageType,
     $gettingType,
     $sum,
     $insuareValue,
     0,0,0   
   );
   $bodyObject = json_decode($body);
   if(!$bodyObject) {
     $this->message = "Invalid $method body:[$body]";
	 return false;
   }   
   if(!$response = $this->postRequest($method,$body)) return false;
   $pickObject = json_decode($response);
   if(!$pickObject) {
     $this->message = "Invalid $method response:[$response]";
	 return false;
   } elseif ($pickObject->RejectedSendings!=null){
     $this->message = "Error $method: ";
     foreach($pickObject->RejectedSendings as $i=> $rejected)
      $this->message .= '['.$rejected->ErrorMessage.'];';
	 return false;
   } else $this->createdSendings = $pickObject->CreatedSendings;

   return true;
 }
}
?>
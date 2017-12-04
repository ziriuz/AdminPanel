<?php
class CurlHttpException extends Exception { }
class SdekException extends Exception { }
class CurlHttpApi{
	public function curlHttpApiRequest($method, $url, $query='', $payload='', $request_headers=array())
	{
		$url = $this->curlAppendQuery($url, $query);
		$ch = curl_init($url);
		$this->curlSetopts($ch, $method, $payload, $request_headers);
		$response = curl_exec($ch);
		$errno = curl_errno($ch);
		$error = curl_error($ch);
		curl_close($ch);
		if ($errno) throw new CurlHttpException($error, $errno);//"/\r\n\r\n|\n\n|\r\r/"
		list($message_headers, $message_body) = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);
		$this->last_response_headers = $this->curlParseHeaders($message_headers);
		return $message_body;
	}
	protected function curlAppendQuery($url, $query)
	{
		if (empty($query)) return $url;
		if (is_array($query)) return "$url?".http_build_query($query);
		else return "$url?$query";
	}
	protected function curlSetopts($ch, $method, $payload, $request_headers)
	{
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_USERAGENT, 'php-api-client');
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		if (!empty($request_headers)){
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
                }
		if ($method != 'GET' && !empty($payload))
		{
		   if (is_array($payload)) $payload = http_build_query($payload);
		   curl_setopt ($ch, CURLOPT_POSTFIELDS, $payload);
		}
	}
	protected function curlParseHeaders($message_headers)
	{
		$header_lines = preg_split("/\r\n|\n|\r/", $message_headers);
		$headers = array();
		list(, $headers['http_status_code'], $headers['http_status_message']) = explode(' ', trim(array_shift($header_lines)), 3);
		foreach ($header_lines as $header_line)
		{
			list($name, $value) = explode(':', $header_line, 2);
			$name = strtolower($name);
			$headers[$name] = trim($value);
		}
		return $headers;
	}
}
class SdekApi extends CurlHttpApi{
 private $infoReportReq = '<?xml version="1.0"?><InfoRequest Date="%s" Account="%s" Secure="%s"><Order DispatchNumber="%s"/></InfoRequest>';
 private $statusReportReq = '<?xml version="1.0"?><StatusReport Date="%s" Account="%s" Secure="%s" ShowHistory="0" ><Order DispatchNumber="%s"/></StatusReport>';
 private $deleteReq = '<?xml version="1.0"?><DeleteRequest Date="%s" Account="%s" Secure="%s" OrderCount="1" Number="%s"><Order Number="%s"/></DeleteRequest>';
 private static $pvzlistReq = 'https://integration.cdek.ru/pvzlist.php?type=PVZ&citypostcode=%s';
 private $deliveryReq = '<?xml version="1.0"?>
<DeliveryRequest  Date="%s" Account="%s" Secure="%s" Number="1" OrderCount="1" >
<Order Number="%s"
SendCityPostCode="%d"
RecCityPostCode="%d"
RecipientName="%s"
Phone="%s"
Comment="%s"
TariffTypeCode="%d"
RecientCurrency="%s"
DeliveryRecipientCost="%f"
DeliveryRecipientVATRate="%s"
DeliveryRecipientVATSum="%f"
ItemsCurrency="%s">
<Address PvzCode ="%s" />
<Package Number ="1"  BarCode ="1"  Weight ="%d" >
%s
</Package>
</Order>
</DeliveryRequest>';//<AddService  ServiceCode ="30" ></AddService>
 private $deliveryItemsReq = '<Item WareKey="%s" Cost="%f" Payment="%f" Weight ="%d" Amount="%d" Comment ="%s" PaymentVATRate="VAT0" PaymentVATSum="0" />';
 private $login;
 private $pwd;
 private $apiUrl;
 private $message;
 private $request;
 private $response;
 private $dt;
 private $secToken;
 function __construct($key,$pass) {
    $this->dt = date('Y-m-d');
    $this->apiUrl = SDEK_API_URL;
    $this->login = $key;
    $this->pwd = $pass;
    $this->secToken=md5($this->dt.'&'.$this->pwd);
 } 
 function getMessage(){
    return $this->message.".\nrequest:".$this->request.".\nresponse:".$this->response;
 }
 private function Request($method,$body){
     $payload = array("xml_request" => $body);
     $request_headers = array("Expect:");//"Content-Type: application/xml; charset=utf-8") ;
     $response = $this->curlHttpApiRequest('POST', $this->apiUrl.'/'. $method,'', $payload, $request_headers);
     return $response;
 }
 private function RequestXml($method,$body){
     return new SimpleXMLElement($this->Request($method,$body)); 
 }
 function createOrder($order,$returnObj=true){
     $sendCityPostCode="127566";
     $tariffTypeCode="136";
     $recientCurrency="RUB";
     $deliveryRecipientVATRate="VAT0";
     $deliveryRecipientVATSum="0";
     $itemsCurrency="RUB";
     $method = 'new_orders.php';
     $items='';
     foreach($order['items'] as $i=>$item){
          $items.= sprintf($this->deliveryItemsReq,
                  $item['alt_code'],
                  0.01,
                  $item['price'],
                  $item['weight'],
                  $item['quantity'],
                  $item['name']
                 );                  
     }
     $body = sprintf($this->deliveryReq,
             $this->dt,$this->login,$this->secToken,
             $order['order_number'],
             $sendCityPostCode,
             $order['post_id'],
             $order['customer_name'],
             $order['phone'],
             $order['note'],
             $tariffTypeCode,
             $recientCurrency,
             $order['delivery_total'],
             $deliveryRecipientVATRate,
             $deliveryRecipientVATSum,
             $itemsCurrency,
             $order['transp_code'],
             $order['weight_total'],
             $items
             );
     if ($returnObj){
         return $this->RequestXml($method,$body);
     }
     else{
         return $this->Request($method,$body);
     }
 }
 function deleteOrder($orderNumber,$returnObj=true){
     $method = 'delete_orders.php';
     $body = sprintf($this->deleteReq
               ,$this->dt,$this->login,$this->secToken,$orderNumber,$orderNumber);
     if ($returnObj){
         return $this->RequestXml($method,$body);
     }
     else{
         return $this->Request($method,$body);
     }
     //<ChangePeriod DateFirst ="2017­01­01"  DateLast ="2017­06­17" />
 } 
 function statusReport($trackingNumber,$returnObj=true){
     $method = 'status_report_h.php';
     $body = sprintf($this->statusReportReq
               ,$this->dt,$this->login,$this->secToken,$trackingNumber);
     if ($returnObj){
         return $this->RequestXml($method,$body);
     }
     else{
         return $this->Request($method,$body);
     }
     //<ChangePeriod DateFirst ="2017­01­01"  DateLast ="2017­06­17" />
 }
 function infoReport($trackingNumber,$returnObj=true){
     $method = 'info_report.php';
     $body = sprintf($this->infoReportReq
               ,$this->dt,$this->login,$this->secToken,$trackingNumber);
     if ($returnObj){
         return $this->RequestXml($method,$body);
     }
     else{
         return $this->Request($method,$body);
     }
 }
 static function getPvzList($postCode,$returnObj=true){
     $method = sprintf(self::$pvzlistReq,$postCode);
     $payload = array();
     $request_headers = array("Expect:");//"Content-Type: application/xml; charset=utf-8") ;
     $curl = new CurlHttpApi();
     $response = $curl->curlHttpApiRequest('GET', $method,'', $payload, $request_headers);
     if ($returnObj){
         return new SimpleXMLElement($response); 
     }
     else{
         return $response;
     }
 } 
}
function getSdekOrderInfo($trackingNumber,$test=false){
    //switch between companies
    $sdekApi = new SdekApi(SDEK_API_KEY, SDEK_API_PASS);
    $xml = $sdekApi->InfoReport($trackingNumber);
    if (isset($xml->Order["ErrorCode"]) && 'ERR_INVALID_DISPACHNUMBER'==$xml->Order["ErrorCode"]){
        //order not found in new API,try new API 
        $sdekApi = new SdekApi(SDEK_API_OLD_KEY, SDEK_API_OLD_PASS);
        $xml = $sdekApi->InfoReport($trackingNumber);
    }  
    if (isset($xml->Order["ErrorCode"])){
        throw new SdekException($xml->Order["Msg"]);
    }
    $shippingOrder = $xml->Order;
    $xml = $sdekApi->statusReport($trackingNumber);
    if (isset($xml->Order["ErrorCode"])){
        throw new SdekException($xml->Order["Msg"]);
    }    
    $shippingOrder->DeliveryStatus["Date"] = $xml->Order->Status["Date"];
    $shippingOrder->DeliveryStatus["Code"] = $xml->Order->Status["Code"];
    $shippingOrder->DeliveryStatus["Description"] = $xml->Order->Status["Description"];
    $shippingOrder->DeliveryStatus["CityCode"] = $xml->Order->Status["CityCode"];
    $shippingOrder->DeliveryStatus["CityName"] = $xml->Order->Status["CityName"];
    $sum = 0.0;
    foreach($shippingOrder->AddedService as $i=>$addService){
        if ($addService["Sum"]>0){
           $sum += floatval($addService["Sum"]);
        }
    }
    $shippingOrder["addServiceSum"] = $sum;
    return $shippingOrder;
}
function createSdekOrder($order,$test=false){
    $sdekApi = new SdekApi(SDEK_API_KEY, SDEK_API_PASS);
    $xml = $sdekApi->createOrder($order);
    if (isset($xml->Order["ErrorCode"])){
        throw new SdekException($xml->Order["Msg"]);
    }
    $shippingOrder = $xml->Order;
    return $shippingOrder;
}
function deleteSdekOrder($orderNumber,$test=false){
    $sdekApi = new SdekApi(SDEK_API_KEY, SDEK_API_PASS);
    $xml = $sdekApi->deleteOrder($orderNumber);
    if (isset($xml->DeleteRequest->Order["ErrorCode"])){
        throw new SdekException($xml->DeleteRequest->Order["Msg"]);
    }
    return $xml->DeleteRequest->Order;
}

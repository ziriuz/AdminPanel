<?php
class Controller_Kluvonos extends Controller
{
	/*function __construct()
	{
	  $this->model = new ModelKluvonos();
	  $this->view = new View();
	}*/
	
	function action_index()
	{
            header('Content-type: text/html; charset=utf-8');
            $this->view->generate('kluvonos_view.php', 'kluvonos_template_view.php');         
	}
	function action_products()
	{
        $load = filter_input(INPUT_GET,'load', FILTER_SANITIZE_STRING);
        //$orderNumber = filter_input(INPUT_GET,'order_number',FILTER_VALIDATE_FLOAT);
        $this->load->model('product');
        if($load=='true'){
           $data = $this->model_product->loadProducts();
        } else {
           $data = $this->model_product->getProducts();
        }
        //$data = $this->model->getProducts();
        header('Content-type: text/html; charset=utf-8');
        $this->view->generate('kluvonos_products_view.php', 'kluvonos_template_view.php', $data);              
    }
	function action_orders()
	{
        $load = filter_input(INPUT_GET,'load', FILTER_SANITIZE_STRING);
        $this->load->model('order');
        if($load=='true'){
            $this->model_order->loadOrders();
            $this->model_order->updateTrackingNum();
        }        
        $data = $this->model_order->getOrders();
        //$data = $this->model->getOrders();
        header('Content-type: text/html; charset=utf-8');
        $this->view->generate('kluvonos_orders_view.php', 'kluvonos_template_view.php', $data);
    }
    function action_service()
	{
            
        $token = filter_input(INPUT_GET,'token', FILTER_SANITIZE_STRING);
        $data=array();
        $data["token"]=$token;
        try{
            if($token=='Order'){
               $order_id = (isset($_GET["order_id"])?$_GET["order_id"]:null);
               if ($order_id==null){
                   throw new Exception("Unknown order");
               }
               $this->load->model('order');
               $data['order'] = $this->model_order->getOrderDetails($order_id);
               $data["message"]="ok";
               $data['order']['comments'] = str_replace(array("\n", "\r"),'\n', addslashes($data['order']['comments']));
               $view = 'kluvonos_order';
            }              
            elseif ($token == 'SaveOrder') {
               $details = array();
               $details['order_id'] = filter_input(INPUT_POST, 'order_id', FILTER_SANITIZE_STRING);
               $details['delivery_dt'] = filter_input(INPUT_POST, 'delivery_dt', FILTER_SANITIZE_STRING);
               $details['delivery_address'] = filter_input(INPUT_POST, 'delivery_address', FILTER_SANITIZE_STRING);
               $details['delivery_total'] = filter_input(INPUT_POST, 'delivery_total', FILTER_SANITIZE_STRING);
               $details['cash_on_delivery'] = filter_input(INPUT_POST, 'cash_on_delivery', FILTER_SANITIZE_STRING);
               $details['comments'] = filter_input(INPUT_POST, 'comments', FILTER_SANITIZE_STRING);
               $details['transp_number'] = filter_input(INPUT_POST, 'transp_number', FILTER_SANITIZE_STRING);
               $details['transp_dest_code'] = filter_input(INPUT_POST, 'transp_dest_code', FILTER_SANITIZE_STRING);
               $details['transp_order_date'] = filter_input(INPUT_POST, 'transp_order_date', FILTER_SANITIZE_STRING);
               $details['transp_order_status'] = filter_input(INPUT_POST, 'transp_order_status', FILTER_SANITIZE_STRING);
               if ($details['order_id'] == null){
                   throw new Exception("Unknown order");
               }
               $this->load->model('order');
               $this->model_order->saveOrderDetails($details);
               $data["order"] = &$details;
               $data["message"] = isset($details["message"])?$details["message"]:"hmm";
               $view = 'kluvonos_save';
            }
            elseif ($token == 'save_doc_item') {
               $fields = array(
                    'line_id' => FILTER_SANITIZE_STRING,
                    'alt_code' => FILTER_SANITIZE_STRING,
                    'size_id' => FILTER_SANITIZE_STRING,
                    'color_id' => FILTER_SANITIZE_STRING,
                    'qty' => FILTER_VALIDATE_INT,
                    'price_min' => FILTER_VALIDATE_FLOAT,
                    'price' => FILTER_VALIDATE_FLOAT,
                    'label_code' => FILTER_SANITIZE_STRING
               );
               $details = array();
               foreach($fields as $key=>$filter){
                   $details[$key] = filter_input(INPUT_POST,'doc_item_'.$key, $filter);
               }
               $this->load->model('documents');
               $this->load->model('product');
               $product=$this->model_product->getProductByCode($details['alt_code']);
               if($details["line_id"]==0) $details["line_id"]=null;
               try{
               $draftItem=$this->model_documents->getDraftItem($details["line_id"]);
               if(strcmp($draftItem['alt_code'],$details['alt_code'])!==0){
                   //articul is changed, take product attributes
                   $details['price']=null;
                   $details['price_min']=null;
                   $details['label_code']=null;
                   $details['color_id']=null;
               } 
               else {
                   $details['price']=($details['price']==$product['price']?null:$details['price']);
                   $details['price_min']=($details['price_min']==$product['price_min']?null:$details['price_min']);
               }
               if(strcmp($draftItem['color_id'],$details['color_id'])!==0||strcmp($draftItem['size_id'],$details['size_id'])!==0){
                   $details['label_code']=null;
               }
               $data['action']='update';
               }
               catch(NoDataFoundException $e){
                   $data['action']='create';
               }
               $this->model_documents->saveDraftItem($details);
               $data["message"] = isset($details["message"])?$details["message"]:"hmm";
               $details = $this->model_documents->getDraftItemDetailed($details["line_id"]);
               $data["doc_item"] = &$details;
               $view = 'doc_item';
            }            
            elseif($token=='CreateTranspOrder'){
               $transpOrder=array();
               $transpOrder['order_id'] = filter_input(INPUT_POST,'order_id', FILTER_SANITIZE_STRING);
               $transpOrder['order_number'] = filter_input(INPUT_POST,'order_number', FILTER_SANITIZE_STRING);
               $transpOrder['post_id'] =filter_input(INPUT_POST,'post_id', FILTER_SANITIZE_STRING);
               $transpOrder['customer_name'] =filter_input(INPUT_POST,'customer_name', FILTER_SANITIZE_STRING);
               $transpOrder['phone'] = filter_input(INPUT_POST,'phone', FILTER_SANITIZE_STRING);
               $transpOrder['note'] =htmlspecialchars(filter_input(INPUT_POST,'tk_comments', FILTER_SANITIZE_STRING));
               $transpOrder['delivery_total'] =filter_input(INPUT_POST,'delivery_price', FILTER_SANITIZE_STRING);
               $transp_code=filter_input(INPUT_POST,'transp_code', FILTER_SANITIZE_STRING);
               $transp_dest_code=filter_input(INPUT_POST,'transp_dest_code', FILTER_SANITIZE_STRING);
               $transpOrder['transp_code']=$transp_code=='NO'?$transp_dest_code:$transp_code;               
               $transpOrder['weight_total'] =filter_input(INPUT_POST,'weight_total', FILTER_VALIDATE_INT);
               $transpOrder['items'] = array();
               $transpOrder['items'][0]['alt_code'] = filter_input(INPUT_POST,'order_number', FILTER_SANITIZE_STRING);
               $transpOrder['items'][0]['name'] = htmlspecialchars('Клювонос'); 
               $transpOrder['items'][0]['weight'] = filter_input(INPUT_POST,'weight_total', FILTER_VALIDATE_INT);
               $transpOrder['items'][0]['price'] = filter_input(INPUT_POST,'tk_cash_on_delivery', FILTER_VALIDATE_FLOAT);
               $transpOrder['items'][0]['quantity'] = "1";
               $transpOrder['items'][0]['amount'] = filter_input(INPUT_POST,'tk_cash_on_delivery', FILTER_VALIDATE_FLOAT);
               
               $this->load->model('order');
               $order = $this->model_order->getOrderDetails($transpOrder['order_id']);
               //throw new Exception(var_dump($transpOrder));
               $shippingOrder = createSdekOrder($transpOrder);
               $order["transp_number"] = $shippingOrder['DispatchNumber'];
               $order["transp_dest_code"] = $transpOrder['transp_code'];
               $order["cash_on_delivery"] = $transpOrder['items'][0]['price'] + $transpOrder['delivery_total'];
               $order["transp_order_date"] = date('Y-m-d');
               $order["transp_order_status"] = 'NEW';
               $this->model_order->saveOrderDetails($order);
               $this->model_order->updateOrderStatus($transpOrder['order_id'],3);
               $data["order"] = $order;
               $data["shippingOrder"] = $shippingOrder;
               $data['message'] = 'Заказ отправлен в СДЭК. Tracking number: '.$shippingOrder['DispatchNumber'];
               $view = 'admin_tk_order';
            }
            else {
                throw new Exception("Unknown token");
            }
            header('Content-type: text/html; charset=utf-8');
            $this->view->generate($view.'_json_view.php', 'json_template_view.php', $data);
        }
        catch(Exception $e){
            $data["message"]=$e->getMessage();
            header('Content-type: text/html; charset=utf-8');
            $this->view->generate('error_json_view.php', 'json_template_view.php',$data);                   
        }
    }
}

<?php
require_once 'lib/shopify.php';
class ModelOrder extends Model
{   
  public $totalQty;
  public $totalAmount;
  public $totalAmountMin;
  public function __construct($registry = null) {
      if ($registry!=null){
          $mapping = array("id"=>"order_id",
            'order_id' => "order_id",
            'order_number' => "sid",
            'created_at' => "order_date",
            'subtotal_price' => "subtotal_price",
            'total_price' => "total_price",
            'financial_status' => "financial_status",
            'confirmed' => "confirmed",
            'shipping_type' => "deliv_type",
            'shipping_price' => "deliv_price",
            'customer_name' => "customer_name",
            'first_name' => "first_name",
            'last_name' => "last_name",
            'phone' => "phone",
            'email' => "email",
            'shipping_address' => "address",
            'note' => "note",
            'gateway' => "gateway",
            'total_discounts' => "dis_code",
              
            'shopify_id' => 'shopify_id',
            'shopify_upd' => 'shopify_upd',
            'last_modified' => 'last_modified',
            'delivery_dt' => 'delivery_dt',
            'delivery_address' => 'delivery_address',
            'status' => 'status',
            'status_dt' => 'status_dt',
            'payment_status' => 'payment_status',
            'payment_dt' => 'payment_dt',
            'delivery_total' => 'delivery_total',
            'cash_on_delivery' => 'cash_on_delivery',
            'comments' => 'comments',
            'transp_number' => "transp_number",
            'transp_dest_code' => "transp_dest_code",
            'transp_order_date' => "transp_order_date",
            'transp_order_status' => "transp_order_status"
          );
          $registry->set("mapping",$mapping);
      }
      parent::__construct($registry);
  }
  function getOrders(){
      //$shopifyClient = new ShopifyClient(SHOPIFY_SHOP, "", SHOPIFY_API_KEY, SHOPIFY_SECRET);
      $response = array('orders' => array()); //$shopifyClient->getOrders();
      $query = $this->db->query("SELECT o.order_id FROM orders o WHERE order_id>1000000 ORDER BY order_id desc");
      if ($query->num_rows>0)
      foreach($query->rows as $key => $order){
         try{
            $details = $this->getOrderDetails($order['order_id']);
            $response['orders'][$key] = $details;
         }
         catch (Exception $e){
            foreach($this->mapping as $mapKey => $field){
               $response['orders'][$key][$mapKey] =null;
            }
            $response['orders'][$key]['order_id'] = $order['order_id'];
            $response['orders'][$key]['note'] = $e->getMessage();
          /*  $response['orders'][$key]['order_id'] = $response['orders'][$key]["id"];
            //$response['orders'][$key]['order_number'] = $response['orders'][$key]["order_number"];	
            //$response['orders'][$key]['created_at'] = $response['orders'][$key]["created_at"];
            //$response['orders'][$key]['subtotal_price'] = $response['orders'][$key]["subtotal_price"];
            //$response['orders'][$key]['total_price'] = $response['orders'][$key]["total_price"];
            //$response['orders'][$key]['financial_status'] = $response['orders'][$key]["financial_status"];
            //$response['orders'][$key]['confirmed'] = $response['orders'][$key]["confirmed"];
            //,$this->db->escape($order["gateway"])
            //,$this->db->escape($order["total_discounts"])   
            $response['orders'][$key]['shipping_type'] = $response['orders'][$key]["shipping_lines"]["0"]["title"];
            $response['orders'][$key]['shipping_price'] = $response['orders'][$key]["shipping_lines"]["0"]["price"];
            $response['orders'][$key]['customer_name'] = $response['orders'][$key]["shipping_address"]["name"];
            $response['orders'][$key]['first_name'] = $response['orders'][$key]["shipping_address"]["first_name"];
            $response['orders'][$key]['last_name'] = $response['orders'][$key]["shipping_address"]["last_name"];
            $response['orders'][$key]['phone'] = $response['orders'][$key]["shipping_address"]["phone"];
            $response['orders'][$key]['email'] = $response['orders'][$key]["email"];
            $response['orders'][$key]['shipping_address'] = $response['orders'][$key]["shipping_address"]["address1"].', '.$response['orders'][$key]["shipping_address"]["province"].', '.$response['orders'][$key]["shipping_address"]["zip"];
            $response['orders'][$key]['note'] = $response['orders'][$key]["note"];
            
            $response['orders'][$key]['shopify_id'] = null;
            $response['orders'][$key]['shopify_upd'] = null;
            $response['orders'][$key]['last_modified'] = null;
            $response['orders'][$key]['delivery_dt'] = null;
            $response['orders'][$key]['delivery_address'] = null;
            $response['orders'][$key]['status'] = null;
            $response['orders'][$key]['status_dt'] = null;
            $response['orders'][$key]['payment_status'] = null;
            $response['orders'][$key]['payment_dt'] = null;
            $response['orders'][$key]['delivery_total'] = null;
            $response['orders'][$key]['cash_on_delivery'] = null;
            $response['orders'][$key]['comments'] = null;*/
        }
     }
     return $response;
  }
  function loadOrders(){
      $last_id = null;
      $query = $this->db->query("SELECT ifnull(max(order_id),0) last_id FROM orders");
      if ($query->num_rows>0) {
          $last_id = $query->row['last_id'];
      }
      $shopifyClient = new ShopifyClient(SHOPIFY_SHOP, "", SHOPIFY_API_KEY, SHOPIFY_SECRET);
      $response = $shopifyClient->getOrders($last_id);
      foreach($response['orders'] as $key => $order){
        $stmt=sprintf(      "INSERT INTO `orders` (order_id,
         `sid`,`first_name`,`last_name`, `middle_name`,`email`,`phone`,`address`,
         `order_date`,`delivery_date`,`status`,`note`,deliv_type,deliv_price,gateway,dis_code)
            VALUES (%f,
         '%s','%s','%s','%s','%s','%s','%s',
         '%s',null,0,'%s','%s',%f,'%s','%s');"
         ,$this->db->escape($order["id"])
         ,$this->db->escape($order["order_number"])
         ,$this->db->escape($order["shipping_address"]["first_name"])
         ,$this->db->escape($order["shipping_address"]["last_name"])
         ,''
         ,$this->db->escape($order["email"])
         ,$this->db->escape($order["shipping_address"]["phone"])
         ,$this->db->escape($order["shipping_address"]["zip"].', '.$order["shipping_address"]["city"].', '.$order["shipping_address"]["address1"].', ('.$order["shipping_address"]["country"].', '.$order["shipping_address"]["province"].')' )
         ,$this->db->escape($order["created_at"])
         ,$this->db->escape($order["note"])
		 ,$this->db->escape($order["shipping_lines"]["0"]["title"])
         ,$this->db->escape($order["shipping_lines"]["0"]["price"] )
         ,$this->db->escape($order["gateway"])
         ,$this->db->escape($order["total_discounts"])
        );
        $query = $this->db->query($stmt);
        //if (isset($order["fulfillments"][0])){
		//$fulfills = $order["fulfillments"][0]["line_items"];
        foreach($order["line_items"] as $itemKey => $item){
            $stmt = sprintf(
             "INSERT INTO order_details (nmcl_id,nmcl_name,ctg_id,order_id,quantity,price,amount) ".
             "VALUES ( %f,'%s',318,%f,%f,%f,%f)"
                    ,$this->db->escape($item["product_id"])
                    ,$this->db->escape($item["title"])
                    ,$this->db->escape($order["id"])
                    ,$this->db->escape($item["quantity"])
                    ,$this->db->escape($item["price"])
                    ,$this->db->escape($item["quantity"]*$item["price"])
            );
            $query = $this->db->query($stmt);
        }
        //}
     }
     return true;
  }
  function updateAddress(){
      $last_id = 0;
      $shopifyClient = new ShopifyClient(SHOPIFY_SHOP, "", SHOPIFY_API_KEY, SHOPIFY_SECRET);
      $response = $shopifyClient->getOrders($last_id);
      foreach($response['orders'] as $key => $order){
        $stmt=sprintf(      "UPDATE `orders` set `address` = '%s' where order_id = %f",
         $this->db->escape($order["shipping_address"]["zip"].', '.$order["shipping_address"]["city"].', '.$order["shipping_address"]["address1"].', ('.$order["shipping_address"]["country"].', '.$order["shipping_address"]["province"].')' 
         ),$order["id"]);
        $query = $this->db->query($stmt);
     }
     return true;
  }
  function updateZip(&$order){
      if(isset($order["shipping_address"])){
     $stmt=sprintf("UPDATE `orders` set `post_id` = '%s' where order_id = %f and post_id is null",
              $this->db->escape($order["shipping_address"]["zip"]),$order["id"]
           );
     $this->db->query($stmt);
      }
  }
   function updateTrackingNum(){
      $last_id = 0;
      $shopifyClient = new ShopifyClient(SHOPIFY_SHOP, "", SHOPIFY_API_KEY, SHOPIFY_SECRET);
      $response = $shopifyClient->getOrders($last_id);
      foreach($response['orders'] as $key => $order){
        if(isset($order["fulfillments"])&& isset($order["fulfillments"][0]) ){
        $tn = $order["fulfillments"][0]["tracking_number"];
        if(isset($tn)&&strlen($tn)>0){
          try{
          $o = $this->getOrderDetails($order["id"]);
          if ($o["shopify_id"] != null) {
            $stmt=sprintf("UPDATE `kn_orders` set `transp_number` = '%s' where shopify_id = %f and transp_number is null",
                          $this->db->escape($tn),$order["id"]);
          } else {
            $stmt = sprintf(
                 "INSERT INTO kn_orders (shopify_id,transp_number) "
                 ."VALUES (%f,%s)",
                    $this->db->escape($order["id"]),
                    $this->formatStringValue($tn));
          }
          $query = $this->db->query($stmt);
          } catch (Exception $e){
              
          }
        }
        }
        $this->updateZip($order);
     }
     return true;
  }
  function updateOrderStatus($order_id,$status){
      $stmt = sprintf("UPDATE orders set status = %d WHERE order_id=%f",
                $this->db->escape($status),
                $this->db->escape($order_id)
              );
      $this->db->query($stmt);
  }
  function saveOrderDetails(&$details){
      $res = true;
      if ($details){
          $order = $this->getOrderDetails($details['order_id']);
          if ($order["shopify_id"] != null) {
            $stmt = sprintf("UPDATE kn_orders set ".
            //"shopify_upd = ".$this->formatValue($details['shopify_upd']).
            "delivery_dt = %s".
            ",delivery_address = %s".
           // ",status = ".$this->formatValue($details['status']).
           // ",status_dt = ".$this->formatValue($details['status_dt']).
           // ",payment_status = ".$this->formatValue($details['payment_status']).
           // ",payment_dt = ".$this->formatValue($details['payment_dt']).
            ",delivery_total = %s".
            ",cash_on_delivery = %s".
            ",comments = %s".
            ",transp_number = %s".
            ",transp_dest_code = %s".
            ",transp_order_date = %s".
            ",transp_order_status= %s".
            " WHERE shopify_id = %f",
            $this->formatStringValue($details['delivery_dt']),
            $this->formatStringValue($details['delivery_address']),
           // ",status = ".$this->formatValue($details['status']).
           // ",status_dt = ".$this->formatValue($details['status_dt']).
           // ",payment_status = ".$this->formatValue($details['payment_status']).
           // ",payment_dt = ".$this->formatValue($details['payment_dt']).
            $this->formatStringValue($details['delivery_total']),
            $this->formatStringValue($details['cash_on_delivery']),
            $this->formatStringValue($details['comments']),
            $this->formatStringValue($details['transp_number']),
            $this->formatStringValue($details['transp_dest_code']),
            $this->formatStringValue($details['transp_order_date']),
            $this->formatStringValue($details['transp_order_status']),
            
            $this->db->escape($details['order_id'])
            );
          } else {
            $stmt = sprintf(
                 "INSERT INTO kn_orders (shopify_id,delivery_dt,delivery_address,delivery_total,cash_on_delivery,comments,transp_number,transp_dest_code,transp_order_date,transp_order_status) "
                 ."VALUES (%f,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
                    $this->db->escape($details['order_id']),
                    $this->formatStringValue($details['delivery_dt']),
                    $this->formatStringValue($details['delivery_address']),
                    $this->formatStringValue($details['delivery_total']),
                    $this->formatStringValue($details['cash_on_delivery']),
                    $this->formatStringValue($details['comments']),
                    $this->formatStringValue($details['transp_number']),
                    $this->formatStringValue($details['transp_dest_code']),
                    $this->formatStringValue($details['transp_order_date']),
                    $this->formatStringValue($details['transp_order_status'])
                 );
          }
          $query = $this->db->query($stmt);
          if ($this->db->countAffected()>0){
             $details["message"] = "Success";
          } else {
             $details["message"] = "Order not modified (order_id:[{$details['order_id']}])";
          }
      } else {
          throw new Exception("Invalid parameters");
      } 
     return $res;
  }  
  function getOrderDetails($orderId){
    $query = $this->db->query(sprintf(
       "SELECT o.*,kn.*, concat(o.first_name,' ',o.last_name) customer_name, od.amount subtotal_price, od.amount+ifnull(ifnull(delivery_total,o.deliv_price),0) total_price, 'pending' financial_status, 1 confirmed "
       . " FROM orders o LEFT JOIN kn_orders kn ON o.order_id = kn.shopify_id "
       . " LEFT JOIN (SELECT sum(amount) amount, order_id FROM order_details GROUP BY order_id) od ON o.order_id = od.order_id "
       . " WHERE o.order_id = %f"
            ,$this->db->escape($orderId))
    );
    if ($query->num_rows>0) {
       foreach($this->mapping as $key => $field){
         $result[$key] = $query->row[$field];
       }
       $queryItems = $this->db->query(sprintf(
            "SELECT od.* FROM order_details od WHERE od.order_id = %f"
            ,$this->db->escape($orderId))
       );
       if ($queryItems->num_rows>0) {
           $result['items'] =  $queryItems->rows;
       }
    } else {
        throw new Exception("Order not found [order_id:{$orderId}]");
    }
    return $result;
  }
}

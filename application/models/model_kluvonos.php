<?php
require_once 'lib/shopify.php';
class ModelKluvonos extends Model
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
            //'subtotal_price' => "deliv_type",
            //'total_price' => "deliv_type",
            //'financial_status' => "deliv_type",
            //'confirmed' => "deliv_type",
            'shipping_type' => "deliv_type",
            'shipping_price' => "deliv_price",
            'customer_name' => "customer_name",
            'phone' => "phone",
            'email' => "email",
            'shipping_address' => "address",
            'note' => "note",
            
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
          );
          $registry->set("mapping",$mapping);
      }
      parent::__construct($registry);
  }
  function getProducts(){
      $shopifyClient = new ShopifyClient(SHOPIFY_SHOP, "", SHOPIFY_API_KEY, SHOPIFY_SECRET);
      $response = $shopifyClient->getProducts();
      return $response;
  }
  function loadProducts(){
      $shopifyClient = new ShopifyClient(SHOPIFY_SHOP, "", SHOPIFY_API_KEY, SHOPIFY_SECRET);
      $response = $shopifyClient->getProducts();
      foreach($response['products'] as $key => $product){
        $stmt = sprintf(
        "INSERT INTO nomenclatures (nmcl_id,nmcl_name,title,alt_code ,grp_id,price,price_mid,price_min,status,create_date,foto_name , foto_alt,description , tech_description)".
        " VALUES( %f,'%s','%s','%s',%d,%f,%f,%f,%d,'%s','%s','%s','%s','%s')",
                $this->db->escape($product["id"]),
				$this->db->escape($product["title"]),
				$this->db->escape($product["title"]),
				$this->db->escape($product["variants"][0]["sku"]),
				$this->db->escape(KLUVONOS_GROUP),
				$this->db->escape($product["variants"][0]["price"]),
				$this->db->escape($product["variants"][0]["price"]*0.6),
				$this->db->escape($product["variants"][0]["price"]*0.4),
				$this->db->escape(0),
				$this->db->escape($product["created_at"]),
				$this->db->escape($product["image"]["src"]),
				$this->db->escape($product["title"]),
				$this->db->escape($product["body_html"]),
				$this->db->escape(''));
        $query = $this->db->query($stmt);
      }
      return true;
  }
}

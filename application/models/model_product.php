<?php
require_once 'lib/shopify.php';
class ModelProduct extends Model
{   
  public $totalQty;
  public $totalAmount;
  public $totalAmountMin;
  public function __construct($registry = null) {
      if ($registry!=null){ //nmcl_id,nmcl_name,title,alt_code ,grp_id,price,price_mid,price_min,status,create_date,foto_name , foto_alt,description , tech_description
          $mapping = array("id"=>"nmcl_id",
            'nmcl_id' => "nmcl_id",
            'name' => "nmcl_name",
            'title' => "title",
            'sku' => "alt_code",
            'group' => "grp_id",
            'price' => "price",
            'price_mid' => "price_mid",
            'price_min' => "price_min",
            'status' => "status",
            'created_at' => 'create_date',
            'image' => 'foto_name',
            'image_alt' => 'foto_alt',
            'description' => 'description',
            'tech_description' => 'tech_description',
            'weight' => 'wght'
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
  function loadNewProducts(){
      $last_id = null;
      $query = $this->db->query("SELECT ifnull(max(nmcl_id),0) last_id FROM nomenclatures");
      if ($query->num_rows>0) {
          $last_id = $query->row['last_id'];
      }
      $shopifyClient = new ShopifyClient(SHOPIFY_SHOP, "", SHOPIFY_API_KEY, SHOPIFY_SECRET);
      $response = $shopifyClient->getProducts($last_id);
      foreach($response['products'] as $key => $product){
        $stmt = sprintf(
        "INSERT INTO nomenclatures (nmcl_id,nmcl_name,title,alt_code ,grp_id,price,price_mid,price_min,status,create_date,foto_name , foto_alt,description , tech_description,wght)".
        " VALUES( %f,'%s','%s','%s',%d,%f,%f,%f,%d,'%s','%s','%s','%s','%s',%f)",
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
				$this->db->escape(''),
                $this->db->escape($product["variants"][0]["weight"])
                );
        $query = $this->db->query($stmt);
      }
      return true;
  }
  function loadProducts(){
      $shopifyClient = new ShopifyClient(SHOPIFY_SHOP, "", SHOPIFY_API_KEY, SHOPIFY_SECRET);
      $response = $shopifyClient->getProducts();
      $details = array();
      foreach($response['products'] as $key => $product){
          $details['id'] = $product["id"];
          $details['nmcl_id'] = $product["id"];
          $details['name'] = $product["title"];
          $details['title'] = $product["title"];
          $details['sku'] = $product["variants"][0]["sku"];
          $details['group'] = KLUVONOS_GROUP;
          $details['price'] = $product["variants"][0]["price"];
          $details['price_mid'] = $product["variants"][0]["price"]*0.6;
          $details['price_min'] = $product["variants"][0]["price"]*0.4;
          $details['status'] = 0;
          $details['created_at'] = $product["created_at"];
          $details['image'] = $product["image"]["src"];
          $details['image_alt'] = $product["title"];
          $details['description'] = $product["body_html"];
          $details['tech_description'] = '';
          $details['weight'] = $product["variants"][0]["weight"];
          try{
              $this->saveProduct($details);
          } catch (SqlException $ex) {
              $response['products'][$key]['message'] = $ex->getMessage();
          }
      }
      return $response;
  }  
  function saveProduct(&$details){
      $res = true;
      if ($details){
          try{
              $product = $this->getProduct($details['nmcl_id']);
              $stmt = sprintf("UPDATE nomenclatures set ".
                      "nmcl_name = %s , ".
                      "title = %s , ".
                      "alt_code = %s , ".
                      "grp_id = %d , ".
                      "price = %f , ".
                      "price_mid = %f , ".
                      "price_min = %f , ".
                      "status = %d , ".
                      "create_date = %s , ".
                      "foto_name = %s , ".
                      "foto_alt = %s , ".
                      "description = %s , ".
                      "tech_description = %s , ".
                      "wght = %f ".
                      " WHERE nmcl_id = %f",                     
            $this->formatStringValue($details['name']),
            $this->formatStringValue($details['title']),
            $this->formatStringValue($details['sku']),
            $this->db->escape($details['group']),
            $this->db->escape($details['price']),
            $this->db->escape($details['price_mid']),
            $this->db->escape($details['price_min']),
            $this->db->escape($details['status']),
            $this->formatStringValue($details['created_at']),
            $this->formatStringValue($details['image']),
            $this->formatStringValue($details['image_alt']), 
            $this->formatStringValue($details['description']),
            $this->formatStringValue($details['tech_description']),
            $this->db->escape($details['weight']),
            $this->db->escape($details['id'])           
                    );
          } catch (NoDataFoundException $e) {
            $stmt = sprintf(
        "INSERT INTO nomenclatures (nmcl_id,nmcl_name,title,alt_code ,grp_id,price,price_mid,price_min,status,create_date,foto_name , foto_alt,description , tech_description,wght)".
        " VALUES( %f,'%s','%s','%s',%d,%f,%f,%f,%d,'%s','%s','%s','%s','%s',%f)",
                $this->db->escape($details["id"]),
				$this->db->escape($details["name"]),
				$this->db->escape($details["title"]),
				$this->db->escape($details["sku"]),
				$this->db->escape($details['group']),
				$this->db->escape($details["price"]),
				$this->db->escape($details["price_mid"]),
				$this->db->escape($details["price_min"]),
				$this->db->escape($details['status']),
				$this->db->escape($details["created_at"]),
				$this->db->escape($details["image"]),
				$this->db->escape($details["image_alt"]),
				$this->db->escape($details["description"]),
				$this->db->escape($details['tech_description']),
                $this->db->escape($details['weight'])
                 );            
          }
          $query = $this->db->query($stmt);
          if ($this->db->countAffected()>0){
             $details["message"] = "Success";
          } else {
             $details["message"] = "Product not modified (product_id:[{$details['id']}])";
          }
      } else {
          throw new Exception("Invalid parameters");
      } 
     return $res;
  }  
  function getProduct($productId){
    $query = $this->db->query(sprintf(
       "SELECT n.* FROM nomenclatures n "
       . " WHERE n.nmcl_id = %f"
            ,$this->db->escape($productId))
    );
    if ($query->num_rows>0) {
       foreach($this->mapping as $key => $field){
         $result[$key] = $query->row[$field];
       }
    } else {
        throw new NoDataFoundException("Product not found",$productId);
    }
    return $result;
  }
  function getProductByCode($code){
    $query = $this->db->query(sprintf(
       "SELECT n.* FROM nomenclatures n "
       . " WHERE n.alt_code = %s"
            ,$this->formatStringValue($code))
    );
    if ($query->num_rows==1) {
       foreach($this->mapping as $key => $field){
         $result[$key] = $query->row[$field];
       }
    } elseif ($query->num_rows<1) {
        throw new NoDataFoundException("Product not found",$code);
    } else {
        throw new TooManyRowsException("More then one product found",$code);
    }
    return $result;
  }
}

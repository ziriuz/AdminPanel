<?php

    require 'lib/shopify.php';

    /* Define your APP`s key and secret*/
    define('SHOPIFY_API_KEY','b904b7262fed210dab12278bae8ed5aa');
    define('SHOPIFY_SECRET','a124df02306b313e7ad910c0b8f02e44');//aea2432d5524a5d13ecacaa4e393f35b');
    define('SHOPIFY_SHOP','xn-m0a2c0ajebv3h.myshopify.com');

    /* Define requested scope (access rights) - checkout https://docs.shopify.com/api/authentication/oauth#scopes   */
    define('SHOPIFY_SCOPE','read_orders'); //eg: define('SHOPIFY_SCOPE','read_orders,write_orders');

    
    $shopifyClient = new ShopifyClient(SHOPIFY_SHOP, "", SHOPIFY_API_KEY, SHOPIFY_SECRET);
    $response = $shopifyClient->getProducts();
      header('Content-type: text/html; charset=utf-8');  
?> 
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//RU" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML lang="ru,en">
<HEAD>
    <META content="text/html" charset="utf-8" http-equiv="Content-Type" />
    <!-- Bootstrap core CSS -->
    <link href="application/views/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="application/views/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link rel="stylesheet" href="application/views/css/common.css"/>
    <link rel="stylesheet" href="comment.css"/>
</HEAD>
<BODY>
<?php
    //var_dump($response);
    echo"<table class=\"table\"><tr><td>title</td>
        <td>sku</td>
		<td>inventory_quantity</td>
        <td>price</td>
        <td>barcode</td>
        <td>weight</td> 
        <td>weight_unit</td>
        <td>image</td><td>image uri</td><td>tags</td><td  width=\"20%\">describtion</td>
        <td>id</td>
        <td>vendor</td>
        <td>product_type</td>
        <td>created_at</td>
        <td>handle</td>
        <td>updated_at</td>
        <td>published_at</td>
        <td>published_scope</td>
        </tr>";
    foreach ($response ["products"] as $key => $product) {
        echo "<tr>";
        echo "<td>{$product["title"]}</td>";	
		echo "<td>{$product["variants"][0]["sku"]}</td>";
        echo "<td>{$product["variants"][0]["inventory_quantity"]}</td>";
        echo "<td>{$product["variants"][0]["price"]}</td>";
        echo "<td>{$product["variants"][0]["barcode"]}</td>";
        echo "<td>{$product["variants"][0]["weight"]}</td>" ;
        echo "<td>{$product["variants"][0]["weight_unit"]}</td>";        
        echo "<td><img width=100 src=\"{$product["image"]["src"]}\"></td>";
        echo "<td>{$product["image"]["src"]}</td>";
        echo "<td>{$product["tags"]}</td>";
        if (isset($_GET["desc"]))
        echo "<td width=\"20%\"><div style=\"width:400px;height:100px;overflow:auto\">{$product["body_html"]}</div></td>";
        else echo "<td/>";
        echo "<td>{$product["id"]}</td>";
        echo "<td>{$product["vendor"]}</td>";
        echo "<td>{$product["product_type"]}</td>";
        echo "<td>{$product["created_at"]}</td>";
        echo "<td>{$product["handle"]}</td>";
        echo "<td>{$product["updated_at"]}</td>";
        echo "<td>{$product["published_at"]}</td>";
        echo "<td>{$product["published_scope"]}</td>";
        echo "</tr>";
    }
    echo"</table>";
/*        ["vendor"]
        ["product_type"]
        ["created_at"]
        ["handle"]
        ["updated_at"]
        ["published_at"]
        ["published_scope"]
        ["tags"]
        ["variants"]=> array(1) {
             [0]=> array(24) { 
                  ["id"]=> float(33418883596) 
                  ["product_id"]=> float(9558193932) 
                  ["title"]=> string(13) "Default Title" 
                  ["price"]=> string(6) "430.00" 
                  ["sku"]=> string(0) "" 
                  ["position"]=> int(1) 
                  ["grams"]=> int(100) 
                  ["inventory_policy"]=> string(8) "continue" 
                  ["compare_at_price"]=> NULL 
                  ["fulfillment_service"]=> string(6) "manual" 
                  ["inventory_management"]=> string(7) "shopify" 
                  ["option1"]=> string(13) "Default Title" 
                  ["option2"]=> NULL 
                  ["option3"]=> NULL 
                  ["created_at"]=> string(25) "2017-02-01T02:25:28+03:00" ["updated_at"]=> string(25) "2017-02-14T19:35:11+03:00" 
                  ["taxable"]=> bool(false) 
                 ["barcode"]=> string(0) "" 
                 ["image_id"]=> NULL 
                 ["inventory_quantity"]=> int(0) 
                 ["weight"]=> float(0.1) 
                 ["weight_unit"]=> string(2) "kg" 
                 ["old_inventory_quantity"]=> int(0) 
                 ["requires_shipping"]=> bool(true) } } 
        ["options"]=> array(1) { [0]=> array(5) { ["id"]=> float(11550318028) ["product_id"]=> float(9558193932) ["name"]=> string(5) "Title" ["position"]=> int(1) ["values"]=> array(1) { [0]=> string(13) "Default Title" } } } 
        ["images"]=> array(3) { [0]=> array(7) { 
            ["id"]=> float(22158865356) 
            ["product_id"]=> float(9558193932) 
            ["position"]=> int(1) 
            ["created_at"]=> string(25) "2017-02-09T11:57:46+03:00" 
            ["updated_at"]=> string(25) "2017-02-09T11:57:54+03:00" 
            ["src"]=> string(76) "https://cdn.shopify.com/s/files/1/1706/3635/products/letter.jpg?v=1486630674" 
            ["variant_ids"]=> array(0) { } } 
            [1]=> array(7) { ["id"]=> float(22075294220) ["product_id"]=> float(9558193932) ["position"]=> int(2) ["created_at"]=> string(25) "2017-02-06T21:48:58+03:00" ["updated_at"]=> string(25) "2017-02-09T11:57:54+03:00" ["src"]=> string(117) "https://cdn.shopify.com/s/files/1/1706/3635/products/IMG_7608-3_155dd963-f21c-47f7-a433-e6aaa2af17e5.jpg?v=1486630674" ["variant_ids"]=> array(0) { } } 
            [2]=> array(7) { ["id"]=> float(22134566604) ["product_id"]=> float(9558193932) ["position"]=> int(3) ["created_at"]=> string(25) "2017-02-08T13:33:02+03:00" ["updated_at"]=> string(25) "2017-02-09T11:57:54+03:00" ["src"]=> string(75) "https://cdn.shopify.com/s/files/1/1706/3635/products/ring1.jpg?v=1486630674" ["variant_ids"]=> array(0) { } } } 
        ["image"]=> array(7){ 
            ["id"]=> float(22158865356) 
            ["product_id"]=> float(9558193932) 
            ["position"]=> int(1) 
            ["created_at"]=> string(25) "2017-02-09T11:57:46+03:00" 
            ["updated_at"]=> string(25) "2017-02-09T11:57:54+03:00" 
            ["src"]=> string(76) "https://cdn.shopify.com/s/files/1/1706/3635/products/letter.jpg?v=1486630674" 
            ["variant_ids"]=> array(0) { } } } [1]=> array(16) { 
                ["id"]=> float(9558198156) ["title"]=> string(31) "Клювонос "Дорога"" ["body_html"]=> string(878) "
*/
    
    
    /*
    if (isset($_GET['code'])) { // if the code param has been sent to this page... we are in Step 2
        // Step 2: do a form POST to get the access token
        $shopifyClient = new ShopifyClient($_GET['shop'], "", SHOPIFY_API_KEY, SHOPIFY_SECRET);
        session_unset();

        // Now, request the token and store it in your session.
        $_SESSION['token'] = $shopifyClient->getAccessToken($_GET['code']);
        if ($_SESSION['token'] != '')
            $_SESSION['shop'] = $_GET['shop'];

        header("Location: index.php");
        exit;       
    }
    // if they posted the form with the shop name
    else if (isset($_POST['shop'])) {

        // Step 1: get the shopname from the user and redirect the user to the
        // shopify authorization page where they can choose to authorize this app
        $shop = isset($_POST['shop']) ? $_POST['shop'] : $_GET['shop'];
        $shopifyClient = new ShopifyClient($shop, "", SHOPIFY_API_KEY, SHOPIFY_SECRET);

        // get the URL to the current page
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") { $pageURL .= "s"; }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["SCRIPT_NAME"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"];
        }

        // redirect to authorize url
        header("Location: " . $shopifyClient->getAuthorizeUrl(SHOPIFY_SCOPE, $pageURL));
        exit;
    }
*/
    // first time to the page, show the form below
?>
</BODY>
</HTML>
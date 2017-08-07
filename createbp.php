<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
require_once( 'simplexml.php' );
global $wpdb;
if (!file_exists(wp_upload_dir()['basedir'] . '/best-price')) {
    wp_mkdir_p(wp_upload_dir()['basedir'] . '/best-price');
}
if (!file_exists(wp_upload_dir()['basedir'] . '/best-price/bp.xml')) {
    touch(wp_upload_dir()['basedir'] . '/best-price/bp.xml');
}
if (file_exists(wp_upload_dir()['basedir'] . '/best-price/bp.xml')) {
    $xmlFile = wp_upload_dir()['basedir'] . '/best-price/bp.xml';
} else {
    echo "Could not create file.";
}
$xml = new feed_SimpleXMLExtended('<?xml version="1.0" encoding="utf-8"?><webstore/>');
$now = date('Y-n-j G:i');
$xml->addChild('date', "$now");
$products = $xml->addChild('products');
$xml_rows = generate_products_xml_data();
$featureslist = get_option('features', array());
foreach ($xml_rows as $prod_id => $row) {
   
    $product = $products->addChild('product');
    $product->mpn = NULL;
    $product->mpn->addCData(addslashes(trim($row['skus_ds']) == '' ? $prod_id : $row['skus_ds']));
    $product->addChild('productId', $prod_id);
    $product->name = NULL;
    $product->name->addCData($row['title']);
    $product->link = NULL;
    $product->link->addCData(get_permalink($prod_id));
    $product->image = NULL;
    $product->image->addCData($row['image_ds']);
    $product->categoryPath = NULL;
    $product->categoryPath->addCData($row['category_path']);
    $product->addChild('categoryID', $row['category_id']);
    $product->addChild('price', $row['price']);
    $product->description = NULL;
    $product->description->addCData($row['descr']);
    if (strcmp($row['stockstatus'], "instock") == 0) {
        $product->addChild('instock', "Y");
        $product->addChild('availability', $row['availabilityST']);
    } else {
        if (strcmp($row['backorder'], "notify") == 0) {
            $product->addChild('instock', "N");
            $product->addChild('availability', "Διαθέσιμο κατόπιν παραγγελίας");
        } else if (strcmp($row['backorder'], "yes") == 0) {
            $product->addChild('instock', "Y");
            $product->addChild('availability', $row['availabilityST']);
        } else {
            $product->addChild('instock', "N");
            $product->addChild('availability', "Μη διαθέσιμο");
        }
    }
    $product->addChild('size', $row['sizestring']);
    $product->manufacturer = NULL;
    $product->manufacturer->addCData($row['manufacturer']);
    $product->color = NULL;
    $product->color->addCData($row['colorstring']);
    $features = $product->addChild('features');
    if ($featureslist != null) {
        foreach ($featureslist as $feature) {
            if (array_key_exists($feature, $row['terms']) && array_key_exists($feature, $row['attributes'])) {
                $attname = $row['attributes'][$feature]->get_taxonomy_object()->attribute_name;
                $features->$attname = NULL;
                $features->$attname->addCData(implode(', ', $row['terms'][$feature]));
            }
        }
    }
}
echo '</br>SUCCESSFUL CREATION OF Best Price XML</br>';
$xml->saveXML($xmlFile);
echo 'The file is located at <a href="' . wp_upload_dir()['baseurl'] . '/best-price/bp.xml" target="_blank">' . wp_upload_dir()['baseurl'] . '/best-price/bp.xml</a>'
?>

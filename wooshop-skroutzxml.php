<?php

/* Plugin Name: WooCommerce XML feed for Skroutz.gr & Bestprice.gr
  Plugin URI: http://www.enartia.com
  Description: XML feed creator for Skroutz & Best Price
  Version: 1.1.2
  Author: Enartia
  Author URI: https://www.enartia.com
  License: GPLv3 or later
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
load_plugin_textdomain('skroutz-woocommerce-feed', false, dirname(plugin_basename(__FILE__)) . '/languages/');

function papaki_wooshop_skroutzxml_activate() {
    
}

register_activation_hook(__FILE__, 'papaki_wooshop_skroutzxml_activate');

function skroutz_xml_admin_menu() {

    /* add new top level */
    add_menu_page(
            __('Skroutz & BestPrice', 'skroutz-woocommerce-feed'), __('Skroutz & BestPrice', 'skroutz-woocommerce-feed'), 'manage_options', 'skroutz_xml_admin_menu', 'skroutz_xml_admin_page', plugins_url('/', __FILE__) . '/images/xml-icon.png'
    );

    /* add the submenus */
    add_submenu_page(
            'skroutz_xml_admin_menu', __('Create Feeds', 'skroutz-woocommerce-feed'), __('Create Feeds', 'skroutz-woocommerce-feed'), 'manage_options', 'skroutz_xml_create_page', 'skroutz_xml_create_page'
    );
}

add_action('admin_menu', 'skroutz_xml_admin_menu');
add_action('admin_init', 'register_mysettings');

function skroutz_xml_admin_page() {

    add_action('wp', 'skroutz_xml_setup_schedule');
    $skicon = plugins_url('/', __FILE__) . '/images/skroutz.png';
    $bpicon = plugins_url('/', __FILE__) . '/images/bp.png';
    echo '<div><img src="' . $skicon . '" height="150px"> <img src="' . $bpicon . '" height="150px">';


    echo '<h2>' . __('Create Feeds for Skroutz.gr and bestprice.gr', 'skroutz-woocommerce-feed') . '</h2>';
    echo '</div>';

    global $woocommerce;
    $attribute_taxonomies = wc_get_attribute_taxonomies();

    echo '<form method="post" action="options.php">';
    settings_fields('skroutz-group');
    do_settings_sections('skroutz-group');
    echo '<table class="form-table">
        <tr valign="top">
        <th scope="row">' . __('When in Stock Availability', 'skroutz-woocommerce-feed') . '</th><td>';


    $options = get_option('instockavailability');
    $items = array(
        __('Άμεση παραλαβή / Παράδoση 1 έως 3 ημέρες', 'skroutz-woocommerce-feed'),
        __('Παράδοση σε 1 - 3 ημέρες', 'skroutz-woocommerce-feed'),
        __('Παράδοση σε 4 - 10 ημέρες', 'skroutz-woocommerce-feed'),
        __('Ιδιότητα: Διαθεσιμότητα', 'skroutz-woocommerce-feed'),
        __('Custom Availability', 'skroutz-woocommerce-feed')
    );
    echo "<select id='drop_down1' name='instockavailability'>";
    foreach ($items as $key => $item) {
        $selected = ($options['instockavailability'] == $key) ? 'selected="selected"' : '';
        echo "<option value='" . esc_html($key) . "' $selected>" . esc_html($item) . "</option>";
    }
    echo "</select>";
    echo " <em>" . __('Επιλέξτε το <strong>Ιδιότητα: Διαθεσιμότητα</strong> μόνο αν έχετε προσθέσει μια Ιδιότητα Προϊόντος με ονομασία "Διαθεσιμότητα" ', 'skroutz-woocommerce-feed') . "</em>";
    echo '</td>
        </tr>
         
        </tr>
        
        <tr valign="top">
        <th scope="row">' . __('If a Product is out of Stock', 'skroutz-woocommerce-feed') . '</th>
        <td>';

    $options2 = get_option('ifoutofstock');
    $items = array(__('Include as out of Stock or Upon Request', 'skroutz-woocommerce-feed'), __('Exclude from feed', 'skroutz-woocommerce-feed'));
    echo "<select id='drop_down2' name='ifoutofstock'>";
    foreach ($items as $key => $item) {
        $selected = ($options2['ifoutofstock'] == $key) ? 'selected="selected"' : '';
        echo "<option value='" . esc_html($key) . "' $selected>" . esc_html($item) . "</option>";
    }
    echo "</select>";
    echo '</td>        </tr>   ';
    //echo '  </tr>';

    echo '	        <tr valign="top">
        <th scope="row">' . __('Features for bestprice', 'skroutz-woocommerce-feed') . '</th><td>';

    $attribute_terms = array();
    foreach ($attribute_taxonomies as $tax) {
        $term = wc_attribute_taxonomy_name($tax->attribute_name);
        $attribute_terms[$tax->attribute_id] = '';
        if (taxonomy_exists($term)) {
            $attribute_terms[$tax->attribute_id] = $term;
        }
    }
    $options3 = get_option('features');

    echo "<select id='drop_down3' name='features[]' multiple='multiple'>";
    foreach ($attribute_taxonomies as $tax) {
        $selected = false;
        if (is_array($options3) && in_array($attribute_terms[$tax->attribute_id], $options3)) {
            $selected = true;
        }

        echo "<option value='" . esc_html($attribute_terms[$tax->attribute_id]) . "' " . selected($selected, true, false) . ">" . esc_html($tax->attribute_label) . "</option>";
    }
    echo "</select>";

    echo '</td>
        </tr>';
    echo "<tr>";
    echo '<th scope="row">' . __('Skroutz Attributes', 'skroutz-woocommerce-feed') . '</th>';
    echo "<td>";
    $skroutz_atts_color = get_option('skroutz_atts_color', 'pa_color');
    $skroutz_atts_size = get_option('skroutz_atts_size', 'pa_size');
    $skroutz_atts_manuf = get_option('skroutz_atts_manuf', 'pa_brand');
    //print_r($skroutz_atts);


    echo '<label>' . __('Size', 'skroutz-woocommerce-feed') . ': <select name="skroutz_atts_size">';

    foreach ($attribute_taxonomies as $tax) {
        $selected = false;
        if ($skroutz_atts_size == $attribute_terms[$tax->attribute_id]) {
            $selected = true;
        }

        echo "<option value='" . esc_html($attribute_terms[$tax->attribute_id]) . "' " . selected($selected, true, false) . ">" . esc_html($tax->attribute_label) . "</option>";
    }
    echo '</select></label>&nbsp;&nbsp;';
    echo '<label>' . __('Color', 'skroutz-woocommerce-feed') . ': <select name="skroutz_atts_color">';
    foreach ($attribute_taxonomies as $tax) {
        $selected = false;
        if ($skroutz_atts_color == $attribute_terms[$tax->attribute_id]) {
            $selected = true;
        }

        echo "<option value='" . esc_html($attribute_terms[$tax->attribute_id]) . "' " . selected($selected, true, false) . ">" . esc_html($tax->attribute_label) . "</option>";
    }
    echo '</select></label>&nbsp;&nbsp;';
    echo '<label>' . __('Manufacturer', 'skroutz-woocommerce-feed') . ': <select name="skroutz_atts_manuf">';
    if ($skroutz_atts_manuf == '') {
        $selected = true;
    }
    echo "<option value='' " . selected($selected, true, false) . ">" . __('-Empty-', 'skroutz-woocommerce-feed') . "</option>";
    foreach ($attribute_taxonomies as $tax) {
        $selected = false;
        if ($skroutz_atts_manuf == $attribute_terms[$tax->attribute_id]) {
            $selected = true;
        }

        echo "<option value='" . esc_html($attribute_terms[$tax->attribute_id]) . "' " . selected($selected, true, false) . ">" . esc_html($tax->attribute_label) . "</option>";
    }
    echo '</select></label>';
    echo "</td>";
    echo "</tr>";
    echo ' </table>';
    submit_button();
    echo '</form></div>';


    echo '<a class="button button-primary" href="' . get_admin_url() . 'admin.php?page=skroutz_xml_create_page">' . __('Create XML Feeds', 'skroutz-woocommerce-feed') . '</a>';
}

function register_mysettings() { // whitelist options
    register_setting('skroutz-group', 'instockavailability', 'sanitize_options');
    register_setting('skroutz-group', 'ifoutofstock', 'sanitize_options');
    register_setting('skroutz-group', 'features', 'sanitize_options_multi');
    //register_setting('skroutz-group', 'skroutz_atts', 'sanitize_options_multi');
    register_setting('skroutz-group', 'skroutz_atts_color', 'sanitize_options');
    register_setting('skroutz-group', 'skroutz_atts_manuf', 'sanitize_options');
    register_setting('skroutz-group', 'skroutz_atts_size', 'sanitize_options');
}

function sanitize_options($input) {

    return esc_html($input);
}

function sanitize_options_multi($input) {

    $output = array();

    foreach ($input as $in_value) {
        $output[] = esc_html($in_value);
    }


    return $output;
}

function skroutz_xml_create_page() {

    $skicon = plugins_url('/', __FILE__) . '/images/skroutz.png';
    $bpicon = plugins_url('/', __FILE__) . '/images/bp.png';
    echo '<div><img src="' . $skicon . '" height="150px"> <img src="' . $bpicon . '" height="150px">';
    echo '<h2>' . __('Create Feeds for Skroutz.gr and bestprice.gr', 'skroutz-woocommerce-feed') . '</h2>';
    echo '</div>';

    settings_fields('skroutz-group');
    do_settings_sections('skroutz-group');

    $active = 0; // get_option('activefeeds');
    if ($active == 0 | $active == 1) {
        require_once 'createsk.php';
    }
    echo '</br>';
    if ($active == 0 | $active == 2) {
        require_once 'createbp.php';
    }
    if (!wp_next_scheduled('skroutz_xml_hourly_event')) {
        wp_schedule_event(time(), 'hourly', 'skroutz_xml_hourly_event');
    }
}

add_action('skroutz_xml_hourly_event', 'skroutz_xml_do_this_hourly');

/**
 * On the scheduled action hook, run a function.
 */
function skroutz_xml_do_this_hourly() {
    // do something every hour


    $active = 0; // get_option('activefeeds');
    if ($active == 0 | $active == 1) {
        require_once 'createsk.php';
    }
    if ($active == 0 | $active == 2) {
        require_once 'createbp.php';
    }

    if (!wp_next_scheduled('skroutz_xml_hourly_event')) {
        wp_schedule_event(time(), 'hourly', 'skroutz_xml_hourly_event');
    }
}

function generate_products_xml_data() {
    $xml_rows = array();
    $instockavailability = get_option('instockavailability');
    $avaibilities = array("Άμεση παραλαβή / Παράδoση 1 έως 3 ημέρες", "Παράδοση σε 1 - 3 ημέρες", "Παράδοση σε 4 - 10 ημέρες", "attribute");
    $availabilityST = $avaibilities[$instockavailability];
    $ifoutofstock = get_option('ifoutofstock');
    $format_price = false;
    if (function_exists('wc_get_price_decimal_separator') && function_exists('wc_get_price_thousand_separator') && function_exists('wc_get_price_decimals')) {
        $decimal_separator = wc_get_price_decimal_separator();
        $thousand_separator = wc_get_price_thousand_separator();
        $decimals = wc_get_price_decimals();
        $format_price = true;
    }
    $result = wc_get_products(array('status' => array('publish'), 'limit' => -1));
    foreach ($result as $index => $prod) {

        $attributes = $prod->get_attributes();
        $stockstatus_ds = $prod->get_stock_status();
        if ((strcmp($stockstatus_ds, "outofstock") == 0) & ($ifoutofstock == 1)) {
            continue;
        }
        $onfeed = $prod->get_meta('onfeed');
        if (strcmp($onfeed, "no") == 0) {
            continue;
        }
        $xml_rows[$prod->get_id()] = array(
            'onfeed' => $onfeed,
            'stockstatus' => $stockstatus_ds,
            'attributes' => $attributes
        );

        switch ($instockavailability) {
            case 3:
                //_product_attributes
                $_product_attributes_ser_ds = $attributes;
                if (is_serialized($_product_attributes_ser_ds)) {
                    $_product_attributes = unserialize($_product_attributes_ser_ds);
                    foreach ($_product_attributes as $key => $attr) {
                        if ($attr['name'] == 'Διαθεσιμότητα') {
                            $availabilityST = $attr['value'];
                            break;
                        }
                    }
                }
                break;
            case 4:
                //_product_attributes            
                $tmp_availability = $prod->get_meta('_custom_availability');
                if ($tmp_availability != '') {
                    $availabilityST = $tmp_availability;
                }
                break;
            default:
                break;
        }
        $xml_rows[$prod->get_id()]['availabilityST'] = $availabilityST;
        $price = $prod->get_price();
        $xml_rows[$prod->get_id()]['price_raw'] = $price;
        if ($format_price) {
            $price = number_format($price, $decimals, $decimal_separator, $thousand_separator);
        }
        $xml_rows[$prod->get_id()]['price'] = addslashes($price);
        $image_ds = get_the_post_thumbnail_url($prod->get_id(), 'shop_catalog');
        $xml_rows[$prod->get_id()]['image_ds'] = $image_ds;
        $skus_ds = $prod->get_sku();
        $xml_rows[$prod->get_id()]['skus_ds'] = $skus_ds;
        $categories_ds = $prod->get_category_ids();
        $_weight_ds = $prod->get_weight();
        $xml_rows[$prod->get_id()]['_weight_ds'] = $_weight_ds;
        $skroutz_atts_color = get_option('skroutz_atts_color', 'pa_color');
        $skroutz_atts_size = get_option('skroutz_atts_size', 'pa_size');
        $skroutz_atts_manuf = get_option('skroutz_atts_manuf', 'pa_brand');


        $sizestring = '';
        $xml_rows[$prod->get_id()]['sizes'] = array();
        if (isset($attributes[$skroutz_atts_size]) && $attributes[$skroutz_atts_size] != null) {
            $sizes = $attributes[$skroutz_atts_size]->get_terms();
            foreach ($sizes as $i => $size_term) {
                $sizestring .= format_number_skroutz($size_term->name) . ', ';
                $xml_rows[$prod->get_id()]['sizes'][] = format_number_skroutz($size_term->name);
            }
        }
        if (strlen($sizestring) > 2) {
            $sizestring = substr($sizestring, 0, -2);
        }
        $xml_rows[$prod->get_id()]['sizestring'] = $sizestring;
        $man = '';
        if (isset($attributes[$skroutz_atts_manuf]) && $attributes[$skroutz_atts_manuf] != null) {
            $brands = $attributes[$skroutz_atts_manuf]->get_terms();
            foreach ($brands as $brand_term) {
                $man = $brand_term->name;
            }
        }
        $xml_rows[$prod->get_id()]['manufacturer'] = $man;
        $colorRes = '';
        $xml_rows[$prod->get_id()]['colors'] = array();
        if (isset($attributes[$skroutz_atts_color]) && $attributes[$skroutz_atts_color] != null) {
            $colors = $attributes[$skroutz_atts_color]->get_terms();
            foreach ($colors as $color_term) {
                $colorRes .= $color_term->name . ', ';
                $xml_rows[$prod->get_id()]['colors'][] = $color_term->name;
            }
        }
        if (strlen($colorRes) > 2) {
            $colorRes = substr($colorRes, 0, -2);
        }
        $xml_rows[$prod->get_id()]['colorstring'] = $colorRes;
        $xml_rows[$prod->get_id()]['terms'] = array();
        foreach ($attributes as $att_key => $prod_att) {
            $xml_rows[$prod->get_id()]['terms'][$att_key] = array();
            $prod_terms = $prod_att->get_terms();
            foreach ($prod_terms as $the_term) {
                $xml_rows[$prod->get_id()]['terms'][$att_key][] = $the_term->name;
            }
        }
        $prod_category_tree = array_map('get_term', array_reverse(wc_get_product_cat_ids($prod->get_id())));
        $xml_rows[$prod->get_id()]['categories'] = array();
        $category_path = '';
        for ($i = 0; $i < count($prod_category_tree); $i++) {
            if ($i == 0) {
                $xml_rows[$prod->get_id()]['category_id'] = $prod_category_tree[$i]->term_id;
            }
            $category_path.=$prod_category_tree[$i]->name;
            $xml_rows[$prod->get_id()]['categories'][] = $prod_category_tree[$i]->name;
            if ($i < count($prod_category_tree) - 1)
                $category_path.=', ';
        }
        $xml_rows[$prod->get_id()]['category_path'] = $category_path;
        $title = str_replace("'", " ", $prod->get_title());
        $title = str_replace("&", "+", $title);
        $title = strip_tags($title);
        $xml_rows[$prod->get_id()]['title'] = $title;
        $backorder = $prod->get_backorders();
        $xml_rows[$prod->get_id()]['backorder'] = $backorder;
        $xml_rows[$prod->get_id()]['descr'] = $prod->get_short_description();
    }
    return $xml_rows;
}

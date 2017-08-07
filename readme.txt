=== WooCommerce XML feed for Skroutz.gr & Bestprice.gr ===
Plugin URI: http://www.enartia.com
Description: XML feed creator for Skroutz & Best Price
Requires at least: 4.7
Tested up to: 4.8
Stable tag: 1.1.2
Contributors: enartia,g.georgopoulos
Author URI: https://www.enartia.com
Tags: ecommerce, e-commerce,  wordpress ecommerce, xml, feed
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Create Skroutz.gr and Bestprice.gr XML feeds for Woocommerce

== Description ==

With this plugin you can create XML feeds for Skroutz.gr and Bestprice.gr.


== Frequently Asked Questions ==

= When in Stock Availability =
Dropdown  option "When in Stock Availability"   with options will show for all in Stock products 
"Available", "1 to 3 days", "4 to 7 days", "7+ days" as availability

= If Product Attribute: Availability is used =
Dropdown  option "When in Stock Availability" value "Product Attribute: Availability" must be used

= If Custom Availability plugin is used =
Dropdown  option "When in Stock Availability" value "Custom Availability" must be used

= If a Product is out of Stock =
Dropdown  option "If a Product is out of Stock"  with options will 
"Include as out of Stock or Upon Request" or "Exclude from feed"

= Add mpn/isbn to product =

To add mpn/isbn to the product just fill in the SKU field of WooCommerce


= Add color =

To add the color to a product , in order to be printed on the XML feed add an attribute with Slug "color" , Type "Select" and Name of your choice

= Add manufacturer =

To add the manufacturer to a product , in order to be printed on the XML feed add an attribute with Slug "manufacturer" , Type "Select" and Name of your choice

OR

Brands plugins are supported to be shown as manufacturer.


= Add sizes =

To add the size to a product, in order to be printed on the XML feed, add an attribute with Slug "size", Type "Select" and Name of your choice. 
Then is created a variable product with this attribute.

If you have stock management enabled on variations, sizes with stock lower or equal to 0 will not be shown on the feed

= Remove item from feed =

If you want to remove items from the feed, you can add a special field in the product edit area "onfeed" with value "no".

= Backorder =
If you have enabled backorder and set to notify, the product will be shown as upon order and not in stock. 

If you have selected Yes, the product will be shown as available and in stock. 

If you have selected no to backorder, the product will be not available. 


== Changelog ==

= Version: 1.0.2 =
WooCommerce 3.0 compatibility.

= Version: 1.0.0 =
Initial Release




=== OpenCart Customers Import ===
Contributors: Mostafa Saeed
Tags: OpenCart, Customers, WooCommerce
Tested up to: 5.0.3
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
This plugin allows you to import OpenCart customers with their hashed passwords. And when the imported customer tries to login, It updates the OpenCart hashed password to WordPress hash instead. It makes use of the fact that SHA1 function will always give the same hash with the same password! So when the user enters the right password, The SHA1 function will give the same imported hash.

== Installation ==
1. Install the plugin from WordPress plugins page.
1. Activate the plugin through the 'Plugins' screen in admin dashboard.
1. From Users menu go to 'OpenCart Customers Import'.
1. Upload the 'oc_customer' table data in JSON format.

=== WooCommerce Local Pickup Time Select ===
Contributors: mjbanks
Donate link: http://mattbanks.me/donate/
Tags: woocommcerce, shipping, local pickup, checkout fields, ecommerce, e-commerce, wordpress ecommerce
Requires at least: 3.8
Tested up to: 3.9
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add an an option to WooCommerce checkout pages for Local Pickup orders to allow the user to choose a pickup time, defined in the admin area.

== Description ==

Local Pickup Time extends the [WooCommerce](http://wordpress.org/plugins/woocommerce/) Local Pickup shipping option to allow users to choose a pickup time.

In the admin area, under WooCommerce -> Settings -> General, you can set the start and end times for order pickups each day, as well as define days the store is closed and allow you to select a time interval for allowing pickups.

**Right now, the plugin works for pickups on the current day only. It will support an option to choose the number of days ahead to allow pickup orders in an upcoming version.**

** Requires WooCommerce 2.x **

== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'woocommerce-local-pickup-time'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `woocommerce-local-pickup-time.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `woocommerce-local-pickup-time.zip`
2. Extract the `woocommerce-local-pickup-time` directory to your computer
3. Upload the `woocommerce-local-pickup-time` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard

=== Usage ===

Navigate to `WooCommerce -> Settings -> General`, edit your start and end times for daily pickups, set your days closed and time interval for pickups.

== Frequently Asked Questions ==

= Things aren't displaying properly =

Go to `WooCommerce -> Settings -> General` and Save Changes to trigger the options to update.

Make sure to set your Timezone on the WordPress Admin Settings page.

== Screenshots ==

1. Front-end display on Checkout page

== Changelog ==

= 1.0.2 =
* fix typos

= 1.0.1 =
* properly set closing time if trying to order after hours

= 1.0.0 =
* initial version

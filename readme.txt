=== Plugin Name ===
Contributors: mjbanks
Donate link: http://mattbanks.me
Tags: woocommcerce, shipping, local pickup, checkout fields, ecommerce, e-commerce, wordpress ecommerce
Requires at least: 3.8
Tested up to: 3.8.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add an an option to WooCommerce checkout pages for Local Pickup orders to allow the user to choose a pickup time, defined in the admin area.

== Description ==

Local Pickup Time extends the [WooCommerce](http://wordpress.org/plugins/woocommerce/) Local Pickup shipping option to allow users to choose a pickup time.

In the admin area, under WooCommerce -> Settings -> General, you can set the start and end times for order pickups each day, as well as define days the store is closed and allow you to select a time interval for allowing pickups.

**Right now, the plugin works for pickups on the current day only. It will support an option to choose the number of days ahead to allow pickup orders in an upcoming version.**

== Installation ==

This section describes how to install the plugin and get it working.

1. Unzip the archive and put the `local-pickup-time` folder into the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Set your Timezone in the WordPress Admin on the Settings page

=== Usage ===

Navigate to `WooCommerce -> Settings -> General`, edit your start and end times for daily pickups, set your days closed and time interval for pickups.

== Frequently Asked Questions ==

= Things aren't displaying properly =

Go to `WooCommerce -> Settings -> General` and Save Changes to trigger the options to update.

Make sure to set your Timezone on the WordPress Admin Settings page.

== Screenshots ==

1. Front-end display on Checkout page
2. WooCommerce -> Settings -> General page, showing plugin options

== Changelog ==

= 1.0 =
* initial version

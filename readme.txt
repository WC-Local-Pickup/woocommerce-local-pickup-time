=== WooCommerce Local Pickup Time Select ===
Contributors: tnolte, mjbanks, vyskoczilova
Donate link: https://www.ndigitals.com/donate/
Tags: woocommcerce, shipping, local pickup, checkout fields, ecommerce, e-commerce, wordpress ecommerce
Requires at least: 4.9
Tested up to: 5.6.0
Stable tag: 1.3.13
Requires PHP: 7.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add an option to WooCommerce checkout pages for Local Pickup orders to allow the user to choose a pickup time, defined in the admin area.

== Description ==

Local Pickup Time extends the [WooCommerce](http://wordpress.org/plugins/woocommerce/) Local Pickup shipping option to allow users to choose a pickup time.

In the admin area, under WooCommerce -> Settings -> General, you can set the start and end times for order pickups each day, as well as define days the store is closed and allow you to select a time interval for allowing pickups. In addition, you can specify a time delay between when a customer places their order and when they can pickup their order to account for processing time, as well as how many days ahead a customer can choose for their delivery.

** Requires WooCommerce 4.x **

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

= How do I change the location of the pickup time select box during checkout? =

The location, by default, is hooked to `woocommerce_after_order_notes`. This can be overridden using the `local_pickup_time_select_location` filter. [A list of available hooks can be seen in the WooCommerce documentation](http://docs.woothemes.com/document/hooks/).

= How do I change the location of the pickup time shown in the admin Order Details screen? =

The location, by default, is hooked to `woocommerce_admin_order_data_after_billing_address`. This can be overridden using the `local_pickup_time_admin_location` filter. [A list of available hooks can be seen in the WooCommerce documentation](http://docs.woothemes.com/document/hooks/).

== Screenshots ==

1. Frontend Display on Checkout Page
2. Backend Settings Screen - Part 1
3. Backend Settings Screen - Part 2
4. Backend Order Listing Includes Pickup Date/Time
5. Backend Order Details Screen Includes Pickup Date/Time

== Changelog ==

= 1.3.13 =
* Compatibility versions bump.
* Plugin owner/author transition.

= 1.3.12 =
* Fixes Time Zone related pickup delay issue.

= 1.3.11 =
* NPM development dependencies security vulnerability update.

= 1.3.10 =
* Adds check for PHP 7.1+ Date/Time object changes and makes appropriate PHP version specific calls.

= 1.3.9 =
* Fixes how the starting interval is set, especially during the middle of the current open/close pickup time.
* Sets end time 1 interval past it in order to have the end close pickup time be inclusive for pickup.
* Changes field title and help text for Pickup Days Ahead to be clear that the number does now represent the number of open days inclusive of the current day.
* Updates language files to handle some new dashboard labels and help text.
* Fixes an incorrect use of the pickup time interval instead of the delay, causing incorrect starting pickup time.
* Changes direct WC_Order id attribute access to use get_id() method.
* Fixes additonal issue with how time delay is handled on the first available pickup time.

= 1.3.8 =
* Fixes issue with recognizing the current date/time as the start of the pickup time selection.

= 1.3.7 =
* Fixes an issue with not using the WordPress locale and built-in language pack to translate the pickup date output on checkout.

= 1.3.6 =
* Fixes WordPress date/time format handling for pickup time selection.
* Fixes issues with some interval combinations.

= 1.3.5 =
* fixes issue with allowing customers to pick a pickup date/time on a non-open day.

= 1.3.4 =
* fixes 1.3.3 patch issue.

= 1.3.3 =
* fixes PHP 5.6 issue with DateTime syntax usage.

= 1.3.2 =
* fixes issue with missing space in pickup time options, from 1.3.1.
* adds pickup date/time to Order List in Admin Dashboard.
* adds longer pickup delay option.

= 1.3.1 =
* expands pickup time delay to include 4/8/16/24/36 hours.
* changes days ahead setting to allow any number of days via text input.
* changes customer display of pickup time to include the date using the WordPress date format setting.
* removes closed dates from option list even if it's not today.

= 1.3.0 =
* fix pickup time for multiple locales and update translations (props vyskoczilova)

= 1.2.0 =
* added option to select the delay from the current time until the order can be picked up
* added option to select the number of days ahead for allowing orders to be picked up

= 1.1.0 =
* added `local_pickup_time_select_location` filter to customize location of pickup time select during checkout
* added `local_pickup_time_admin_location` filter to customize location of pickup time shown in the admin Order Details screen

= 1.0.3 =
* replace deprecated call to $order->order_custom_fields, which no longer words in WooCommerce 2.1

= 1.0.2 =
* fix typos

= 1.0.1 =
* properly set closing time if trying to order after hours

= 1.0.0 =
* initial version

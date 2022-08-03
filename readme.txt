=== WooCommerce Local Pickup Time Select ===
Contributors: tnolte, mjbanks, vyskoczilova
Donate link: https://www.ndigitals.com/donate/
Tags: woocommcerce, shipping, local pickup, checkout fields, ecommerce, e-commerce, wordpress ecommerce
Requires at least: 4.9
Tested up to: 6.0.1
Stable tag: 1.4.2
Requires PHP: 7.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add an option to the WooCommerce checkout for Local Pickup orders to allow the user to choose a pickup time, defined in the admin area.

== Description ==

Local Pickup Time extends the [WooCommerce](http://wordpress.org/plugins/woocommerce/) Local Pickup shipping option to allow users to choose a pickup time.

In the admin area, under "WooCommerce -> Settings -> Shipping -> Local Pickup Time settings", you can set the start and end times for order pickups each day, as well as define days the store is closed and allow you to select a time interval for allowing pickups. In addition, you can specify a time delay between when a customer places their order and when they can pickup their order to account for processing time, as well as how many days ahead a customer can choose for their delivery.

= Features =

- **Daily Pickup Available Start/End Time:** Set the starting time and ending time for each day that pickups are available.
- **Pickup Time Interval to Allow Pickup Planning:** Define Pickup Time intervals to ensure that pickups are spaced out with adequate time to manage the number of pickups at any given time.
- **Pickup Time Delay to Allow for Required Product Preparation Time:** Setup a pickup delay to ensure that you have the required preparation time for products to be available.
- **Make Pickup Time Optional:** Allow pickup time to be optional in cases where a customer should only have the option to choose a Pickup Time but not be required to do so.
- **Limit Local Pickup Time to Local Shipping Methods Only:** Instead of always presenting a Pickup Time option on checkout only present the Pickup Time on the WooCommerce "Local Pickup" shipping method.
  - **Ability to limit to specific Shipping Zones.** Pickup Time can be limited to only specific Shipping Zones.
- **"Ready for Pickup" Order Status:** A custom Order Status of "Ready for Pickup" is available in order to better track the progress of your orders.
  - **Custom "Ready for Pickup" customer notification email template.** A custom "Email notification" can be setup under "WooCommerce -> Settings -> Emails" for when an Order is changed to "Ready for Pickup".

== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'WooCommerce Local Pickup Time Select'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Download a zip file of the plugins, which can be done from the WordPress plugin directory
2. Navigate to 'Add New' in the plugins dashboard
3. Click on the "Upload Plugin" button
4. Choose the downloaded Zip file from your computer with "Choose File"
5. Click 'Install Now'
6. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download a zip file of the plugins, which can be done from the WordPress plugin directory
2. Extract the Zip file to a directory on your computer
3. Upload the `woocommerce-local-pickup-time-select` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard

=== Usage ===

Navigate to `WooCommerce -> Settings -> Shipping -> Local Pickup Time settings`,  to edit your start and end times for daily pickups, set your days closed and time interval for pickups.

== Frequently Asked Questions ==

= Things aren't displaying properly =

- Go to `WooCommerce -> Settings -> Shipping -> Local Pickup Time settings` and "Save Changes" to trigger the options to update.
- Make sure to set your Timezone on the WordPress Admin Settings page to a proper value that is not a UTC offset.
- If "Limit to Local Pickup Shipping Methods?" is checked in the "Local Pickup Time settings", ensure you have a Shipping Zone that includes a "Local Pickup" Shipping Method. Additionally, make sure that each "Local Pickup" Shipping Method you want to have a "Pickup Time" has it enabled.

= How do I change the location of the pickup time select box during checkout? =

The location, by default, is hooked to `woocommerce_after_order_notes`. This can be overridden using the `local_pickup_time_select_location` filter. [A list of available hooks can be seen in the WooCommerce documentation](http://docs.woothemes.com/document/hooks/).

= How do I change the location of the pickup time shown in the admin Order Details screen? =

The location, by default, is hooked to `woocommerce_admin_order_data_after_billing_address`. This can be overridden using the `local_pickup_time_admin_location` filter. [A list of available hooks can be seen in the WooCommerce documentation](http://docs.woothemes.com/document/hooks/).

== Screenshots ==

1. Frontend Display on Checkout Page
2. Shipping Settings -> Local Pickup Time Settings Screen
3. Local Pickup Shipping Method Settings Screen
4. Order Listing Includes Pickup Date/Time
5. Order Details Screen Includes Pickup Date/Time
6. Ready for Pickup Order Email Notification

== Changelog ==

### 1.4.2
#### Fixed
- Checkout prevented on non-Local Shipping methods.
- Updated WordPress Supported Versions.

### 1.4.1
#### Changed
- Updated the README to provide details and usage on the latest functionality and features.

#### Fixed
- Possible PHP Fatal Error when using new Local Pickup association functionality.

#### Added
- Added new screenshot for "Ready for Pickup" email notification.

### 1.4.0
#### Changed
- Updated WordPress & WooCommerce Supported Versions.

#### Fixed
- Updated Plugin Development Dependencies

#### Added
- Added New Ready for Pickup Order Status & Customer Email
- Added Pickup Time Required & Local Pickup Link Capabilities

--------

[See the previous changelogs here](https://github.com/WC-Local-Pickup/woocommerce-local-pickup-time/blob/main/CHANGELOG.md#changelog)

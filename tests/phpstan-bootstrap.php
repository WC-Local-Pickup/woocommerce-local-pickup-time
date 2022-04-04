<?php
/**
 * Phpstan bootstrap file.
 *
 * @package   Local_Pickup_Time
 * @author    Matt Banks <mjbanks@gmail.com>
 * @license   GPL-2.0+
 * @link      http://mattbanks.me
 * @copyright 2014-2020 Matt Banks
 */

// Define WooCommerce path for autoloading in phpstan.
defined( 'WC_ABSPATH' ) || define( 'WC_ABSPATH', 'wordpress/wp-content/plugins/woocommerce/' );

// Define WordPress language directory.
defined( 'WP_LANG_DIR' ) || define( 'WP_LANG_DIR', 'wordpress/wp-includes/languages/' );

// Define Plugin base directory.
defined( 'WCLOCALPICKUPTIME_PLUGIN_BASE' ) || define( 'WCLOCALPICKUPTIME_PLUGIN_BASE', '.' );

<?php
/**
 * Phpstan bootstrap file.
 *
 * @package   Local_Pickup_Time
 * @author    Matt Banks <mjbanks@gmail.com>
 * @author    Tim Nolte <tim.nolte@ndigitals.com>
 * @copyright 2014-2024 WC Local Pickup Time
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt GPL-2.0+
 * @link      https://github.com/WC-Local-Pickup
 */

// Define whether running under WP-CLI.
defined( 'WP_CLI' ) || define( 'WP_CLI', false );

// Define WooCommerce path for autoloading in phpstan.
defined( 'WC_ABSPATH' ) || define( 'WC_ABSPATH', 'tools/local-dev/wp-content/plugins/woocommerce/' );

// Define WordPress language directory.
defined( 'WP_LANG_DIR' ) || define( 'WP_LANG_DIR', 'tools/local-dev/wp/wp-includes/languages/' );

// Define Plugin base directory.
defined( 'WCLOCALPICKUPTIME_PLUGIN_BASE' ) || define( 'WCLOCALPICKUPTIME_PLUGIN_BASE', '.' );

// Define Plugin base path.
defined( 'WCLOCALPICKUPTIME_PLUGIN_DIR' ) || define( 'WCLOCALPICKUPTIME_PLUGIN_DIR', dirname( __DIR__ ) );

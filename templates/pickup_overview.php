<?php
/**
 * Pickup Overview
 *
 * Main Template for Pickup Time Overview.
 * This Template categorizes Orders by their Pickup Time and computes two tables for each of those "buckets".
 * One is a simple order list. The other one counts the articles that are included in those buckets and prints out this count.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div name="pickup_time_overview">



	<?php

	//strings for translation
	__( 'Past', 'woocommerce-local-pickup-time' );
	__( 'Today', 'woocommerce-local-pickup-time' );
	__( 'Tomorrow', 'woocommerce-local-pickup-time' );
	__( 'Next Seven Days', 'woocommerce-local-pickup-time' );
	__( 'Rest Of Orders', 'woocommerce-local-pickup-time' );

	$plugin = Local_Pickup_Time::get_instance();
	//fill buckets
	 $buckets = $this->pickup_overview_fill_buckets();
	?>

		<?php foreach ($buckets as $label => $bucket) {

			//produce "printable" label. (replace _ with space && make first letter of each word uppercase)
			$label_text = ucwords(str_replace ( "_", " ", $label));
         ?>
		<div id="<?php echo $label ?>_pickup_time_overview">
                    <h1><?php echo esc_html_e( $label_text, 'woocommerce-local-pickup-time' )  ?></h1>
                    <?php if (!empty($bucket)) { ?>
					<h2>Order Overview</h2>
			<div id="pickup_time_order_overview_<?php echo $label ?>">
				<?php  include( 'order_overview_table.php' );//, array( 'bucket' => $bucket ) )//do_action( 'woocommerce_checkout_billing' ) ?>
			</div>
<h2>Product Quantity</h2>
			<div id="pickup_time_item_count_overview_<?php echo $label ?>">
				<?php  include( 'item_count_table.php' );//, array( 'bucket' => $bucket ) )//do_action( 'woocommerce_checkout_shipping' ) ?>
			</div>
                    <?php }
else{					?>
<p><?php echo esc_html_e( 'No Orders for this timeframe', 'woocommerce-local-pickup-time' ) 	?></p>
<?php } ?>
		</div>

		<?php }  ?>



</div>

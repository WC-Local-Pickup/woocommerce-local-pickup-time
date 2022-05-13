<?php
/**
 * Customer ready for pickup order email (plain text)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/plain/customer-ready-for-pickup-order.php.
 *
 * HOWEVER, on occasion WooCommerce Local Pickup Time will need to update
 * template files and you (the theme developer) will need to copy the new files
 * to your theme to maintain compatibility. We try to do this as little as
 * possible, but it does happen. When this occurs the version of the template
 * file will be bumped and the readme will list any important changes.
 *
 * PHPStan ignore statements required as this template setup meets WooCommerce
 * requirements.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package Local_Pickup_Time\Templates\Emails\Plain
 * @since 1.4.0
 */

defined( 'ABSPATH' ) || exit;

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
/* @phpstan-ignore-next-line */
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/* translators: %s: Customer first name */
echo sprintf( esc_html__( 'Hi %s,', 'woocommerce-local-pickup-time-select' ), esc_html( $order->get_billing_first_name() ) ) . "\n\n"; /* @phpstan-ignore-line */
echo esc_html__( 'We have finished preparing your order for pickup.', 'woocommerce-local-pickup-time-select' ) . "\n\n";

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 1.4.0
 */
/* @phpstan-ignore-next-line */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n----------------------------------------\n\n";

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
/* @phpstan-ignore-next-line */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
/* @phpstan-ignore-next-line */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n\n----------------------------------------\n\n";

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
/* @phpstan-ignore-next-line */
if ( $additional_content ) {
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );
	echo "\n\n----------------------------------------\n\n";
}

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );

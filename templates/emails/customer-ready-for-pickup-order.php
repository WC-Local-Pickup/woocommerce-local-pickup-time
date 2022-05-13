<?php
/**
 * Customer ready for pickup order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-ready-for-pickup-order.php.
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
 * @package Local_Pickup_Time\Templates\Emails
 * @since 1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
/* @phpstan-ignore-next-line */
do_action( 'woocommerce_email_header', $email_heading, $email );

/* translators: %s: Customer first name */
$email_salutation = sprintf( esc_html__( 'Hi %s,', 'woocommerce-local-pickup-time-select' ), esc_html( $order->get_billing_first_name() ) ); /* @phpstan-ignore-line */
$email_order_note = esc_html__( 'We have finished preparing your order for pickup.', 'woocommerce-local-pickup-time-select' );

$email_content = <<<HTML
<p>{$email_salutation}</p>
<p>{$email_order_note}</p>
HTML;

echo wp_kses( $email_content, array( array( '<p>' ) ) );

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 *
 * @since 1.4.0
 */
/* @phpstan-ignore-next-line */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

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

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
/* @phpstan-ignore-next-line */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
/* @phpstan-ignore-next-line */
do_action( 'woocommerce_email_footer', $email );

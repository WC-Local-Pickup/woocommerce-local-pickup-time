<?php
/**
 * Class WC_Email_Customer_Ready_For_Pickup_Order file.
 *
 * @package Local_Pickup_Time_Admin\Emails
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Email_Customer_Ready_For_Pickup_Order', false ) ) {

	/**
	 * Customer Ready for Pickup Order Email.
	 *
	 * Order ready for pickup emails are sent to the customer when the order is marked Ready for Pickup.
	 *
	 * @class   WC_Email_Customer_Ready_For_Pickup_Order
	 * @package Local_Pickup_Time_Admin\Emails
	 */
	class WC_Email_Customer_Ready_For_Pickup_Order extends WC_Email {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'customer_ready_for_pickup_order';
			$this->customer_email = true;
			$this->title          = __( 'Ready for Pickup order', 'woocommerce-local-pickup-time-select' );
			$this->description    = __( 'Order ready for pickup emails are sent to the customer when the order is marked Ready for Pickup.', 'woocommerce-local-pickup-time-select' );
			$this->template_base  = WCLOCALPICKUPTIME_PLUGIN_DIR . 'templates/';
			$this->template_html  = 'emails/customer-ready-for-pickup-order.php';
			$this->template_plain = 'emails/plain/customer-ready-for-pickup-order.php';
			$this->placeholders   = array(
				'{order_date}'   => '',
				'{order_number}' => '',
			);

			// Triggers for this email.
			add_action( 'woocommerce_order_status_ready-for-pickup_notification', array( $this, 'trigger' ), 10, 2 );

			// Call parent constructor.
			parent::__construct();
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @param int                    $order_id The order ID.
		 * @param object|WC_Order|string $order    Order object.
		 *
		 * @return void
		 */
		public function trigger( $order_id, $order = '' ) {
			$this->setup_locale();

			if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
				$order = wc_get_order( $order_id );
			}

			/* @phpstan-ignore-next-line */
			if ( is_a( $order, 'WC_Order' ) ) {
				$this->object                         = $order;
				$this->recipient                      = $this->object->get_billing_email();
				/* @phpstan-ignore-next-line */
				$this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
				$this->placeholders['{order_number}'] = $this->object->get_order_number();
			}

			if ( $this->is_enabled() && $this->get_recipient() ) {
				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}

			$this->restore_locale();
		}

		/**
		 * Get email subject.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_subject() {
			return __( 'Your {site_title} order is now ready for pickup', 'woocommerce-local-pickup-time-select' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'We look forward to your arrival', 'woocommerce-local-pickup-time-select' );
		}

		/**
		 * Get content html.
		 *
		 * @return string
		 */
		public function get_content_html() {
			return wc_get_template_html(
				$this->template_html,
				array(
					'order'              => $this->object,
					'email_heading'      => $this->get_heading(),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => false,
					'plain_text'         => false,
					'email'              => $this,
				),
				'',
				$this->template_base
			);
		}

		/**
		 * Get content plain.
		 *
		 * @return string
		 */
		public function get_content_plain() {
			return wc_get_template_html(
				$this->template_plain,
				array(
					'order'              => $this->object,
					'email_heading'      => $this->get_heading(),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => false,
					'plain_text'         => true,
					'email'              => $this,
				),
				'',
				$this->template_base
			);
		}

		/**
		 * Default content to show below main email content.
		 *
		 * @since 3.7.0
		 * @return string
		 */
		public function get_default_additional_content() {
			return __( 'We look forward to your pending arrival.', 'woocommerce-local-pickup-time-select' );
		}
	}

}

return new WC_Email_Customer_Ready_For_Pickup_Order();

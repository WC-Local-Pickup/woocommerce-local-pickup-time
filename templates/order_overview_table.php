<?php
/**
 * Pickup Time Order Overview Table
 *
 * Computes table of orders for $bucket.
 * This variable has to be set in the file, where this template is included.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}


//$plugin = Local_Pickup_Time::get_instance();
//$pickup_time_meta_key = $plugin->get_order_meta_key();

?>
    <table class="wp-list-table widefat fixed striped posts">
        <thead>
          <th>Order</th>
  		    <th>Status</th>
          <th>Pickup Time</th>
          <th>Total</th>
  		    <th>Payment Method</th>
        </thead>
        <tbody>
<?php
            foreach ($bucket as $order) {
?>
          <tr id="post-<?php echo $order->get_id() ?>">
          <td><a href="<?php echo get_site_url() ?>/wp-admin/post.php?post=<?php echo $order->get_id() ?>&action=edit" class="order-view"><strong>#<?php echo $order->get_id() ?> <?php echo $order->get_billing_first_name()  ?> <?php echo $order->get_billing_last_name() ?></strong></td>
				  <td class="order_status column-order_total"><?php echo $order->get_status() ?></td>
          <td class="order_date column-order_date"><?php echo $plugin->pickup_time_select_translatable(get_post_meta($order->get_id(), $this->order_meta_key, true))  ?></td>
          <td class="order_total column-order_total"><?php echo $order->get_total() ?></td>
				  <td class="payment_method column-order_total"><?php echo get_post_meta( $order->get_id(), '_payment_method_title', true ) ?></td>
          </tr>
<?php } ?>
        </tbody>
    </table>

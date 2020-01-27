<?php
/**
 * Product Item Count Table
 * Computes a table of products and it's quantities within a array of orders
 * Variable $bucket has to be set in the file, where this template is included.
 */
if (!defined('ABSPATH')) {
    exit;
}

//$plugin = Local_Pickup_Time::get_instance();
//$pickup_time_meta_key = $plugin->get_order_meta_key();

?>
    <table class="wp-list-table widefat fixed striped posts">
      <thead>
        <th>Product</th>
        <th>Quantity</th>
      </thead>
      <tbody>
<?php

			$itemscount = $this->get_item_count($bucket);

            foreach ($itemscount as $product_id => $quantity) {
				          $product = new WC_Product ($product_id);
                ?>
            <tr id="product-<?php echo $product_id ?>">
              <td><a href="<?php echo get_site_url() ?>/wp-admin/post.php?post=<?php echo $product_id ?>&action=edit" class="order-view"><strong>#<?php echo $product_id ?> <?php echo $product->get_title()  ?> </strong></td>
              <td ><?php echo $quantity ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

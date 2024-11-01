<?php if ( ! defined( 'ABSPATH' ) ) exit; 
/*
	Plugin Name: Split Order by category
	Plugin URI: 
	Description: This plugin split order multiple orders.
	Version: 1.0
	Author: SunArc
	Author URI: https://sunarctechnologies.com/
	Text Domain: woocommerce-split-order-category
	License: GPL2

*/

global $wpdb;
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
} else {

    clearstatcache();
}


require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
define('wosc_sunarc_plugin_dir', dirname(__FILE__));


register_activation_hook(__FILE__, 'wosc_plugin_activate');

function wosc_plugin_activate() {
    $option_name = 'split_by_cat_falg';
    $new_value = 'no';
    update_option($option_name, $new_value);
	
}

// Deactivation Pluign 
function wosc_deactivation() {
     $option_name = 'split_by_cat_falg';
    $new_value = '';
    update_option($option_name, $new_value);
	$option_name1 = 'splitordercategory';
    $new_value1 = '';
    update_option($option_name1, $new_value1);
}

register_deactivation_hook(__FILE__, 'wosc_deactivation');

// Uninstall Pluign 
function wosc_uninstall() {
    $option_name = 'split_by_cat_falg';
    $new_value = '';
    update_option($option_name, $new_value);
	$option_name1 = 'splitordercategory';
    $new_value1 = '';
    update_option($option_name1, $new_value1);
    
}


$SUNARC_all_plugins = get_plugins();

$SUNARC_activate_all_plugins = apply_filters('active_plugins', get_option('active_plugins'));

if (array_key_exists('woocommerce/woocommerce.php', $SUNARC_all_plugins) && in_array('woocommerce/woocommerce.php', $SUNARC_activate_all_plugins)) {
     $optionVal = get_option('split_by_cat_falg');
     $splitDefault = get_option('splitordercategory');
     if ($optionVal == 'yes' && $splitDefault == 'default') {
        require_once wosc_sunarc_plugin_dir . '/include/splitorder.php';
    }  
}


function wosc_register_my_custom_submenu_page() {
    add_submenu_page( 'woocommerce', 'Split By Category', 'Split By Category', 'manage_options', 'split-order-by-category', 'wosc_my_custom_submenu_page_callback' ); 
}
function wosc_my_custom_submenu_page_callback() {
  
	 require_once wosc_sunarc_plugin_dir . '/include/setting.php';
}
add_action('admin_menu', 'wosc_register_my_custom_submenu_page',99);


add_action( 'woocommerce_email', 'wosc_remove_hooks' );

function wosc_remove_hooks( $email_class ) {
		remove_action( 'woocommerce_low_stock_notification', array( $email_class, 'low_stock' ) );
		remove_action( 'woocommerce_no_stock_notification', array( $email_class, 'no_stock' ) );
		remove_action( 'woocommerce_product_on_backorder_notification', array( $email_class, 'backorder' ) );
		
		// New order emails
		remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_failed_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		
		// Processing order emails
		remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
		
		// Completed order emails
		remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );
			
		// Note emails
		remove_action( 'woocommerce_new_customer_note_notification', array( $email_class->emails['WC_Email_Customer_Note'], 'trigger' ) );
}


add_action( 'woocommerce_order_item_meta_end', 'wosc_display_custom_data_in_emails', 10, 4 );
function wosc_display_custom_data_in_emails( $item_id, $item, $order, $bool ) {
	     $optionVal = get_option('split_by_cat_falg');
     $splitDefault = get_option('splitordercategory');
     if ($optionVal == 'yes' && $splitDefault == 'splitaccordingcategory') {
    $terms = wp_get_post_terms( $item->get_product_id(), 'product_cat', array( 'fields' => 'names' ) ); 
    echo "<br><small>"  .'Category Name : '. implode(', ', $terms) . "</small>";
	 }
}





	

add_action('woocommerce_checkout_create_order', 'wosc_before_checkout_create_order', 20, 2);
function wosc_before_checkout_create_order( $order, $data ) {
 $order->update_meta_data( '_custom_meta_hide', 'yes' );

}

function action_woocommerce_checkout_order_processed( $order_id, $posted_data, $order ) {
 $optionVal = get_option('split_by_cat_falg');
	 $splitDefault = get_option('splitordercategory');
	if($optionVal=='yes'){	
   update_post_meta($order_id,'_order_total',0);  
	}
}; 
add_action( 'woocommerce_checkout_order_processed', 'action_woocommerce_checkout_order_processed', 10, 3 ); 


add_filter( 'woocommerce_order_number', 'change_woocommerce_order_number' );
function change_woocommerce_order_number( $order_id ) {
	
	$optionVal = get_option('split_by_cat_falg');
    $splitDefault = get_option('splitordercategory');
	if($optionVal=='yes'){
	$pricetotal = get_post_meta($order_id,'_order_total',true);
	if($pricetotal==0){
    $suffix = '--Main Order--';
    $new_order_id = $order_id . $suffix;
    return $new_order_id;
	}
	else 
	{
		$suffix = '--Split Order--';
    $new_order_id = $order_id . $suffix;
    return $new_order_id;
	}
	}
	
 }

add_filter( 'woocommerce_endpoint_order-received_title', 'sunarc_thank_you_title' );
 
function sunarc_thank_you_title( $old_title ){
	   $optionVal = get_option('split_by_cat_falg');
     $splitDefault = get_option('splitordercategory');
	 if ($optionVal == 'yes' && $splitDefault == 'splitaccordingcategory') {
  $order_id = wc_get_order_id_by_order_key( $_GET['key'] ); 
  update_post_meta($order_id,'_order_total',0);  
 	?>
	<script>
	jQuery(document).ready(function () {
    jQuery('.woocommerce-order-details__title').text('Main Order details');
});
</script>
	<?php
	 }
}


<?php
/**
 * Plugin Name: WooCommerce Pre-Order email notification
 * Plugin URI: https://github.com/PravinWork/Woocommerce-Pre-Order-Ready-email-Notification
 * Description: Woocommerce add-on for marking status change of Pre-Order Item's Order
 * Author: Pravin Durugkar (pra.durugkar@gmail.com)
 * Author URI: https://github.com/PravinWork
 * Version: 0.1
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 */
define('FDG_WPOEN_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FDG_WPOEN_PLUGIN_URL', plugin_dir_url( __FILE__ ));

//$upload_dir = wp_upload_dir();
//include_once( $upload_dir['basedir'] . '/crwc-custom-emails/crwc-email-functions.php' );

//include_once( FDG_WPOEN_PLUGIN_DIR . 'crwc-email-functions.php' );
//die(FDG_WPOEN_PLUGIN_DIR . 'crwc-email-functions.php');

add_filter( 'woocommerce_email_classes', 'crwc_custom_woocommerce_emails' );
function crwc_custom_woocommerce_emails( $email_classes ) {
	//* Custom welcome email to customer when purchasing online training program

	require_once( FDG_WPOEN_PLUGIN_DIR . 'class-fdg-pre-order-email.php' );
	//die(FDG_WPOEN_PLUGIN_DIR . 'class-crwc-welcome-email.php');

	$email_classes['CRWC_Welcome_Email'] = new CRWC_Welcome_Email(); // add to the list of email classes that WooCommerce loads

	return $email_classes;
}

//custom hooks for custom woocommerce order email
function register_custom_order_status_action( $actions ){
	$actions[] = 'woocommerce_pre_order_ready_mail_send';
	return $actions;
}
add_filter( 'woocommerce_email_actions', 'register_custom_order_status_action' );


add_action( 'init', 'fdg_register_shipped_order_status');
/* Register new status*/
function fdg_register_shipped_order_status() {
     register_post_status( 'wc-fdg_pre_order_ready', array(
     'label' => _x('Pre-Order is Ready','wdm'),
     'public' => true,
     'exclude_from_search' => false,
     'show_in_admin_all_list' => true,
     'show_in_admin_status_list' => true,
     'label_count' => _n_noop( 'Pre-Order is Ready <span class="count">(%s)</span>', 'Pre-Order is Ready <span class="count">(%s)</span>' )
    ));
}

add_action( 'woocommerce_order_actions', 'fdg_add_order_meta_box_actions' );
/* Add Order action to Order action meta box */
function fdg_add_order_meta_box_actions($actions) {
  $actions['fdg_pre_order_ready'] = __( 'Pre-Order is Ready', 'woocommerce');
  return $actions; 
}

add_filter( 'wc_order_statuses', 'add_shipped_to_order_statuses');
/* Adds new Order status - Shipped in Order statuses*/
function add_shipped_to_order_statuses($order_statuses)
{
      $new_order_statuses = array();
      // add new order status after Completed
      foreach ( $order_statuses as $key => $status ) 
      {
         $new_order_statuses[ $key ] = $status;
         if ( 'wc-on-hold' === $key ) 
         {
            $new_order_statuses['wc-fdg_pre_order_ready'] = __('Pre-Order is Ready','wdm');    
         }
      }
   return $new_order_statuses;
}

//Add callback if Shipped action called
add_action( 'woocommerce_order_action_fdg_pre_order_ready', 'fdg_pre_order_ready_callback' ,10,1);
function fdg_pre_order_ready_callback($order){ 
	//die("action called");
    do_action("woocommerce_pre_order_ready_mail_send");
}
//Add callback if Status changed to Shipping    
add_action('woocommerce_order_status_fdg_pre_order_ready', 'wdm_order_status_shipped_callback',10,1);
function wdm_order_status_shipped_callback($order_id){
	//die("status called");
    do_action("woocommerce_pre_order_ready_mail_send");
}
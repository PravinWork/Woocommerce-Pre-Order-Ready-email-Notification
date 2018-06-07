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
include_once( FDG_WPOEN_PLUGIN_DIR . 'crwc-email-functions.php' );

add_action( 'woocommerce_order_actions', 'fdg_add_order_meta_box_actions' );
/* Add Order action to Order action meta box */
function fdg_add_order_meta_box_actions($actions) {
  $actions['fdg_pre_order_ready'] = __( 'Pre-Order is Ready', 'woocommerce');
  return $actions; 
}

//Add callback if Shipped action called
add_action( 'woocommerce_order_action_fdg_pre_order_ready', array( $this, 'fdg_pre_order_ready_callback' ),10,1);
function fdg_pre_order_ready_callback($order){ 
    do_action("woocommerce_pre_order_ready_mail_send");
}
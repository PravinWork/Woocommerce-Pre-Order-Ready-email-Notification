<?php
//crwc-welcome-email.php
/**
 *
 * Welcome email content template
 *
 * The file is prone to modifications after plugin upgrade or alike; customizations are advised via hooks/filters
 *
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php _e( 'Thank you for your purchase of Online training course. Your account has been successfully created over the Online Training Program portal.', 'woocommerce' ); ?></p>

<p><?php _e( 'Use the following credentials to login to the portal:', 'woocommerce' ); ?></p>

<p>
	<strong><?php __( 'Login URL: ', 'woocommerce' ) ?></strong><?php _e( 'https://example.com' ); ?><br />
	<strong><?php __( 'Username: ', 'woocommerce' ) ?></strong><?php echo make_clickable( esc_attr( $order->billing_email ) ); ?><br />
</p>

<p><?php _e( 'Below are the order details for your reference.' ) ?></p>

<?php
/**
* @hooked WC_Emails::order_details() Shows the order details table.
* @hooked WC_Emails::order_schema_markup() Adds Schema.org markup.
* @since 2.5.0
*/
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/**
* @hooked WC_Emails::order_meta() Shows order meta data.
*/
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
* @hooked WC_Emails::customer_details() Shows customer details
* @hooked WC_Emails::email_address() Shows email address
*/
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
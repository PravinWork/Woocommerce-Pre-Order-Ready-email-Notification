<?php
// crwc-email-functions.php

add_filter( 'woocommerce_email_classes', 'crwc_custom_woocommerce_emails' );

function crwc_custom_woocommerce_emails( $email_classes ) {

	//* Custom welcome email to customer when purchasing online training program

	//$upload_dir = wp_upload_dir();
	//FDG_WPOEN_PLUGIN_DIR
	//require_once( $upload_dir['basedir'] . '/crwc-custom-emails/class-crwc-welcome-email.php' );

	require_once( DG_WPOEN_PLUGIN_DIR . 'class-crwc-welcome-email.php' );
	$email_classes['CRWC_Welcome_Email'] = new CRWC_Welcome_Email(); // add to the list of email classes that WooCommerce loads


	return $email_classes;
	
}



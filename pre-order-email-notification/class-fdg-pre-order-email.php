<?php
//class-crwc-welcome-email.php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Welcome Email class used to send out welcome emails to customers purchasing a course
 *
 * @extends \WC_Email
 */

class CRWC_Welcome_Email extends WC_Email {
	
	/**
	 * Set email defaults
	 */
	public function __construct() {
		//die('Pravd-1');

		// Unique ID for custom email
		//$this->id = 'crwc_welcome_email';
		$this->id = 'fdg_pre_order_email';

		// Is a customer email
		$this->customer_email = true;
		
		// Title field in WooCommerce Email settings
		$this->title = __( 'Pre-Order Ready Email', 'woocommerce' );

		// Description field in WooCommerce email settings
		$this->description = __( 'Pre-Order Ready email is sent when an Pre-order item is ready and available to dispatch.', 'woocommerce' );

		// Default heading and subject lines in WooCommerce email settings
		$this->subject = apply_filters( 'fdg_pre_order_email_default_subject', __( 'Your Pre-Order is Ready', 'woocommerce' ) );
		$this->heading = apply_filters( 'crwc_pre_order_email_default_heading', __( 'Your Pre-Order is Ready', 'woocommerce' ) );
		
		// these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar
		$upload_dir = wp_upload_dir();
		
		//DG_WPOEN_PLUGIN_DIR
		//$this->template_base  = $upload_dir['basedir'] . '/crwc-custom-emails/';	// Fix the template base lookup for use on admin screen template path display
		$this->template_base  = FDG_WPOEN_PLUGIN_DIR ;
		$this->template_html  = 'emails/fdg-pre-order-email.php';
		$this->template_plain = 'emails/plain/fdg-pre-order-email.php';

		// Trigger email when payment is complete
		//add_action( 'woocommerce_payment_complete', array( $this, 'trigger' ) );
		//add_action( 'woocommerce_order_status_on-hold_to_processing', array( $this, 'trigger' ) );
		//add_action( 'woocommerce_order_status_on-hold_to_completed', array( $this, 'trigger' ) );
		//add_action( 'woocommerce_order_status_failed_to_processing', array( $this, 'trigger' ) );
		//add_action( 'woocommerce_order_status_failed_to_completed', array( $this, 'trigger' ) );
		//add_action( 'woocommerce_order_status_pending_to_processing', array( $this, 'trigger' ) );
		
		add_action( 'woocommerce_order_action_fdg_pre_order_ready', array( $this, 'trigger' ) ,10,1);
		add_action('woocommerce_order_status_fdg_pre_order_ready', array( $this, 'trigger' ),10,1);
		add_action( 'woocommerce_pre_order_ready_mail_send', array( $this, 'trigger' ) );

		// Call parent constructor to load any other defaults not explicity defined here
		parent::__construct();

	}


	/**
	 * Prepares email content and triggers the email
	 *
	 * @param int $order_id
	 */
	public function trigger( $order_id ) {
		//die('Pravd');

		// Bail if no order ID is present
		if ( ! $order_id )
			return;
		
		// Send welcome email only once and not on every order status change		
		if ( ! get_post_meta( $order_id, '_fdg_pre_order_email_sent', true ) ) {
			
			// setup order object
			$this->object = new WC_Order( $order_id );
			
			// get order items as array
			$order_items = $this->object->get_items();

			//* Maybe include an additional check to make sure that the online training program account was created
			/* Uncomment and add your own conditional check
			$online_training_account_created = get_post_meta( $this->object->id, '_crwc_user_account_created', 1 );
			
			if ( ! empty( $online_training_account_created ) && false === $online_training_account_created ) {
				return;
			}
			*/

			/* Proceed with sending email */
			
			$this->recipient = $this->object->billing_email;

			// replace variables in the subject/headings
			$this->find[] = '{order_date}';
			$this->replace[] = date_i18n( woocommerce_date_format(), strtotime( $this->object->order_date ) );

			$this->find[] = '{order_number}';
			$this->replace[] = $this->object->get_order_number();

			if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
				return;
			}

			// All well, send the email
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			
			// add order note about the same
			$this->object->add_order_note( sprintf( __( '%s email sent to the customer.', 'woocommerce' ), $this->title ) );

			// Set order meta to indicate that the welcome email was sent
			update_post_meta( $this->object->id, '_fdg_pre_order_email_sent', 1 );
			
		}
		
	}
	
	/**
	 * get_content_html function.
	 *
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html( $this->template_html, array(
			'order'					=> $this->object,
			'email_heading'			=> $this->email_heading( $this->course_info['program'] ),
			'sent_to_admin'			=> false,
			'plain_text'			=> false,
			'email'					=> $this
		) );
	}


	/**
	 * get_content_plain function.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html( $this->template_plain, array(
			'order'					=> $this->object,
			'email_heading'			=> $this->email_heading( $this->course_info['program'] ),
			'sent_to_admin'			=> false,
			'plain_text'			=> true,
			'email'					=> $this
		) );
	}


	/**
	 * Initialize settings form fields
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'    => array(
				'title'   => __( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => 'Enable this email notification',
				'default' => 'yes'
			),
			'subject'    => array(
				'title'       => __( 'Subject', 'woocommerce' ),
				'type'        => 'text',
				'description' => sprintf( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', $this->subject ),
				'placeholder' => '',
				'default'     => ''
			),
			'heading'    => array(
				'title'       => __( 'Email Heading', 'woocommerce' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.' ), $this->heading ),
				'placeholder' => '',
				'default'     => ''
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'woocommerce' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
				'default'       => 'html',
				'class'         => 'email_type wc-enhanced-select',
				'options'     => array(
					'plain'	    => __( 'Plain text', 'woocommerce' ),
					'html' 	    => __( 'HTML', 'woocommerce' ),
					'multipart' => __( 'Multipart', 'woocommerce' ),
				)
			)
		);
	}
		
}
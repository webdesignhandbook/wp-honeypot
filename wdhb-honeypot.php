<?php
/**
 * Add a honeypot to comment forms
 *
 * @author WebDesignHandbook
 * @license GPLv2
 * @link http://webdesignhandbook.com/plugins/honeypot
 *
 * @wordpress-plugin
 * Plugin Name: WDHB Honeypot
 * Plugin URI: http://webdesignhandbook.com/plugins/honeypot
 * Description: Deter spam bots by setting a trap with a dummy input field -- no CAPTCHAs!
 * Version: 1.0.0
 * Author: WebDesignHandbook
 * License: GPLv2
 * Text Domain: wdhb-honeypot
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Add the honeypot field
 */
function wdhb_honeypot_add_field( $fields ) {
	if( ! isset( $fields['wdhb_honeypot'] ) ) {

		// Import the view
		ob_start();
		require( plugin_dir_path( __FILE__ ) . 'views/honeypot_field.php' );
		$fields['wdhb_honeypot'] = ob_get_clean();
	};
	return $fields;
}
add_filter( 'comment_form_default_fields', 'wdhb_honeypot_add_field' );

/**
 * Check if the honeypot field has been filled
 */
function wdhb_honeypot_check_field( $comment_id, $comment_approved ) {
	if( isset( $_POST['wdhb_honeypot'] ) && $_POST['wdhb_honeypot'] !== "" ) {
		wp_spam_comment( $comment_id );
	}
}
add_action( 'comment_post', 'wdhb_honeypot_check_field', 99, 2 );

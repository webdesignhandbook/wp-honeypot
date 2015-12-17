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

		// Add JavaScript to hide the field
		wp_enqueue_script( 'hide-honeypot', plugins_url( 'js/hide_honeypot_field.js', __FILE__ ) );
	};
	return $fields;
}
add_filter( 'comment_form_default_fields', 'wdhb_honeypot_add_field' );

/**
 * Check if the honeypot field has been filled
 */
function wdhb_honeypot_check_field( $comment_id, $comment_object ) {
	if( $comment_object['wdhb_honeypot'] !== "" ) {
		wp_spam_comment( $comment_id );
	}
}
add_action( 'wp_insert_comment','wdhb_honeypot_check_field' );


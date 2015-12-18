<?php
/**
 * Adds a honeypot to comment forms
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
 * Adds the honeypot field
 */
function wdhb_honeypot_add_field( $field ) {

	// Include CSS to hide the honeypot from normal users
	wp_enqueue_style('hide-honeypot', plugins_url('/css/hide-honeypot.css', __FILE__ ) );

	// Duplicate the comment form
	$doc = new DOMDocument();
	$doc->loadHTML( $field );

	$p = $doc->getElementsByTagName('p')->item(0);
	$p->setAttribute('class', 'comment-form-honeypot');

	$textarea = $doc->getElementById('comment');
	$textarea->setAttribute('id', 'wdhb_honeypot');
	$textarea->setAttribute('name', 'wdhb_honeypot');

	$label = $doc->getElementsByTagName('label')->item(0);
	$label->setAttribute('for', 'wdhb_honeypot');

	return $doc->saveHTML() . $field;
}
add_filter( 'comment_form_field_comment', 'wdhb_honeypot_add_field' );

/**
 * Swaps back the values of the honeypot and the legitimate field
 */
function wdhb_honeypot_swap_fields( $comment_post_id ) {
	if( isset( $_POST['wdhb_honeypot'] ) ) {
		$temp = $_POST['wdhb_honeypot'];
		$_POST['wdhb_honeypot'] = $_POST['comment'];
		$_POST['comment'] = $temp;
	}
}
add_filter( 'init', 'wdhb_honeypot_swap_fields' );

/**
 * Check if the honeypot field has been filled
 */
function wdhb_honeypot_check_field( $comment_id, $comment_approved ) {
	if( isset( $_POST['wdhb_honeypot'] ) && $_POST['wdhb_honeypot'] !== "" ) {
		wp_spam_comment( $comment_id );
	}
}
add_action( 'comment_post', 'wdhb_honeypot_check_field', 99, 2 );

<?php
/**
 * Helper functions.
 *
 * @package FloatyButton
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default options.
 *
 * @return array
 */
function vzflty_get_default_options() {
	return array(
		'enabled'               => 0,
		'button_template'       => 'default',
		'button_label'          => __( 'Book now', 'floaty-button' ),
		'position'              => 'bottom_right',
		'action_type'           => 'link',
		'link_url'              => '',
		'link_target'           => '_blank',
		'iframe_url'            => '',
		'event_name'            => 'floaty_click',
		'custom_css'            => '',
		'whatsapp_phone'        => '',
		'whatsapp_message'      => '',
		'apointoo_enabled'      => 0,
		'apointoo_merchant_id'  => '',
	);
}

/**
 * Get plugin options merged with defaults.
 *
 * @return array
 */
function vzflty_get_options() {
	$raw_options = get_option( VZFLTY_OPTION_KEY, array() );

	if ( ! is_array( $raw_options ) ) {
		$raw_options = array();
	}

	return wp_parse_args( $raw_options, vzflty_get_default_options() );
}

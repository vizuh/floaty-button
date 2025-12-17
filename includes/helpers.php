<?php
/**
 * Helper functions.
 *
 * @package FloatyBookNowChat
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
		'mode'                  => 'custom',
		'button_template'       => 'default',
		'button_label'          => __( 'Book now', 'floaty-book-now-chat' ),
		'position'              => 'bottom_right',
		'action_type'           => 'link',
		'link_url'              => '',
		'link_target'           => '_blank',
		'iframe_url'            => '',
		'event_name'            => 'vzflty_click',
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

/**
 * Helper to safely fetch an option value with a default.
 *
 * @param array  $options Options array.
 * @param string $key     Option key.
 * @param mixed  $default Default value.
 *
 * @return mixed
 */
function vzflty_get_option_value( $options, $key, $default = '' ) {
	if ( ! is_array( $options ) ) {
		return $default;
	}

	return array_key_exists( $key, $options ) ? $options[ $key ] : $default;
}

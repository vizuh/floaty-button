<?php
/**
 * Frontend rendering.
 *
 * @package FloatyButton
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public-facing assets and data.
 */
class VZFLTY_Frontend {

	/**
	 * Enqueue assets when required.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		$options = vzflty_get_options();

		if ( ! $this->should_render( $options ) ) {
			return;
		}

		$style_handle  = 'vzflty-floaty';
		$script_handle = 'vzflty-floaty';

		wp_register_script(
			$script_handle,
			plugins_url( 'assets/js/floaty-button.js', VZFLTY_PLUGIN_FILE ),
			array(),
			VZFLTY_VERSION,
			true
		);

		wp_enqueue_style(
			$style_handle,
			plugins_url( 'assets/css/floaty-button.css', VZFLTY_PLUGIN_FILE ),
			array(),
			VZFLTY_VERSION
		);

		$custom_css = isset( $options['custom_css'] ) ? trim( $options['custom_css'] ) : '';

		if ( '' !== $custom_css ) {
			$inline_css = "/* Scope your selectors with #floaty-button-container */\n" . wp_strip_all_tags( $custom_css );
			$inline_css = apply_filters( 'vzflty_inline_css', $inline_css, $options );
			wp_add_inline_style( $style_handle, $inline_css );
		}

		wp_localize_script( $script_handle, 'VZFLTY_SETTINGS', $this->prepare_script_data( $options ) );
		wp_enqueue_script( $script_handle );
	}

	/**
	 * Prepare script data.
	 *
	 * @param array $options Options array.
	 *
	 * @return array
	 */
	private function prepare_script_data( $options ) {
		return array(
			'buttonLabel'     => $options['button_label'],
			'buttonTemplate'  => $options['button_template'],
			'position'        => $options['position'],
			'actionType'      => $options['action_type'],
			'linkUrl'         => $options['link_url'],
			'linkTarget'      => $options['link_target'],
			'iframeUrl'       => $options['iframe_url'],
			'eventName'       => $options['event_name'],
			'whatsappPhone'   => $options['whatsapp_phone'],
			'whatsappMessage' => $options['whatsapp_message'],
			'apointoo'        => array(
				'enabled'    => (bool) $options['apointoo_enabled'],
				'merchantId' => $options['apointoo_merchant_id'],
			),
		);
	}

	/**
	 * Check render conditions.
	 *
	 * @param array $options Options array.
	 *
	 * @return bool
	 */
	private function should_render( $options ) {
		if ( empty( $options['enabled'] ) ) {
			return false;
		}

		$template            = $options['button_template'];
		$action              = $options['action_type'];
		$has_whatsapp_phone = ! empty( $options['whatsapp_phone'] );

		if ( 'whatsapp' === $template ) {
			return $has_whatsapp_phone;
		}

		if ( 'link' === $action && empty( $options['link_url'] ) ) {
			return false;
		}

		if ( 'iframe_modal' === $action && empty( $options['iframe_url'] ) ) {
			return false;
		}

		return true;
	}
}

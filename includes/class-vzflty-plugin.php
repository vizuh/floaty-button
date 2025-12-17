<?php
/**
 * Core plugin wiring.
 *
 * @package FloatyBookNowChat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin orchestrator.
 */
class VZFLTY_Plugin {

	/**
	 * Admin handler.
	 *
	 * @var VZFLTY_Admin
	 */
	private $admin;

	/**
	 * Frontend handler.
	 *
	 * @var VZFLTY_Frontend
	 */
	private $frontend;

	/**
	 * Boot the plugin.
	 *
	 * @return void
	 */
	public function init() {
		load_plugin_textdomain(
			'floaty-button',
			false,
			dirname( plugin_basename( VZFLTY_PLUGIN_FILE ) ) . '/languages/'
		);

		$this->frontend = new VZFLTY_Frontend();
		add_action( 'wp_enqueue_scripts', array( $this->frontend, 'enqueue_assets' ) );

		if ( is_admin() ) {
			$this->admin = new VZFLTY_Admin();
			add_action( 'admin_init', array( $this->admin, 'register_settings' ) );
			add_action( 'admin_menu', array( $this->admin, 'add_settings_page' ) );
		}

		add_filter( 'plugin_action_links_' . plugin_basename( VZFLTY_PLUGIN_FILE ), array( $this, 'add_settings_link' ) );
	}

	/**
	 * Add settings link on the plugins list.
	 *
	 * @param array $links Existing links.
	 *
	 * @return array
	 */
	public function add_settings_link( $links ) {
		$url           = admin_url( 'options-general.php?page=vzflty-settings' );
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( $url ),
			esc_html__( 'Settings', 'floaty-book-now-chat' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Activation hook.
	 *
	 * Migrates legacy options if the new option is empty.
	 *
	 * @return void
	 */
	public static function activate() {
		$new_options = get_option( VZFLTY_OPTION_KEY, array() );

		if ( empty( $new_options ) ) {
			$legacy_options = get_option( 'floaty_button_options', array() );

			if ( ! empty( $legacy_options ) ) {
				update_option( VZFLTY_OPTION_KEY, $legacy_options );
			} else {
				add_option( VZFLTY_OPTION_KEY, vzflty_get_default_options() );
			}
		}
	}
}

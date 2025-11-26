<?php
/**
 * Plugin Name: Floaty Button
 * Plugin URI:  https://github.com/vizuh/floaty-button
 * Description: Floaty button
 * Version:     1.0.0
 * Author:      Vizuh
 * Author URI:  https://vizuh.com
 * Text Domain: floaty-button
 * Requires at least: 6.4
 * Tested up to:      6.6
 * Requires PHP:      8.0
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Floaty_Button_Plugin {
	const OPTION_KEY = 'floaty_button_options';

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function load_textdomain() {
		load_plugin_textdomain(
			'floaty-button',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);
	}

	public function register_settings() {
		register_setting(
			'floaty_button_settings',
			self::OPTION_KEY,
			array( $this, 'sanitize_options' )
		);

		add_settings_section(
			'floaty_button_main_section',
			__( 'Main Settings', 'floaty-button' ),
			null,
			'floaty-button-settings'
		);

		add_settings_field(
			'enabled',
			__( 'Enable Plugin', 'floaty-button' ),
			array( $this, 'render_checkbox_field' ),
			'floaty-button-settings',
			'floaty_button_main_section',
			array( 'key' => 'enabled' )
		);

		add_settings_field(
			'button_template',
			__( 'Button Template', 'floaty-button' ),
			array( $this, 'render_select_field' ),
			'floaty-button-settings',
			'floaty_button_main_section',
			array(
				'key'     => 'button_template',
				'options' => array(
					'default'  => __( 'Default Button', 'floaty-button' ),
					'whatsapp' => __( 'WhatsApp Floating Button', 'floaty-button' ),
				),
				'default' => 'default',
			)
		);

		add_settings_field(
			'button_label',
			__( 'Button Label', 'floaty-button' ),
			array( $this, 'render_text_field' ),
			'floaty-button-settings',
			'floaty_button_main_section',
			array( 'key' => 'button_label', 'default' => 'Book now' )
		);

		add_settings_field(
			'position',
			__( 'Button Position', 'floaty-button' ),
			array( $this, 'render_select_field' ),
			'floaty-button-settings',
			'floaty_button_main_section',
			array(
				'key'     => 'position',
				'options' => array(
					'bottom_right' => __( 'Bottom Right', 'floaty-button' ),
					'bottom_left'  => __( 'Bottom Left', 'floaty-button' ),
				),
				'default' => 'bottom_right',
			)
		);

		add_settings_field(
			'action_type',
			__( 'Action Type', 'floaty-button' ),
			array( $this, 'render_select_field' ),
			'floaty-button-settings',
			'floaty_button_main_section',
			array(
				'key'     => 'action_type',
				'options' => array(
					'link'         => __( 'Open Link', 'floaty-button' ),
					'iframe_modal' => __( 'Open Iframe Modal', 'floaty-button' ),
				),
			)
		);

		add_settings_field(
			'link_url',
			'Link URL',
			array( $this, 'render_text_field' ),
			'floaty-button-settings',
			'floaty_button_main_section',
			array( 'key' => 'link_url' )
		);

		add_settings_field(
			'link_target',
			'Link Target',
			array( $this, 'render_select_field' ),
			'floaty-button-settings',
			'floaty_button_main_section',
			array(
				'key' => 'link_target',
				'options' => array(
					'_blank' => 'New Tab (_blank)',
					'_self' => 'Same Tab (_self)'
				)
			)
		);

		add_settings_field(
			'iframe_url',
			'Iframe URL',
			array( $this, 'render_text_field' ),
			'floaty-button-settings',
			'floaty_button_main_section',
			array( 'key' => 'iframe_url' )
		);

		add_settings_field(
			'event_name',
			'DataLayer Event Name',
			array( $this, 'render_text_field' ),
			'floaty-button-settings',
			'floaty_button_main_section',
			array( 'key' => 'event_name', 'default' => 'floaty_click' )
		);

		add_settings_field(
			'custom_css',
			__( 'Custom CSS', 'floaty-button' ),
			array( $this, 'render_textarea_field' ),
			'floaty-button-settings',
			'floaty_button_main_section',
			array( 'key' => 'custom_css' )
		);

		add_settings_section(
			'floaty_button_whatsapp_section',
			__( 'WhatsApp Settings', 'floaty-button' ),
			null,
			'floaty-button-settings'
		);

		add_settings_field(
			'whatsapp_phone',
			__( 'WhatsApp Phone Number', 'floaty-button' ),
			array( $this, 'render_text_field' ),
			'floaty-button-settings',
			'floaty_button_whatsapp_section',
			array(
				'key'         => 'whatsapp_phone',
				'description' => __( 'Enter your WhatsApp number in international format (digits only). Example: 5511999999999.', 'floaty-button' ),
			)
		);

		add_settings_field(
			'whatsapp_message',
			__( 'Prefilled Message', 'floaty-button' ),
			array( $this, 'render_text_field' ),
			'floaty-button-settings',
			'floaty_button_whatsapp_section',
			array(
				'key'         => 'whatsapp_message',
				'description' => __( 'Optional. Example: Hi, I\'d like to book an appointment.', 'floaty-button' ),
			)
		);

		add_settings_section(
			'floaty_button_google_reserve_section',
			__( 'Google Reserve Integration', 'floaty-button' ),
			null,
			'floaty-button-settings'
		);

		add_settings_field(
			'google_reserve_enabled',
			__( 'Enable Google Reserve', 'floaty-button' ),
			array( $this, 'render_checkbox_field' ),
			'floaty-button-settings',
			'floaty_button_google_reserve_section',
			array( 'key' => 'google_reserve_enabled' )
		);

		add_settings_field(
			'google_reserve_merchant_id',
			__( 'Merchant ID', 'floaty-button' ),
			array( $this, 'render_text_field' ),
			'floaty-button-settings',
			'floaty_button_google_reserve_section',
			array(
				'key'         => 'google_reserve_merchant_id',
				'description' => __( 'Enter the Merchant ID provided by Appointo (e.g., <code>my-business-name-123</code>).', 'floaty-button' ),
			)
		);
	}

	public function sanitize_options( $input ) {
                $output = array();

		$output['enabled']       = ! empty( $input['enabled'] ) ? 1 : 0;
		$output['button_template'] = in_array( $input['button_template'] ?? 'default', array( 'default', 'whatsapp' ), true ) ? $input['button_template'] : 'default';
		$output['button_label']  = sanitize_text_field( $input['button_label'] ?? 'Book now' );
		$output['position']      = in_array(
			$input['position'] ?? 'bottom_right',
			array( 'bottom_right', 'bottom_left' ),
			true
		) ? $input['position'] : 'bottom_right';

		$output['action_type']   = in_array(
			$input['action_type'] ?? 'link',
			array( 'link', 'iframe_modal' ),
			true
		) ? $input['action_type'] : 'link';

		$output['link_url']      = esc_url_raw( $input['link_url'] ?? '' );

		$output['link_target']   = in_array(
			$input['link_target'] ?? '_blank',
			array( '_blank', '_self' ),
			true
		) ? $input['link_target'] : '_blank';

		$output['iframe_url']    = esc_url_raw( $input['iframe_url'] ?? '' );
		$output['event_name']    = sanitize_key( $input['event_name'] ?? 'floaty_click' );
		$output['custom_css']    = wp_strip_all_tags( $input['custom_css'] ?? '' );
		$output['whatsapp_phone']   = preg_replace( '/[^0-9]/', '', $input['whatsapp_phone'] ?? '' );
		$output['whatsapp_message'] = sanitize_text_field( $input['whatsapp_message'] ?? '' );

		return $output;
	}

	public function add_settings_page() {
		add_options_page(
			'Floaty Button Settings',
			'Floaty Button',
			'manage_options',
			'floaty-button-settings',
			array( $this, 'render_settings_page' )
		);
	}

	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1>Floaty Button Settings</h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'floaty_button_settings' );
				do_settings_sections( 'floaty-button-settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function render_checkbox_field( $args ) {
		$options = get_option( self::OPTION_KEY );
		$key = $args['key'];
		$checked = isset( $options[ $key ] ) && $options[ $key ] ? 'checked' : '';
		printf(
			'<input type="checkbox" name="%s[%s]" value="1" %s />',
			esc_attr( self::OPTION_KEY ),
			esc_attr( $key ),
			esc_attr( $checked )
		);
	}

	public function render_text_field( $args ) {
		$options = get_option( self::OPTION_KEY );
		$key = $args['key'];
		$value = isset( $options[ $key ] ) ? $options[ $key ] : ( $args['default'] ?? '' );
		printf(
			'<input type="text" name="%s[%s]" value="%s" class="regular-text" />',
			esc_attr( self::OPTION_KEY ),
			esc_attr( $key ),
			esc_attr( $value )
		);
		if ( ! empty( $args['description'] ) ) {
			printf(
				'<p class="description">%s</p>',
				wp_kses_post( $args['description'] )
			);
		}
	}

	public function render_select_field( $args ) {
		$options = get_option( self::OPTION_KEY );
		$key = $args['key'];
		$current_value = isset( $options[ $key ] ) ? $options[ $key ] : ( $args['default'] ?? '' );
		printf( '<select name="%s[%s]">', esc_attr( self::OPTION_KEY ), esc_attr( $key ) );
		foreach ( $args['options'] as $value => $label ) {
			printf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $value ),
				selected( $current_value, $value, false ),
				esc_html( $label )
			);
		}
		echo '</select>';
	}

	public function render_textarea_field( $args ) {
		$options = get_option( self::OPTION_KEY );
		$key = $args['key'];
		$value = isset( $options[ $key ] ) ? $options[ $key ] : ( $args['default'] ?? '' );
		printf(
			'<textarea name="%s[%s]" rows="10" cols="50" class="large-text code">%s</textarea>',
			esc_attr( self::OPTION_KEY ),
			esc_attr( $key ),
			esc_textarea( $value )
		);
	}

	public function enqueue_scripts() {
		$options = get_option( self::OPTION_KEY );

		if ( empty( $options['enabled'] ) ) {
			return;
		}

		wp_register_script(
			'floaty-button',
			plugins_url( 'assets/js/floaty-button.js', __FILE__ ),
			array(),
			'1.0.0',
			true
		);

		wp_enqueue_style(
			'floaty-button',
			plugins_url( 'assets/css/floaty-button.css', __FILE__ ),
			array(),
			'1.0.0'
		);

		if ( ! empty( $options['custom_css'] ) ) {
			wp_add_inline_style( 'floaty-button', $options['custom_css'] );
		}

		$config = array(
			'buttonLabel'     => $options['button_label'] ?? 'Book now',
			'buttonTemplate'  => $options['button_template'] ?? 'default',
			'position'        => $options['position'] ?? 'bottom_right',
			'actionType'      => $options['action_type'] ?? 'link',
			'linkUrl'         => $options['link_url'] ?? '',
			'linkTarget'      => $options['link_target'] ?? '_blank',
			'iframeUrl'       => $options['iframe_url'] ?? '',
			'eventName'       => $options['event_name'] ?? 'floaty_click',
			'whatsappPhone'   => $options['whatsapp_phone'] ?? '',
			'whatsappMessage' => $options['whatsapp_message'] ?? '',
		);

		wp_localize_script( 'floaty-button', 'FLOATY_BUTTON_SETTINGS', $config );
		wp_enqueue_script( 'floaty-button' );
	}
}

new Floaty_Button_Plugin();

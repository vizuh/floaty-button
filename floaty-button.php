<?php
/**
 * Plugin Name: Floaty Button
 * Description: A customizable floating CTA button that can open links or iframe modals.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: floaty-button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Floaty_Button_Plugin {
	const OPTION_KEY = 'floaty_button_options';

	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function register_settings() {
		register_setting(
			'floaty_button_settings',
			self::OPTION_KEY,
			array( $this, 'sanitize_options' )
		);

		add_settings_section(
			'floaty_button_main_section',
			'Main Settings',
			null,
			'floaty-button-settings'
		);

		add_settings_field(
			'enabled',
			'Enable Plugin',
			array( $this, 'render_checkbox_field' ),
			'floaty-button-settings',
			'floaty_button_main_section',
			array( 'key' => 'enabled' )
		);

                add_settings_field(
                        'button_label',
                        'Button Label',
                        array( $this, 'render_text_field' ),
                        'floaty-button-settings',
                        'floaty_button_main_section',
                        array( 'key' => 'button_label', 'default' => 'Book now' )
                );

                add_settings_field(
                        'position',
                        'Button Position',
                        array( $this, 'render_select_field' ),
                        'floaty-button-settings',
                        'floaty_button_main_section',
                        array(
                                'key' => 'position',
                                'options' => array(
                                        'bottom_right' => 'Bottom Right',
                                        'bottom_left'  => 'Bottom Left'
                                ),
                                'default' => 'bottom_right',
                        )
                );

                add_settings_field(
                        'action_type',
                        'Action Type',
                        array( $this, 'render_select_field' ),
                        'floaty-button-settings',
                        'floaty_button_main_section',
			array(
				'key' => 'action_type',
				'options' => array(
					'link' => 'Open Link',
					'iframe_modal' => 'Open Iframe Modal'
				)
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
			'Custom CSS',
			array( $this, 'render_textarea_field' ),
			'floaty-button-settings',
			'floaty_button_main_section',
			array( 'key' => 'custom_css' )
		);
	}

	public function sanitize_options( $input ) {
                $output = array();

                $output['enabled']       = ! empty( $input['enabled'] ) ? 1 : 0;
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
		echo "<input type='checkbox' name='" . self::OPTION_KEY . "[$key]' value='1' $checked />";
	}

	public function render_text_field( $args ) {
		$options = get_option( self::OPTION_KEY );
		$key = $args['key'];
		$value = isset( $options[ $key ] ) ? $options[ $key ] : ( $args['default'] ?? '' );
		echo "<input type='text' name='" . self::OPTION_KEY . "[$key]' value='" . esc_attr( $value ) . "' class='regular-text' />";
	}

	public function render_select_field( $args ) {
		$options = get_option( self::OPTION_KEY );
		$key = $args['key'];
		$current_value = isset( $options[ $key ] ) ? $options[ $key ] : ( $args['default'] ?? '' );
		echo "<select name='" . self::OPTION_KEY . "[$key]'>";
		foreach ( $args['options'] as $value => $label ) {
			$selected = selected( $current_value, $value, false );
			echo "<option value='$value' $selected>$label</option>";
		}
		echo "</select>";
	}

	public function render_textarea_field( $args ) {
		$options = get_option( self::OPTION_KEY );
		$key = $args['key'];
		$value = isset( $options[ $key ] ) ? $options[ $key ] : ( $args['default'] ?? '' );
		echo "<textarea name='" . self::OPTION_KEY . "[$key]' rows='10' cols='50' class='large-text code'>" . esc_textarea( $value ) . "</textarea>";
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
                        'buttonLabel'   => $options['button_label'] ?? 'Book now',
                        'position'      => $options['position'] ?? 'bottom_right',
                        'actionType'    => $options['action_type'] ?? 'link',
                        'linkUrl'       => $options['link_url'] ?? '',
                        'linkTarget'    => $options['link_target'] ?? '_blank',
                        'iframeUrl'     => $options['iframe_url'] ?? '',
                        'eventName'     => $options['event_name'] ?? 'floaty_click',
		);

		wp_localize_script( 'floaty-button', 'FLOATY_BUTTON_SETTINGS', $config );
		wp_enqueue_script( 'floaty-button' );
	}
}

new Floaty_Button_Plugin();

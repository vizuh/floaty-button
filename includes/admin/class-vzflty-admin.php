<?php
/**
 * Admin area logic.
 *
 * @package FloatyButton
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin settings and pages.
 */
class VZFLTY_Admin {

	/**
	 * Settings page slug.
	 */
	const PAGE_SLUG = 'vzflty-settings';

	/**
	 * Option group.
	 */
	const OPTION_GROUP = 'vzflty_settings';

	/**
	 * Available tabs.
	 *
	 * @var array
	 */
	private $tabs = array(
		'general'  => 'general',
		'whatsapp' => 'whatsapp',
		'apointoo' => 'apointoo',
	);

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			self::OPTION_GROUP,
			VZFLTY_OPTION_KEY,
			array(
				'sanitize_callback' => array( $this, 'sanitize_options' ),
			)
		);

		$this->register_general_section();
		$this->register_whatsapp_section();
		$this->register_apointoo_section();
	}

	/**
	 * Add the settings page.
	 *
	 * @return void
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'Floaty Settings', 'floaty-button' ),
			__( 'Floaty', 'floaty-button' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Render the settings page with tabs.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$options    = vzflty_get_options();
		$active_tab = $this->get_active_tab();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Floaty Settings', 'floaty-button' ); ?></h1>
			<h2 class="nav-tab-wrapper">
				<?php foreach ( $this->tabs as $tab_key => $tab_slug ) : ?>
					<?php
					$tab_url = add_query_arg(
						array(
							'page' => self::PAGE_SLUG,
							'tab'  => $tab_slug,
						),
						admin_url( 'options-general.php' )
					);
					?>
					<a href="<?php echo esc_url( $tab_url ); ?>" class="nav-tab<?php echo esc_attr( $active_tab === $tab_slug ? ' nav-tab-active' : '' ); ?>">
						<?php
						if ( 'general' === $tab_slug ) {
							esc_html_e( 'General', 'floaty-button' );
						} elseif ( 'whatsapp' === $tab_slug ) {
							esc_html_e( 'WhatsApp', 'floaty-button' );
						} else {
							esc_html_e( 'Apointoo Booking', 'floaty-button' );
						}
						?>
					</a>
				<?php endforeach; ?>
			</h2>

			<?php $this->render_notices( $active_tab, $options ); ?>

			<form action="options.php" method="post">
				<?php
				settings_fields( self::OPTION_GROUP );
				do_settings_sections( $this->get_section_page_id( $active_tab ) );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Sanitize options on save.
	 *
	 * @param array $input Raw input.
	 *
	 * @return array
	 */
	public function sanitize_options( $input ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return vzflty_get_options();
		}

		if ( ! is_array( $input ) ) {
			return vzflty_get_default_options();
		}

		$input  = wp_unslash( $input );
		$output = vzflty_get_default_options();

		$output['enabled']         = ! empty( $input['enabled'] ) ? 1 : 0;
		$output['button_template'] = in_array( $input['button_template'] ?? 'default', array( 'default', 'whatsapp' ), true ) ? $input['button_template'] : 'default';
		$output['button_label']    = sanitize_text_field( $input['button_label'] ?? '' );
		$output['position']        = in_array( $input['position'] ?? 'bottom_right', array( 'bottom_right', 'bottom_left' ), true ) ? $input['position'] : 'bottom_right';

		$output['action_type'] = in_array( $input['action_type'] ?? 'link', array( 'link', 'iframe_modal' ), true ) ? $input['action_type'] : 'link';

		$output['link_url'] = isset( $input['link_url'] ) ? esc_url_raw( $input['link_url'] ) : '';

		$output['link_target'] = in_array( $input['link_target'] ?? '_blank', array( '_blank', '_self' ), true ) ? $input['link_target'] : '_blank';

		$output['iframe_url'] = isset( $input['iframe_url'] ) ? esc_url_raw( $input['iframe_url'] ) : '';
		$output['event_name'] = sanitize_key( $input['event_name'] ?? 'floaty_click' );

		$output['custom_css'] = isset( $input['custom_css'] ) ? wp_strip_all_tags( $input['custom_css'] ) : '';

		$output['whatsapp_phone']   = isset( $input['whatsapp_phone'] ) ? preg_replace( '/[^0-9]/', '', $input['whatsapp_phone'] ) : '';
		$output['whatsapp_message'] = sanitize_text_field( $input['whatsapp_message'] ?? '' );

		$output['apointoo_enabled']     = ! empty( $input['apointoo_enabled'] ) ? 1 : 0;
		$output['apointoo_merchant_id'] = sanitize_text_field( $input['apointoo_merchant_id'] ?? '' );

		return $output;
	}

	/**
	 * Register the General tab fields.
	 *
	 * @return void
	 */
	private function register_general_section() {
		$page_id = $this->get_section_page_id( 'general' );

		add_settings_section(
			'vzflty_settings_general',
			__( 'General', 'floaty-button' ),
			null,
			$page_id
		);

		add_settings_field(
			'enabled',
			__( 'Enable plugin', 'floaty-button' ),
			array( $this, 'render_checkbox_field' ),
			$page_id,
			'vzflty_settings_general',
			array(
				'key' => 'enabled',
			)
		);

		add_settings_field(
			'button_label',
			__( 'Button label', 'floaty-button' ),
			array( $this, 'render_text_field' ),
			$page_id,
			'vzflty_settings_general',
			array(
				'key'     => 'button_label',
				'default' => __( 'Book now', 'floaty-button' ),
			)
		);

		add_settings_field(
			'position',
			__( 'Button position', 'floaty-button' ),
			array( $this, 'render_select_field' ),
			$page_id,
			'vzflty_settings_general',
			array(
				'key'     => 'position',
				'options' => array(
					'bottom_right' => __( 'Bottom right', 'floaty-button' ),
					'bottom_left'  => __( 'Bottom left', 'floaty-button' ),
				),
				'default' => 'bottom_right',
			)
		);

		add_settings_field(
			'action_type',
			__( 'Action type', 'floaty-button' ),
			array( $this, 'render_select_field' ),
			$page_id,
			'vzflty_settings_general',
			array(
				'key'     => 'action_type',
				'options' => array(
					'link'         => __( 'Open link', 'floaty-button' ),
					'iframe_modal' => __( 'Open iframe modal', 'floaty-button' ),
				),
				'default' => 'link',
			)
		);

		add_settings_field(
			'link_url',
			__( 'Link URL', 'floaty-button' ),
			array( $this, 'render_text_field' ),
			$page_id,
			'vzflty_settings_general',
			array(
				'key'         => 'link_url',
				'description' => __( 'Used when action type is set to link.', 'floaty-button' ),
			)
		);

		add_settings_field(
			'link_target',
			__( 'Link target', 'floaty-button' ),
			array( $this, 'render_select_field' ),
			$page_id,
			'vzflty_settings_general',
			array(
				'key'     => 'link_target',
				'options' => array(
					'_blank' => __( 'New tab (_blank)', 'floaty-button' ),
					'_self'  => __( 'Same tab (_self)', 'floaty-button' ),
				),
				'default' => '_blank',
			)
		);

		add_settings_field(
			'iframe_url',
			__( 'Iframe URL', 'floaty-button' ),
			array( $this, 'render_text_field' ),
			$page_id,
			'vzflty_settings_general',
			array(
				'key'         => 'iframe_url',
				'description' => __( 'Used when action type is set to iframe modal.', 'floaty-button' ),
			)
		);

		add_settings_field(
			'event_name',
			__( 'DataLayer event name', 'floaty-button' ),
			array( $this, 'render_text_field' ),
			$page_id,
			'vzflty_settings_general',
			array(
				'key'     => 'event_name',
				'default' => 'floaty_click',
			)
		);

		add_settings_field(
			'custom_css',
			__( 'Custom CSS', 'floaty-button' ),
			array( $this, 'render_textarea_field' ),
			$page_id,
			'vzflty_settings_general',
			array(
				'key'         => 'custom_css',
				'description' => __( 'Scope your rules with #floaty-button-container to avoid theme conflicts.', 'floaty-button' ),
			)
		);
	}

	/**
	 * Register the WhatsApp tab fields.
	 *
	 * @return void
	 */
	private function register_whatsapp_section() {
		$page_id = $this->get_section_page_id( 'whatsapp' );

		add_settings_section(
			'vzflty_settings_whatsapp',
			__( 'WhatsApp', 'floaty-button' ),
			null,
			$page_id
		);

		add_settings_field(
			'button_template',
			__( 'Button template', 'floaty-button' ),
			array( $this, 'render_select_field' ),
			$page_id,
			'vzflty_settings_whatsapp',
			array(
				'key'     => 'button_template',
				'options' => array(
					'default'  => __( 'Default button', 'floaty-button' ),
					'whatsapp' => __( 'WhatsApp floating button', 'floaty-button' ),
				),
				'default' => 'default',
			)
		);

		add_settings_field(
			'whatsapp_phone',
			__( 'WhatsApp phone number', 'floaty-button' ),
			array( $this, 'render_text_field' ),
			$page_id,
			'vzflty_settings_whatsapp',
			array(
				'key'         => 'whatsapp_phone',
				'description' => __( 'Use the full international format with digits only. Example: 5511999999999.', 'floaty-button' ),
			)
		);

		add_settings_field(
			'whatsapp_message',
			__( 'Prefilled message', 'floaty-button' ),
			array( $this, 'render_text_field' ),
			$page_id,
			'vzflty_settings_whatsapp',
			array(
				'key'         => 'whatsapp_message',
				'description' => __( 'Optional message shown when the chat opens.', 'floaty-button' ),
			)
		);
	}

	/**
	 * Register the Apointoo tab fields.
	 *
	 * @return void
	 */
	private function register_apointoo_section() {
		$page_id = $this->get_section_page_id( 'apointoo' );

		add_settings_section(
			'vzflty_settings_apointoo',
			__( 'Apointoo Booking', 'floaty-button' ),
			null,
			$page_id
		);

		add_settings_field(
			'apointoo_enabled',
			__( 'Enable Apointoo booking integration', 'floaty-button' ),
			array( $this, 'render_checkbox_field' ),
			$page_id,
			'vzflty_settings_apointoo',
			array(
				'key' => 'apointoo_enabled',
			)
		);

		add_settings_field(
			'apointoo_merchant_id',
			__( 'Merchant ID', 'floaty-button' ),
			array( $this, 'render_text_field' ),
			$page_id,
			'vzflty_settings_apointoo',
			array(
				'key'         => 'apointoo_merchant_id',
				'description' => __( 'Displays booking button flow for Google Search/Maps where available via Apointoo.', 'floaty-button' ),
			)
		);
	}

	/**
	 * Render a checkbox field.
	 *
	 * @param array $args Field args.
	 *
	 * @return void
	 */
	public function render_checkbox_field( $args ) {
		$options = vzflty_get_options();
		$key     = $args['key'];
		$value   = ! empty( $options[ $key ] );
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( VZFLTY_OPTION_KEY . '[' . $key . ']' ); ?>" value="1" <?php checked( $value ); ?> />
		</label>
		<?php
	}

	/**
	 * Render a text input field.
	 *
	 * @param array $args Field args.
	 *
	 * @return void
	 */
	public function render_text_field( $args ) {
		$options     = vzflty_get_options();
		$key         = $args['key'];
		$default     = $args['default'] ?? '';
		$value       = isset( $options[ $key ] ) ? $options[ $key ] : $default;
		$description = $args['description'] ?? '';
		?>
		<input type="text" class="regular-text" name="<?php echo esc_attr( VZFLTY_OPTION_KEY . '[' . $key . ']' ); ?>" value="<?php echo esc_attr( $value ); ?>" />
		<?php if ( $description ) : ?>
			<p class="description"><?php echo wp_kses_post( $description ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render a select field.
	 *
	 * @param array $args Field args.
	 *
	 * @return void
	 */
	public function render_select_field( $args ) {
		$options = vzflty_get_options();
		$key     = $args['key'];
		$choices = $args['options'] ?? array();
		$default = $args['default'] ?? '';
		$current = isset( $options[ $key ] ) ? $options[ $key ] : $default;
		?>
		<select name="<?php echo esc_attr( VZFLTY_OPTION_KEY . '[' . $key . ']' ); ?>">
			<?php foreach ( $choices as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current, $value ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Render a textarea field.
	 *
	 * @param array $args Field args.
	 *
	 * @return void
	 */
	public function render_textarea_field( $args ) {
		$options     = vzflty_get_options();
		$key         = $args['key'];
		$default     = $args['default'] ?? '';
		$value       = isset( $options[ $key ] ) ? $options[ $key ] : $default;
		$description = $args['description'] ?? '';
		?>
		<textarea name="<?php echo esc_attr( VZFLTY_OPTION_KEY . '[' . $key . ']' ); ?>" rows="8" cols="50" class="large-text code"><?php echo esc_textarea( $value ); ?></textarea>
		<?php if ( $description ) : ?>
			<p class="description"><?php echo wp_kses_post( $description ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Output contextual notices per tab.
	 *
	 * @param string $active_tab Active tab slug.
	 * @param array  $options    Current options.
	 *
	 * @return void
	 */
	private function render_notices( $active_tab, $options ) {
		if ( 'general' === $active_tab ) {
			$status_label = ! empty( $options['enabled'] ) ? __( 'Enabled', 'floaty-button' ) : __( 'Disabled', 'floaty-button' );
			printf(
				'<p class="description"><strong>%s</strong> %s</p>',
				esc_html__( 'Status:', 'floaty-button' ),
				esc_html( $status_label )
			);

			if ( 'link' === $options['action_type'] && 'whatsapp' !== $options['button_template'] && empty( $options['link_url'] ) ) {
				printf(
					'<div class="notice notice-warning"><p>%s</p></div>',
					esc_html__( 'Add a link URL to make the button work when using the link action.', 'floaty-button' )
				);
			}
		}

		if ( 'whatsapp' === $active_tab && 'whatsapp' === $options['button_template'] && empty( $options['whatsapp_phone'] ) ) {
			printf(
				'<div class="notice notice-warning"><p>%s</p></div>',
				esc_html__( 'Enter a WhatsApp phone number to display the WhatsApp floating button.', 'floaty-button' )
			);
		}
	}

	/**
	 * Get the active tab slug.
	 *
	 * @return string
	 */
	private function get_active_tab() {
		$tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$tab = $tab ? sanitize_key( $tab ) : 'general';

		return in_array( $tab, $this->tabs, true ) ? $tab : 'general';
	}

	/**
	 * Map tab to settings page id.
	 *
	 * @param string $tab Tab slug.
	 *
	 * @return string
	 */
	private function get_section_page_id( $tab ) {
		$allowed = $this->tabs;
		$tab     = in_array( $tab, $allowed, true ) ? $tab : 'general';

		return self::PAGE_SLUG . '-' . $tab;
	}
}

<?php
/**
 * Admin area logic.
 *
 * @package FloatyBookNowChat
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
	 * Cached options.
	 *
	 * @var array|null
	 */
	private $options = null;

	/**
	 * Available tabs.
	 *
	 * @var array
	 */
	private $tabs = array(
		'general'  => array(
			'slug' => 'general',
		),
		'whatsapp' => array(
			'slug' => 'whatsapp',
		),
		'custom'   => array(
			'slug' => 'custom',
		),
		'apointoo' => array(
			'slug' => 'apointoo',
		),
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
		$this->register_custom_section();
		$this->register_apointoo_section();
	}

	/**
	 * Add the settings page.
	 *
	 * @return void
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'Floaty Book Now Chat Settings', 'floaty-book-now-chat' ),
			__( 'Floaty', 'floaty-book-now-chat' ),
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

		$active_tab      = $this->get_active_tab();
		$this->options   = vzflty_get_options();
		$current_page    = $this->get_section_page_id( $active_tab );
		$tab_definitions = $this->get_tab_definitions();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Floaty Book Now Chat Settings', 'floaty-book-now-chat' ); ?></h1>
			<h2 class="nav-tab-wrapper">
				<?php foreach ( $tab_definitions as $tab_data ) : ?>
					<?php
					$tab_slug  = $tab_data['slug'];
					$tab_url   = $this->get_tab_url( $tab_slug );
					$is_active = ( $active_tab === $tab_slug );
					?>
					<a href="<?php echo esc_url( $tab_url ); ?>" class="nav-tab<?php echo esc_attr( $is_active ? ' nav-tab-active' : '' ); ?>">
						<?php echo esc_html( $tab_data['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</h2>

			<?php $this->render_notices( $active_tab ); ?>

			<form action="options.php" method="post">
				<?php
				settings_fields( self::OPTION_GROUP );
				do_settings_sections( $current_page );
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

		$input          = wp_unslash( $input );
		$current_values = vzflty_get_options();
		$output         = $current_values;
		$mode           = $this->resolve_mode( $current_values );

		if ( array_key_exists( 'mode', $input ) ) {
			$mode = in_array( $input['mode'], array( 'whatsapp', 'custom' ), true ) ? $input['mode'] : $mode;
		}

		$output['mode']            = $mode;
		$output['button_template'] = ( 'whatsapp' === $mode ) ? 'whatsapp' : 'default';

		if ( array_key_exists( 'enabled', $input ) ) {
			$output['enabled'] = ! empty( $input['enabled'] ) ? 1 : 0;
		}

		if ( array_key_exists( 'button_label', $input ) ) {
			$output['button_label'] = sanitize_text_field( $input['button_label'] );
		}

		if ( array_key_exists( 'position', $input ) ) {
			$output['position'] = in_array( $input['position'], array( 'bottom_right', 'bottom_left' ), true ) ? $input['position'] : $current_values['position'];
		}

		if ( array_key_exists( 'action_type', $input ) ) {
			$output['action_type'] = in_array( $input['action_type'], array( 'link', 'iframe_modal' ), true ) ? $input['action_type'] : $current_values['action_type'];
		}

		if ( array_key_exists( 'link_url', $input ) ) {
			$output['link_url'] = esc_url_raw( $input['link_url'] );
		}

		if ( array_key_exists( 'link_target', $input ) ) {
			$output['link_target'] = in_array( $input['link_target'], array( '_blank', '_self' ), true ) ? $input['link_target'] : $current_values['link_target'];
		}

		if ( array_key_exists( 'iframe_url', $input ) ) {
			$output['iframe_url'] = esc_url_raw( $input['iframe_url'] );
		}

		if ( array_key_exists( 'event_name', $input ) ) {
			$output['event_name'] = sanitize_key( $input['event_name'] );
		}

		if ( array_key_exists( 'custom_css', $input ) ) {
			$output['custom_css'] = wp_strip_all_tags( $input['custom_css'] );
		}

		if ( array_key_exists( 'whatsapp_phone', $input ) ) {
			$output['whatsapp_phone'] = preg_replace( '/[^0-9]/', '', $input['whatsapp_phone'] );
		}

		if ( array_key_exists( 'whatsapp_message', $input ) ) {
			$output['whatsapp_message'] = sanitize_text_field( $input['whatsapp_message'] );
		}

		if ( array_key_exists( 'apointoo_enabled', $input ) ) {
			$output['apointoo_enabled'] = ! empty( $input['apointoo_enabled'] ) ? 1 : 0;
		}

		if ( array_key_exists( 'apointoo_merchant_id', $input ) ) {
			$output['apointoo_merchant_id'] = sanitize_text_field( $input['apointoo_merchant_id'] );
		}

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
			__( 'General', 'floaty-book-now-chat' ),
			null,
			$page_id
		);

		add_settings_field(
			'enabled',
			__( 'Enable plugin', 'floaty-book-now-chat' ),
			array( $this, 'render_checkbox_field' ),
			$page_id,
			'vzflty_settings_general',
			array(
				'key' => 'enabled',
			)
		);

		add_settings_field(
			'button_label',
			__( 'Button label', 'floaty-book-now-chat' ),
			array( $this, 'render_text_field' ),
			$page_id,
			'vzflty_settings_general',
			array(
				'key'     => 'button_label',
				'default' => __( 'Book now', 'floaty-book-now-chat' ),
			)
		);

		add_settings_field(
			'position',
			__( 'Button position', 'floaty-book-now-chat' ),
			array( $this, 'render_select_field' ),
			$page_id,
			'vzflty_settings_general',
			array(
				'key'     => 'position',
				'options' => array(
					'bottom_right' => __( 'Bottom right', 'floaty-book-now-chat' ),
					'bottom_left'  => __( 'Bottom left', 'floaty-book-now-chat' ),
				),
				'default' => 'bottom_right',
			)
		);

		add_settings_field(
			'mode',
			__( 'Button mode', 'floaty-book-now-chat' ),
			array( $this, 'render_select_field' ),
			$page_id,
			'vzflty_settings_general',
			array(
				'key'     => 'mode',
				'options' => array(
					'whatsapp' => __( 'WhatsApp', 'floaty-book-now-chat' ),
					'custom'   => __( 'Custom', 'floaty-book-now-chat' ),
				),
				'default' => 'custom',
			)
		);

		add_settings_field(
			'event_name',
			__( 'DataLayer event name', 'floaty-book-now-chat' ),
			array( $this, 'render_text_field' ),
			$page_id,
			'vzflty_settings_general',
			array(
				'key'     => 'event_name',
				'default' => 'vzflty_click',
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
			__( 'WhatsApp', 'floaty-book-now-chat' ),
			null,
			$page_id
		);

		add_settings_field(
			'whatsapp_phone',
			__( 'WhatsApp phone number', 'floaty-book-now-chat' ),
			array( $this, 'render_text_field' ),
			$page_id,
			'vzflty_settings_whatsapp',
			array(
				'key'         => 'whatsapp_phone',
				'description' => __( 'Use the full international format with digits only. Example: 5511999999999.', 'floaty-book-now-chat' ),
			)
		);

		add_settings_field(
			'whatsapp_message',
			__( 'Prefilled message', 'floaty-book-now-chat' ),
			array( $this, 'render_text_field' ),
			$page_id,
			'vzflty_settings_whatsapp',
			array(
				'key'         => 'whatsapp_message',
				'description' => __( 'Optional message shown when the chat opens.', 'floaty-book-now-chat' ),
			)
		);
	}

	/**
	 * Register the Custom tab fields.
	 *
	 * @return void
	 */
	private function register_custom_section() {
		$page_id = $this->get_section_page_id( 'custom' );

		add_settings_section(
			'vzflty_settings_custom',
			__( 'Custom', 'floaty-book-now-chat' ),
			null,
			$page_id
		);

		add_settings_field(
			'action_type',
			__( 'Action type', 'floaty-book-now-chat' ),
			array( $this, 'render_select_field' ),
			$page_id,
			'vzflty_settings_custom',
			array(
				'key'     => 'action_type',
				'options' => array(
					'link'         => __( 'Open link', 'floaty-book-now-chat' ),
					'iframe_modal' => __( 'Open iframe modal', 'floaty-book-now-chat' ),
				),
				'default' => 'link',
			)
		);

		add_settings_field(
			'link_url',
			__( 'Link URL', 'floaty-book-now-chat' ),
			array( $this, 'render_text_field' ),
			$page_id,
			'vzflty_settings_custom',
			array(
				'key'         => 'link_url',
				'description' => __( 'Used when action type is set to link.', 'floaty-book-now-chat' ),
			)
		);

		add_settings_field(
			'link_target',
			__( 'Link target', 'floaty-book-now-chat' ),
			array( $this, 'render_select_field' ),
			$page_id,
			'vzflty_settings_custom',
			array(
				'key'     => 'link_target',
				'options' => array(
					'_blank' => __( 'New tab (_blank)', 'floaty-book-now-chat' ),
					'_self'  => __( 'Same tab (_self)', 'floaty-book-now-chat' ),
				),
				'default' => '_blank',
			)
		);

		add_settings_field(
			'iframe_url',
			__( 'Iframe URL', 'floaty-book-now-chat' ),
			array( $this, 'render_text_field' ),
			$page_id,
			'vzflty_settings_custom',
			array(
				'key'         => 'iframe_url',
				'description' => __( 'Used when action type is set to iframe modal.', 'floaty-book-now-chat' ),
			)
		);

		add_settings_field(
			'custom_css',
			__( 'Custom CSS', 'floaty-book-now-chat' ),
			array( $this, 'render_textarea_field' ),
			$page_id,
			'vzflty_settings_custom',
			array(
				'key'         => 'custom_css',
				'description' => __( 'Scope your rules with #floaty-button-container to avoid theme conflicts.', 'floaty-book-now-chat' ),
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
			__( 'Apointoo Booking', 'floaty-book-now-chat' ),
			array( $this, 'render_apointoo_description' ),
			$page_id
		);

		add_settings_field(
			'apointoo_enabled',
			__( 'Enable Apointoo booking integration', 'floaty-book-now-chat' ),
			array( $this, 'render_checkbox_field' ),
			$page_id,
			'vzflty_settings_apointoo',
			array(
				'key' => 'apointoo_enabled',
			)
		);

		add_settings_field(
			'apointoo_merchant_id',
			__( 'Merchant ID', 'floaty-book-now-chat' ),
			array( $this, 'render_text_field' ),
			$page_id,
			'vzflty_settings_apointoo',
			array(
				'key'         => 'apointoo_merchant_id',
				'description' => __( 'Merchant ID provided by Apointoo.', 'floaty-book-now-chat' ),
			)
		);
	}

	/**
	 * Render the Apointoo section description.
	 *
	 * @return void
	 */
	public function render_apointoo_description() {
		$email        = antispambot( 'support@vizuh.com' );
		$mailto       = 'mailto:' . $email;
		$mailto_label = sprintf( '<a href="%1$s">%2$s</a>', esc_url( $mailto ), esc_html( $email ) );

		printf(
			'<p class="description">%s</p>',
			esc_html__( 'Used for booking flows on Google Search/Maps where available via Apointoo.', 'floaty-book-now-chat' )
		);

		printf(
			'<p class="description">%s</p>',
			wp_kses(
				sprintf(
					__( 'To get your Apointoo Merchant ID, email %s', 'floaty-book-now-chat' ),
					$mailto_label
				),
				array(
					'a' => array(
						'href' => array(),
					),
				)
			)
		);

		printf(
			'<p class="description"><small>%s</small></p>',
			esc_html__( 'Google booking visibility depends on eligibility and provider setup.', 'floaty-book-now-chat' )
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
		$options = $this->get_options();
		$key     = $args['key'];
		$value   = ! empty( $options[ $key ] );
		?>
		<input type="hidden" name="<?php echo esc_attr( VZFLTY_OPTION_KEY . '[' . $key . ']' ); ?>" value="0" />
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
		$options     = $this->get_options();
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
		$options = $this->get_options();
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
		$options     = $this->get_options();
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
	 *
	 * @return void
	 */
	private function render_notices( $active_tab ) {
		$options = $this->get_options();
		$mode    = $this->resolve_mode( $options );

		if ( 'general' === $active_tab ) {
			$status_label = ! empty( $options['enabled'] ) ? __( 'Enabled', 'floaty-book-now-chat' ) : __( 'Disabled', 'floaty-book-now-chat' );
			printf(
				'<p class="description"><strong>%s</strong> %s</p>',
				esc_html__( 'Status:', 'floaty-book-now-chat' ),
				esc_html( $status_label )
			);

			if ( 'whatsapp' === $mode ) {
				printf(
					'<div class="notice notice-info"><p>%s</p></div>',
					esc_html__( 'Configure WhatsApp details in the WhatsApp tab.', 'floaty-book-now-chat' )
				);
			} elseif ( 'custom' === $mode ) {
				printf(
					'<div class="notice notice-info"><p>%s</p></div>',
					esc_html__( 'Configure action and URLs in the Custom tab.', 'floaty-book-now-chat' )
				);
			}
		}

		if ( 'whatsapp' === $active_tab ) {
			if ( 'whatsapp' !== $mode ) {
				printf(
					'<div class="notice notice-info"><p>%s</p></div>',
					esc_html__( 'This tab is used when Button Mode is set to WhatsApp in the General tab.', 'floaty-book-now-chat' )
				);
			} elseif ( empty( $options['whatsapp_phone'] ) ) {
				printf(
					'<div class="notice notice-warning"><p>%s</p></div>',
					esc_html__( 'Enter a WhatsApp phone number to display the WhatsApp floating button.', 'floaty-book-now-chat' )
				);
			}
		}

		if ( 'custom' === $active_tab ) {
			if ( 'custom' !== $mode ) {
				printf(
					'<div class="notice notice-info"><p>%s</p></div>',
					esc_html__( 'This tab applies when Button Mode is set to Custom in the General tab.', 'floaty-book-now-chat' )
				);
			} else {
				$action_type = $options['action_type'] ?? 'link';

				if ( 'link' === $action_type && empty( $options['link_url'] ) ) {
					printf(
						'<div class="notice notice-warning"><p>%s</p></div>',
						esc_html__( 'Add a link URL to make the button work when using the link action.', 'floaty-book-now-chat' )
					);
				}

				if ( 'iframe_modal' === $action_type && empty( $options['iframe_url'] ) ) {
					printf(
						'<div class="notice notice-warning"><p>%s</p></div>',
						esc_html__( 'Add an iframe URL to load content inside the modal.', 'floaty-book-now-chat' )
					);
				}
			}
		}
	}

	/**
	 * Get the active tab slug.
	 *
	 * @return string
	 */
	private function get_active_tab() {
		$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		return array_key_exists( $tab, $this->tabs ) ? $tab : 'general';
	}

	/**
	 * Map tab to settings page id.
	 *
	 * @param string $tab Tab slug.
	 *
	 * @return string
	 */
	private function get_section_page_id( $tab ) {
		$allowed_tab = array_key_exists( $tab, $this->tabs ) ? $tab : 'general';

		return self::PAGE_SLUG . '-' . $allowed_tab;
	}

	/**
	 * Build tab URL.
	 *
	 * @param string $slug Tab slug.
	 *
	 * @return string
	 */
	private function get_tab_url( $slug ) {
		return add_query_arg(
			array(
				'page' => self::PAGE_SLUG,
				'tab'  => $slug,
			),
			admin_url( 'options-general.php' )
		);
	}

	/**
	 * Get tab definitions with translated labels.
	 *
	 * @return array
	 */
	private function get_tab_definitions() {
		return array(
			'general'  => array(
				'slug'  => 'general',
				'label' => __( 'General', 'floaty-book-now-chat' ),
			),
			'whatsapp' => array(
				'slug'  => 'whatsapp',
				'label' => __( 'WhatsApp', 'floaty-book-now-chat' ),
			),
			'custom'   => array(
				'slug'  => 'custom',
				'label' => __( 'Custom', 'floaty-book-now-chat' ),
			),
			'apointoo' => array(
				'slug'  => 'apointoo',
				'label' => __( 'Apointoo Booking', 'floaty-book-now-chat' ),
			),
		);
	}

	/**
	 * Get cached options.
	 *
	 * @return array
	 */
	private function get_options() {
		if ( null === $this->options ) {
			$this->options = vzflty_get_options();
		}

		return $this->options;
	}

	/**
	 * Resolve button mode with backward compatibility.
	 *
	 * @param array $options Saved options.
	 *
	 * @return string
	 */
	private function resolve_mode( $options ) {
		$mode = isset( $options['mode'] ) ? $options['mode'] : '';

		if ( 'whatsapp' === $mode ) {
			return 'whatsapp';
		}

		if ( 'custom' === $mode ) {
			return 'custom';
		}

		if ( isset( $options['button_template'] ) && 'whatsapp' === $options['button_template'] ) {
			return 'whatsapp';
		}

		return 'custom';
	}
}

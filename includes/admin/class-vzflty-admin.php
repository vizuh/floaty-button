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
		'lead_capture' => array(
			'slug' => 'lead_capture',
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
		$this->register_apointoo_section();
		$this->register_lead_capture_section();
		$this->register_gtm_section();
	}

	/**
	 * Add the settings page.
	 *
	 * @return void
	 */
	public function add_settings_page() {
		// Main menu.
		add_menu_page(
			__( 'Floaty Button', 'floaty-book-now-chat' ),
			__( 'Floaty Button', 'floaty-book-now-chat' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_settings_page' ),
			'dashicons-format-chat',
			30
		);

		// Settings Submenu (Default).
		$settings_hook = add_submenu_page(
			self::PAGE_SLUG,
			__( 'Settings', 'floaty-book-now-chat' ),
			__( 'Settings', 'floaty-book-now-chat' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_settings_page' )
		);

		// Leads Submenu.
		$leads_hook = add_submenu_page(
			self::PAGE_SLUG,
			__( 'Leads', 'floaty-book-now-chat' ),
			__( 'Leads', 'floaty-book-now-chat' ),
			'manage_options',
			'vzflty-leads',
			array( $this, 'render_leads_page' )
		);

		if ( $settings_hook ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		}
	}

	/**
	 * Enqueue admin scripts on the settings page.
	 *
	 * @param string $hook_suffix The current admin page hook suffix.
	 *
	 * @return void
	 */
	public function enqueue_admin_scripts( $hook_suffix ) {
		// Check for Top Level Page hook or Settings Submenu hook.
		if ( 'toplevel_page_' . self::PAGE_SLUG !== $hook_suffix && 'floaty-button_page_' . self::PAGE_SLUG !== $hook_suffix ) {
			return;
		}

		wp_enqueue_script(
			'vzflty-admin',
			plugins_url( 'assets/js/vzflty-admin.js', VZFLTY_PLUGIN_FILE ),
			array(),
			VZFLTY_VERSION,
			true
		);
	}

	/**
	 * Render the leads page.
	 *
	 * @return void
	 */
	public function render_leads_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		require_once dirname( __DIR__ ) . '/admin/class-vzflty-leads-list-table.php';
		
		$list_table = new VZFLTY_Leads_List_Table();
		$list_table->prepare_items();

		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Leads', 'floaty-book-now-chat' ); ?></h1>
			<hr class="wp-header-end">
			
			<div class="card" style="max-width: 100%; margin-top: 20px; padding: 20px;">
				<form method="post">
					<?php $list_table->display(); ?>
				</form>
			</div>
		</div>
		<?php
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
			$mode = in_array( $input['mode'], array( 'whatsapp', 'custom', 'lead_capture' ), true ) ? $input['mode'] : $mode;
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

		if ( array_key_exists( 'whatsapp_rr_numbers', $input ) ) {
			$output['whatsapp_rr_numbers'] = sanitize_textarea_field( $input['whatsapp_rr_numbers'] );
		}

		if ( array_key_exists( 'apointoo_enabled', $input ) ) {
			$output['apointoo_enabled'] = ! empty( $input['apointoo_enabled'] ) ? 1 : 0;
		}

		if ( array_key_exists( 'apointoo_merchant_id', $input ) ) {
			$output['apointoo_merchant_id'] = sanitize_text_field( $input['apointoo_merchant_id'] );
		}

		// Device targeting.
		if ( array_key_exists( 'show_on_desktop', $input ) ) {
			$output['show_on_desktop'] = ! empty( $input['show_on_desktop'] ) ? 1 : 0;
		}

		if ( array_key_exists( 'show_on_mobile', $input ) ) {
			$output['show_on_mobile'] = ! empty( $input['show_on_mobile'] ) ? 1 : 0;
		}

		// Page targeting.
		if ( array_key_exists( 'page_targeting', $input ) ) {
			$output['page_targeting'] = in_array( $input['page_targeting'], array( 'all', 'homepage', 'specific' ), true ) ? $input['page_targeting'] : $current_values['page_targeting'];
		}

		if ( array_key_exists( 'target_pages', $input ) && is_array( $input['target_pages'] ) ) {
			$output['target_pages'] = array_map( 'absint', $input['target_pages'] );
		} elseif ( ! array_key_exists( 'target_pages', $input ) ) {
			// Checkbox not checked, reset to empty array.
			$output['target_pages'] = array();
		}

		// GTM DataLayer.
		if ( array_key_exists( 'gtm_enabled', $input ) ) {
			$output['gtm_enabled'] = ! empty( $input['gtm_enabled'] ) ? 1 : 0;
		}

		if ( array_key_exists( 'gtm_event_name', $input ) ) {
			$output['gtm_event_name'] = sanitize_key( $input['gtm_event_name'] );
		}

		// Lead Capture Fields.
		if ( array_key_exists( 'lc_field_name_enabled', $input ) ) {
			$output['lc_field_name_enabled'] = ! empty( $input['lc_field_name_enabled'] ) ? 1 : 0;
		}
		if ( array_key_exists( 'lc_field_email_enabled', $input ) ) {
			$output['lc_field_email_enabled'] = ! empty( $input['lc_field_email_enabled'] ) ? 1 : 0;
		}
		if ( array_key_exists( 'lc_field_phone_enabled', $input ) ) {
			$output['lc_field_phone_enabled'] = ! empty( $input['lc_field_phone_enabled'] ) ? 1 : 0;
		}
		if ( array_key_exists( 'lc_redirect_type', $input ) ) {
			$output['lc_redirect_type'] = in_array( $input['lc_redirect_type'], array( 'whatsapp', 'link' ), true ) ? $input['lc_redirect_type'] : 'whatsapp';
		}
		
		// Zoho Fields.
		if ( array_key_exists( 'zoho_enabled', $input ) ) {
			$output['zoho_enabled'] = ! empty( $input['zoho_enabled'] ) ? 1 : 0;
		}
		if ( array_key_exists( 'zoho_action_url', $input ) ) {
			$output['zoho_action_url'] = esc_url_raw( $input['zoho_action_url'] );
		}
		if ( array_key_exists( 'zoho_xnQsjsdp', $input ) ) {
			$output['zoho_xnQsjsdp'] = sanitize_text_field( $input['zoho_xnQsjsdp'] );
		}
		if ( array_key_exists( 'zoho_xmIwtLD', $input ) ) {
			$output['zoho_xmIwtLD'] = sanitize_text_field( $input['zoho_xmIwtLD'] );
		}

		// Language Tokens.
		$i18n_keys = array(
			'i18n_form_title',
			'i18n_form_subtitle',
			'i18n_name_placeholder',
			'i18n_email_placeholder',
			'i18n_phone_placeholder',
			'i18n_submit_label',
			'i18n_success_message',
		);
		foreach ( $i18n_keys as $key ) {
			if ( array_key_exists( $key, $input ) ) {
				$output[ $key ] = sanitize_text_field( $input[ $key ] );
			}
		}

		// Integration Settings.
		if ( array_key_exists( 'integration_enabled', $input ) ) {
			$output['integration_enabled'] = ! empty( $input['integration_enabled'] ) ? 1 : 0;
		}
		if ( array_key_exists( 'integration_webhook_url', $input ) ) {
			$output['integration_webhook_url'] = esc_url_raw( $input['integration_webhook_url'] );
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
					'whatsapp'     => __( 'WhatsApp', 'floaty-book-now-chat' ),
					'custom'       => __( 'Custom', 'floaty-book-now-chat' ),
					'lead_capture' => __( 'Lead Capture Form', 'floaty-book-now-chat' ),
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

		// Display Rules Section.
		add_settings_section(
			'vzflty_settings_display',
			__( 'Display Rules', 'floaty-book-now-chat' ),
			null,
			$page_id
		);

		add_settings_field(
			'show_on_desktop',
			__( 'Show on desktop', 'floaty-book-now-chat' ),
			array( $this, 'render_checkbox_field' ),
			$page_id,
			'vzflty_settings_display',
			array(
				'key' => 'show_on_desktop',
			)
		);

		add_settings_field(
			'show_on_mobile',
			__( 'Show on mobile', 'floaty-book-now-chat' ),
			array( $this, 'render_checkbox_field' ),
			$page_id,
			'vzflty_settings_display',
			array(
				'key' => 'show_on_mobile',
			)
		);

		add_settings_field(
			'page_targeting',
			__( 'Show on pages', 'floaty-book-now-chat' ),
			array( $this, 'render_select_field' ),
			$page_id,
			'vzflty_settings_display',
			array(
				'key'     => 'page_targeting',
				'options' => array(
					'all'      => __( 'All pages', 'floaty-book-now-chat' ),
					'homepage' => __( 'Homepage only', 'floaty-book-now-chat' ),
					'specific' => __( 'Specific pages', 'floaty-book-now-chat' ),
				),
				'default' => 'all',
			)
		);

		add_settings_field(
			'target_pages',
			__( 'Select pages', 'floaty-book-now-chat' ),
			array( $this, 'render_pages_multiselect' ),
			$page_id,
			'vzflty_settings_display',
			array(
				'key'         => 'target_pages',
				'description' => __( 'Only used when "Specific pages" is selected above.', 'floaty-book-now-chat' ),
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

		
		add_settings_field(
			'whatsapp_rr_numbers',
			__( 'Round Robin Numbers', 'floaty-book-now-chat' ),
			array( $this, 'render_textarea_field' ),
			$page_id,
			'vzflty_settings_whatsapp',
			array(
				'key'         => 'whatsapp_rr_numbers',
				'description' => __( 'Enter multiple WhatsApp numbers separated by commas for Round Robin rotation. If empty, the single number above is used.', 'floaty-book-now-chat' ),
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
					/* translators: %s: mailto link for requesting an Apointoo Merchant ID. */
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
	 * Register the Lead Capture tab fields.
	 *
	 * @return void
	 */
	private function register_lead_capture_section() {
		$page_id = $this->get_section_page_id( 'lead_capture' );

		add_settings_section(
			'vzflty_settings_lc_display',
			__( 'Form Configuration', 'floaty-book-now-chat' ),
			null,
			$page_id
		);

		add_settings_field(
			'lc_field_name_enabled',
			__( 'Name field', 'floaty-book-now-chat' ),
			array( $this, 'render_checkbox_field' ),
			$page_id,
			'vzflty_settings_lc_display',
			array(
				'key' => 'lc_field_name_enabled',
			)
		);

		add_settings_field(
			'lc_field_email_enabled',
			__( 'Email field', 'floaty-book-now-chat' ),
			array( $this, 'render_checkbox_field' ),
			$page_id,
			'vzflty_settings_lc_display',
			array(
				'key' => 'lc_field_email_enabled',
				'description' => __( 'Show "Email" field.', 'floaty-book-now-chat' )
			)
		);

		add_settings_field(
			'lc_field_phone_enabled',
			__( 'Phone field', 'floaty-book-now-chat' ),
			array( $this, 'render_checkbox_field' ),
			$page_id,
			'vzflty_settings_lc_display',
			array(
				'key' => 'lc_field_phone_enabled',
				'description' => __( 'Show "Phone" field (Required).', 'floaty-book-now-chat' ),
				'default' => 1
			)
		);

		add_settings_field(
			'lc_redirect_type',
			__( 'After submit redirect to', 'floaty-book-now-chat' ),
			array( $this, 'render_select_field' ),
			$page_id,
			'vzflty_settings_lc_display',
			array(
				'key' => 'lc_redirect_type',
				'options' => array(
					'whatsapp' => __( 'WhatsApp (uses WhatsApp tab settings)', 'floaty-book-now-chat' ),
					'link'     => __( 'Custom Link (uses Custom tab settings)', 'floaty-book-now-chat' ),
				),
				'default' => 'whatsapp'
			)
		);

		// Integrations Section
		add_settings_section(
			'vzflty_settings_lc_integrations',
			__( 'Integrations (Zoho)', 'floaty-book-now-chat' ),
			null,
			$page_id
		);

		add_settings_field(
			'zoho_enabled',
			__( 'Enable Zoho WebTwLead', 'floaty-book-now-chat' ),
			array( $this, 'render_checkbox_field' ),
			$page_id,
			'vzflty_settings_lc_integrations',
			array(
				'key' => 'zoho_enabled',
			)
		);

		add_settings_field(
			'zoho_action_url',
			__( 'Action URL', 'floaty-book-now-chat' ),
			array( $this, 'render_text_field' ),
			$page_id,
			'vzflty_settings_lc_integrations',
			array(
				'key' => 'zoho_action_url',
				'description' => 'https://crm.zoho.com/crm/WebToLeadForm'
			)
		);

		add_settings_field(
			'zoho_xnQsjsdp',
			__( 'Token: xnQsjsdp', 'floaty-book-now-chat' ),
			array( $this, 'render_text_field' ),
			$page_id,
			'vzflty_settings_lc_integrations',
			array(
				'key' => 'zoho_xnQsjsdp',
			)
		);

		add_settings_field(
			'zoho_xmIwtLD',
			__( 'Token: xmIwtLD', 'floaty-book-now-chat' ),
			array( $this, 'render_text_field' ),
			$page_id,
			'vzflty_settings_lc_integrations',
			array(
				'key' => 'zoho_xmIwtLD',
			)
		);
	}

	/**
	 * Register the GTM tab fields.
	 *
	 * @return void
	 */
	private function register_gtm_section() {
		$page_id = $this->get_section_page_id( 'gtm' );

		add_settings_section(
			'vzflty_settings_gtm',
			__( 'Google Tag Manager', 'floaty-book-now-chat' ),
			array( $this, 'render_gtm_section_description' ),
			$page_id
		);

		add_settings_field(
			'gtm_enabled',
			__( 'Enable GTM DataLayer', 'floaty-book-now-chat' ),
			array( $this, 'render_checkbox_field' ),
			$page_id,
			'vzflty_settings_gtm',
			array(
				'key' => 'gtm_enabled',
			)
		);

		add_settings_field(
			'gtm_event_name',
			__( 'Event name', 'floaty-book-now-chat' ),
			array( $this, 'render_text_field' ),
			$page_id,
			'vzflty_settings_gtm',
			array(
				'key'         => 'gtm_event_name',
				'default'     => 'vzflty_click',
				'description' => __( 'Custom event name pushed to dataLayer on button click.', 'floaty-book-now-chat' ),
			)
		);
	}

	/**
	 * Render GTM section description.
	 *
	 * @return void
	 */
	public function render_gtm_section_description() {
		?>
		<p><?php esc_html_e( 'Push button clicks to Google Tag Manager dataLayer for tracking in GA4, Google Ads, and other analytics tools.', 'floaty-book-now-chat' ); ?></p>
		<?php
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
	 * Render a pages multiselect field.
	 *
	 * @param array $args Field args.
	 *
	 * @return void
	 */
	public function render_pages_multiselect( $args ) {
		$options     = $this->get_options();
		$key         = $args['key'];
		$selected    = isset( $options[ $key ] ) && is_array( $options[ $key ] ) ? $options[ $key ] : array();
		$description = $args['description'] ?? '';

		$pages = get_pages( array( 'sort_column' => 'post_title' ) );

		if ( empty( $pages ) ) {
			echo '<p>' . esc_html__( 'No pages found.', 'floaty-book-now-chat' ) . '</p>';
			return;
		}
		?>
		<fieldset>
			<?php foreach ( $pages as $page ) : ?>
				<label style="display: block; margin-bottom: 5px;">
					<input
						type="checkbox"
						name="<?php echo esc_attr( VZFLTY_OPTION_KEY . '[' . $key . '][]' ); ?>"
						value="<?php echo esc_attr( $page->ID ); ?>"
						<?php checked( in_array( (string) $page->ID, $selected, true ) || in_array( $page->ID, $selected, true ) ); ?>
					/>
					<?php echo esc_html( $page->post_title ); ?>
				</label>
			<?php endforeach; ?>
		</fieldset>
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
			'lead_capture' => array(
				'slug'  => 'lead_capture',
				'label' => __( 'Lead Capture', 'floaty-book-now-chat' ),
			),
			'gtm'      => array(
				'slug'  => 'gtm',
				'label' => __( 'GTM / Analytics', 'floaty-book-now-chat' ),
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

		if ( 'lead_capture' === $mode ) {
			return 'lead_capture';
		}

		if ( isset( $options['button_template'] ) && 'whatsapp' === $options['button_template'] ) {
			return 'whatsapp';
		}

		return 'custom';
	}
}

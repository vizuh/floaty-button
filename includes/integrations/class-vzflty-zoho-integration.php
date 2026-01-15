<?php
/**
 * Zoho CRM Integration.
 *
 * @package FloatyBookNowChat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/interface-vzflty-integration.php';

/**
 * Handles sending leads to Zoho CRM via Web-to-Lead.
 */
class VZFLTY_Zoho_Integration implements VZFLTY_Integration {

	/**
	 * Get slug.
	 *
	 * @return string
	 */
	public function get_slug() {
		return 'zoho';
	}

	/**
	 * Send lead to Zoho.
	 *
	 * @param array $lead_data Lead data.
	 *
	 * @return bool|WP_Error
	 */
	public function send( $lead_data ) {
		$options = vzflty_get_options();
		
		$action_url = vzflty_get_option_value( $options, 'zoho_action_url', '' );
		if ( empty( $action_url ) ) {
			return new WP_Error( 'missing_config', 'Zoho Action URL not configured.' );
		}

		// Prepare Payload
		$payload = array(
			// Standard Zoho Fields
			'Last Name' => $lead_data['lead_name'], 
			'Email'     => $lead_data['lead_email'],
			'Mobile'    => $lead_data['lead_phone'],
			'Lead Source' => 'Site', // Hardcoded as per requirement request or mapping
			
			// Custom Enterprise Fields
			'LEADCF16'  => isset( $lead_data['wpp_number'] ) ? $lead_data['wpp_number'] : '', // Vendedor / WhatsApp Number
			'LEADCF17'  => isset( $lead_data['source_url'] ) ? $lead_data['source_url'] : '', // Auditoria / Ref URL
		);

		// Handle Click IDs / Audit info if needed in LEADCF17 or Description
		// If click_ids exist, append them?
		if ( ! empty( $lead_data['click_ids'] ) ) {
			// Append click IDs to Ref URL field or separate notes? 
			// Let's append to LEADCF17 for redundancy or put in Description if mapped.
			// Ideally we don't pollute the ref URL. Let's stick to source_url for LEADCF17 as requested "Ref_URL".
		}

		// Security Tokens (Hidden Fields)
		$zoho_tokens = array(
			'xnQsjsdp'   => vzflty_get_option_value( $options, 'zoho_xnQsjsdp', '' ),
			'xmIwtLD'    => vzflty_get_option_value( $options, 'zoho_xmIwtLD', '' ),
			'actionType' => 'TGVhZHM=',
			'returnURL'  => isset( $lead_data['source_url'] ) ? $lead_data['source_url'] : home_url(), 
		);

		$body = array_merge( $payload, $zoho_tokens );

		// Send Request
		// Note: Zoho Web-to-Lead expects a form POST (application/x-www-form-urlencoded), not JSON.
		// wp_remote_post defaults to this if body is an array.
		$response = wp_remote_post( $action_url, array(
			'body'      => $body,
			'timeout'   => 15,
			'sslverify' => true,
		) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );

		// Zoho redirects (302) on success.
		if ( $code >= 200 && $code < 400 ) {
			return true;
		}

		return new WP_Error( 'zoho_error', 'Zoho returned status ' . $code );
	}
}

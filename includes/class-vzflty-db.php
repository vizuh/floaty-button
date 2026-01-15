<?php
/**
 * Database handler.
 *
 * @package FloatyBookNowChat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles database tables and operations.
 */
class VZFLTY_DB {

	/**
	 * Leads table name.
	 *
	 * @var string
	 */
	public $leads_table;

	/**
	 * Queue table name.
	 *
	 * @var string
	 */
	public $queue_table;

	/**
	 * Assignments table name (for round-robin).
	 *
	 * @var string
	 */
	public $assignments_table;

	/**
	 * Constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->leads_table       = $wpdb->prefix . 'vzflty_leads';
		$this->queue_table       = $wpdb->prefix . 'vzflty_queue';
		$this->assignments_table = $wpdb->prefix . 'vzflty_assignments';
	}

	/**
	 * install tables.
	 *
	 * @return void
	 */
	public function install() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql_leads = "CREATE TABLE {$this->leads_table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			lead_name varchar(255) NOT NULL,
			lead_email varchar(255) DEFAULT '',
			lead_phone varchar(50) NOT NULL,
			lead_normalized_phone varchar(50) DEFAULT '',
			utm_data text DEFAULT NULL,
			utm_source varchar(100) DEFAULT '',
			utm_medium varchar(100) DEFAULT '',
			utm_campaign varchar(100) DEFAULT '',
			click_ids text DEFAULT NULL,
			landing_page text DEFAULT NULL,
			wpp_number varchar(50) DEFAULT '',
			status varchar(50) DEFAULT 'new',
			integration_status varchar(50) DEFAULT 'pending',
			source_url varchar(1000) DEFAULT '',
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY status (status),
			KEY created_at (created_at),
			KEY utm_source (utm_source)
		) $charset_collate;";

		$sql_queue = "CREATE TABLE {$this->queue_table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			lead_id bigint(20) unsigned NOT NULL,
			type varchar(50) NOT NULL,
			payload text NOT NULL,
			status varchar(50) DEFAULT 'pending',
			retries int(11) DEFAULT 0,
			next_retry_at datetime DEFAULT CURRENT_TIMESTAMP,
			last_error text DEFAULT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY lead_id (lead_id)
		) $charset_collate;";

		$sql_assignments = "CREATE TABLE {$this->assignments_table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			visitor_hash varchar(64) NOT NULL,
			route_id varchar(100) NOT NULL,
			assigned_number varchar(50) NOT NULL,
			assigned_at datetime DEFAULT CURRENT_TIMESTAMP,
			expires_at datetime DEFAULT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY visitor_hash (visitor_hash)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql_leads );
		dbDelta( $sql_queue );
		dbDelta( $sql_assignments );
	}

	/**
	 * Insert a lead.
	 *
	 * @param array $data Lead data.
	 *
	 * @return int|false Insert ID or false on failure.
	 */
	public function insert_lead( $data ) {
		global $wpdb;

		$defaults = array(
			'status'     => 'new',
			'created_at' => current_time( 'mysql' ),
		);

		$data = wp_parse_args( $data, $defaults );

		$inserted = $wpdb->insert(
			$this->leads_table,
			$data,
			array(
				'%s', // lead_name.
				'%s', // lead_email.
				'%s', // lead_phone.
				'%s', // lead_normalized_phone.
				'%s', // utm_data (json).
				'%s', // utm_source.
				'%s', // utm_medium.
				'%s', // utm_campaign.
				'%s', // click_ids.
				'%s', // landing_page.
				'%s', // wpp_number.
				'%s', // status.
				'%s', // integration_status.
				'%s', // source_url.
				'%s', // created_at.
			)
		);

		if ( ! $inserted ) {
			return false;
		}

		return $wpdb->insert_id;
	}

	/**
	 * Add item to queue.
	 *
	 * @param int    $lead_id Lead ID.
	 * @param string $type    Integration type.
	 * @param array  $payload Data payload.
	 *
	 * @return int|false Insert ID or false.
	 */
	public function add_to_queue( $lead_id, $type, $payload ) {
		global $wpdb;

		$inserted = $wpdb->insert(
			$this->queue_table,
			array(
				'lead_id' => $lead_id,
				'type'    => $type,
				'payload' => wp_json_encode( $payload ),
				'status'  => 'pending',
			),
			array(
				'%d',
				'%s',
				'%s',
				'%s',
			)
		);

		return $wpdb->insert_id;
	}

	/**
	 * Get leads with pagination.
	 *
	 * @param int $limit  Number of items.
	 * @param int $offset Offset.
	 *
	 * @return array
	 */
	public function get_leads( $limit = 20, $offset = 0 ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$this->leads_table} ORDER BY created_at DESC LIMIT %d OFFSET %d",
				$limit,
				$offset
			)
		);
	}

	/**
	 * Get total leads count.
	 *
	 * @return int
	 */
	public function get_total_leads() {
		global $wpdb;
		return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$this->leads_table}" );
	}
}

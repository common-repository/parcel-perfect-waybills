<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

global $db_version;
$db_version = '1.9';

class PPW_Install_Tables {
	
	public function __construct() {
		$this->PPW_Shipping_Packages();
		$this->PPW_Order_Tracking();
		$this->PPW_Waybills();
		$this->PPW_Error_Log();
	}
	
	
	private function PPW_Shipping_Packages() {
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'ppw_shipping_packages';
		
		$Charset_Collate = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE $table_name (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`created_date_time` varchar(255) NOT NULL,
		`updated_date_time` varchar(255) NOT NULL,
		`shipping_class` varchar(255) NOT NULL,
		`shipping_class_name` varchar(455) NOT NULL,
		`label` varchar(1500) NOT NULL,
		`no_items` varchar(255) NOT NULL,
		`height` varchar(500) NOT NULL,
		`width` varchar(500) NOT NULL,
		`length` varchar(500) NOT NULL,
		`weight` varchar(500) NOT NULL,
		`ignore` BOOLEAN NOT NULL DEFAULT FALSE,
		PRIMARY KEY  (`id`),
		UNIQUE KEY `id` (`id`)
		)$Charset_Collate;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
	
	private function PPW_Order_Tracking() {
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'ppw_order_tracking';
		
		$Charset_Collate = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE $table_name (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`created_date_time` varchar(255) NOT NULL,
		`updated_date_time` varchar(255) NOT NULL,
		`order` mediumint NOT NULL,
		`service` varchar(255) NOT NULL,
		`ppw_suburb_name` varchar(255) NOT NULL,
		`ppw_suburb_postal_code` smallint NOT NULL,
		`ppw_suburb_pcode` smallint NOT NULL,
		PRIMARY KEY  (`id`),
		UNIQUE KEY `id` (`id`)
		)$Charset_Collate;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
	
	private function PPW_Waybills() {
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'ppw_waybills';
		
		$Charset_Collate = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE $table_name (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`order` int(11) NOT NULL,
		`created_date_time` varchar(255) NOT NULL,
		`waybill` varchar(255) NOT NULL,
		`reference` varchar(255) NOT NULL,
		`destination_name` varchar(255) NOT NULL,
		`destination_pcode` varchar(10) NOT NULL,
		`destination_place` varchar(10) NOT NULL,
		`service` varchar(10) NOT NULL,
		`status` varchar(50) NOT NULL,
		`error_log` int(11) NULL,
		PRIMARY KEY  (`id`),
		UNIQUE KEY `id` (`id`)
		)$Charset_Collate;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
	
	private function PPW_Error_Log() {
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'ppw_error_log';
		
		$Charset_Collate = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE $table_name (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`order` int(11) NOT NULL,
		`created_date_time` varchar(255) NOT NULL,
		`error` varchar(1000) NOT NULL,
		PRIMARY KEY  (`id`),
		UNIQUE KEY `id` (`id`)
		)$Charset_Collate;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
	
	public static function PPW_Install_Tables_Setup() {
		
		new PPW_Install_Tables();
	}
}

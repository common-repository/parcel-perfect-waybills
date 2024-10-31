<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

class PPW_Class {
	
	protected $loader;
	protected $plugin_name;
	protected $version;
	
	public function __construct() {
		$this->Load_Dependencies();
		$this->Load_Admin_Section();
		$this->run();
	}
	
	private function Load_Dependencies() {
		require_once plugin_dir_path( __FILE__ ) . 'PPW_Loader_Class.php';
		require_once plugin_dir_path( __FILE__ ) . 'PPW_Admin_Pages.php';
		
		$this->loader = new PPW_Loader_Class();
	}
	
	private function Load_Admin_Section() {
		
		add_action( 'admin_menu', 'PPW_Admin_Menu' );
		
		function ppw_Admin_Menu() {
			global $wpdb, $wp_version, $_registered_pages;
			add_menu_page( 'Parcel Perfect', 'Parcel Perfect', 'manage_options', 'ppw_admin', 'ppw_options', 'dashicons-admin-tools', 99 );
			add_submenu_page( 'ppw_admin', 'Shipping Packages', 'Shipping Packages', 'manage_options', 'ppw_shipping_packages', 'ppw_shipping_packages' );
			add_submenu_page( null, 'Add Shipping Package', 'Add Shipping Package', 'manage_options', 'ppw_shipping_packages_add', 'ppw_shipping_packages_add' );  
			add_submenu_page( null, 'Edit Shipping Package', 'Edit Shipping Package', 'manage_options', 'ppw_shipping_packages_edit', 'ppw_shipping_packages_edit' );
			add_submenu_page( null, 'Delete Shipping Package', 'Delete Shipping Package', 'manage_options', 'ppw_shipping_packages_delete', 'ppw_shipping_packages_delete' );
			add_submenu_page( 'ppw_admin', 'Error Logs', 'Error Logs', 'manage_options', 'ppw_error_logs', 'ppw_error_logs' );
		}
	}
	
	public function get_loader() {
		return $this->loader;
	}
	
	public function run() {
		
		$this->define_public_hooks();
	}
	
	private function Define_Public_Hooks() {
		add_action( 'wp_loaded', array( $this->loader, 'Enqueue_Scripts' ) );
	}
}

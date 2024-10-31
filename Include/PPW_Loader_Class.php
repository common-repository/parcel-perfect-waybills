<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

class PPW_Loader_Class {
	
	private $plugin_name;
	private $version;
	
	public function __construct() {
		$this->Common_Terms();
	}
	
	public function Common_Terms() {
		
		if ( ! defined( 'PPW_Plugin_Name' ) ) {
			define( 'PPW_Plugin_Name', 'parcel-perfect-waybills' );
		}
		
		if ( ! defined( 'PPW_Plugin_Dir' ) ) {
			define( 'PPW_Plugin_Dir', WP_PLUGIN_DIR . '/' . PPW_Plugin_Name );
		}
		
		if ( ! defined( 'PPW_Plugin_URL' ) ) {
			define( 'PPW_Plugin_URL', site_url() . '/wp-content/plugins/' . PPW_Plugin_Name );
		}
		
		require_once( PPW_Plugin_Dir . '/Include/PPW_Functions.php' );
		
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			require_once( PPW_Plugin_Dir . '/Include/classes/PP_WC_Shipping_Method.php' );
		}
	}
	
	public function Enqueue_Scripts() {
		if ( ! did_action( 'wp_enqueue_media' ) ) {
			add_action( 'admin_enqueue_scripts', 'wp_enqueue_media' );
		}
		
		wp_register_style( 'ppw_plugin_style', PPW_Plugin_URL . '/Include/css/style.css', array() );
		wp_register_style( 'ppw_jquery_ui', PPW_Plugin_URL . '/Include/css/jquery-ui.css', array() );
		wp_register_style( 'ppw_datatables', PPW_Plugin_URL . '/Include/css/datatables.css', array() );
		wp_register_style( 'ppw_datatables_btns', PPW_Plugin_URL . '/Include/css/datatables_btns.css', array() );
		wp_register_style( 'ppw_print', PPW_Plugin_URL . '/Include/css/print.min.css', array() );
		wp_register_style( 'ppw_tom', PPW_Plugin_URL . '/Include/css/tom.css', array() );
		
		wp_enqueue_style( 'ppw_plugin_style' );
		wp_enqueue_style( 'ppw_jquery_ui' );
		wp_enqueue_style( 'ppw_datatables' );
		wp_enqueue_style( 'ppw_datatables_btns' );
		wp_enqueue_style( 'ppw_print' );
		wp_enqueue_style( 'ppw_tom' );
		
		wp_register_script( 'ppw_custom_js', PPW_Plugin_URL . '/Include/js/custom.js', array( 'jquery' ) );
		wp_register_script( 'ppw_tom_js', PPW_Plugin_URL . '/Include/js/tom.js', array( 'jquery' ) );
		wp_register_script( 'ppw_datatables_js', PPW_Plugin_URL . '/Include/js/datatables.js', array( 'jquery','jquery-ui-core','jquery-ui-widget' ) );
		wp_register_script( 'ppw_datatables_btns', PPW_Plugin_URL . '/Include/js/datatables_btns.js', array( 'jquery','jquery-ui-core','jquery-ui-widget' ) );
		wp_register_script( 'ppw_datatables_cols', PPW_Plugin_URL . '/Include/js/datatables_cols.js', array( 'jquery','jquery-ui-core','jquery-ui-widget' ) );
		wp_register_script( 'ppw_datatables_export', PPW_Plugin_URL . '/Include/js/datatables_export.js', array( 'jquery','jquery-ui-core','jquery-ui-widget' ) );
		wp_register_script( 'ppw_datatables_print', PPW_Plugin_URL . '/Include/js/datatables_print.js', array( 'jquery','jquery-ui-core','jquery-ui-widget' ) );
		wp_register_script( 'ppw_timepickerjs', PPW_Plugin_URL . '/Include/js/timepicker.js', array( 'jquery','jquery-ui-core','jquery-ui-widget' ) );
		wp_register_script( 'ppw_print', PPW_Plugin_URL . '/Include/js/print.min.js', array( 'jquery','jquery-ui-core','jquery-ui-widget' ) );
		
		// Localize the script with new data
		$translation_array = array(
			'ajax' => admin_url('admin-ajax.php'),
			'admin_packages' => get_admin_url().'admin.php?page=ppw_shipping_packages',
			'places' => PPW_Plugin_URL . '/Include/PPW_GetPlaces.php',
			'ppw_url' => PPW_Plugin_URL,
		);
		wp_localize_script( 'ppw_custom_js', 'ppw', $translation_array );
		
		wp_enqueue_script( 'ppw_custom_js' );
		wp_enqueue_script( 'ppw_tom_js' );
		wp_enqueue_script( 'jquery-ui-core');
		wp_enqueue_script( 'jquery-ui-widget');
		wp_enqueue_script( 'ppw_datatables_js' );
		wp_enqueue_script( 'ppw_datatables_btns' );
		wp_enqueue_script( 'ppw_datatables_cols' );
		wp_enqueue_script( 'ppw_datatables_export' );
		wp_enqueue_script( 'ppw_datatables_print' );
		wp_enqueue_script( 'ppw_timepickerjs' );
		wp_enqueue_script( 'ppw_print' );
	}
	
}

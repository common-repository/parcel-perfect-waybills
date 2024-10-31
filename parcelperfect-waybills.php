<?php
/**
 * Plugin Name:       Parcel Perfect Waybills
 * Description:       This plugin powers Parcel Perfect API to create waybills for Woocommerce orders
 * Version:           3.1.1
 * Author:            Alwyn Malan
 * Text Domain:       parcel-perfect-waybills
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

function activate_ppw_plugin() {
	require_once plugin_dir_path( __FILE__ ) . '/Include/PPW_Install_Tables.php';
	PPW_Install_Tables::PPW_Install_Tables_Setup();
}

register_activation_hook( __FILE__, 'activate_ppw_plugin' );

require plugin_dir_path( __FILE__ ) . '/Include/PPW_Class.php';

function Run_PPW_Class() {
	$PPW_Class = new PPW_Class();
}

Run_PPW_Class();
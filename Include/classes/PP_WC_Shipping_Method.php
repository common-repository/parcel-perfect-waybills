<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}
// Parcel Perfect Shipping Method class for white label plugin

/**
 * Load the ppshipping method class
 */
function ppw_shipping_shipping_method_init() {

	if ( ! class_exists( 'PP_WC_Shipping_Method' ) ) {

		class PP_WC_Shipping_Method extends WC_Shipping_Method {

			/**
			 * Private Variable used for instances of class outside of Class
			 */
			protected static $instance = NULL;

			/**
			 * PP Shipping Class Constructor
			 */
			public function __construct( $instance_id = 0 ) {
				$this->id                 = 'ppw_shipping'; // Id for your shipping method. Should be uunique.
				$this->instance_id        = absint( $instance_id );
				$this->method_title       = __( 'Parcel Perfect' );  // Title shown in admin
				$this->method_description = __( 'Parcel Perfect Shipping Method' ); // Description shown in admin

				$this->supports = array(
					'settings',
					'shipping-zones',
					'instance-settings',
					'instance-settings-modal',
				);

				$this->title = "Parcel Perfect"; // This can be added as an setting but for this example its forced.

				$this->init();
			}

			/**
			 * Method to instantiate class to access option values
			 */
			public static function get_instance()
			{
				if ( NULL === self::$instance )
					self::$instance = new self;

				return self::$instance;
			}

			/**
			 * Init your settings
			 *
			 * @access public
			 * @return void
			 */
			public function init() {

				// Load the settings API
				$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
				$this->init_settings(); // This is part of the settings API. Loads settings you previously init.

				$this->enabled = $this->get_option( 'enabled' );

				if ( $this->get_option( 'account_id' ) == '' ) {
					$this->enabled = 'no';
				}

				// Save settings in admin if you have any defined
				add_action( 'woocommerce_update_options_shipping_' . $this->id, array(
					$this,
					'process_admin_options'
				) );
			}

			/**
			 * Initialise Gateway Settings Form Fields
			 *
			 * @access public
			 * @return void
			 */
			public function init_form_fields() {
				$settings = get_option('woocommerce_ppw_shipping_settings',array());
				
				$this->form_fields = array(
					'enabled'     => array(
						'title'       => __( 'Enable Parcel Perfect Shipping', 'ppw_shipping' ),
						'description' => __( 'Enables Parcel Perfect as a shipping option on the checkout', 'ppw_shipping' ),
						'type'        => 'checkbox',
						'label'       => __( 'Enabled', 'ppw_shipping' )
					),
				);

			}

			/**
			 * Display the options
			 *
			 * @access public
			 * @return void
			 */
			public function admin_options() {
				?>
				<h2><?php _e( 'Parcel Perfect Shipping', 'woocommerce' ); ?></h2>
				<table class="form-table">
					<?php $this->generate_settings_html(); ?>
				</table> <?php
			}

			/**
			 * calculate_shipping function.
			 *
			 * @access public
			 *
			 * @param mixed $package
			 *
			 * @return void
			 */
			public function calculate_shipping( $package = array() ) {
				if ( $this->settings['enabled'] !== 'yes' ||
					 empty($package['destination']['address_1']) ||
					 empty($package['destination']['suburb_pcode']) ||
					 empty($package['destination']['postcode'])
				) {
					return;
				}
				
				global $wpdb, $woocommerce;
				
				$load = array(
					'PP_Url'		=>	get_option('ppw_e_pp_url'),
					'PP_User'		=>	get_option('ppw_e_pp_username'),
					'PP_Password'	=>	get_option('ppw_e_pp_password')
				);
				
				require_once(PPW_Plugin_Dir . '/Include/classes/ParcelPerfect.php');
				
				$ParcelPerfect = new ParcelPerfect(json_decode(json_encode($load)));
				$token = $ParcelPerfect->GenerateToken();
				
				$items = $woocommerce->cart->get_cart();

				$ppw_cart = ppw_get_cart($items);
				
				$query = array();
				$query['details'] = array();
				$query['details']['quoteDate'] = date('d.m.Y');
				$query['details']['origpers'] = get_bloginfo('name');
				$query['details']['origperadd1'] = get_option( 'woocommerce_store_address' );
				$query['details']['origperadd2'] = get_option( 'woocommerce_store_address_2' );
				$query['details']['origplace'] = get_option('ppw_origin_courier_code');
				$query['details']['origtown'] = get_option( 'woocommerce_store_address_2' );
				$query['details']['origperpcode'] = get_option( 'woocommerce_store_postcode' );
				$query['details']['origpercontact'] = get_bloginfo('name');
				$query['details']['origperemail'] = get_option( 'admin_email' );
				
				if (!empty($package['destination']['first_name'])) {
					$query['details']['destpers'] = $package['destination']['first_name'].' '.$package['destination']['last_name'];
				}
				else {
					$query['details']['destpers'] = $package['destination']['shipping_first_name'].' '.$package['destination']['shipping_last_name'];
				}
				
				if (!empty($package['destination']['address_1'])) {
					$query['details']['destperadd1'] = $package['destination']['address_1'];
				}
				else {
					$query['details']['destperadd1'] = $package['destination']['shipping_address_1'];
				}
				
				if (!empty($package['destination']['address_2'])) {
					$query['details']['destperadd2'] = $package['destination']['address_2'];
				}
				else {
					$query['details']['destperadd2'] = $package['destination']['shipping_address_2'];
				}
				
				$query['details']['destplace'] = $package['destination']['suburb_pcode'];
				
				if (!empty($package['destination']['postcode'])) {
					$query['details']['destperpcode'] = $package['destination']['postcode'];
				}
				else {
					$query['details']['destperpcode'] = $package['destination']['shipping_postcode'];
				}
				
				if (!empty($package['destination']['first_name'])) {
					$query['details']['destpercontact'] = $package['destination']['first_name'].' '.$package['destination']['last_name'];
				}
				else {
					$query['details']['destpercontact'] = $package['destination']['shipping_first_name'].' '.$package['destination']['last_name'];
				}
				
				if (!empty($package['destination']['phone'])) {
					$query['details']['destperphone'] = $package['destination']['phone'];
				}
				else {
					$query['details']['destperphone'] = $package['destination']['phone'];
				}
				
				if (!empty($package['destination']['phone'])) {
					$query['details']['destpercell'] = $package['destination']['phone'];
				}
				else {
					$query['details']['destpercell'] = $package['destination']['phone'];
				}
				
				if (!empty($package['destination']['email'])) {
					$query['details']['destperemail'] = $package['destination']['email'];
				}
				else {
					$query['details']['destperemail'] = $package['destination']['billing_email'];
				}
				
				$query['details']['reference'] = get_option('ppw_abr').'-'.date('Ymd').'-'.date('His');
				$query['details']['insuranceflag'] = '0';
				$query['details']['instype'] = '0';
				$query['details']['declaredvalue'] = '0';
				
				$query['contents'] = $ppw_cart;
				
				$query['s_ttype'] = 'I';
				
				$result = $ParcelPerfect->GetQuote($query);
				
				if (empty($result) || (!empty($result->errorcode) && $result->errorcode != 0)) {
					return;
				}

				$shipping_options = $this->ppw_shipping_get_shipping_options();

				/**
				 * Loop through services allowed and add rate to checkout page
				 */
				foreach ($result->results[0]->rates as $rate) {
					if (isset($shipping_options[$rate->service])) {
						
						$datetime = new DateTime();
						$date = $datetime->createFromFormat('Y-m-d', $rate->duedate);
						
						$date->modify('+1 day');
						$start = $date->format('Y-m-d');
						
						$date->modify('+1 day');
						$end = $date->format('Y-m-d');
						
						$delivery = $this->ppw_shipping_get_days($start).' - '.$this->ppw_shipping_get_days($end).' days<br />';

						$this->add_rate( array(
							'id'       => $rate->service.'-'.$result->results[0]->quoteno,
							'label'    => $rate->name. ' ('.$rate->service.') '.$delivery,
							'cost'     => $rate->total,
							'calc_tax' => 'per_order'
						));
					}
				}
			}

			/**
			 * The list of shipping options
			 *
			 * @return array
			 */
			private function ppw_shipping_get_shipping_options() {
				return array(
					'IPP' => __( 'International PP', 'ppw_shipping' ),
					'ONX' => __( 'Overnight Air', 'ppw_shipping' ),
					'ECO' => __( 'Economy Road', 'ppw_shipping' ),
					'BUD' => __( 'Budget/Economy', 'ppw_shipping' ),
					'EXR' => __( 'Express Road', 'ppw_shipping' ),
					'INC' => __( 'Incity Express', 'ppw_shipping' ),
					'SDX' => __( 'Same day Express', 'ppw_shipping' ),
					'DWN' => __( 'Domestic Wine', 'ppw_shipping'),
					'RDF' => __( 'Economy Domestic Road Freight', 'ppw_shipping')
				);
			}
			
			private function ppw_shipping_get_days($date) {
				$start = strtotime(date('Y-m-d'));
				$end = strtotime($date);

				$days_between = ceil(abs($end - $start) / 86400);

				return $days_between;	
			}
		}
	}
}

add_action( 'woocommerce_shipping_init', 'ppw_shipping_shipping_method_init' );

/**
 * Add the PP shipping method
 *
 * @param $methods
 *
 * @return mixed
 */
function ppw_shipping_add_shipping_method( $methods ) {
	$methods['ppw_shipping'] = 'PP_WC_Shipping_Method';

	return $methods;
}

add_filter( 'woocommerce_shipping_methods', 'ppw_shipping_add_shipping_method' );

function ppw_shipping_add_suburb_data_order_page($order){
	global $post_id,$wpdb;
	
	$order = new WC_Order( $post_id );
	$result = $wpdb->get_row("select * from `".$wpdb->prefix."ppw_order_tracking` where `order` = ".$order->get_id());
	
	if (!empty($result->ppw_suburb_name)) {
		echo '<p><strong>'.__('Suburb').':</strong> ' . $result->ppw_suburb_name . '</p>';
	}
	
	if (!empty($result->ppw_suburb_postal_code)) {
		echo '<p><strong>'.__('Postal Code').':</strong> ' . $result->ppw_suburb_postal_code . '</p>';
	}
	
	if (!empty($result->ppw_suburb_pcode)) {
		echo '<p><strong>'.__('Courier Code').':</strong> ' . $result->ppw_suburb_pcode . '</p>';
	}
}

add_action( 'woocommerce_admin_order_data_after_shipping_address', 'ppw_shipping_add_suburb_data_order_page', 10, 1 );

function ppw_shipping_array_insert( &$array, $position, $insert ) {
	if ( is_int( $position ) ) {
		array_splice( $array, $position, 0, $insert );
	} else {
		$pos   = array_search( $position, array_keys( $array ) );
		$array = array_merge(
			array_slice( $array, 0, $pos ),
			$insert,
			array_slice( $array, $pos )
		);
	}
}

function ppw_shipping_add_custom_checkout_field( $checkout ) { 
	global $wpdb;
	
	$settings = get_option('woocommerce_ppw_shipping_settings',array());
	
	if ($settings['enabled'] == 'yes') {
		// Output the hidden field
		echo '<div id="pp_shipping_suburb_name_container">
				<input type="hidden" class="input-hidden" name="ppw_suburb_name" id="ppw_suburb_name" value="">
			</div>
			<div id="pp_shipping_suburb_postal_code_container">
				<input type="hidden" class="input-hidden" name="ppw_suburb_postal_code" id="ppw_suburb_postal_code" value="">
			</div>
			<div id="pp_shipping_suburb_pcode_container">
				<input type="hidden" class="input-hidden" name="ppw_suburb_pcode" id="ppw_suburb_pcode" value="">
			</div>';
	}
}

add_action( 'woocommerce_before_order_notes', 'ppw_shipping_add_custom_checkout_field' );

function clear_shipping_fields_values( $value, $input ) {
    $keys = ['city'];
    
	$key  = str_replace('shipping_', '', $input);
    if( in_array($key, $keys) && is_checkout() ) {
        $value = '';
    }
	
	$key  = str_replace('billing_', '', $input);
    if( in_array($key, $keys) && is_checkout() ) {
        $value = '';
    }
    return $value;
}

add_filter( 'woocommerce_checkout_get_value' , 'clear_shipping_fields_values' , 10, 2 );

function ppw_shipping_woocommerce_cart_shipping_packages($packages) {
	
	if (isset($_POST['ppw_suburb_name'])) {
		$shipping_suburb_name = $_POST['ppw_suburb_name'];
	}
	else {
		if (isset($_POST['post_data']) && !empty($_POST['post_data'])) {
			$post_data = ppw_shipping_get_all_post_data($_POST['post_data']);
			
			$shipping_suburb_name = $post_data['ppw_suburb_name'];
		}
		else {
			$shipping_suburb_name = '';
		}
	}
	
	if (isset($_POST['ppw_suburb_postal_code'])) {
		$shipping_suburb_postal_code = $_POST['ppw_suburb_postal_code'];
	}
	else {
		if (isset($_POST['post_data']) && !empty($_POST['post_data'])) {
			$post_data = ppw_shipping_get_all_post_data($_POST['post_data']);
			
			$shipping_suburb_postal_code = $post_data['ppw_suburb_postal_code'];
		}
		else {
			$shipping_suburb_postal_code = '';
		}
	}
	
	if (isset($_POST['ppw_suburb_pcode'])) {
		$pp_suburb_pcode = $_POST['ppw_suburb_pcode'];
	}
	else {
		if (isset($_POST['post_data']) && !empty($_POST['post_data'])) {
			$post_data = ppw_shipping_get_all_post_data($_POST['post_data']);
			
			$pp_suburb_pcode = $post_data['ppw_suburb_pcode'];
		}
		else {
			$pp_suburb_pcode = '';
		}
	}

	// Reset the packages
	$packages = array();

	// Bulky items
	$bulky_items = array();
	$regular_items = array();

	// Sort bulky from regular
	foreach ( WC()->cart->get_cart() as $item ) {
		if ( $item['data']->needs_shipping() ) {
			if ( $item['data']->get_shipping_class() == 'bulky' ) {
				$bulky_items[] = $item;
			} else {
				$regular_items[] = $item;
			}
		}
	}

	// Put inside packages
	if ( $bulky_items ) {
		$packages[] = array(
			'contents' => $bulky_items,
			'contents_cost' => array_sum( wp_list_pluck( $bulky_items, 'line_total' ) ),
			'applied_coupons' => WC()->cart->applied_coupons,
			'destination' => array(
				'first_name' => WC()->customer->get_shipping_first_name(),
				'last_name' => WC()->customer->get_shipping_last_name(),
				'phone' => WC()->customer->get_billing_phone(),
				'email' => WC()->customer->get_email(),
				'country' => WC()->customer->get_shipping_country(),
				'state' => WC()->customer->get_shipping_state(),
				'postcode' => WC()->customer->get_shipping_postcode(),
				'address_1' => WC()->customer->get_shipping_address(),
				'address_2' => WC()->customer->get_shipping_address_2(),
				'suburb_name' => urldecode($shipping_suburb_name),
				'suburb_postal_code' => urldecode($shipping_suburb_postal_code),
				'suburb_pcode' => urldecode($pp_suburb_pcode),
				'city' => WC()->customer->get_shipping_city(),
			)
		);
	}
	if ( $regular_items ) {
		$packages[] = array(
			'contents' => $regular_items,
			'contents_cost' => array_sum( wp_list_pluck( $regular_items, 'line_total' ) ),
			'applied_coupons' => WC()->cart->applied_coupons,
			'destination' => array(
				'first_name' => WC()->customer->get_shipping_first_name(),
				'last_name' => WC()->customer->get_shipping_last_name(),
				'phone' => WC()->customer->get_billing_phone(),
				'email' => WC()->customer->get_email(),
				'country' => WC()->customer->get_shipping_country(),
				'state' => WC()->customer->get_shipping_state(),
				'postcode' => WC()->customer->get_shipping_postcode(),
				'address_1' => WC()->customer->get_shipping_address(),
				'address_2' => WC()->customer->get_shipping_address_2(),
				'suburb_name' => urldecode($shipping_suburb_name),
				'suburb_postal_code' => urldecode($shipping_suburb_postal_code),
				'suburb_pcode' => urldecode($pp_suburb_pcode),
				'city' => WC()->customer->get_shipping_city(),
			)
		);
	}
	
	return $packages;
}

add_filter( 'woocommerce_cart_shipping_packages', 'ppw_shipping_woocommerce_cart_shipping_packages' );

function ppw_shipping_before_checkout_create_order( $order_id, $data, $order ) {
	global $wpdb;
	
	$args = array(
		'created_date_time'			=> date('Y-m-d H:i:s'),
		'updated_date_time'			=> date('Y-m-d H:i:s'),
		'order'						=> $order_id,
		'service'					=> $data['shipping_method'][0],
		'ppw_suburb_name'			=> $_POST['ppw_suburb_name'],
		'ppw_suburb_postal_code'	=> intval($_POST['ppw_suburb_postal_code']),
		'ppw_suburb_pcode'			=> intval($_POST['ppw_suburb_pcode']),
	);
	
	$wpdb->insert($wpdb->prefix.'ppw_order_tracking',$args);
	
	update_post_meta( $order_id, '_billing_city', $_POST['ppw_suburb_name']);
    update_post_meta($order_id, '_shipping_city',$_POST['ppw_suburb_name'] ); 
}

add_action('woocommerce_checkout_order_processed', 'ppw_shipping_before_checkout_create_order', 10, 3);

function ppw_shipping_get_all_post_data($post_data) {
	$result_array = array();
	
	$items = explode('&',$post_data);
	
	foreach ($items as $i) {
		$key_value = explode('=',$i);
		
		$key = $key_value[0];
		$value = $key_value[1];
		
		$result_array[$key] = str_replace('+',' ',$value);
	}
	
	return $result_array;
}
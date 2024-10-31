<?php

// Adding Meta container admin shop_order pages
add_action( 'add_meta_boxes', 'ppw_add_waybill_meta_boxes' );
if ( ! function_exists( 'ppw_add_waybill_meta_boxes' ) )
{
	function ppw_add_waybill_meta_boxes()
	{
		add_meta_box( 'ppw_waybill_history_box', __('Waybill Information','woocommerce'), 'ppw_waybill_history_box', 'shop_order', 'side', 'high' );
		add_meta_box( 'ppw_generate_waybill_box', __('Create a Waybill','woocommerce'), 'ppw_create_waybill_box', 'shop_order', 'side', 'high' );
	}
}

add_action('woocommerce_order_status_changed','ppw_order_action_shipping');

function ppw_order_action_shipping($order_id) {
	global $wpdb;
	
	$order = wc_get_order( $order_id );
	$status = $order->status;

	if ($status == 'processing' && $order->has_shipping_method('ppw_shipping')) {
		$orderdetail = $wpdb->get_row('select service,ppw_suburb_name,ppw_suburb_postal_code,ppw_suburb_pcode from '.$wpdb->prefix.'ppw_order_tracking where `order` = '.$order_id);
		
		$waybill = 'W-'.get_option("ppw_abr").'-'.$order->get_order_number();
		$service = substr($orderdetail->service,0,3);
		$destplace = $orderdetail->ppw_suburb_pcode;
		$destname = $orderdetail->ppw_suburb_name;
		$destpostal = $orderdetail->ppw_suburb_postal_code;
		
		ppw_action_waybill($order_id,$waybill,$service,$destname,$destpostal,$destplace);
	}
}

function ppw_dispatched_email($order_id, $waybill) {
	$order = wc_get_order( $order_id );
	$status = $order->status;
	
	$to = $order->get_billing_email();
	$subject = get_bloginfo('name'). ' | Order #'.$order->get_order_number().' Dispatched';
	
	$message = '<table style="width: 100%;">
				  <tr>
					<td>
					  <center>
						<table style="width: 600px; margin: 0 auto; text-align: left;">
						  <tr>
							<td>
								<p style="margin-bottom: 50px; text-align: center;"><img alt="" src="'.get_option('ppw_tracking_email_logo').'" /></p>
								<p style="font-family: calibri; text-align: center; font-size: 14px;"><strong>Hello '.$order->get_shipping_first_name(). ' ' .$order->get_shipping_last_name().', your order has left our warehouse!</strong></p>
								<p style="margin-top: 30px; margin-bottom: 30px; font-family: calibri; text-align: center; font-size: 14px;">Your order has been dispatched and will be arriving within the next 1 - 3 working days. You can track your order by clicking on the tracking link below.</p>
								<p style="text-align: left; font-family: calibri; font-size: 14px;">Lynx Freight and Courier Services<br />
								 Waybill number:  '.$waybill.'<br />
								 <a href="'.get_option("ppw_tracking_link").$order->get_order_number().'" style="color: '.get_option("ppw_tracking_color").';">'.get_option("ppw_tracking_link").$waybill.'</a>
								</p>
								<p style="text-align: left; margin-top: 50px; margin-bottom: 50px; font-family: calibri; font-size: 14px;">If you have any questions or queries regarding your order please email <a href="mailto:'.get_option( "ppw_tracking_email" ).'" style="color: '.get_option("ppw_tracking_color").';">'.get_option( "ppw_tracking_email" ).'</a></p>
								<p style="font-family: calibri; margin-top: 30px; font-size: 10px; text-align: center; color: #ffffff; background-color: '.get_option("ppw_tracking_color").'; border-top: solid 5px '.get_option("ppw_tracking_color").'; border-bottom: solid 5px '.get_option("ppw_tracking_color").';">Copyright &copy; '.date("Y").' - <a href="http://'.get_site_url().'" style="color: #ffffff;">'.get_site_url().'</a></p>
							</td>
						  </tr>
						</table>
					  </center>
					</td>
				  </tr>
				</table>';
	
	// Always set content-type when sending HTML email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	// More headers
	$headers .= 'From: <'.get_option( "ppw_tracking_email" ).'>' . "\r\n";

	$sendmail = wp_mail($to,$subject,$message,$headers);
}

add_action("wp_ajax_ppw_get_places", "ppw_get_places");
add_action("wp_ajax_nopriv_ppw_get_places", "ppw_get_places");

function ppw_get_places() {
	$load = array(
		'PP_Url'		=>	get_option('ppw_e_pp_url'),
		'PP_User'		=>	get_option('ppw_e_pp_username'),
		'PP_Password'	=>	get_option('ppw_e_pp_password')
	);
	
	require_once(PPW_Plugin_Dir . '/Include/classes/ParcelPerfect.php');
	
	$ParcelPerfect = new ParcelPerfect(json_decode(json_encode($load)));
	$token = $ParcelPerfect->GenerateToken();
	
	if ($_REQUEST['searchType'] == 'name') {
		$query = array(
				'name' => sanitize_text_field($_REQUEST['searchTerm'])
			);
			
		$result = $ParcelPerfect->GetPlaceByName($query);
	}
	else {
		$query = array(
				'postcode' => sanitize_text_field($_REQUEST['searchTerm'])
			);
			
		$result = $ParcelPerfect->GetPlaceByCode($query);
	}
	
	$Content = '';
	
	if ($result->errorcode == 0) {
		foreach ($result->results as $suburb) {
			$Content .= '<div id="place_'.esc_attr($suburb->place).'" class="ppw_suburb">
							<p><strong>Name:</strong> <i><span class="ppw_suburb_name">'.ucwords($suburb->town).'</span></i><br />
							   <strong>Postal Code:</strong> <i><span class="ppw_suburb_pcode">'.$suburb->pcode.'</span></i></p>
						 </div>';
		}
	}
	else {
		$Content = '<span style="color: red;">Unfortunately an error has occured.  Please contact the plugin developer to investigate';
	}
	
	echo wp_kses_post($Content);
	wp_die();
}

add_action("wp_ajax_ppw_create_waybill", "ppw_create_waybill");
add_action("wp_ajax_nopriv_ppw_create_waybill", "ppw_create_waybill");

function ppw_create_waybill() {	
	$order_id = sanitize_text_field($_REQUEST['order']);
	$waybill = sanitize_text_field($_REQUEST['ppw_waybill_no']);
	$service = sanitize_text_field($_REQUEST['ppw_courier_service']);
	$destplace = sanitize_text_field($_REQUEST['ppw_dest_code']);
	$destname = sanitize_text_field($_REQUEST['ppw_destination_name']);
	$destpostal = sanitize_text_field($_REQUEST['ppw_destination_pcode']);
	
	ppw_action_waybill($order_id,$waybill,$service,$destname,$destpostal,$destplace);
	
	wp_die();
}

function ppw_action_waybill($order_id,$waybill,$service,$destname,$destpostal,$destplace) {
	$settings = get_option('woocommerce_ppw_shipping_settings',array());
	if ( $settings['enabled'] !== 'yes') {
		return;
	}
	
	$order = wc_get_order( $order_id );
	
	$status = $order->status;
	
	if (empty($waybill) || empty($service) || empty($destname) || empty($destpostal) || empty($destplace)) {
		return;
	}
		
	$load = array(
		'PP_Url'		=>	get_option('ppw_i_pp_url'),
		'PP_User'		=>	get_option('ppw_i_pp_username'),
		'PP_Password'	=>	get_option('ppw_i_pp_password')
	);
	
	require_once(PPW_Plugin_Dir . '/Include/classes/ParcelPerfect.php');
	
	$ParcelPerfect = new ParcelPerfect(json_decode(json_encode($load)));
	$token = $ParcelPerfect->GenerateToken();
	
	$query = array();
	$query['details'] = array();
	$query['details']['waybill'] = sanitize_text_field($waybill);
	$query['details']['service'] = sanitize_text_field($service);
	$query['details']['waydate'] = date('d.m.Y');
	$query['details']['origpers'] = get_bloginfo('name');
	$query['details']['origperadd1'] = get_option( 'woocommerce_store_address' );
	$query['details']['origperadd2'] = get_option( 'woocommerce_store_address_2' );
	$query['details']['origplace'] = get_option('ppw_origin_courier_code');
	$query['details']['origtown'] = get_option( 'woocommerce_store_city' );
	$query['details']['origperpcode'] = get_option( 'woocommerce_store_postcode' );
	$query['details']['origpercontact'] = get_bloginfo('name');
	$query['details']['origperemail'] = get_option( 'admin_email' );
	$query['details']['destpers'] = $order->get_shipping_first_name(). ' ' .$order->get_shipping_last_name();
	$query['details']['destperadd1'] = $order->get_shipping_address_1();
	$query['details']['destperadd2'] = $order->get_shipping_address_2();
	$query['details']['destplace'] = sanitize_text_field($destplace);
	$query['details']['destperpcode'] = $order->get_shipping_postcode();
	$query['details']['destpercontact'] = $order->get_shipping_first_name(). ' ' .$order->get_shipping_last_name();
	$query['details']['destperphone'] = $order->get_billing_phone();
	$query['details']['destpercell'] = $order->get_billing_phone();
	$query['details']['destperemail'] = $order->get_billing_email();
	$query['details']['notifydestpers'] = '1';
	$query['details']['isCollection'] = 1;
	$query['details']['specinstruction'] = 'None';
	$query['details']['reference'] = sanitize_text_field(str_replace("W","R",$waybill));
	$query['details']['insuranceflag'] = 0;
	$query['details']['declaredvalue'] = '0';
	$query['details']['pieces'] = '1';
	$query['ttype'] = 'I';
	$query['wayrefs'] = '';
	$query['printWaybill'] = 1;
	$query['printLabels'] = 1;
	
	$items = $order->get_items();
	$ppw_cart = ppw_get_cart($items);
	
	$query['contents'] = $ppw_cart;
			
	$result = $ParcelPerfect->SubmitWaybill($query);
	
	$Status = 'Success';
	$error_log_id = null;
	
	global $wpdb;
	
	if ($result->errorcode != 0) {
		$Status = 'Error';
		
		$log_error = array(
			'order'				=>	sanitize_text_field($order_id),
			'created_date_time'	=>	date('Y-m-d H:i:s'),
			'error'				=>	json_encode($result)
		);
		
		$wpdb->insert($wpdb->prefix.'ppw_error_log',$log_error);
		
		$error_log_id = $wpdb->insert_id;
	}
	
	$args = array(
		'order'				=>	sanitize_text_field($order_id),
		'created_date_time'	=>	date('Y-m-d H:i:s'),
		'waybill'			=>	sanitize_text_field($waybill),
		'reference'			=>	sanitize_text_field($waybill),
		'destination_name'	=>	sanitize_text_field($destname),
		'destination_pcode'	=>	sanitize_text_field($destpostal),
		'destination_place'	=>	sanitize_text_field($destplace),
		'service'			=>	sanitize_text_field($service),
		'status'			=>	$Status,
		'error_log'			=>	$error_log_id
	);
	
	$wpdb->insert($wpdb->prefix.'ppw_waybills',$args);
	
	if ($result->errorcode == 0) {
		$PDF_Waybill_Decoded = base64_decode( $result->results[0]->waybillBase64 );
		$PDF_Waybill_Decoded = base64_decode( $PDF_Waybill_Decoded );
		$PDF_Label_Decoded = base64_decode( $result->results[0]->labelsBase64 );
		$PDF_Label_Decoded = base64_decode( $PDF_Label_Decoded );

		$waybill_filename = sanitize_text_field($waybill).'_Waybill';
		$label_filename = sanitize_text_field($waybill).'_Label';
		$waybill_file = PPW_Plugin_Dir . '/waybills/'.$waybill_filename.'.pdf';
		$label_file = PPW_Plugin_Dir . '/waybills/'.$label_filename.'.pdf';

		file_put_contents($waybill_file, $PDF_Waybill_Decoded) or print_r(error_get_last());
		file_put_contents($label_file, $PDF_Label_Decoded) or print_r(error_get_last());
		
		$order->add_order_note(sanitize_text_field($waybill).'_Waybill.pdf successfully created.');
		$order->save();
		
		$order->add_order_note(sanitize_text_field($waybill).'_Label.pdf successfully created.');
		$order->save();
		
		ppw_dispatched_email($order_id,$waybill);
	}
}

function ppw_create_waybill_box() {
	global $post,$wpdb; 
	
	$order = wc_get_order( $post->ID );
	$order_number = $order->get_order_number();
	
	$items = $order->get_items();
	$ppw_cart = ppw_get_cart($items);
	
	$existing_waybills = $wpdb->get_results('SELECT `waybill` FROM `'.$wpdb->prefix.'ppw_waybills` WHERE `order` = "'.$post->ID.'" AND `status` = "Success"');
	
	$waybill_string = '';
	
	foreach ($existing_waybills as $w) {
		$waybill_string .= $w->waybill.',';
	}
	
	$destination_name = '';
	$destination_postal_code = '';
	$destination_pcode = '';
	
	$result = $wpdb->get_row("select * from `".$wpdb->prefix."ppw_order_tracking` where `order` = ".$order->get_id());
		
	if (!empty($result)) {
		$service = substr($result->service,0,3);
	
		$destination_name = $result->ppw_suburb_name;
		$destination_postal_code = $result->ppw_suburb_postal_code;
		$destination_pcode = $result->ppw_suburb_pcode;
	}

	?>
	
	<div id="ppw_create_waybill">
		<div id="ppw_loader" style="display: none;"><div class="loader-icon"></div></div>
		<div id="ppw_create_waybill_details">
			<table cellpadding="0" cellspacing="0" style="width: 100%;">
				<tr>
					<td>
						<h4>Shipping From:</h4>
						<p style="font-size: 10px;">
							<strong><?php echo get_bloginfo('name'); ?></strong><br />
							<?php echo esc_html(get_option( 'woocommerce_store_address' )); ?><br />
							<?php echo esc_html(get_option( 'woocommerce_store_address_2' )); ?><br />
							<?php echo esc_html(get_option( 'woocommerce_store_city' )); ?><br />
							<?php echo esc_html(get_option( 'woocommerce_store_postcode' )); ?><br /><br />
							<?php echo esc_html(get_option( 'admin_email' )); ?><br />
							<strong>Courier Code:</strong> <?php echo esc_html(get_option('ppw_origin_courier_code')); ?>
						</p>
					</td>
					<td>
						<h4>Shipping To:</h4>
						<p style="font-size: 10px;">
							<strong><?php echo esc_html($order->get_shipping_first_name()). ' ' .esc_html($order->get_shipping_last_name()); ?></strong><br />
							<?php echo esc_html($order->get_shipping_address_1()); ?><br />
							<?php echo esc_html($order->get_shipping_address_2()); ?><br />
							<span class="ppw_address_suburb_name"><?php echo $destination_name; ?></span><br />
							<?php echo esc_html($order->get_shipping_postcode()); ?><br /><br />
							<?php echo esc_html($order->get_billing_email()); ?><br />
							<?php echo esc_html($order->get_billing_phone()); ?><br /><br />
						</p>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<h4>Shipping Contents:</h4>
						<table id="ppw_shipping_contents" cellpadding="0" cellspacing="0" style="width: 100%;">
							<thead>
								<th>Label</th>
								<th>Dimensions</th>
								<th>Amount</th>
								<th>Weight</th>
							</thead>
							<tbody>
							<?php
								foreach ($ppw_cart as $i) {
									echo '<tr>
											<td>'.$i["description"].'</td>
											<td>'.$i["dim1"].' x '.$i["dim2"].' x '.$i["dim3"].'</td>
											<td>'.$i["pieces"].'</td>
											<td>'.$i["actmass"].'kg</td>
										  </tr>';
								}
							?>
							</tbody>
						</table>
					</td>
				</tr>
			</table>
			<p>
				<h4>Courier Service:</h4>
				<?php
					$services = array(
									'IPP'	=>	'International PP',
									'ONX'	=>	'Overnight Air',
									'ECO'	=>	'Economy Road',
									'BUD'	=>	'Budget/Economy',
									'EXR'	=>	'Express Road',
									'INC'	=>	'Incity Express',
									'SDX'	=>	'Same day Express',
									'DWN'	=>	'Domestic Wine',
									'RDF'	=>	'Economy Domestic Road Freight',
								);
				?>
				<select id="ppw_courier_service" name="ppw_courier_service">
					<option value="">Select Courier Service</option>
					<?php foreach ($services as $k=>$s) : ?>
						<option value="<?php echo $k; ?>" <?php echo (!empty($service) && $service  == $k) ? 'selected="selected"' : ''; ?>><?php echo $s; ?></option>
					<?php endforeach; ?>
				</select>
				<h4>Select Destination Courier Code:</h4>
				<input id="ppw_dest_courier" type="text" name="ppw_dest_courier" ppw-referrer="ppw_dest_" />
				<p><strong>Destination Name:</strong> <span class="ppw_dest_courier_name"><?php echo $destination_name; ?></span><br /></p>
				<p><strong>Destination Postal Code:</strong> <span class="ppw_dest_courier_postal_code"><?php echo $destination_postal_code; ?></span></p>
				<p><strong>Destination Courier Code:</strong> <span class="ppw_dest_courier_code"><?php echo $destination_pcode; ?></span></p>
				<h4>Waybill No:</h4>
				<p><input id="ppw_waybill_number" type="text" name="ppw_waybill_number" value="<?php echo 'W-'.esc_attr(get_option("ppw_abr")).'-'.esc_attr($order_number);?>" placeholder="Waybill No" />
			</p>
			<p>
				<input id="ppw_existing_waybills" name="ppw_existing_waybills" type="hidden" value="<?php echo esc_attr($waybill_string); ?>" />
				<input id="ppw_destination_name" name="destination_name" type="hidden" value="<?php echo esc_attr($destination_name); ?>" />
				<input id="ppw_destination_pcode" name="destination_pcode" type="hidden" value="<?php echo esc_attr($destination_pcode); ?>" />
				<a class="button-primary ppw_create_waybill_from_details" href="#" ppw-order="<?php echo esc_attr($post->ID); ?>">Create Waybill</a>
			</p>
		</div>
	</div>
	
	<?php
}

function ppw_waybill_history_box() {
	global $post,$wpdb;
	
	$Waybills = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."ppw_waybills` WHERE `order` = ".$post->ID);
	
	echo '<div id="ppw">';
	
	if (count($Waybills) > 0) {
		foreach ($Waybills as $w) {
			$status = 'alert-danger';
			$action = '<a href="'.get_admin_url().'admin.php?page=ppw_error_logs&order='.$w->order.'" class="button-primary" target="_blank">Error Log</a>';

			if ($w->status == 'Success') {
				$status = 'alert-success';
				$action = '<a href="'.PPW_Plugin_URL.'/waybills/'.$w->waybill.'_Waybill.pdf" class="button-primary" target="_blank">Waybill</a>
						   <a href="'.PPW_Plugin_URL.'/waybills/'.$w->waybill.'_Label.pdf" class="button-primary" target="_blank">Label</a>';
			}

			echo '<div id="PPW_Waybill_'.esc_attr($post->ID).'">
					<p class="ppw_waybill_item '.$status.'">
						'.$w->waybill.'
						'.$action.'
					</p>
					<p class="ppw_waybill_date_print">
						'.$w->created_date_time.'
						<a href="#" class="ppw_print" ppw-waybill="'.esc_attr($w->waybill).'">Print '.$w->waybill.'</a>
						<IFRAME id="ppw_'.$w->waybill.'_waybill_iframe" width="1" height="1" src= scrolling="no" frameborder="0"></IFRAME>
						<IFRAME id="ppw_'.$w->waybill.'_label_iframe" width="1" height="1" src= scrolling="no" frameborder="0"></IFRAME>
					</p>
				  </div>';
		}
	}
	else {
		echo '<p style="font-style: italic;">No waybills have been generated for this order.</p>';
	}
	
	echo '</div>';
}

add_action( 'wp_ajax_ppw_error_log_filter', 'ppw_error_log_filter' );
add_action( 'wp_ajax_nopriv_ppw_error_log_filter', 'ppw_error_log_filter' );

function ppw_error_log_filter() {
	global $wpdb;
	
	$Logs = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."ppw_error_log WHERE created_date_time BETWEEN %s AND %s",sanitize_text_field($_REQUEST['log_start_date']),sanitize_text_field($_REQUEST['log_end_date'])));

	$Content = '<table id="ppw_datatable" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th>Date</th>
					<th>Order #</th>
					<th>Error</th>
				</tr>
			</thead>
			<tbody>';
			
	foreach ($Logs as $l) {
		$Content .= '<tr>
								<td>'.esc_html(substr($l->created_date_time,0,10)).'</td>
								<td>'.esc_html($l->order).'</td>
								<td>'.esc_html($l->error).'</td>
							  </tr>';
	}
		
	$Content .= '</tbody></table>';
	
	echo wp_kses_post($Content);
	
	die();
}

add_action("wp_ajax_ppw_save_shipping_package", "ppw_save_shipping_package");
add_action("wp_ajax_nopriv_ppw_save_shipping_package", "ppw_save_shipping_package");

function ppw_save_shipping_package() {
	global $wpdb;
	
	$args = array(
		'created_date_time'		=>	date('Y-m-d H:i:s'),
		'updated_date_time'		=>	date('Y-m-d H:i:s'),
		'shipping_class'		=>	sanitize_text_field($_REQUEST['shipping_class']),
		'shipping_class_name'	=>	sanitize_text_field($_REQUEST['shipping_class_name']),
		'label'					=>	sanitize_text_field(implode(';',$_REQUEST['label'])),
		'no_items'				=>	sanitize_text_field($_REQUEST['no_items']),
		'height'				=>	sanitize_text_field(implode(';',$_REQUEST['height'])),
		'width'					=>	sanitize_text_field(implode(';',$_REQUEST['width'])),
		'length'				=>	sanitize_text_field(implode(';',$_REQUEST['length'])),
		'weight'				=>	sanitize_text_field(implode(';',$_REQUEST['weight'])),
		'ignore'				=>	sanitize_text_field($_REQUEST['shipping_class_waybill'])
	);
	
	$wpdb->insert($wpdb->prefix.'ppw_shipping_packages',$args);
	
	wp_die();
}

add_action("wp_ajax_ppw_update_shipping_package", "ppw_update_shipping_package");
add_action("wp_ajax_nopriv_ppw_update_shipping_package", "ppw_update_shipping_package");

function ppw_update_shipping_package() {
	global $wpdb;
	
	$args = array(
		'updated_date_time'		=>	date('Y-m-d H:i:s'),
		'shipping_class'		=>	sanitize_text_field($_REQUEST['shipping_class']),
		'shipping_class_name'	=>	sanitize_text_field($_REQUEST['shipping_class_name']),
		'label'					=>	sanitize_text_field(implode(';',$_REQUEST['label'])),
		'no_items'				=>	sanitize_text_field($_REQUEST['no_items']),
		'height'				=>	sanitize_text_field(implode(';',$_REQUEST['height'])),
		'width'					=>	sanitize_text_field(implode(';',$_REQUEST['width'])),
		'length'				=>	sanitize_text_field(implode(';',$_REQUEST['length'])),
		'weight'				=>	sanitize_text_field(implode(';',$_REQUEST['weight'])),
		'ignore'				=>	sanitize_text_field($_REQUEST['shipping_class_waybill'])
	);
	
	$wpdb->update($wpdb->prefix.'ppw_shipping_packages',$args,array('id'=>sanitize_text_field($_REQUEST['package_id'])));
	
	wp_die();
}

add_action("wp_ajax_ppw_delete_shipping_package", "ppw_delete_shipping_package");
add_action("wp_ajax_nopriv_ppw_delete_shipping_package", "ppw_delete_shipping_package");

function ppw_delete_shipping_package() {
	global $wpdb;
	
	$wpdb->delete($wpdb->prefix.'ppw_shipping_packages',array('id'=>sanitize_text_field($_REQUEST['package_id'])));
	
	wp_die();
}

add_action("wp_ajax_ppw_get_dim_breakdown", "ppw_get_dim_breakdown");
add_action("wp_ajax_nopriv_ppw_get_dim_breakdown", "ppw_get_dim_breakdown");

function ppw_get_dim_breakdown() {
	global $wpdb;
	
	$amount = sanitize_text_field($_REQUEST['amount']);
	$i = 1;
	
	$Content = '<table id="ppw_package_breakdown" cellpadding="0" cellspacing="0">';
	
	for ($i; $i <= $amount; $i++) {
		$Content .= '<tr>
						<td><strong>Package with '.$i.' items:</strong></td>
						<td>Label:</td>
						<td><input type="text" name="ppw_breakdown_label[]" value="" /></td>
						<td>Width (cm):</td>
						<td><input type="number" name="ppw_breakdown_width[]" value="" min="1" max="1000" /></td>
						<td>Length (cm):</td>
						<td><input type="number" name="ppw_breakdown_length[]" value="" min="1" max="1000" /></td>
						<td>Height (cm):</td>
						<td><input type="number" name="ppw_breakdown_height[]" value="" min="1" max="1000" /></td>
						<td>Weight (kg):</td>
						<td><input type="number" name="ppw_breakdown_weight[]" value="" min="1" max="50" /></td>
					</tr>';
	}
	
	$Content .= '</table>';
	
	echo $Content;
	
	wp_die();
}

function ppw_get_shipping_classes() {
	global $wpdb;
	$packages = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'ppw_shipping_packages');
	$shipping_packages = array();

	if (!empty($packages)) {
		foreach ($packages as $p) {
			$p_array = array(
				'shipping_class'		=>	$p->shipping_class,
				'shipping_class_name'	=>	$p->shipping_class_name,
				'no_items'				=>	$p->no_items,
				'ignore'				=>	$p->ignore,
				'dimensions'			=>	array()
			);
			
			$label = explode(';',$p->label);
			$length = explode(';',$p->length);
			$height = explode(';',$p->height);
			$width = explode(';',$p->width);
			$weight = explode(';',$p->weight);
					
			$loop = (int)$p->no_items;
			$i = 0;
			
			for ($i;$i < $loop; $i++) {
				$package_dimensions = array(
					'label'		=>	$label[$i],
					'length'	=>	$length[$i],
					'height'	=>	$height[$i],
					'width'		=>	$width[$i],
					'weight'	=>	$weight[$i]
				);
				
				$p_array['dimensions'][$i+1] = $package_dimensions;
			}
			
			$shipping_packages[$p->shipping_class] = $p_array;
		}
	}
	
	return $shipping_packages;
}

function ppw_get_cart($items) {
	$shipping_classes = ppw_get_shipping_classes();
	$default_no_class = 'default-no-shipping-class';
	$cart_breakdown = array();
	$ppw_cart = array();
	$Item_no = 1;

	foreach ($items as $item) {
		$product = wc_get_product($item['product_id']);
		$sc = $product->get_shipping_class();

		if (empty($item['bundled_by'])) {
			if (!empty($sc)) {
				if (!$shipping_classes[$sc]['ignore']) {
					if (!empty($shipping_classes[$sc])) {
						if (!empty($cart_breakdown[$sc])) {
							$cart_breakdown[$sc] = $cart_breakdown[$sc] + $item['quantity'];
						}
						else {
							$cart_breakdown[$sc] = $item['quantity'];
						}
					}
					else {
						if (!empty($cart_breakdown[$default_no_class])) {
							$cart_breakdown[$default_no_class] = $cart_breakdown[$default_no_class] + $item['quantity'];
						}
						else {
							$cart_breakdown[$default_no_class] = $item['quantity'];
						}
					}
				}
			}
			else {
				if (!$shipping_classes[$default_no_class]['ignore']) {
					if (!empty($shipping_classes[$default_no_class])) {
						if (!empty($cart_breakdown[$default_no_class])) {
							$cart_breakdown[$default_no_class] = $cart_breakdown[$default_no_class] + $item['quantity'];
						}
						else {
							$cart_breakdown[$default_no_class] = $item['quantity'];
						}
					}
					else {
						$Item_weight = $product->get_weight();

						if (empty($Item_weight) || $Item_weight == 0) {
							$Item_weight = 1;
						}

						$Item_length = $product->get_length();

						if (empty($Item_length) || $Item_length == 0) {
							$Item_length = 1;
						}

						$Item_width = $product->get_width();

						if (empty($Item_width) || $Item_width == 0) {
							$Item_width = 1;
						}

						$Item_height = $product->get_height();

						if (empty($Item_height) || $Item_height == 0) {
							$Item_height = 1;
						}

						$ActMass = $item['quantity'] * $Item_weight;

						$add_item = array(
							'item'			=>	$Item_no,
							'description'	=>	get_the_title($item['product_id']),
							'pieces'		=>	$item['quantity'],
							'dim1'			=>	$Item_length,
							'dim2'			=>	$Item_width,
							'dim3'			=>	$Item_height,
							'actmass'		=>	$ActMass
						);

						$Item_no++;
						$ppw_cart[] = $add_item;
					}
				}
			}
		}
	}

	foreach ($cart_breakdown as $key=>$quantity) {
		$max_items = $shipping_classes[$key]['no_items'];

		if ($quantity > $max_items) {
			$max_packages = $quantity / $max_items;
			$max_packages = floor($max_packages);

			$ActMass = $max_packages * $shipping_classes[$key]['dimensions'][$max_items]['weight'];

			$add_item = array(
				'item'			=>	$Item_no,
				'description'	=>	$shipping_classes[$key]['dimensions'][$max_items]['label'],
				'pieces'		=>	$max_packages,
				'dim1'			=>	$shipping_classes[$key]['dimensions'][$max_items]['length'],
				'dim2'			=>	$shipping_classes[$key]['dimensions'][$max_items]['height'],
				'dim3'			=>	$shipping_classes[$key]['dimensions'][$max_items]['width'],
				'actmass'		=>	$ActMass
			);

			$ppw_cart[] = $add_item;
			$Item_no++;

			$left_over = $quantity - ( $max_packages * $max_items );

			if ($left_over > 0) {
				$add_item = array(
					'item'			=>	$Item_no,
					'description'	=>	$shipping_classes[$key]['dimensions'][$left_over]['label'],
					'pieces'		=>	1,
					'dim1'			=>	$shipping_classes[$key]['dimensions'][$left_over]['length'],
					'dim2'			=>	$shipping_classes[$key]['dimensions'][$left_over]['height'],
					'dim3'			=>	$shipping_classes[$key]['dimensions'][$left_over]['width'],
					'actmass'		=>	$shipping_classes[$key]['dimensions'][$left_over]['weight']
				);

				$ppw_cart[] = $add_item;
				$Item_no++;
			}
		}
		else {
			$add_item = array(
				'item'			=>	$Item_no,
				'description'	=>	$shipping_classes[$key]['dimensions'][$quantity]['label'],
				'pieces'		=>	1,
				'dim1'			=>	$shipping_classes[$key]['dimensions'][$quantity]['length'],
				'dim2'			=>	$shipping_classes[$key]['dimensions'][$quantity]['height'],
				'dim3'			=>	$shipping_classes[$key]['dimensions'][$quantity]['width'],
				'actmass'		=>	$shipping_classes[$key]['dimensions'][$quantity]['weight']
			);

			$ppw_cart[] = $add_item;
			$Item_no++;
		}
	}
	
	return $ppw_cart;
}
<?php
function ppw_options() {
	if (isset($_POST['submit']) && !empty($_POST['submit'])) {
		update_option('ppw_e_pp_url',sanitize_url($_POST['ppw_e_pp_url']));
		update_option('ppw_e_pp_username',sanitize_text_field($_POST['ppw_e_pp_username']));
		update_option('ppw_e_pp_password',sanitize_text_field($_POST['ppw_e_pp_password']));
		update_option('ppw_i_pp_url',sanitize_url($_POST['ppw_i_pp_url']));
		update_option('ppw_i_pp_username',sanitize_text_field($_POST['ppw_i_pp_username']));
		update_option('ppw_i_pp_password',sanitize_text_field($_POST['ppw_i_pp_password']));
		update_option('ppw_origin_name',sanitize_text_field($_POST['ppw_origin_name']));
		update_option('ppw_origin_postalcode',sanitize_text_field($_POST['ppw_origin_postalcode']));
		update_option('ppw_origin_courier_code',sanitize_text_field($_POST['ppw_origin_courier_code']));
		update_option('ppw_abr',sanitize_text_field($_POST['ppw_abr']));
		update_option('ppw_tracking_email',sanitize_text_field($_POST['ppw_tracking_email']));
		update_option('ppw_tracking_email_logo',sanitize_text_field($_POST['ppw_tracking_email_logo']));
		update_option('ppw_tracking_link',sanitize_text_field($_POST['ppw_tracking_link']));
		update_option('ppw_tracking_color',sanitize_text_field($_POST['ppw_tracking_color']));
	}
	?>
	
	<div id="ppw">
		<?php if (isset($_POST['submit']) && !empty($_POST['submit'])) : ?>
			<div class="alert alert-success">Your Parcel Perfect Waybill settings was successfully saved.</div>
		<?php endif; ?>
		<div class="adminBlock">
			<h3>Parcel Perfect Waybill Options</h3>
			<form id="ppw_options" name="ppw_options" method="post" action="admin.php?page=ppw_admin">
				<h4>Parcel Perfect EcomService: </h4>
				<table id="PPW_EcomService_Table" style="width: 100%;">
					<tr>
						<td style="width: 300px;">Parcel Perfect EcomService API Url</td>
						<td><input id="ppw_e_pp_url" type="text" name="ppw_e_pp_url" value="<?php echo esc_attr(get_option('ppw_e_pp_url')); ?>" /></td>
					</tr>
					<tr>
						<td>Parcel Perfect EcomService API Username</td>
						<td><input id="ppw_e_pp_username" type="text" name="ppw_e_pp_username" value="<?php echo esc_attr(get_option('ppw_e_pp_username')); ?>" /></td>
					</tr>
					<tr>
						<td>Parcel Perfect EcomService API Password</td>
						<td><input id="ppw_e_pp_password" type="text" name="ppw_e_pp_password" value="<?php echo esc_attr(get_option('ppw_e_pp_password')); ?>" /></td>
					</tr>
				</table>
				<h4>Parcel Perfect IntegrationService: </h4>
				<table id="PPW_IntegrationService_Table" style="width: 100%;">
					<tr>
						<td style="width: 300px;">Parcel Perfect IntegrationService API Url</td>
						<td><input id="ppw_i_pp_url" type="text" name="ppw_i_pp_url" value="<?php echo esc_attr(get_option('ppw_i_pp_url')); ?>" /></td>
					</tr>
					<tr>
						<td>Parcel Perfect IntegrationService API Username</td>
						<td><input id="ppw_i_pp_username" type="text" name="ppw_i_pp_username" value="<?php echo esc_attr(get_option('ppw_i_pp_username')); ?>" /></td>
					</tr>
					<tr>
						<td>Parcel Perfect IntegrationService API Password</td>
						<td><input id="ppw_i_pp_password" type="text" name="ppw_i_pp_password" value="<?php echo esc_attr(get_option('ppw_i_pp_password')); ?>" /></td>
					</tr>
				</table>
				<h4>Parcel Perfect Miscellaneous: </h4>
				<table id="PPW_Miscellaneous_Table" style="width: 100%;">
					<tr>
						<td style="width: 300px;">Selectc Origin Place</td>
						<td><input id="ppw_origin_selector" type="text" name="ppw_origin_selector" value=""  /></td>
					</tr>
					<tr>
						<td style="width: 300px;">Origin Suburb Name</td>
						<td><input id="ppw_origin_name" type="text" name="ppw_origin_name" value="<?php echo esc_attr(get_option('ppw_origin_name')); ?>"  ppw-referrer="ppw_origin_" readonly /></td>
					</tr>
					<tr>
						<td style="width: 300px;">Origin Suburb Postal Code</td>
						<td><input id="ppw_origin_postalcode" type="text" name="ppw_origin_postalcode" value="<?php echo esc_attr(get_option('ppw_origin_postalcode')); ?>"  ppw-referrer="ppw_origin_" readonly /></td>
					</tr>
					<tr>
						<td style="width: 300px;">Origin Suburb Courier Code</td>
						<td><input id="ppw_origin_courier_code" type="text" name="ppw_origin_courier_code" value="<?php echo esc_attr(get_option('ppw_origin_courier_code')); ?>"  ppw-referrer="ppw_origin_" readonly /></td>
					</tr>
					<tr>
						<td style="width: 300px;">Parcel Perfect Reference Abbreviation</td>
						<td><input id="ppw_abr" type="text" name="ppw_abr" value="<?php echo esc_attr(get_option('ppw_abr')); ?>" /></td>
					</tr>
				</table>
				<h4>Parcel Perfect Tracking E-mail: </h4>
				<table id="PPW_Tracking_Table" style="width: 100%;">
					<tr>
						<td style="width: 300px;">Enter Reply E-mail Address</td>
						<td><input id="ppw_tracking_email" type="text" name="ppw_tracking_email"  value="<?php echo esc_attr(get_option('ppw_tracking_email')); ?>"  /></td>
					</tr>
					<tr>
						<td style="width: 300px;">Select Logo image</td>
						<td>
							<input id="ppw_tracking_email_logo" type="text" name="ppw_tracking_email_logo" value="<?php echo esc_attr(get_option('ppw_tracking_email_logo')); ?>"  />
							<div style="clear: both;"></div>
							<a href="#" class="button-primary ppw_tracking_upload_image" style="margin-left: 5px;" data-type="add">Select Image</a>
						</td>
					</tr>
					<tr>
						<td style="width: 300px;"></td>
						<td>
							<img id="ppw_tracking_email_logo_preview" alt="" src="<?php echo esc_attr(get_option('ppw_tracking_email_logo')); ?>" style="width: 75px;" />
						</td>
					</tr>
					<tr>
						<td style="width: 300px;">Enter Links & Footer Color Code</td>
						<td><input id="ppw_tracking_color" type="text" name="ppw_tracking_color" value="<?php echo esc_attr(get_option('ppw_tracking_color')); ?>"  /></td>
					</tr>
					<tr>
						<td style="width: 300px;">Enter Tracking Link:</td>
						<td><input id="ppw_tracking_link" type="text" name="ppw_tracking_link" value="<?php echo esc_attr(get_option('ppw_tracking_link')); ?>"  /></td>
					</tr>
				</table>
				<p><input type="submit" name="submit" value="Update" class="button-primary" />
			</form>
		</div>
	</div>
	
	<?php
}

function ppw_shipping_packages() {
	global $wpdb;
	
	$packages = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'ppw_shipping_packages ORDER BY created_date_time DESC');
	
	$row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '".$wpdb->prefix."ppw_shipping_packages' AND column_name = 'ignore'");
	
	?>
	
	<div id="ppw">
		<p><a class="button-primary" href="<?php echo get_admin_url(); ?>admin.php?page=ppw_shipping_packages_add" style="margin-left:-5px;">Add Package</a></p>
		<div class="adminBlock">
		<h2>Parcel Perfect Waybills Shipping Setup</h2>
			<table cellpadding="0" cellspacing="0" style="width: 100%;">
			<tr>
					<td>ID</td>
					<td><strong>Created Date</strong></td>
					<td style="padding-left: 10px;"><strong>Shipping Class</strong></td>
					<td><strong>Max no Items per package</strong></td>
					<td><strong>Include in Waybills</strong></td>
					<td></td>
					<td></td>
				</tr>
			<?php
				foreach ($packages as $p):
				
				?>
				<tr>
					<td style="padding: 10px 5px;"><?php echo esc_html($p->id); ?></td>
					<td style="padding: 10px 5px;"><?php echo esc_html($p->created_date_time); ?></td>
					<td style="padding: 10px 5px;"><?php echo esc_html($p->shipping_class); ?></td>
					<td style="padding: 10px 5px;"><?php echo esc_html($p->no_items); ?></td>
					<td style="padding: 10px 5px;"><?php echo ($p->ignore ? 'No' : 'Yes') ?></td>
					<td><a class="button"  href="admin.php?page=ppw_shipping_packages_edit&id=<?php echo esc_attr($p->id); ?>">Edit Package</a></td>				
					<td><a class="button"  href="admin.php?page=ppw_shipping_packages_delete&id=<?php echo esc_attr($p->id); ?>">Delete Package</a></td>				
				</tr>
			<?php endforeach; ?>
			</table>
		</div>
	</div>
	<?php
}

function ppw_shipping_packages_add() {
	global $wpdb;
	
	$shipping_classes = get_terms( array('taxonomy' => 'product_shipping_class', 'hide_empty' => false ) );	
	
	$default = new stdClass();
	$default->slug = 'default-no-shipping-class';
	$default->name = 'Default - No shipping class';
	
	$shipping_classes[] = $default;
	?>
	
	<div id="ppw">
		<p><a class="button-primary" href="<?php echo get_admin_url(); ?>admin.php?page=ppw_shipping_packages" style="margin-left:-5px;">Go Back</a></p>
		<div class="adminBlock">
			<h2>Add Shipping Package</h2>
			
			<div id="ppw_loader" style="display: none;"><div class="loader-icon"></div></div>
			<div id="FormContent">
				<form action="admin.php?page=ppw_shipping_packages" method="post" onsubmit="return ppw_add_shipping_package();">
					<p><i>Please select a shipping package to use for this shipping box specification:</i></p>
					<select id="ppw_pack_shipping_class" name="ppw_pack_shipping_class" required>
						<option value=""></option>
						<?php
							foreach ($shipping_classes as $sc) {
								$exist = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'ppw_shipping_packages WHERE shipping_class = "'.sanitize_text_field($sc->slug).'"');
								
								if (empty($exist)) {
									echo '<option value="'.esc_attr($sc->slug).'">'.$sc->name.'</option>';
								}
							}
						?>
					</select>
					<div style="clear: both;"></div>
					<p><i>Please select the maximum amount of items per package:</i></p>
					<select id="ppw_pack_no_items" name="ppw_pack_no_items" required>
						<option value=""></option>
						<?php
							$i = 1;
							
							for ($i; $i <= 50; $i++) {
								echo '<option value="'.esc_attr($i).'">'.esc_html($i).'</option>';
							}
						?>
					</select>
					<div style="clear: both;"></div>
					<p><i>Please indicate whether this shipping class should be ignored from waybills:</i></p>
					<select id="ppw_pack_ignore" name="ppw_pack_ignore" required>
						<option value="0">Include in waybills</option>
						<option value="1">Exclude from waybills</option>
					</select>
					<div style="clear: both;"></div>
					<div id="ppw_package_dim_breakdown" style="display: none;">
						<h3>Package dimensions breakdown</h3>
						<div>
						
						</div>
					</div>
					<input type="submit" name="submit" value="Save Shipping Package" class="button-primary" style="margin: 25px 0px 0px 0px;" />
				</form>
			</div>
			<div id="AjaxMessages" style="display: none;">
			
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
	
	<?php	
}

function ppw_shipping_packages_edit() {
	global $wpdb;
	
	$shipping_classes = get_terms( array('taxonomy' => 'product_shipping_class', 'hide_empty' => false ) );
	$p = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'ppw_shipping_packages WHERE id = %s',sanitize_text_field($_GET["id"])));
	
	$default = new stdClass();
	$default->slug = 'default-no-shipping-class';
	$default->name = 'Default - No shipping class';
	
	$shipping_classes[] = $default;
	
	$pp_pack_include = 'selected="selected"';
	$pp_pack_exclude = '';
	
	if ($p->ignore) {
		$pp_pack_include = '';
		$pp_pack_exclude = 'selected="selected"';
	}

	?>
	
	<div id="ppw">
		<p><a class="button-primary" href="<?php echo get_admin_url(); ?>admin.php?page=ppw_shipping_packages" style="margin-left:-5px;">Go Back</a></p>
		<div class="adminBlock">
			<h2>Edit Shipping Package</h2>
			
			<div id="ppw_loader" style="display: none;"><div class="loader-icon"></div></div>
			<div id="FormContent">
				<form action="admin.php?page=ppw_shipping_packages" method="post" onsubmit="return ppw_update_shipping_package();">
					<p><i>Please select a shipping package to use for this shipping box specification:</i></p>
					<select id="ppw_pack_shipping_class" name="ppw_pack_shipping_class" required>
						<option value=""></option>
						<option value="<?php echo esc_html($p->shipping_class); ?>" selected="selected"><?php echo $p->shipping_class_name; ?></option>
						<?php
							foreach ($shipping_classes as $sc) {
								$exist = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'ppw_shipping_packages WHERE shipping_class = "'.sanitize_text_field($sc->slug).'"');
								
								if (empty($exist)) {
									echo '<option value="'.esc_attr($sc->slug).'">'.esc_html($sc->name).'</option>';
								}
							}
						?>
					</select>
					<div style="clear: both;"></div>
					<p><i>Please select the maximum amount of items per package:</i></p>
					<select id="ppw_pack_no_items" name="ppw_pack_no_items" required>
						<?php
							$i = 1;
							
							for ($i; $i <= 50; $i++) {
								if ($i == $p->no_items) {
									$selected = 'selected="selected"';
								}
								else {
									$selected = '';
								}
								
								echo '<option value="'.esc_attr($i).'" '.$selected.'>'.esc_html($i).'</option>';
							}
						?>
					</select>
					<div style="clear: both;"></div>
					<p><i>Please indicate whether this shipping class should be ignored from waybills:</i></p>
					<select id="ppw_pack_ignore" name="ppw_pack_ignore" required>
						<option value="0" <?php echo esc_attr($pp_pack_include); ?>>Include in waybills</option>
						<option value="1" <?php echo esc_attr($pp_pack_exclude); ?>>Exclude from waybills</option>
					</select>
					<div style="clear: both;"></div>
					<div id="ppw_package_dim_breakdown">
						<h3>Package dimensions breakdown</h3>
						<div>
							<table id="ppw_package_breakdown" cellpadding="0" cellspacing="0">
								<?php
									$amount = $p->no_items;
									$i = 0;
									
									$label = explode(';',$p->label);
									$width = explode(';',$p->width);
									$length = explode(';',$p->length);
									$height = explode(';',$p->height);
									$weight = explode(';',$p->weight);
									
									echo '<table id="ppw_package_breakdown" cellpadding="0" cellspacing="0">';
									
									for ($i; $i < $amount; $i++) {
										echo '<tr>
														<td>Label:</td>
														<td><input type="text" name="ppw_breakdown_label[]" value="'.esc_attr($label[$i]).'" /></td>
														<td>Width (cm):</td>
														<td><input type="number" name="ppw_breakdown_width[]" value="'.esc_attr($width[$i]).'" min="1" max="1000" /></td>
														<td>Length (cm):</td>
														<td><input type="number" name="ppw_breakdown_length[]" value="'.esc_attr($length[$i]).'" min="1" max="1000" /></td>
														<td>Height (cm):</td>
														<td><input type="number" name="ppw_breakdown_height[]" value="'.esc_attr($height[$i]).'" min="1" max="1000" /></td>
														<td>Weight (kg):</td>
														<td><input type="number" name="ppw_breakdown_weight[]" value="'.esc_attr($weight[$i]).'" min="1" max="50" /></td>
													</tr>';
									}
									
									echo '</table>';
								?>
							</table>
						</div>
					</div>
					<input id="package_id" type="hidden" name="package_id" value="<?php echo esc_attr($p->id); ?>" />
					<input type="submit" name="submit" value="Update Shipping Package" class="button-primary" style="margin: 25px 0px 0px 0px;" />
				</form>
			</div>
			<div id="AjaxMessages" style="display: none;">
			
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
	
	<?php	
}

function ppw_shipping_packages_delete() {
	global $wpdb;
	
	$p = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'ppw_shipping_packages WHERE id = %s',sanitize_text_field($_GET["id"])));
	?>
	<div id="ppw">
		<p><a class="button-primary" href="<?php echo get_admin_url(); ?>admin.php?page=ppw_shipping_packages" style="margin-left:-5px;">Go Back</a></p>
		<div class="adminBlock">
			<h2>Delete Shipping Package</h2>
			
			<div id="ppw_loader" style="display: none;"><div class="loader-icon"></div></div>
			<div id="FormContent">
				<form action="admin.php?page=ppw_shipping_packages" method="post" onsubmit="return ppw_delete_shipping_package();">
					<p><i>Are you sure you would like to delete the shipping package connected to <?php echo esc_html($p->shipping_class_name); ?>?</i></p>
					<input id="package_id" type="hidden" name="package_id" value="<?php echo esc_attr($p->id); ?>" />
					<input type="submit" name="submit" value="Delete Shipping Package" class="button-primary" />
				</form>
			</div>
			<div id="AjaxMessages" style="display: none;">
			
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
	<?php	
}

function ppw_error_logs() {
	global $wpdb;
	
	$Logs = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ppw_error_log");
	
	?>
	<div id="ppw">
		<div class="adminBlock">
			<h2>Error Logs</h2>

			<h3>Refine Search</h3>

			<div id="FormContent">
				<form method="post" onsubmit="return PPW_error_log_Filter();">
					<div style="clear: both;"></div>
					<p><i>Start Date</i></p>
					<input id="ppw_error_log_start_date" type="text" name="ppw_error_log_start_date" value="" required autocomplete="off" />
					<div style="clear: both;"></div>
					<p><i>End Date</i></p>
					<input id="ppw_error_log_end_date" type="text" name="ppw_error_log_end_date" value="" required autocomplete="off" />
					<div style="clear: both;"></div>
					<input id="ppw_error_log_order" type="hidden" name="ppw_error_log_order" value="<?php if (!empty($_GET['order'])) : echo esc_attr($_GET['order']); endif; ?>" />
					<input type="submit" name="submit" value="Filter Results" class="Btn-Primary" />
				</form>
			</div>

			<hr />

			<div id="ppw_error_log_data">
				<table id="ppw_datatable" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th>Date</th>
							<th>Order #</th>
							<th>Error</th>
						</tr>
					</thead>
					<tbody>
					<?php

						foreach ($Logs as $l) {
							echo '<tr>
									<td>'.substr(esc_html($l->created_date_time),0,10).'</td>
									<td>'.esc_html($l->order).'</td>
									<td>'.esc_html($l->error).'</td>
								  </tr>';
						}

					?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
<?php
}
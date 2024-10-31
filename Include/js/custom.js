var tomselect;

jQuery(document).ready(function() {
	if (jQuery('body').hasClass('woocommerce-checkout') && jQuery('#ppw_suburb_name').length) {
		var target;
		
		if (jQuery('#ship-to-different-address-checkbox').is(":checked")) {
			target = '#shipping_city';
		}
		else {
			target = '#billing_city';
		}
		
		tomselect = new TomSelect(target,{
			maxItems: 1,
			valueField: 'value',
			labelField: 'town',
			searchField: 'town',
			// fetch remote data
			load: function(query, callback) {

				var url = ppw.places + '?q=' + encodeURIComponent(query);
				fetch(url)
					.then(response => response.json())
					.then(json => {
						callback(json.items);
					}).catch(()=>{
						callback();
					});

			},
			// custom rendering functions for options and items
			render: {
				option: function(item, escape) {
					return `<div id="place_`+item.place+`" class="ppqad_suburb">
								<p><strong>Name:</strong> <i><span class="ppqad_suburb_name">`+item.town+`</span></i><br />
								   <strong>Postal Code:</strong> <i><span class="ppqad_suburb_pcode">`+item.pcode+`</span></i></p>
							 </div>`;
				},
				item: function(item, escape) {
					return `<div id="place_`+item.place+`" class="ppqad_suburb">
								<p><i><span class="ppqad_suburb_name">`+item.town+`</span></i></p>
							 </div>`;
				}
			},
			onItemAdd: function(values, item) {
				var suburb = values.split('#-#');
				jQuery('#ppw_suburb_name').val(suburb[1]);
				jQuery('#ppw_suburb_postal_code').val(suburb[2]);
				jQuery('#ppw_suburb_pcode').val(suburb[0]);
			},
			onItemRemove: function(values) {
				jQuery('#ppw_suburb_name').val('');
				jQuery('#ppw_suburb_postal_code').val('');
				jQuery('#ppw_suburb_pcode').val('');
			}
		});
	}
	
	jQuery('#ship-to-different-address-checkbox').click(function() {
		if (jQuery('#ppw_suburb_name').length) {
			tomselect.destroy();
		
			if (jQuery(this).is(':checked')) {
				target = '#shipping_city';
			}
			else {
				target = '#billing_city';
			}
			
			tomselect = new TomSelect(target,{
				maxItems: 1,
				valueField: 'value',
				labelField: 'town',
				searchField: 'town',
				// fetch remote data
				load: function(query, callback) {

					var url = ppw.places + '?q=' + encodeURIComponent(query);
					fetch(url)
						.then(response => response.json())
						.then(json => {
							callback(json.items);
						}).catch(()=>{
							callback();
						});

				},
				// custom rendering functions for options and items
				render: {
					option: function(item, escape) {
						return `<div id="place_`+item.place+`" class="ppqad_suburb">
									<p><strong>Name:</strong> <i><span class="ppqad_suburb_name">`+item.town+`</span></i><br />
									   <strong>Postal Code:</strong> <i><span class="ppqad_suburb_pcode">`+item.pcode+`</span></i></p>
								 </div>`;
					},
					item: function(item, escape) {
						return `<div id="place_`+item.place+`" class="ppqad_suburb">
									<p><i><span class="ppqad_suburb_name">`+item.town+`</span></i></p>
								 </div>`;
					}
				},
				onItemAdd: function(values, item) {
					var suburb = values.split('#-#');
					jQuery('#ppw_suburb_name').val(suburb[1]);
					jQuery('#ppw_suburb_postal_code').val(suburb[2]);
					jQuery('#ppw_suburb_pcode').val(suburb[0]);
				},
				onItemRemove: function(values) {
					jQuery('#ppw_suburb_name').val('');
					jQuery('#ppw_suburb_postal_code').val('');
					jQuery('#ppw_suburb_pcode').val('');
				}
			});
		}
	});
	
	if (jQuery('#ppw_datatable').length) {
		var ppw_datatable_search = '';
		var ppw_search_order = jQuery('#ppw_error_log_order').val();
		
		jQuery('#ppw_datatable').DataTable({
				dom: 'Bfrtip',
				buttons: [
					'copy', 'csv', 'excel', 'print'
				],
				search: {
				   search: ppw_search_order
				}
			});
		
		jQuery( "#ppw_error_log_start_date" ).datepicker({ dateFormat: 'yy-mm-dd' });
		jQuery( "#ppw_error_log_end_date" ).datepicker({ dateFormat: 'yy-mm-dd' });
	}
	
	jQuery('#ppw_pack_no_items').on('change',function () {
		var amount = jQuery(this).val();
		jQuery('#ppw_package_dim_breakdown div').html('');
		
		jQuery('#ppw_loader').show();
		jQuery('#FormContent').hide();
		
		if (amount > 0) {			
			jQuery.ajax({
				type : "post",
				url : ppw.ajax,
				data : {
					'action': "ppw_get_dim_breakdown",
					'amount' : amount
				},
				success: function(response) {
					jQuery('#ppw_package_dim_breakdown div').html(response);
					jQuery('#ppw_package_dim_breakdown').show();
					
					jQuery('#ppw_loader').hide();
					jQuery('#FormContent').show();
				}
			});
		}
	});
	
	if (jQuery('#ppw_origin_selector').length) {
		tomselect = new TomSelect('#ppw_origin_selector',{
			maxItems: 1,
			valueField: 'value',
			labelField: 'town',
			searchField: 'town',
			// fetch remote data
			load: function(query, callback) {

				var url = ppw.places + '?q=' + encodeURIComponent(query);
				fetch(url)
					.then(response => response.json())
					.then(json => {
					callback(json.items);
				}).catch(()=>{
					callback();
				});

			},
			// custom rendering functions for options and items
			render: {
				option: function(item, escape) {
					return `<div id="place_`+item.place+`" class="ppqad_suburb">
<p><strong>Name:</strong> <i><span class="ppqad_suburb_name">`+item.town+`</span></i><br />
<strong>Postal Code:</strong> <i><span class="ppqad_suburb_pcode">`+item.pcode+`</span></i></p>
</div>`;
				},
				item: function(item, escape) {
					return `<div id="place_`+item.place+`" class="ppqad_suburb">
<p><i><span class="ppqad_suburb_name">`+item.town+`</span></i></p>
</div>`;
				}
			},
			onItemAdd: function(values, item) {
				var suburb = values.split('#-#');
				jQuery('#ppw_origin_name').val(suburb[1]);
				jQuery('#ppw_origin_postalcode').val(suburb[2]);
				jQuery('#ppw_origin_courier_code').val(suburb[0]);
			},
			onItemRemove: function(values) {
				jQuery('#ppw_origin_name').val('');
				jQuery('#ppw_origin_postalcode').val('');
				jQuery('#ppw_origin_courier_code').val('');
			}
		});
	}
	
	if (jQuery('#ppw_dest_courier').length) {
		tomselect = new TomSelect('#ppw_dest_courier',{
			maxItems: 1,
			valueField: 'value',
			labelField: 'town',
			searchField: 'town',
			// fetch remote data
			load: function(query, callback) {

				var url = ppw.places + '?q=' + encodeURIComponent(query);
				fetch(url)
					.then(response => response.json())
					.then(json => {
					callback(json.items);
				}).catch(()=>{
					callback();
				});

			},
			// custom rendering functions for options and items
			render: {
				option: function(item, escape) {
					return `<div id="place_`+item.place+`" class="ppqad_suburb">
<p><strong>Name:</strong> <i><span class="ppqad_suburb_name">`+item.town+`</span></i><br />
<strong>Postal Code:</strong> <i><span class="ppqad_suburb_pcode">`+item.pcode+`</span></i></p>
</div>`;
				},
				item: function(item, escape) {
					return `<div id="place_`+item.place+`" class="ppqad_suburb">
<p><i><span class="ppqad_suburb_name">`+item.town+`</span></i></p>
</div>`;
				}
			},
			onItemAdd: function(values, item) {
				var suburb = values.split('#-#');
				jQuery('span.ppw_dest_courier_name').html(suburb[1]);
				jQuery('#ppw_destination_name').val(suburb[1]);
				jQuery('span.ppw_dest_courier_postal_code').html(suburb[2]);
				jQuery('span.ppw_dest_courier_code').html(suburb[0]);
				jQuery('#ppw_destination_pcode').val(suburb[0]);
			},
			onItemRemove: function(values) {
				jQuery('span.ppw_dest_courier_name').html('');
				jQuery('#ppw_destination_name').val('');
				jQuery('span.ppw_dest_courier_postal_code').html('');
				jQuery('span.ppw_dest_courier_code').html('');
				jQuery('#ppw_destination_pcode').val('');
			}
		});
	}
	
	jQuery('#ppw_create_waybill').on('click','.ppw_create_waybill_from_details',function(e) {
		var ppw_waybill_error = false;
		
		var order = jQuery(this).attr('ppw-order');
		var ppw_dest_code = jQuery('#ppw_destination_pcode').val();
		var ppw_waybill_no = jQuery('#ppw_waybill_number').val();
		var ppw_courier_service = jQuery('#ppw_courier_service').val();
		var ppw_destination_name = jQuery('#ppw_destination_name').val();
		var ppw_destination_pcode = jQuery('#ppw_destination_pcode').val();
		
		var ppw_existing_waybills = jQuery('#ppw_existing_waybills').val();
		
		if (ppw_dest_code == null || ppw_dest_code == '') {
			ppw_waybill_error = true;
			jQuery('#ppw_dest_courier_code').addClass('BorderRed');
		}
		else {
			jQuery('#ppw_dest_courier_code').removeClass('BorderRed');
		}
		
		if (ppw_waybill_no == null || ppw_waybill_no == '') {
			ppw_waybill_error = true;
			jQuery('#ppw_waybill_number').addClass('BorderRed');
		}
		else {
			jQuery('#ppw_waybill_number').removeClass('BorderRed');
		}
		
		if (ppw_courier_service == null || ppw_courier_service == '') {
			ppw_waybill_error = true;
			jQuery('#ppw_courier_service').addClass('BorderRed');
		}
		else {
			jQuery('#ppw_courier_service').removeClass('BorderRed');
		}
		
		var splitString = ppw_existing_waybills.split(',');
		var waybill_found;
		for (var i = 0; i < splitString.length; i++) {
			var stringPart = splitString[i];
			if (stringPart != ppw_waybill_no) continue;

			waybill_found = true;
			break;
		}
		
		if (waybill_found) {
			ppw_waybill_error = true;
			alert('A waybill with waybill number '+ppw_waybill_no+' already exists for this order.  Please choose another waybill number.');
		}
		
		if (!ppw_waybill_error) {
			jQuery('#ppw_create_waybill #ppw_create_waybill_details').hide();
			jQuery('#ppw_create_waybill #ppw_loader').show();
			
			jQuery.ajax({
				url : ppw.ajax,
				data : {
					'action': "ppw_create_waybill",
					'order' : order,
					'ppw_destination_name' : ppw_destination_name,
					'ppw_destination_pcode' : ppw_destination_pcode,
					'ppw_dest_code' : ppw_dest_code,
					'ppw_waybill_no' : ppw_waybill_no,
					'ppw_courier_service' : ppw_courier_service
				},
				success: function(response) {
					console.log(response);
					location.reload();
				}
			});
		}
	});
	
	jQuery('.ppw_print').on('click',function(e) {
		e.preventDefault();
		
		var waybill = jQuery(this).attr('ppw-waybill');
		
		printJS({
			printable: ppw.ppw_url + '/waybills/'+waybill+'_Waybill.pdf',
			type: 'pdf',
			onPrintDialogClose: function() {
				printJS(ppw.ppw_url + '/waybills/'+waybill+'_Label.pdf');
			}
		});
		
		return false;
	});
	
	jQuery('#ppw').on( 'click', '.ppw_tracking_upload_image', function(e){

		e.preventDefault();

		var button = jQuery(this),
		custom_uploader = wp.media({
			title: 'Insert image',
			library : {
				// uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
				type : 'image'
			},
			button: {
				text: 'Use this image' // button label text
			},
			multiple: false
		}).on('select', function() { // it also has "open" and "close" events
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			
			jQuery('#ppw_tracking_email_logo').val(attachment.url);
			jQuery('#ppw_tracking_email_logo_preview').attr('src',attachment.url);
			
		}).open();
	
	});
});

function ppw_close_popup() {
	jQuery('#popupreferrer').val('');
	jQuery('html.responsive').removeClass('noscroll');
	jQuery('.popupbg').removeClass('makepopupscroll');
	jQuery('.popupcontentcontainer #ppshipping_loader').hide();
	jQuery('.suburb_list').html('');
	jQuery('.popupresult').hide();
	jQuery('.popupinfo').show();
	jQuery('#ppw_suburb_finder').val('');
	jQuery('.popupbg').hide();
}

function ppw_find_suburb() {
	var searchTerm = jQuery('#ppw_suburb_finder').val();
	var searchType = 'name';
	
	jQuery('.suburb_list').html('');
	
	if (searchTerm == '' || searchTerm == null) {
		jQuery('.popupinfo').hide();
		jQuery('.suburb_list').html('<span style="color: red;">Please enter a suburb name or postal code to search for your suburb.</span>');
		jQuery('.popupresult').show();
		return false;
	}
	
	if (ppw_isNumeric(searchTerm)) {
		searchType = 'postal_code';
	}
	
	jQuery('.popupinfo').hide();
	jQuery('#ppw_loader').show();
	
	jQuery.ajax({
		url : ppw.ajax,
		data : {
			'action': "ppw_get_places",
			'searchTerm' : searchTerm,
			'searchType' : searchType
		},
		success: function(response) {
			jQuery('.popupresult').show();
			jQuery('#ppw_loader').hide();
			
			jQuery('.suburb_list').append('<div class="ppshipping_suburb_result">'+response+'</div>');
		}
	});
	
	return false;
}

function ppw_isNumeric(str) {
  if (typeof str != "string") return false // we only process strings!  
  return !isNaN(str) &&
         !isNaN(parseFloat(str))
}

function ppw_add_shipping_package() {
	var shipping_class = jQuery('#ppw_pack_shipping_class').val();
	var shipping_class_name = jQuery( "#ppw_pack_shipping_class option:selected" ).text();
	var shipping_class_waybill = jQuery('#ppw_pack_ignore').val();
	var label = jQuery("input[name='ppw_breakdown_label[]']").map(function(){return jQuery(this).val();}).get();
	var no_items = jQuery('#ppw_pack_no_items').val();
	var width = jQuery("input[name='ppw_breakdown_width[]']").map(function(){return jQuery(this).val();}).get();
	var length = jQuery("input[name='ppw_breakdown_length[]']").map(function(){return jQuery(this).val();}).get();
	var height = jQuery("input[name='ppw_breakdown_height[]']").map(function(){return jQuery(this).val();}).get();
	var weight = jQuery("input[name='ppw_breakdown_weight[]']").map(function(){return jQuery(this).val();}).get();
	
	if (shipping_class == '' || shipping_class == null) {
		alert('Please select a valid shipping class for this shipping package.  If no options exist, please create a shipping class or delete an existing shipping package.');
		return false;
	}
	
	if (label == '' || label == null) {
		alert('Please enter valid label(s) for this shipping package.');
		return false;
	}
	
	if (no_items == '' || no_items == null || no_items < 1) {
		alert('Please enter a valid number of items for this shipping package.');
		return false;
	}
	
	if (width == '' || width == null) {
		alert('Please enter valid width(s) in cm for this shipping package box.');
		return false;
	}
	
	if (length == '' || length == null) {
		alert('Please enter valid length(s) in cm for this shipping package box.');
		return false;
	}
	
	if (height == '' || height == null) {
		alert('Please enter valid height(s) in cm for this shipping package box.');
		return false;
	}
	
	if (weight == '' || weight == null) {
		alert('Please enter valid weight(s) in kg for this shipping package box.');
		return false;
	}
	
	jQuery('#ppw #ppw_loader').show();
	jQuery('#FormContent').hide();
	
	jQuery.ajax({
		type : "post",
		url : ppw.ajax,
		data : {
			'action': 'ppw_save_shipping_package',
			'shipping_class' : shipping_class,
			'shipping_class_name' : shipping_class_name,
			'shipping_class_waybill' : shipping_class_waybill,
			'label' : label,
			'no_items' : no_items,
			'width' : width,
			'length' : length,
			'height' : height,
			'weight' : weight
		},
		success: function(response) {
			window.location.assign(ppw.admin_packages);
		}
	});
	
	return false;
}

function ppw_update_shipping_package() {
	var shipping_class = jQuery('#ppw_pack_shipping_class').val();
	var shipping_class_name = jQuery( "#ppw_pack_shipping_class option:selected" ).text();
	var shipping_class_waybill = jQuery('#ppw_pack_ignore').val();
	var label = jQuery("input[name='ppw_breakdown_label[]']").map(function(){return jQuery(this).val();}).get();
	var no_items = jQuery('#ppw_pack_no_items').val();
	var width = jQuery("input[name='ppw_breakdown_width[]']").map(function(){return jQuery(this).val();}).get();
	var length = jQuery("input[name='ppw_breakdown_length[]']").map(function(){return jQuery(this).val();}).get();
	var height = jQuery("input[name='ppw_breakdown_height[]']").map(function(){return jQuery(this).val();}).get();
	var weight = jQuery("input[name='ppw_breakdown_weight[]']").map(function(){return jQuery(this).val();}).get();
	
	var package_id = jQuery('#package_id').val();
	
	if (shipping_class == '' || shipping_class == null) {
		alert('Please select a valid shipping class for this shipping package.  If no options exist, please create a shipping class or delete an existing shipping package.');
		return false;
	}
	
	if (label == '' || label == null) {
		alert('Please enter valid label(s) for this shipping package.');
		return false;
	}
	
	if (no_items == '' || no_items == null || no_items < 1) {
		alert('Please enter a valid number of items for this shipping package.');
		return false;
	}
	
	if (width == '' || width == null) {
		alert('Please enter valid width(s) in cm for this shipping package box.');
		return false;
	}
	
	if (length == '' || length == null) {
		alert('Please enter valid length(s) in cm for this shipping package box.');
		return false;
	}
	
	if (height == '' || height == null) {
		alert('Please enter valid height(s) in cm for this shipping package box.');
		return false;
	}
	
	if (weight == '' || weight == null) {
		alert('Please enter valid weight(s) in kg for this shipping package box.');
		return false;
	}
	
	jQuery('#ppw #ppw_loader').show();
	jQuery('#FormContent').hide();
	
	jQuery.ajax({
		type : "post",
		url : ppw.ajax,
		data : {
			'action': 'ppw_update_shipping_package',
			'package_id' : package_id,
			'shipping_class' : shipping_class,
			'shipping_class_name' : shipping_class_name,
			'shipping_class_waybill' : shipping_class_waybill,
			'label' : label,
			'no_items' : no_items,
			'width' : width,
			'length' : length,
			'height' : height,
			'weight' : weight
		},
		success: function(response) {
			window.location.assign(ppw.admin_packages);
		}
	});
	
	return false;
}

function ppw_delete_shipping_package() {
	var package_id = jQuery('#package_id').val();
	
	jQuery('#ppw #ppw_loader').show();
	jQuery('#FormContent').hide();
	
	jQuery.ajax({
		type : "post",
		url : ppw.ajax,
		data : {
			'action': 'ppw_delete_shipping_package',
			'package_id' : package_id
		},
		success: function(response) {
			window.location.assign(ppw.admin_packages);
		}
	});
	
	return false;
}

function PPW_error_log_Filter() {
	var log_start_date = jQuery('#ppw_error_log_start_date').val();
	var log_end_date = jQuery('#ppw_error_log_end_date').val();
	var ppw_search_order = jQuery('#ppw_error_log_order').val();
	
	jQuery('#ppw_error_log_data').html('');
	
	jQuery.ajax({
		url: ppw.ajax,
		data: {
			'action':'ppw_error_log_filter',
			'log_start_date':log_start_date,
			'log_end_date':log_end_date
		},
		success:function(data) {
			jQuery('#ppw_error_log_data').html(data);
			
			jQuery('#ppw_datatable').DataTable({
				dom: 'Bfrtip',
				buttons: [
					'copy', 'csv', 'excel', 'print'
				],
				search: {
				   search: ppw_search_order
				}
			});
		},
		error: function(errorThrown){
			console.log(errorThrown);
		}
	});
	
	return false;
}
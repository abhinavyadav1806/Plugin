// This jQuery is for the admin menu 'Import Product' on selecting the options from drop down 
// it is sending Json filename where it is decoded and rendering the data in wp_list_table form.
jQuery(document).ready(function(){
	jQuery('#select').change(function(){
		var file = jQuery(this).val();
		// alert(file);

		jQuery.ajax({
			url : custom_object.ajax_url,
			type : 'post',
			data : {
				action : 'custom_action_ajax_add_action',
				file : file
			},
			success : function( response ) {
				jQuery('#data').html(response);
			}
		});
	});
});


// This jQuery is for the admin menu 'Import Product BUTTON' on selecting the particular product from 
// products wp_list_table it is sending Json filename where it is decoded based on the product_id and saving the data in database.
jQuery(document).on('click', '.import', function () {
	var item_sku       = jQuery(this).data('item_sku');
	var item_id        = jQuery(this).data('item_id');
	var file_name      = jQuery('#select').val();
	var btn_to_disable = this;

	jQuery.ajax({
		url : custom_object.ajax_url,
		type: 'post',
		data: {
			action : 'custom_action_to_import_product',
			item_id : item_id,
			sku : item_sku,
			json_file_name : file_name,
		},
		success: function (response) {
			jQuery('#product-imported').html("<h1>Product Imported Successfully<h1>");
			jQuery('#hello').html(response);
			jQuery(btn_to_disable).attr('disabled',true);
		}
	});
});


// This jQuery is for the admin menu 'Import order BUTTON' on selecting the bulk(multiple) product from 
// products wp_list_table it is sending Json filename where it is decoded based on the product_id and saving the data in database.
jQuery( document ).ready( function() {
	jQuery(document).on('click', '#doaction', function () {
		var item_sku      = [];
		var bulkselect_id = [];
		var file_name     = jQuery('#select').val();
	   
		var select_bulk_dropdown = jQuery("#bulk-action-selector-top").val();

		if (select_bulk_dropdown == 'bulk-import') {
			jQuery("input[name='bulk-import[]']:checked").each(function () {
				bulkselect_id.push(jQuery(this).val());
				item_sku.push(jQuery(this).data('item_sku'));
			});
			jQuery.ajax({
				url : custom_object.ajax_url,
				type: 'post',
				data : {
					action : 'custom_action_to_bulk_import_product',
					item_sku : item_sku,
					var_bulkselect_id : bulkselect_id,
					json_dropdown_file_name : file_name
				},
				success : function( response ){
					jQuery('#hello').html(response);
					// alert(response);
				}
			});
		}
	});
});


jQuery(document).ready(function(){
	jQuery(document).on('click', '#select_order', function(){
		var order_file_name = jQuery('#select_order').val();

		jQuery.ajax({
			url : custom_object.ajax_url,
			type : 'post',
			data : {
				action : 'custom_action_to_create_order',
				json_dropdown_file_name : order_file_name
			},
			success : function( order ){
				jQuery('#order').html( order );
			}
		});
	});
});

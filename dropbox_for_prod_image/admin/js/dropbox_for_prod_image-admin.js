(function( $ ) {
	'use strict';
	$(document).ready(function(){
		$(document).on('click','#generate_code', function() {
			var app_key = $('#app_key').val();
			var app_secret = $('#app_secret').val();
			var redirect_url = 'http://localhost/Wordpress_WooCommerce/wp-admin/admin.php?page=dropbox';
			var url = 'https://www.dropbox.com/oauth2/authorize';

			window.open( url + '/?client_id='+app_key+'&redirect_uri='+redirect_url+'&response_type=code');
		});
	});

})( jQuery );



jQuery(document).ready(function() {
	jQuery(document).on('click', '#image_btn', function() {
		var get_post_id = jQuery(this).attr("data-target");
		// alert(getValue);
		var image_name = jQuery('#fileUpload').prop('files')[0];
		
		var Form_Data = new FormData();
		Form_Data.append( 'file', image_name );
		Form_Data.append( 'post_id',get_post_id);
		Form_Data.append('action', 'custom_action_to_dropbox_input_field');

		console.log( Form_Data );

		jQuery.ajax({
			url : custom_object.ajax_url,
			type : 'POST',
			data : Form_Data,
			cache : false,
			processData : false,
			contentType : false,
			success : function( response ){
				jQuery('#alpha').html(response);
				console.log(response);
			}
		});
	});
});
<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       ced_abhinavyadav@cedcoss.com
 * @since      1.0.0
 *
 * @package    Product_importer
 * @subpackage Product_importer/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Product_importer
 * @subpackage Product_importer/admin
 * author     Abhinav <abhinavyadav@cedcoss.com>
 */
class Product_Importer_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Product_importer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Product_importer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/product_importer-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Product_importer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Product_importer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		*/
		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/product_importer-admin.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( 'file_handler', plugin_dir_url( __FILE__ ) . 'js/product_importer-admin.js', array( 'jquery' ), true);
		wp_localize_script( 'file_handler', 'custom_object', 
			array( 'ajax_url' => admin_url('admin-ajax.php'))
		);
	}


	/**
	 * Add_menu_import_product
	 * This is to create a admin menu Named "Import Product".
	 *
	 * @return void
	*/
	public function add_menu_import_product() {
		add_menu_page(
			'Import Product', // menu title
			'Import Product', // menu name
			'manage_options', // capabality
			'menu', 		  // slug
			array($this,'import_product_page_html'), // Callback function
			'dashicons-archive', // icon
			9 // position
		);
	}
	
	
	/**
	 * Import_product_page_html
	 * Callback Function
	 * 
	 * @return void
	*/
	public function import_product_page_html() {
		?>
			<h1> <?php echo esc_html( get_admin_page_title() ); ?> </h1>
		<?php

		// Creating settings to accept a file, the file will be saved in WordPress upload folder. 
		if ( isset( $_POST['uploadBtn'] ) ) {
			if ( '' != $_FILES['fileToUpload']['name'] ) {
				$upload     = wp_upload_dir();
				$upload_dir = $upload['basedir'];
				$file       = $_FILES['fileToUpload']['name'];
				$path       = pathinfo($file);
				
				$filename = $path['filename'];
				$ext      = $path['extension'];

				// Checking Extension i.e It will only accept json file
				if ( 'json' == $ext ) {
					$temp_name         = $_FILES['fileToUpload']['tmp_name'];
					$path_filename_ext = $upload_dir . '/imported_product/' . $filename . '.' . $ext;
					
					if (file_exists($path_filename_ext)) {
						echo '<h4> Sorry, file already exists. </h4>';
					} else {
						move_uploaded_file($temp_name, $path_filename_ext);
						echo '<h4> Congratulations! File Uploaded Successfully. </h4>';
						
						// Insert file in DB by key 'save_filename'
						$save_filename = get_option('save_filename', array());
						// if( empty( $save_filename ) && $save_filename == ''){
						// 	$save_filename[] = $path;
						// }
						// else{
							$save_filename[] = $path;
						// }
						update_option('save_filename', $save_filename);
					}
				} else {
					echo '<h3> No Other File Will Be Accepted Rather Than Json File </h3>';
				}
			}
		}

		
		// Create settings to accept a file
		?>
			<form method="POST" enctype="multipart/form-data">
				<div class="upload">
					<h3> --Choose Json File to upload-- </h3>
					<input type="file" id="fileUpload" name="fileToUpload">
					<input type="submit" name="uploadBtn" id="uploadBtn" value="Upload" /> </p>
				</div>
			</form>
		<?php


		// A dropdown will be created which will list all the uploaded files.
		?>
			<h2>Dropdown Menu</h2>
			<p>Move the mouse over the button to open the dropdown menu.</p>

			<select name="select" id="select">
			<option>Select Json File</option>
				<?php 
					$file = get_option('save_filename');
				foreach ($file as $key => $value) :
					echo "<option id='option' value='" . $value['basename'] . "'>" . $value['basename'] . '</option>'; 
					endforeach;
				?>
			</select>
			<div id='product-imported'></div>
			<div id='data'></div>
			<div id='hello'></div>
		<?php
	}

		
	/**
	 * Render_json_page_data
	 * This is to decode json received from ajax and To render data 
	 *
	 * @return void
	*/
	public function render_json_page_data() {
		if (isset($_POST['file'])) {
			$file = $_POST['file'];

			$upload     = wp_upload_dir();
			$upload_dir = $upload['basedir'];
			$upload_dir = $upload_dir . '/imported_product/' . $file;

			$json = json_decode(file_get_contents($upload_dir), true);
			require_once plugin_dir_path( __FILE__ ) . 'partials/product_importer-admin-display.php';
		
			$Customers_List        = new Customers_List();
			$Customers_List->items = $json;
			$Customers_List->prepare_items();
			print_r($Customers_List->display());
		}
		wp_die();
	}

	
	/**
	 * To_import_product
	 * This is to import product in wp_posts table and wp_postmeta table  
	 *
	 * @return void
	*/
	public function to_import_product() {
		if (isset($_POST['item_id'])) {
			$fetch_id             = $_POST['item_id'];
			$fetch_sku            = $_POST['sku'];
			$fetch_json_file_name = $_POST['json_file_name'];

			$upload            = wp_upload_dir();
			$upload_dir        = $upload['basedir'];
			$upload_dir        = $upload_dir . '/imported_product/' . $fetch_json_file_name;
			$decoded_json_file = json_decode(file_get_contents($upload_dir), true);

			$this->create_product($fetch_id , $fetch_sku , $decoded_json_file);
		}
		wp_die();
	}
	
	
	/**
	 * create_product
	 * This is to create product in wp_posts table and wp_postmeta table
	 *
	 * @param  mixed $fetch_id
	 * @param  mixed $fetch_sku
	 * @param  mixed $decoded_json_file
	 * @return void
	 */
	public function create_product( $fetch_id, $fetch_sku, $decoded_json_file ) {
		$user_id = get_current_user_id();
		global $wpdb;
		$check_existing_sku = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value FROM $wpdb->postmeta WHERE  meta_value='%s'", $fetch_sku) );
		// print_r($check_existing_sku);
		
		if (!$check_existing_sku) {
			foreach ($decoded_json_file as $key => $value) {
				if ( $fetch_id == $value['item']['item_id'] ) {

					if ($value['item']['has_variation'] == 1) {
						$my_product = array(
							'post_author'   => $user_id,
							'post_date' 	=> get_gmt_from_date( $post_date ),
							'post_date_gmt' => get_gmt_from_date( $post_date ),
							'post_content'  => $value['item']['description'],
							'post_title'  	=> $value['item']['name'],
							'post_excerpt'  => $value['item']['description'],
							'post_status'   => 'publish',
							'comment_status'=> 'closed',
							'ping_status'   => 'closed',
							'post_type'		=> 'product'
						);
						// Insert the post into the database
						$post_id = wp_insert_post( $my_product );
						wp_set_object_terms( $post_id, 'variable', 'product_type');

						// ------------------------- To create Variable product Attributes -----------------------------//

							$data_tier_attribute = $value['tier_variation'];
							$this->create_var_product_attribue( $data_tier_attribute, $post_id );
	
						// ---------------------------------------------------------------------------//
	
						// ------------------------- To create Variable product Variation -----------------------------//
	
							$variation = $value['item']['variations'];
							$this->create_variation_variable_product( $variation, $post_id, $data_tier_attribute );
	
						// ---------------------------------------------------------------------------//

					} else {
						// Create product Saving Values in wp_posts table
						$my_product = array(
							'post_author'   => $user_id,
							'post_date' 	=> get_gmt_from_date( $post_date ),
							'post_date_gmt' => get_gmt_from_date( $post_date ),
							'post_content'  => $value['item']['description'],
							'post_title'  	=> $value['item']['name'],
							'post_excerpt'  => $value['item']['description'],
							'post_status'   => 'publish',
							'comment_status'=> 'closed',
							'ping_status'   => 'closed',
							'post_type'		=> 'product'
						);
						// Insert the post into the database
						$post_id = wp_insert_post( $my_product );
						wp_set_object_terms( $post_id, 'simple', 'product_type');

						// -------------------- To create simple product attribute --------------------//

							$data_attributes = $value['item']['attributes'];
							$this->simple_product_attribute($data_attributes, $post_id);

						// ---------------------------------------------------------------------------//
					}

					// --------- Renders image on backend for each imported product edit ---------//

						$pic_url = $value['item']['images'][0];
						$this->render_image($pic_url, $post_id);

					// ---------------------------------------------------------------------------//
					

					// Create product and saving data in  wp_post_meta table
					if (!isset($value['item']['sale_price'])) {
						$price = $value['item']['price'];
					} else {
						$sale_price = $value['item']['sale_price'];
					}
					$my_product_meta = array(
						'_price'   => $price,
						'_sale_price' => $sale_price,
						'_sku'  => $value['item']['item_sku'],
						'_stock'  	=> $value['item']['stock'],
						'_stock_status'  => $value['item']['status'],
						'_downloadable'   => 'no',
						'_virtual'=> 'no',
						'_sold_individually'   => 'no',
						'_backorders'   => 'no',
					);
					// Insert the post into the database
					foreach ($my_product_meta as $key_db => $value_db) {
						update_post_meta( $post_id, $key_db, $value_db );
					}
				}
			}
		} else {
			echo( '<h2>Product Already Exists</h2>' );
		}
	}


	/**
	 * Simple_product_attribute
	 * Passed 2 values from object for creating a simple product attributes 
	 *
	 * @param  mixed $simple_attr
	 * @param  mixed $post_id
	 * @return void
	*/
	public function simple_product_attribute( $simple_attr, $post_id ) {
		foreach ($simple_attr as $value) {
			$attribute_name  = isset($value['attribute_name']) ? $value['attribute_name'] : '' ;
			$attribute_value = isset($value['attribute_value']) ? $value['attribute_value'] : '' ;
		}
		$arr[$attribute_name] =array(
			'name' => $attribute_name,
			'value' => $attribute_value,
			'is_visible' => 1,			
			'is_variation' => 0,
			'is_taxonomy' => 0
		);
		update_post_meta($post_id, '_product_attributes' , $arr);
	}

		
	/**
	 * Render_image
	 * Renders image on backend for each imported product edit
	 *
	 * @param  mixed $pic_url
	 * @param  mixed $post_id
	 * @return void
	*/
	public function render_image( $pic_url, $post_id ) {
		// Add Featured Image to Post
		$image_url        = $pic_url; // Define the image URL here
		$image_name       = basename( $image_url );
		$upload_dir       = wp_upload_dir(); // Set upload folder
		$image_data       = file_get_contents($image_url); // Get image data
		$unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
		$filename         = basename( $unique_file_name ); // Create image file name

		// Check folder permission and define file location
		if ( wp_mkdir_p( $upload_dir['path'] ) ) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}

		// Create the image  file on the server
		file_put_contents( $file, $image_data );

		// Check image file type
		$wp_filetype = wp_check_filetype( $filename, null );

		// Set attachment data
		$attachment = array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => sanitize_file_name( $filename ),
			'post_content'   => '',
			'post_status'    => 'publish'
		);

		// Create the attachment
		$attach_id = wp_insert_attachment( $attachment, $file, $post_id );

		// Include image.php
		require_once ABSPATH . 'wp-admin/includes/image.php';

		// Define attachment metadata
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

		// Assign metadata to attachment
		wp_update_attachment_metadata( $attach_id, $attach_data );

		// And finally assign featured image to post
		set_post_thumbnail( $post_id, $attach_id );
	}

		
	/**
	 * create_var_product_attribue
	 * Passed 2 values from object for creating a Variable product Attributes
	 *
	 * @param  mixed $data_tier_attribute
	 * @param  mixed $post_id
	 * @return void
	*/
	public function create_var_product_attribue( $data_tier_attribute, $post_id ) {
		$attribute_name = '';
		foreach ($data_tier_attribute as $values) {
			$attribute_name    = isset($values['name']) ? $values['name'] : '' ;
			$attribute_options = isset($values['options']) ? $values['options'] : '';
		}
		$attribute_options_implode = implode('|', $attribute_options);
		$arr[$attribute_name]      =array(
			'name' => $attribute_name,
			'value' => $attribute_options_implode,
			'is_visible' => 1,			
			'is_variation' => 1,
			'is_taxonomy' => 0
		);
		update_post_meta($post_id, '_product_attributes' , $arr);
	}
	

	/**
	 * Make_variation
	 * Importing a Product (Simple ,Variable) in DB Using Product SKU and Specific File 
	 *
	 * @param  mixed $variation
	 * @param  mixed $post_id
	 * @param  mixed $data_tier_attribute
	 * @return void
	*/
	public function create_variation_variable_product( $variation, $post_id, $data_tier_attribute ) {
		foreach ($variation as $key => $values) {
			$parent_id = $post_id;
			$name      = $values['name'];

			if (!isset($values['original_price'])) {
				$price = $values['price'];
			} else {
				$original_price = $values['original_price'];
			}
			$var          = array(
				'post_content' => $name,
				'post_title' => $name,
				'post_status'  => 'publish',
				'post_parent'  => $parent_id,
				'post_type'    => 'product_variation'
			);
			$variation_id = wp_insert_post( $var );
			update_post_meta( $variation_id, '_regular_price', $original_price );
			update_post_meta( $variation_id, '_price', $price );
			update_post_meta( $variation_id, '_sku', $values['variation_sku'] );
			
			foreach ($data_tier_attribute as $value) {
				$attr_name = $value['name'];

				foreach ($value['options'] as $option) {
					if ($name == $option) {
						  $attribute_value = $option;
					}
				}
				update_post_meta($variation_id, 'attribute_' . strtolower($attr_name), $attribute_value);

				$attr_image = $value['images_url'];
				foreach ($value['images_url'] as $key1 => $value1) {
					foreach ($value['options'] as $key2=>$option) {
						if ($key1==$key2) {
							$pic_url =$value1;

							$this->render_image($pic_url, $variation_id);
						}
					}
				}
			}
		}
		WC_Product_Variable::sync( $parent_id );	
	}

	
	/**
	 * to_bulk_import_product
	 * Bulk importing product
	 *
	 * @return void
	*/
	public function to_bulk_import_product() {
		$item_sku          = $_POST['item_sku'];
		$var_bulkselect_id = $_POST['var_bulkselect_id'];
		$json_file_name    = $_POST['json_dropdown_file_name'];

		$upload            = wp_upload_dir();
		$upload_dir        = $upload['basedir'];
		$upload_dir        = $upload_dir . '/imported_product/' . $json_file_name;
		$decoded_json_file = json_decode(file_get_contents($upload_dir), true);

		foreach ( $var_bulkselect_id as $single_id ) {
			foreach ( $item_sku as $single_sku ) {
				$this->create_product( $single_id , $single_sku , $decoded_json_file );	
			}
		}	
	}

	// 
	// 
	// ADMIN ORDER --->
	// 
	// 

	public function add_menu_import_order() {
		add_menu_page(
			'Import order', // menu title
			'Import order', // menu name
			'manage_options', // capabality
			'order_menu', 		  // slug
			array($this,'Import_order_page_html'), // Callback function
			'dashicons-image-rotate-right', // icon
			9 // position
		);
	}
	
	/**
	 * Import_order_page_html
	 *
	 * @return void
	*/
	public function Import_order_page_html() {
		?>
			<h1> <?php echo esc_html( get_admin_page_title() ); ?> </h1>
		<?php

		// Creating settings to accept a file
		?>
			<form method="POST" enctype="multipart/form-data">
				<div class="order">
					<h3> -- Choose Json File To Create Order-- </h3>
					<input type="file" id="order_import" name="order_import">
					<input type="submit" name="order_import_btn" id="order_import_btn" value="Upload" /> </p>
				</div>
			</form>
		<?php


		// A dropdown will be created which will list all the uploaded files.
		?>
			<h2>Dropdown Orders Menu</h2>
			<p>Move the mouse over the button to open the dropdown menu.</p>

			<select name="select_order" id="select_order">
			<option>Select Json File</option>
				<?php 
					$file = get_option('save_filename_for_order');
					foreach ($file as $key => $value) :
					echo "<option id='option_order' value='" . $value['basename'] . "'>" . $value['basename'] . '</option>'; 
					endforeach;
				?>
			</select>
			<div id="order"></div>
		<?php

		// Creating settings to accept a order file, the file will be saved in WordPress upload folder. 
		if ( isset( $_POST['order_import_btn'] ) ) {
			if ( '' != $_FILES['order_import']['name'] ) {

				$upload     = wp_upload_dir();
				$upload_dir = $upload['basedir'];
				$file       = $_FILES['order_import']['name'];
				$path       = pathinfo($file);
				
				$filename = $path['filename'];
				$ext      = $path['extension'];

				// Checking Extension i.e It will only accept json file
				if ( 'json' == $ext ) {
					$temp_name         = $_FILES['order_import']['tmp_name'];
					$path_filename_ext = $upload_dir . '/imported_order/' . $filename . '.' . $ext;
					
					if (file_exists($path_filename_ext)) {
						echo '<h2> Sorry, file already exists. </h2>';
					} else {
						move_uploaded_file($temp_name, $path_filename_ext);
						echo '<h2> Congratulations..!! File Uploaded Successfully. </h2>';
						
						// Insert file in DB by key 'save_filename'
						$save_filename = get_option('save_filename_for_order', array());
						// if( empty( $save_filename ) && $save_filename == ''){
						// 	$save_filename[] = $path;
						// }
						// else{
							$save_filename[] = $path;
						// }
						update_option('save_filename_for_order', $save_filename);
					}
				} else {
					echo '<h3> No Other File Will Be Accepted Rather Than Json File </h3>';
				}
			}
		}
	}
	

	public function to_create_order(){
		$fetch_file_name = $_POST['json_dropdown_file_name'];

		$upload     = wp_upload_dir();
		$upload_dir = $upload['basedir'];
		$upload_dir = $upload_dir .'/imported_order/'. $fetch_file_name;
		$json_order = json_decode(file_get_contents($upload_dir), true);

		foreach( $json_order['OrderArray']['Order'] as $key => $value ){
			foreach( $value['TransactionArray']['Transaction'] as $Item_key => $Item_value){

				// -------- FETCHING Json File Values -------------- //
					$OrderStatus = $value['OrderStatus'];
					$Email = $Item_value['Buyer']['Email'];
					$ShippingService = $value['ShippingServiceSelected']['ShippingServiceCost'];
					$ship_charge = $value['ShippingServiceSelected']['ShippingServiceCost']['value'];
					foreach ( $Item_value['Taxes']['TaxDetails'] as $value1){
						$TaxValue = $value1['TaxAmount']['value'];
					}
					// echo'<pre>';
					// print_r($a);
					// die();
				// ------------------ END -------------------------- //

				$fetch_sku_from_json_file = $Item_value['Item']['SKU'];
				$user_id = get_current_user_id();
				global $wpdb;
				$check_existing_sku = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM $wpdb->postmeta WHERE  meta_value='%s'", $fetch_sku_from_json_file) );
				$get_product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE  meta_value='%s'", $fetch_sku_from_json_file) );
				
				if( $fetch_sku_from_json_file == $check_existing_sku){

					$address = array(
						'first_name' => $value['ShippingAddress']['Name'],
						'address_1'  => $value['ShippingAddress']['Street1'],
						'city'    	 => $value['ShippingAddress']['CityName'],
						'country'  	 => $value['ShippingAddress']['CountryName'],
						'email'		 => $Email,
						'phone'      => $value['ShippingAddress']['Phone'],
						'postcode'   => $value['ShippingAddress']['PostalCode'],
						'address_2'  => $value['ShippingAddress']['AddressID'],
					);

					// Now we create the order
					$order = wc_create_order();
					
					// The add_product() function below is located in /plugins/woocommerce/includes/abstracts/abstract_wc_order.php
					$order->add_product( get_product( $get_product_id ), 1); // Use the product IDs to add
					
					// Set addresses
					$order->set_address( $address, 'billing' );
					$order->set_address( $address, 'shipping' );

					// Shipping Charges
					$item = new WC_Order_Item_Shipping();
					$item->set_method_title( $ShippingService );
					$item->set_total( $ship_charge );
					// Add item to order and save Shipping Charges.
					$order->add_item( $item );


					// Get the customer country code
					$country_code = $order->get_shipping_country();
					// Set the array for tax calculations
					$calculate_tax_for = array(
						'country'	=> $country_code, 
						'postcode'	=> $value['ShippingAddress']['PostalCode'], 
						'city'		=> $value['ShippingAddress']['CityName']
					);
					// Get a new instance of the WC_Order_Item_Fee Object
					$item_fee = new WC_Order_Item_Fee();
					$item_fee->set_name( "Tax Fee" ); // Generic fee name
					$item_fee->set_amount( $TaxValue ); // Fee amount					$item_fee->set_tax_class( '' ); // default for ''
					$item_fee->set_tax_status( 'taxable' ); // or 'none'
					$item_fee->set_total( $TaxValue ); // Fee amount
					// Calculating Fee taxes
					$item_fee->calculate_taxes( $calculate_tax_for );
					// Add Fee item to the order
					$order->add_item( $item_fee );


					// Calculate totals
					$order->calculate_totals();
					$order->update_status( strtolower($OrderStatus), 'Order created dynamically - ', TRUE);
				}
			}
			die();
		}
	}
}

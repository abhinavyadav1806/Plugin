<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       ced_abhinavyadav@cedcoss.com
 * @since      1.0.0
 *
 * @package    Dropbox_for_prod_image
 * @subpackage Dropbox_for_prod_image/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Dropbox_for_prod_image
 * @subpackage Dropbox_for_prod_image/admin
 * @author     Abhinav <abhinavyadav@cedcoss.com>
 */
class Dropbox_for_prod_image_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
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
		$this->version = $version;

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
		 * defined in Dropbox_for_prod_image_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Dropbox_for_prod_image_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		*/
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/dropbox_for_prod_image-admin.css', array(), $this->version, 'all' );

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
		 * defined in Dropbox_for_prod_image_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Dropbox_for_prod_image_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		*/
		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/dropbox_for_prod_image-admin.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( 'custom_handler', plugin_dir_url( __FILE__ ) . 'js/dropbox_for_prod_image-admin.js', array( 'jquery' ), false );
		wp_localize_script( 'custom_handler', 'custom_object',
			array( 'ajax_url' => admin_url( 'admin-ajax.php' )
		));
	}
	
	
	/**
	 * drpobox_for_product_images
	 * Admin Menu (Dropbox)
	 * @return void
	*/
	public function dropbox_for_product_images() {
		add_menu_page(
			'Dropbox', // menu title
			'Dropbox', // menu name
			'manage_options', // capabality
			'dropbox', 		  // slug
			array( $this,'dropbox_product_images_page_html' ), // Callback function
			'dashicons-archive', // icon
			9 // position
		);
	}

	
	/**
	 * dropbox_product_images_page_html
	 * 
	 * @return void
	*/
	public function dropbox_product_images_page_html() {
		$url_data = isset( $_GET['code'] ) ?  $_GET['code'] : '';
		// print_r($url_data);
		
		if(isset($_POST['api_token'])){
			$app_key = isset( $_POST['app_key'] ) ? $_POST['app_key'] : '';
			$app_secret = isset( $_POST['app_secret'] ) ? $_POST['app_secret'] : '';
	
			$INPUT = array(
				'code'=> $url_data,
				'grant_type'=> 'authorization_code',
				'redirect_uri'=> 'http://localhost/Wordpress_WooCommerce/wp-admin/admin.php?page=dropbox'
			);
			
			$headers[] = 'Authorization: Basic ' . base64_encode( $app_key .':'. $app_secret );
			// var_dump("https://api.dropboxapi.com/oauth2/token?". http_build_query( $INPUT ));
	
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://api.dropboxapi.com/oauth2/token?". http_build_query( $INPUT ));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			$data = curl_exec( $ch );
			if(curl_errno( $ch ))
				print curl_error( $ch );
			else
				curl_close( $ch );

			echo'<br>';
			print_r($data);

			$hold_response = $data;
			$hold_json_response = json_decode($hold_response, true);
			$save_access_token = $hold_json_response['access_token'];
			$save_data = array( 'app_key' => $app_key, 'app_secret' => $app_secret, 'access_token' => $save_access_token);

			update_option('dropbox_api_data', $save_data );	
		}
		
		
		?>
			<h1>Dropbox Menu</h1>
			<h2> * To Generate Access Code only App key is Required</h2>
			<form method="POST" style='margin: 10px;'>
				<div class="dropbox">
					<label>App Key----</label>
					<input type="text" id="app_key" name="app_key">
				</div>

				<div class="dropbox"><br>
					<label> App Secret </label>
					<input type="text" id="app_secret" name="app_secret"><br><br>
				</div>
				<input type="submit" name="generate_code" id="generate_code" value="Generate Access Code"/> </p>
				<input type="submit" name="api_token" id="api_token" value="API access token"/> </p>
			</form>
			<div id='dropbox_data'></div>
		<?php
	}

	
	/**
	 * add_custom_metabox_product_images
	 *
	 * @return void
	*/
	function add_custom_metabox_product_images() {

		$screens = [ 'product' ];
		foreach ( $screens as $screen ) {
			add_meta_box (
				'dropbox_product_images', 					// Unique ID
				'Dropbox Product Images', 					// Box title
				array($this, 'dropbox_custom_box_html'), 	// Content callback, must be of type callable
				$screen 							  		// Post type
			);
		}
	}

	
	/**
	 * dropbox_custom_box_html
	 *
	 * @return void
	*/
	public function dropbox_custom_box_html(){

		// Create settings to accept a file
		?>
			<form method="POST" id="contact" class="image" enctype="multipart/form-data">
				<div class="upload">
					<h3> Choose Image File to upload </h3>
					<input type="file" id="fileUpload" name="fileToUpload" name="name">
					<input type="button" data-target="<?php echo get_the_ID(); ?> " name="image_btn" id="image_btn" value="Upload Images" /> </p>
				</div>
				
				<div id='checkbox'>
						<?php echo '<h3>'.__('Checkbox:').'</h3>'; ?>
						<p class="form-row">
						<label>
							<?php $save = get_post_meta( get_the_ID(), 'save_checkbok_value', true);
								
							?>
							<input type="checkbox" name="checkbox" id="checkbox" <?php if($save='on'){
									$check="checked";
									echo $check;
								}
								else{
									$check = "";
									echo $check ;
								}?> /> 
							<span><?php esc_html_e('checkbox that allows the use of these images to be used as product images');?></span>
						</label>
					</p>
				</div>

			</form>

			<div>
				<?php $meta_data = get_post_meta( get_the_ID(), 'dropbox_image', true); 
					$str_replace  = str_replace('dl=0', 'dl=1', $meta_data);
					foreach($str_replace as $images ){
						// echo'<pre>';
						// print_r($str_replace);
						if(sizeof($str_replace) == 1){
							set_post_thumbnail(get_the_ID(),$str_replace[0]);
						}else{
							if(sizeof($str_replace)>1){
								array_shift($str_replace);
								update_post_meta( get_the_ID(), '_product_image_gallery', implode(',',$str_replace));
							}
						}
						$gallery = get_post_meta(get_the_ID(), '_product_image_gallery', true);
					}
					echo'<img src="'.$gallery.'" alt="Images" width="200" height="200" style="padding:10px;">';
				?>
			</div>
			<div id="alpha"></div>
		<?php 
	}


	public function save_checkbox_on_product_page() {
		$get_value = isset($_POST['checkbox']) ? sanitize_text_field($_POST['checkbox']) : '';
		if ( isset( $get_value ) ) {
			update_post_meta( get_the_ID(), 'save_checkbok_value', esc_attr( $get_value ) );	
		}
	}


	public function to_fetch_data_inputT_field(){
		$option_table = get_option('dropbox_api_data');
		$access_token = $option_table['access_token'];

		// print_r($_FILES);
		$post_id =(int)$_POST['post_id'];
		
		$temp_name = $_FILES['file']['tmp_name'];
		$name = '/make_new/'.$_FILES['file']['name'];

		$fp = fopen($temp_name, 'rb');
		$size = filesize($temp_name);

		$headers = array(
			'Authorization: Bearer '.$access_token,
			'Content-Type: application/octet-stream',
			'Dropbox-API-Arg: {
				"path":"'.$name.'",
				"mode":"add", 
				"autorename": true,
				"mute": false,
				"strict_conflict": false
		}');

		$ch = curl_init('https://content.dropboxapi.com/2/files/upload');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_PUT, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_INFILE, $fp);
		curl_setopt($ch, CURLOPT_INFILESIZE, $size);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		//For display value as array object ends here
		// echo "<pre>";
		// echo($response.'<br/>');
		// echo($http_code.'<br/>');
		curl_close($ch);
		fclose($fp);
	

		if($http_code == 200){

			$headers = array(
				'Authorization: Bearer '.$access_token,
				'Content-Type: application/json',
			);
			$parameters = array("path" => $name, "settings" => array( "requested_visibility" => "public", "audience" => "public" ,"access" => "viewer"));
	
			$curlOptions = array(
				CURLOPT_HTTPHEADER => $headers,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => json_encode($parameters),
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_VERBOSE => true
			);
		
			$ch = curl_init('https://api.dropboxapi.com/2/sharing/create_shared_link_with_settings');
			curl_setopt_array($ch, $curlOptions);
			$response = curl_exec($ch);

			echo'<pre>';
			echo $response;
			curl_close($ch);

			$hold_json_resp = json_decode( $response, true);
			$image_url_response = $hold_json_resp['url'];
			// print_r($image_url_response);
			// var_dump($post_id);

			$save_image_url = get_post_meta($post_id, 'dropbox_image', true);
			if(empty($save_image_url)){
				$save_image_url = array($image_url_response);
			}else{
				$save_image_url[] = $image_url_response;
			}
			// $save_image_url[] = $image_url_response;
			update_post_meta( $post_id, 'dropbox_image', $save_image_url);
		}
		
		wp_die();
	}
}
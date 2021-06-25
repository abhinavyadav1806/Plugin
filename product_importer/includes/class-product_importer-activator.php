<?php

/**
 * Fired during plugin activation
 *
 * @link       ced_abhinavyadav@cedcoss.com
 * @since      1.0.0
 *
 * @package    Product_importer
 * @subpackage Product_importer/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Product_importer
 * @subpackage Product_importer/includes
 * @author     Abhinav <abhinavyadav@cedcoss.com>
 */
class Product_importer_Activator {
	/**
	 * Short Description. (use period)
	 * Long Description.
	 * 
	 * This is to make a new folder "imported_product" in: 
	 * /opt/lampp/htdocs/Wordpress_WooCommerce/wp-content/uploads
	 *
	 * @since    1.0.0
	*/
	public static function activate() {

		// ONLY IMPORT PRODUCT 
		$upload     = wp_upload_dir();
		$upload_dir = $upload['basedir'];
		$upload_dir = $upload_dir . '/imported_product';
		if (! is_dir($upload_dir)) {
		   mkdir( $upload_dir, 0700 );
		}

		// ONLY For ORDER 
		$upload     = wp_upload_dir();
		$upload_dir = $upload['basedir'];
		$upload_dir = $upload_dir . '/imported_order';
		if (! is_dir($upload_dir)) {
		   mkdir( $upload_dir, 0777 );
		}
	}

}

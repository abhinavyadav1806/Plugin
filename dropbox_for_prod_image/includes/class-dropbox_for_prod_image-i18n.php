<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       ced_abhinavyadav@cedcoss.com
 * @since      1.0.0
 *
 * @package    Dropbox_for_prod_image
 * @subpackage Dropbox_for_prod_image/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Dropbox_for_prod_image
 * @subpackage Dropbox_for_prod_image/includes
 * @author     Abhinav <abhinavyadav@cedcoss.com>
 */
class Dropbox_for_prod_image_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'dropbox_for_prod_image',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}

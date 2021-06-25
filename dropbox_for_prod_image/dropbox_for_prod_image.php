<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              ced_abhinavyadav@cedcoss.com
 * @since             1.0.0
 * @package           Dropbox_for_prod_image
 *
 * @wordpress-plugin
 * Plugin Name:       Dropbox_for_Prod_Image
 * Plugin URI:        Cedcoss_Dropbox_for_Prod_Image
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Abhinav
 * Author URI:        ced_abhinavyadav@cedcoss.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       dropbox_for_prod_image
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'DROPBOX_FOR_PROD_IMAGE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-dropbox_for_prod_image-activator.php
 */
function activate_dropbox_for_prod_image() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-dropbox_for_prod_image-activator.php';
	Dropbox_for_prod_image_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-dropbox_for_prod_image-deactivator.php
 */
function deactivate_dropbox_for_prod_image() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-dropbox_for_prod_image-deactivator.php';
	Dropbox_for_prod_image_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_dropbox_for_prod_image' );
register_deactivation_hook( __FILE__, 'deactivate_dropbox_for_prod_image' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-dropbox_for_prod_image.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_dropbox_for_prod_image() {

	$plugin = new Dropbox_for_prod_image();
	$plugin->run();

}
run_dropbox_for_prod_image();

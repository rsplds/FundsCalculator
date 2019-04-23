<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.rishabhsoft.com/
 * @since             1.0.0
 * @package           Fundscape_calculations
 *
 * @wordpress-plugin
 * Plugin Name:       Funds Calculations
 * Plugin URI:        https://www.rishabhsoft.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Rishabh Software
 * Author URI:        https://www.rishabhsoft.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fundscape_calculations
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
define( 'FUNDSCAPE_CALCULATIONS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-fundscape_calculations-activator.php
 */
function activate_fundscape_calculations() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fundscape_calculations-activator.php';
	Fundscape_calculations_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-fundscape_calculations-deactivator.php
 */
function deactivate_fundscape_calculations() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fundscape_calculations-deactivator.php';
	Fundscape_calculations_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_fundscape_calculations' );
register_deactivation_hook( __FILE__, 'deactivate_fundscape_calculations' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-fundscape_calculations.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_fundscape_calculations() {

	$plugin = new Fundscape_calculations();
	$plugin->run();

}
run_fundscape_calculations();

/**
 *  Add Plugin Setting
 *
 */
function platform_settings ( $links ) {
	$mylinks = array(
 		'<a href="' . admin_url( 'edit.php?post_type=platform&page=platform_options' ) . '">Settings</a>',
 	);
	return array_merge( $links, $mylinks );
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'platform_settings' );


/**
 * Inlcude Custom Post Type Platform
 *
 */
require_once plugin_dir_path( __FILE__ ) . 'admin/post_type_platform.php';


/**
 * Inlcude Meta Box for Platform
 *
 */
require_once plugin_dir_path( __FILE__ ) . 'admin/platform_meta.php';


/**
 * Add Option page for Platform Selection.
 *
 */
require_once plugin_dir_path( __FILE__ ) . 'admin/platform_option.php';


/**
 * Add Option page for Platform Selection.
 *
 */
require_once plugin_dir_path( __FILE__ ) . 'public/platform_form_display.php';


/**
 *  Set Admin notice if platform is not available.
 *
 */
function platform_availibility_notice() {

	/* Notice if there is no platforms available. */
	$all_platforms = get_posts( array( 'post_type' => 'platform', 'numberposts' => 1, 'post_status' => 'publish' ) );
	if ( empty( $all_platforms ) ) {
		echo '<div class="notice notice-warning is-dismissible"><p>You don\'t have any active platform. Please Add a platform and make it active.</p></div>';
	}

	/* Notice if Platform is not selected as active. */
	if ( isset($_POST['active_platform']) ) {
		$active_platform_var = $_POST['active_platform'];
		$active_platform_var = !empty( $active_platform_var ) ? $active_platform_var : 'none';

		if ( $active_platform_var == 'none' || $active_platform_var == '' ) {
			echo '<div class="notice notice-warning is-dismissible"><p>Please select a platform as active.</p></div>';
		} else {
			echo '<div class="notice notice-success is-dismissible"><p>Platform has been selected as active.</p></div>';
		}
	} else {
		$active_platform = stripslashes( get_option( 'active_platform' ) );
		if ( empty( $active_platform ) || $active_platform == 'none' ) {
			echo '<div class="notice notice-warning is-dismissible"><p>Please select a platform as active.</p></div>';
		}
	}

}
add_action( 'admin_notices', 'platform_availibility_notice', 99 );


/**
 *  Make Selected Platform Null if platform is deleted.
 *
 */
function platform_trash_function($post_id) {

	$active_platform = stripslashes( get_option( 'active_platform' ) );
    if( !did_action('trash_post') ) {
    	if ( $post_id == $active_platform ) {
    		update_option( 'active_platform', 'none' );
    	}
    }

}
add_action('wp_trash_post','platform_trash_function',1,1);
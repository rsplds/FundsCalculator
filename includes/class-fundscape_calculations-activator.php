<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.rishabhsoft.com/
 * @since      1.0.0
 *
 * @package    Fundscape_calculations
 * @subpackage Fundscape_calculations/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Fundscape_calculations
 * @subpackage Fundscape_calculations/includes
 * @author     Rishabh Software <https://www.rishabhsoft.com/>
 */
class Fundscape_calculations_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		/* Create Post Type on Plugin Activataion */
		if ( post_type_exists('platform') ) {
			echo '<div class="error"><p>Platform post type is allready exists.</p></div>';
			exit();
		} else {
			register_platform();
			flush_rewrite_rules();
		}


		/* Create Database Table */
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate() . " ENGINE = InnoDB";

		$table_name = $wpdb->prefix . 'fundscape_management';
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			platform_id int(11) NOT NULL,
			platform_funds longtext NOT NULL,
			platform_exchange_trades longtext NOT NULL,
			platform_method int(11) NOT NULL,
			created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			updated_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id),
						INDEX platform_id(platform_id)
		) $charset_collate;";
		dbDelta( $sql );


		/* Create Page for Front End */
		 $fundscape_page = array(
			'post_title'    => 'Fundscape Calculations',
			'post_content'  => '[fundscape_form]',
			'post_status'   => 'publish',
			'post_author'   => 1,
			'post_type' => 'page'
		);
		wp_insert_post( $fundscape_page );

	}

}

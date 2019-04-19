<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.rishabhsoft.com/
 * @since      1.0.0
 *
 * @package    Fundscape_calculations
 * @subpackage Fundscape_calculations/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Fundscape_calculations
 * @subpackage Fundscape_calculations/includes
 * @author     Rishabh Software <https://www.rishabhsoft.com/>
 */
class Fundscape_calculations_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'fundscape_calculations',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}

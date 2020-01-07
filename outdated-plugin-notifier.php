<?php
/**
 * The main file for the Outdated Plugin Notifier plugin.
 *
 * @link    https://everlooksolutions.com
 * @package WordPress
 * @since   1.0.0
 *
 * Plugin Name:       Outdated Plugin Notifier
 * Plugin URI:        https://everlooksolutions.com
 * Description:       Plugin to display last modified date for all plugins.
 * Version:           1.0.2
 * Author:            Carl Gross
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       outdated-plugin-notifier
 * Domain Path:       /languages
 * Requires at least: 4.9.0
 * Requires PHP:      7.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * In the plugin admin table, displays a WordPress version number error message in the plugin description column.
 *
 * @since 1.0.0
 *
 * @param array $plugin_meta An array containing meta information for the plugin in-question.
 * @param array $plugin_file A string containing the name of the plugin's folder, and the name of the plugin's main PHP file.
 * @return array Returns an array containing the meta information for the plugin in-question.
 */
function opn_error_wp( $plugin_meta, $plugin_file ) {
	if ( plugin_basename( __FILE__ ) === $plugin_file ) {// In the plugin admin table, for the Outdated Plugin Notifier plugin, display an error message.
		$plugin_meta[] = 'Your version of WordPress does not meet the minimum requirements.  Please upgrade to WordPress version 4.9.0 or later.';
	}
	return $plugin_meta;
}

/**
 * In the plugin admin table, displays a PHP version number error message in the plugin description column.
 *
 * @since 1.0.0
 *
 * @param array $plugin_meta An array containing meta information for the plugin in-question.
 * @param array $plugin_file A string containing the name of the plugin's folder, and the name of the plugin's main PHP file.
 * @return array Returns an array containing the meta information for the plugin in-question.
 */
function opn_error_php( $plugin_meta, $plugin_file ) {
	if ( plugin_basename( __FILE__ ) === $plugin_file ) {// In the plugin admin table, for the Outdated Plugin Notifier plugin, display an error message.
		$plugin_meta[] = 'Your version of PHP does not meet the minimum requirements.  Please upgrade to PHP version 7.0 or later.';
	}
	return $plugin_meta;
}

/**
 * Inserts a new column into the plugin admin table.
 *
 * @since 1.0.0
 *
 * @param array $columns An array containing the header text for each column in the plugin admin table (each array value is a string).
 * @return array Returns an array containing the header text for each column in the plugin admin table (each array value is a string).
 */
function opn_add_column( $columns ) {
	$columns['last_updated'] = __( 'Last Dev Update', 'outdated-plugin-notifier' );
	return $columns;
}

/**
 * Ensures the new column in the plugin admin table is sortable.
 *
 * @since 1.0.0
 *
 * @param array $columns An array containing the header text for each column in the plugin admin table (each array value is a string).
 * @return array Returns an array containing the header text for each column in the plugin admin table (each array value is a string).
 */
function opn_add_sortable_column( $columns ) {
	$columns['last_updated'] = __( 'Last Dev Update', 'outdated-plugin-notifier' );
	return $columns;
}

/**
 * Ensures WordPress and PHP meet minimum requirements, and that all necessary files exist and are included.  If any tests fail, self-deactivate the plugin and display an error message.  If all tests pass, continue with plugin execution.
 *
 * @since 1.0.0
 */
function opn_main() {

	// Confirm user's version of WordPress meets minimum requirement.
	global $wp_version;// Required to use version_compare().

	$opn_minwpver = '4.9.0';
	if ( 1 === version_compare( $opn_minwpver, $wp_version ) ) {// If user's WordPress version is too old, return an error and quit.
		add_filter( 'plugin_row_meta', 'opn_error_wp', 10, 2 );
		deactivate_plugins( plugin_basename( __FILE__ ) );// Self-deactivate the Outdated Plugin Notifier plugin.
		return;
	}

	// Confirm user's version of PHP meets minimum requirement.
	$opn_minphpver = '7.0';
	if ( version_compare( PHP_VERSION, $opn_minphpver, '<' ) ) {
		add_filter( 'plugin_row_meta', 'opn_error_php', 10, 2 );
		deactivate_plugins( plugin_basename( __FILE__ ) );// Self-deactivate the Outdated Plugin Notifier plugin.
		return;
	}

	// Ensures we can use core WordPress function plugins_api() .
	if ( ! function_exists( 'plugins_api' ) ) {
		$opn_include = include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		if ( false === $opn_include ) {
			esc_html_e( 'WordPress file wp-admin/includes/plugin-install.php not found.', 'outdated-plugin-notifier' );
			return;
		}
	}

	if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {// If the plugin has passed all version checks, and remains activated, then execute main code.
		add_filter( 'manage_plugins_columns', 'opn_add_column' );
		add_filter( 'manage_plugins_sortable_columns', 'opn_add_sortable_column' );
		add_action( 'admin_enqueue_scripts', 'opn_enqueue_js' );
	}
}
add_action( 'load-plugins.php', 'opn_main' );// Use the 'load-plugins.php' hook to ensure the function is run only when the admin Plugins screen is loaded.

/**
 * Obtains slug of each installed plugin, then passes it to the necessary JS file which uses them to fetch the 'last updated date' for each plugin.  Slugs of installed plugins are obtained using the same method as manage_plugins_custom_column(), i.e. sanitize the plugin's 'Name' (https://developer.wordpress.org/reference/hooks/manage_plugins_custom_column/).
 *
 * @since 1.0.2
 */
function opn_enqueue_js() {
	$opn_slugs = array();

	// Obtain information about all installed plugins.
	$opn_plugins = get_plugins();

	if ( ! empty( $opn_plugins ) ) {

		// Extract the slug for all installed plugins and write them to an array.
		foreach ( $opn_plugins as $plugin_file => $plugin_data ) {
			$opn_slugs[] = sanitize_title( $plugin_data['Name'] );
		}
		// Enqueue the plugin's main JS file on the page, which will continue execution of the plugin. The JS file will fetch the 'last updated date' of every plugin and display it on screen.  To the JS file, pass the array of plugin slugs.
		wp_enqueue_script( 'opn-js-scripts', plugin_dir_url( __FILE__ ) . 'assets/js/opn-scripts.js', array(), filemtime( plugin_dir_path( __FILE__ ) . 'assets/js/opn-scripts.js' ), true );
		wp_localize_script(
			'opn-js-scripts',
			'opn_ajax_object',
			array(
				'slugs' => $opn_slugs,
			)
		);
	}
}

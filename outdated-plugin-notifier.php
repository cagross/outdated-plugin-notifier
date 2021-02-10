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
 * Version:           1.0.6
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
 * On the plugins admin page, displays a notice if the current version of WordPress does not meet minimum requirements.
 *
 * @since 1.0.5
 */
function opn_wp_ver_check() {

	?>
	<div class="error notice">
		<p><?php esc_html_e( 'Outdated Plugin Notifier:  Cannot activate the plugin.  Your version of WordPress does not meet the minimum requirements.  Please upgrade to WordPress version 4.9.0 or later.', 'outdated-plugin-notifier' ); ?></p>
	</div>
	<?php

}

/**
 * On the plugins admin page, displays a notice if the current version of PHP does not meet minimum requirements.
 *
 * @since 1.0.5
 */
function opn_php_ver_check() {

	?>
	<div class="error notice">
		<p><?php esc_html_e( 'Outdated Plugin Notifier:  Cannot activate the plugin.  Your version of PHP does not meet the minimum requirements.  Please upgrade to PHP version 7.0.0 or later.', 'outdated-plugin-notifier' ); ?></p>
	</div>
	<?php

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
 * Ensures the new column in the plugin admin table is sortable.  As of v1.0.3 this function is no longer called, i.e. the 'Last Updated' column is not sortable.  This is because AJAX changes in v1.0.3 broke sorting.  This may be reinstated in a future release.
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

	global $wp_version;// Required to use version_compare().

	// Confirm user's version of WordPress meets minimum requirement.
	$opn_minwpver = '4.9.0';
	if ( 1 === version_compare( $opn_minwpver, $wp_version ) ) {// If user's WordPress version is too old, return an error and quit.
		/* This suppresses the default 'Plugin Activated' notice displayed on page. */

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		add_action( 'admin_notices', 'opn_wp_ver_check' );
		deactivate_plugins( plugin_basename( __FILE__ ) );// Self-deactivate the Outdated Plugin Notifier plugin.
		return;
	}

	// Confirm user's version of PHP meets minimum requirement.
	$opn_minphpver = '7.0';
	if ( version_compare( PHP_VERSION, $opn_minphpver, '<' ) ) {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		add_action( 'admin_notices', 'opn_php_ver_check' );
		deactivate_plugins( plugin_basename( __FILE__ ) );// Self-deactivate the Outdated Plugin Notifier plugin.
		return;
	}

	if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {// If the plugin has passed all version checks, and remains activated, then execute main code.
		add_filter( 'manage_plugins_columns', 'opn_add_column' );
		add_action( 'admin_enqueue_scripts', 'opn_enqueue_js' );
	}
}
add_action( 'load-plugins.php', 'opn_main' );// Use the 'load-plugins.php' hook to ensure the function is run only when the admin Plugins screen is loaded.

/**
 * Obtains slug, and directory/filename of each installed plugin, then passes it to the necessary JS file (which uses them to fetch and display the 'last updated date' for each plugin).  As of v1.0.2, slugs of installed plugins are assumed to be identical to the plugin's directory name. As of v1.0.3, this function passes the `directory/file name` of each plugin to the JS file.  The JS file then uses that data to target requisite HTML elements on the page.
 *
 * @since 1.0.2
 */
function opn_enqueue_js() {
	$opn_slugs   = array();
	$opn_dirfile = array();

	// Obtain information about all installed plugins.
	$opn_plugins = get_plugins();

	if ( ! empty( $opn_plugins ) ) {

		// Extract the slug for all installed plugins and write them to an array.
		foreach ( $opn_plugins as $plugin_file => $plugin_data ) {
			$slug          = opn_slug( $plugin_file );
			$opn_slugs[]   = $slug;
			$opn_dirfile[] = $plugin_file;
		}
		// Enqueue the plugin's main JS file on the page, which will continue execution of the plugin. The JS file will fetch the 'last updated date' of every plugin and display it on screen.  To the JS file, pass the array of plugin slugs.
		wp_enqueue_script( 'opn-js-scripts', plugin_dir_url( __FILE__ ) . 'assets/js/opn-scripts.js', array(), filemtime( plugin_dir_path( __FILE__ ) . 'assets/js/opn-scripts.js' ), true );
		wp_localize_script(
			'opn-js-scripts',
			'opn_ajax_object',
			array(
				'slugs'     => $opn_slugs,
				'selectors' => $opn_dirfile,
				'locale'    => str_replace( '_', '-', get_user_locale() ), // In the WordPress locale string, replace _ with -, then pass it to the script.  This is because JS locale strings require a hyphen, not an underscore.  Send this to the script so it can display the date in the user's desired locale.
			)
		);
	}
}

/**
 * Accept as a parameter the directory + filename that is returned by get_plugins().  Then return just the plugin slug.
 *
 * @since 1.0.2
 *
 * @param string $file An string containing the directory + filename that is returned by get_plugins().
 * @return string Returns a string containing the plugin's slug.
 */
function opn_slug( $file ) {
	if ( false === strpos( $file, '/' ) ) {
		$name = basename( $file, '.php' );
	} else {
		$name = dirname( $file );
	}
	return $name;// Return the plugin's slug.
}

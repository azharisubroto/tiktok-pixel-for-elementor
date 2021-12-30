<?php
/**
 * GH Tiktok Pixel For Elementor
 *
 * @wordpress-plugin
 * Plugin Name:       GH Tiktok Pixel for Elementor
 * Plugin URI:        https://growthhackerid.comg/plugins/tiktok-pixel-for-elementor/
 * Description:       Track Click or Submit events and conversions for any Elementor widget with Tiktok Pixel
 * Version:           1.0.0
 * Author:            growthhackerid.com
 * Author URI:        https://growthhackerid.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tiktok-pixel-for-elementor
 * Domain Path:       /languages
 */
namespace WPL\Tiktok_Pixel_Tracker_For_Elementor;

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WPL_ELEMENTOR_EVENTS_TRACKER_VERSION', '1.2.9' );
define( 'WPL_ELEMENTOR_EVENTS_TRACKER_SLUG', 'gh_tiktok_pixel_for_elementor' );
define( 'WPL_ELEMENTOR_EVENTS_TRACKER_FILE', __FILE__ );
define( 'WPL_ELEMENTOR_EVENTS_TRACKER_DIR', trailingslashit( __DIR__ ) );
define( 'WPL_ELEMENTOR_EVENTS_TRACKER_URL', plugin_dir_url( WPL_ELEMENTOR_EVENTS_TRACKER_FILE ) );

/**
 * Load gettext translate for our text domain.
 *
 * @since 1.1
 *
 * @return void
 */
function wpl_gh_tiktok_pixel_for_elementor() {

	load_plugin_textdomain( 'tiktok-pixel-for-elementor' );

	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', __NAMESPACE__ . '\wpl_gh_tiktok_pixel_for_elementor_fail_load' );

		return;
	}

	require_once __DIR__ . '/includes/class-options.php';
	require_once __DIR__ . '/includes/class-main.php';

	$options = new Options();
	$main    = new Main( $options );
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\wpl_gh_tiktok_pixel_for_elementor' );

/**
 * Show in WP Dashboard notice about the plugin is not activated.
 *
 * @since 1.1
 *
 * @return void
 */
function wpl_gh_tiktok_pixel_for_elementor_fail_load() {
	$message = sprintf(
		/* translators: 1: Plugin name 2: Elementor */
		esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'tiktok-pixel-for-elementor' ),
		'<strong>' . esc_html__( 'GH Tiktok Pixel For Elementor', 'tiktok-pixel-for-elementor' ) . '</strong>',
		'<strong>' . esc_html__( 'Elementor', 'tiktok-pixel-for-elementor' ) . '</strong>'
	);

	echo '<div class="error"><p>' . $message . '</p></div>';
}

// eol.

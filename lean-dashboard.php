<?php
/**
 * Plugin Name:     Lean Dashboard
 * Plugin URI:      https://github.com/humayun-sarfraz/Lean-Dashboard
 * Description:     Remove unused dashboard widgets, help panels, and streamline the WP admin menus for a lean experience.
 * Version:         1.0.0
 * Author:          Humayun Sarfraz
 * Author URI:      https://github.com/humayun-sarfraz
 * Text Domain:     lean-dashboard
 * Domain Path:     /languages
 * License:         GPLv2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Lean_Dashboard_Core', false ) ) {

	final class Lean_Dashboard_Core {

		/**
		 * Singleton instance
		 *
		 * @var Lean_Dashboard_Core
		 */
		private static $instance;

		/**
		 * Get or create instance
		 *
		 * @return Lean_Dashboard_Core
		 */
		public static function instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
				self::$instance->init_hooks();
			}
			return self::$instance;
		}

		/**
		 * Private constructor
		 */
		private function __construct() {
			add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );
		}

		/**
		 * Hook into WP actions/filters
		 */
		private function init_hooks() {
			// Remove dashboard widgets
			add_action( 'wp_dashboard_setup', [ $this, 'remove_dashboard_widgets' ], 999 );
			// Remove help tabs
			add_action( 'admin_head', [ $this, 'remove_help_tabs' ] );
			// Clean up admin menu
			add_action( 'admin_menu', [ $this, 'prune_admin_menu' ], 999 );
		}

		/**
		 * Load plugin textdomain for translations
		 */
		public function load_textdomain() {
			load_plugin_textdomain(
				'lean-dashboard',
				false,
				dirname( plugin_basename( __FILE__ ) ) . '/languages/'
			);
		}

		/**
		 * Remove default dashboard widgets
		 */
		public function remove_dashboard_widgets() {
			global $wp_meta_boxes;
			$widgets = [
				'dashboard_quick_press',
				'dashboard_activity',
				'dashboard_right_now',
				'dashboard_recent_comments',
				'dashboard_incoming_links',
				'dashboard_plugins',
				'dashboard_primary',
				'dashboard_secondary',
				'dashboard_site_health',
				'welcome_panel',
			];
			foreach ( $widgets as $widget ) {
				unset( $wp_meta_boxes['dashboard']['normal']['core'][ $widget ] );
				unset( $wp_meta_boxes['dashboard']['side']['core'][ $widget ] );
			}
		}

		/**
		 * Remove all contextual help tabs on admin screens
		 */
		public function remove_help_tabs() {
			$screen = get_current_screen();
			if ( is_object( $screen ) ) {
				$screen->remove_help_tabs();
			}
		}

		/**
		 * Prune unneeded admin menu items
		 */
		public function prune_admin_menu() {
			$menus_to_remove = [
				'link-manager.php',
				'edit-comments.php',
				'themes.php',
				'plugins.php',
				'tools.php',
				'options-general.php',
			];
			foreach ( $menus_to_remove as $slug ) {
				remove_menu_page( $slug );
			}
		}
	}

	// Initialize plugin
	Lean_Dashboard_Core::instance();
}

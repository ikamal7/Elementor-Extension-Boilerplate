<?php

/**
 * Plugin Name: Elementor Extension Boilerplate
 * Description: Custom Elementor extension.
 * Plugin URI:  https://kamal.pw/
 * Version:     1.0.0
 * Author:      Kamal H.
 * Author URI:  https: //kamal.pw/
 * Text Domain: elementor-extension-boilerplate
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Main Elementor Test Extension Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
final class Elementor_Extension_Boilerplate {

    /**
     * Plugin Version
     *
     * @var string The plugin version.
     * @since 1.0.0
     */
    const VERSION = '1.0.0';

    /**
     * Minimum Elementor Version
     *
     * @var string Minimum Elementor version required to run the plugin.
     * @since 1.0.0
     */
    const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

    /**
     * Minimum PHP Version
     *
     * @var string Minimum PHP version required to run the plugin.
     * @since 1.0.0
     */
    const MINIMUM_PHP_VERSION = '7.0';

    /**
     * Instance
     *
     * @access private
     * @static
     * @var Elementor_Test_Extension The single instance of the class.
     * @since 1.0.0
     */
    private static $_instance = null;

    /**
     * Instance
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @access public
     * @static
     * @since 1.0.0
     *
     * @return Elementor_Test_Extension An instance of the class.
     */
    public static function instance() {

        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;

    }

    /**
     * Constructor
     *
     * @access public
     * @since 1.0.0
     */
    public function __construct() {

        add_action( 'init', [$this, 'i18n'] );
        add_action( 'plugins_loaded', [$this, 'init'] );

    }

    /**
     * Load Textdomain
     *
     * Load plugin localization files.
     *
     * Fired by `init` action hook.
     *
     * @access public
     * @since 1.0.0
     */
    public function i18n() {

        load_plugin_textdomain( 'elementor-extension-boilerplate' );

    }

    /**
     * Initialize the plugin
     *
     * Load the plugin only after Elementor (and other plugins) are loaded.
     * Checks for basic plugin requirements, if one check fail don't continue,
     * if all check have passed load the files required to run the plugin.
     *
     * Fired by `plugins_loaded` action hook.
     *
     * @access public
     * @since 1.0.0
     */
    public function init() {

        // Check if Elementor installed and activated
        if ( !did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', [$this, 'admin_notice_missing_main_plugin'] );
            return;
        }

        // Check for required Elementor version
        if ( !version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
            add_action( 'admin_notices', [$this, 'admin_notice_minimum_elementor_version'] );
            return;
        }

        // Check for required PHP version
        if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
            add_action( 'admin_notices', [$this, 'admin_notice_minimum_php_version'] );
            return;
        }

        // Add Plugin actions
        add_action( 'elementor/widgets/widgets_registered', [$this, 'init_widgets'] );
        add_action( 'elementor/controls/controls_registered', [$this, 'init_controls'] );
    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have Elementor installed or activated.
     *
     * @access public
     * @since 1.0.0
     */
    public function admin_notice_missing_main_plugin() {

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor */
            esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor-extension-boilerplate' ),
            '<strong>' . esc_html__( 'Elementor Extension', 'elementor-extension-boilerplate' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'elementor-extension-boilerplate' ) . '</strong>'
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required Elementor version.
     *
     * @access public
     * @since 1.0.0
     */
    public function admin_notice_minimum_elementor_version() {

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-extension-boilerplate' ),
            '<strong>' . esc_html__( 'Elementor Extension', 'elementor-extension-boilerplate' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'elementor-extension-boilerplate' ) . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required PHP version.
     *
     * @access public
     * @since 1.0.0
     */
    public function admin_notice_minimum_php_version() {

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-extension-boilerplate' ),
            '<strong>' . esc_html__( 'Elementor Extension', 'elementor-extension-boilerplate' ) . '</strong>',
            '<strong>' . esc_html__( 'PHP', 'elementor-extension-boilerplate' ) . '</strong>',
            self::MINIMUM_PHP_VERSION
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

    }

    /**
     * Init Widgets
     *
     * Include widgets files and register them
     *
     * @access public
     * @since 1.0.0
     */
    public function init_widgets() {

        // Include Widget files
        require_once __DIR__ . '/widgets/test-widget.php';

        // Register widget
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_Test_Widget() );

    }

    /**
     * Init Controls
     *
     * Include controls files and register them
     *
     * @access public
     * @since 1.0.0
     */
    public function init_controls() {

        // Include Control files
        //require_once __DIR__ . '/controls/test-control.php';

        // Register control
        //\Elementor\Plugin::$instance->controls_manager->register_control( 'control-type-', new \Test_Control() );

    }

}

Elementor_Extension_Boilerplate::instance();
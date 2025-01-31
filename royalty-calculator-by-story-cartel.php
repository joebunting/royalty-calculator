<?php
/*
Plugin Name: Royalty Calculator by Story Cartel
Description: Calculate royalties for your book! Just insert the shortcode [royalty_calculator] or [royalty_calculator type="kdp"] into your post or page to use.
Version: 1.0.4
Author: Story Cartel
Text Domain: royalty-calculator
Domain Path: /languages
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ROYALTY_CALCULATOR_VERSION', filemtime(__FILE__));
define('ROYALTY_CALCULATOR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ROYALTY_CALCULATOR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ROYALTY_CALCULATOR_GITHUB_REPO', 'joebunting/royalty-calculator');
define('ROYALTY_CALCULATOR_PLUGIN_SLUG', 'royalty-calculator');

// Include necessary files
require_once ROYALTY_CALCULATOR_PLUGIN_DIR . 'includes/shortcodes.php';
require_once ROYALTY_CALCULATOR_PLUGIN_DIR . 'includes/ajax-handlers.php';

// Enqueue scripts and styles
function royalty_calculator_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('royalty-calculator-js', ROYALTY_CALCULATOR_PLUGIN_URL . 'assets/js/royalty-calculator.js', array('jquery'), ROYALTY_CALCULATOR_VERSION, true);
    wp_enqueue_style('royalty-calculator-css', ROYALTY_CALCULATOR_PLUGIN_URL . 'assets/css/royalty-calculator.css', array(), ROYALTY_CALCULATOR_VERSION);
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css', array(), '5.15.3');
    wp_localize_script('royalty-calculator-js', 'royaltyCalculatorAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'royalty_calculator_enqueue_scripts');

// Activation hook
function royalty_calculator_activate() {
    // Perform any necessary setup on activation
}
register_activation_hook(__FILE__, 'royalty_calculator_activate');

// Deactivation hook
function royalty_calculator_deactivate() {
    // Perform any necessary cleanup on deactivation
}
register_deactivation_hook(__FILE__, 'royalty_calculator_deactivate');

// Load plugin text domain for internationalization
function royalty_calculator_load_textdomain() {
    load_plugin_textdomain('royalty-calculator', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'royalty_calculator_load_textdomain');

// Update function
function royalty_calculator_github_updater() {
    if (!class_exists('WP_GitHub_Updater')) {
        include_once plugin_dir_path(__FILE__) . 'updater.php';
    }
    if (class_exists('WP_GitHub_Updater')) {
        $updater = new WP_GitHub_Updater(array(
            'slug' => 'royalty-calculator',
            'plugin' => plugin_basename(__FILE__),
            'github_repo' => 'joebunting/royalty-calculator',
            'access_token' => '' // Optional: for private repos
        ));
    }
}
add_action('init', 'royalty_calculator_github_updater');
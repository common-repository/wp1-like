<?php
/**
* Plugin Name: WP1 Like
* Description: Simple Like button plugin for posts, pages, custom post types and WooCommerce products
* Plugin URI:  http://wp1.co/wp/blog/
* Author:      WP1
* Author URI:  http://wp1.co
* Version:     1.1
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define('WP1_LIKE_VER','1.1');
define('WP1_LIKE_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
define('WP1_LIKE_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );

add_action('wp_print_scripts', 'wp1_like_register_scripts');
function wp1_like_register_scripts() {
	if ( !is_admin() ) {
		wp_register_script('wp1_like_script', plugins_url('assets/js/wp1-like.js', __FILE__),'',WP1_LIKE_VER,true);
		wp_enqueue_script('wp1_like_script');
	}
	if ( is_admin() ) {
		wp_register_script('wp1_like_color_script', plugins_url('assets/js/jscolor.js', __FILE__),'',WP1_LIKE_VER,true);
		wp_enqueue_script('wp1_like_color_script');
	}
}

add_action( 'admin_enqueue_scripts', 'wp1_like_register_styles' );
add_action('wp_print_styles', 'wp1_like_register_styles');
function wp1_like_register_styles() {
	wp_register_style('fa_styles', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');	// register
	wp_enqueue_style('fa_styles');	// enqueue
}

function wp1_like_inline_styles() {
	wp_enqueue_style( 'custom-style', plugins_url('assets/css/wp1-like.css', __FILE__) );
	$wp1_like_button_color 			= get_option( 'wp1_like_button_color', '002e62' );
	$wp1_like_button_hover_color 	= get_option( 'wp1_like_button_hover_color', 'd11142' );
	$wp1_like_button_disabled_color 	= get_option( 'wp1_like_button_disabled_color', '999999' );
	$custom_css = '.wp1_like_like, .bbp-reply-content .wp1_like_like, .bbp-reply-content a.wp1_like_like { background:#'.$wp1_like_button_color.' !important; }
		.wp1_like_like:hover, .bbp-reply-content .wp1_like_like:hover, .bbp-reply-content a.wp1_like_like:hover{ background:#'.$wp1_like_button_hover_color.' !important; }
		.wp1_like_like.disabled, .bbp-reply-content .wp1_like_like.disabled, .bbp-reply-content a.wp1_like_like.disabled { background:#'.$wp1_like_button_disabled_color.' !important; }';
	wp_add_inline_style( 'custom-style', $custom_css, true );
}
add_action( 'wp_enqueue_scripts', 'wp1_like_inline_styles' );


include( WP1_LIKE_PLUGIN_DIR_PATH . 'inc/wp1-like-functions.php');


// create option page
function wp1_like_admin() {  
    include('wp1-like_option.php');  
}
function wp1_like_admin_actions() {
	add_options_page('WP1 Like', 'WP1 Like', 'manage_options', 'wp1_like_admin', 'wp1_like_admin');
}
add_action('admin_menu', 'wp1_like_admin_actions');

function wp1_like_admin_action_links($links, $file) {
    static $tb_plugin;
    if (!$tb_plugin) {
        $tb_plugin = plugin_basename(__FILE__);
    }
    if ($file == $tb_plugin) {
        $settings_link = '<a href="options-general.php?page=wp1_like_admin">Settings</a>';
        array_unshift($links, $settings_link);
	}
    return $links;
}
add_filter('plugin_action_links', 'wp1_like_admin_action_links', 10, 2);


// set default values
function wp1_like_set_default_values(){
	if( ! get_option( 'wp1_like_button_text' ) )
		update_option( 'wp1_like_button_text', 'Like' );
	if( ! get_option( 'wp1_like_button_color' ) )
		update_option( 'wp1_like_button_color', '002e62' );
	if( ! get_option( 'wp1_like_button_hover_color' ) )
		update_option( 'wp1_like_button_hover_color', 'd11142' );
	if( ! get_option( 'wp1_like_button_disabled_color' ) )
		update_option( 'wp1_like_button_disabled_color', '999999' );
	if( ! get_option( 'wp1_like_post_types' ) )
		update_option( 'wp1_like_post_types', array('post') );
	if( ! get_option( 'wp1_like_show_count' ) )
		update_option( 'wp1_like_show_count', 'Yes' );
	if( ! get_option( 'wp1_like_thumb_icon' ) )
		update_option( 'wp1_like_thumb_icon', 'fa-thumbs-up' );
}


// activation hook
register_activation_hook( __FILE__, 'wp1like_activate' );
function wp1like_activate() {
	wp1_like_set_default_values();
}


// de-activation hook
register_deactivation_hook( __FILE__, 'wp1like_deactivate' );
function wp1like_deactivate() {
	// do deactivation stuff here...
}
?>
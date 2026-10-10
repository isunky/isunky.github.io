<?php
/** Sunky Software theme setup. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sunky_software_enqueue_styles() {
	wp_enqueue_style( 'sunky-software', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'sunky_software_enqueue_styles' );
add_action( 'enqueue_block_editor_assets', 'sunky_software_enqueue_styles' );

function sunky_software_setup() {
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'editor-styles' );
}
add_action( 'after_setup_theme', 'sunky_software_setup' );

function sunky_software_plugin_notice() {
	if ( ! current_user_can( 'activate_plugins' ) || post_type_exists( 'sunky_product' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>请启用「Sunky 产品内容」插件，产品与版本更新区块才能显示。</p></div>';
}
add_action( 'admin_notices', 'sunky_software_plugin_notice' );

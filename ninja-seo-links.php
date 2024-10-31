<?php
/*
Plugin Name: Ninja SEO Links
Description: With Ninja SEO Links plugin you can automatically add strategic links to desired keywords in the entries of your Wordpress blog
Author URI: http://hyliacom.es
Author: Hyliacom
Version: 0.1
*/

include dirname( __FILE__ ).'/includes/database.php';
include dirname( __FILE__ ).'/includes/list.php';
include dirname( __FILE__ ).'/includes/admin.php';
include dirname( __FILE__ ).'/includes/front.php';

register_activation_hook( __FILE__, 'ninja_seo_links_install' );

/* Load JS */
function ninja_seo_links_scripts() {
  wp_enqueue_script('script', plugins_url( 'assets/js/ninja_seo_links.js', __FILE__ ), array ( 'jquery' ), 1.1, true);
  wp_localize_script('script', 'ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php')));
}
add_action( 'wp_enqueue_scripts', 'ninja_seo_links_scripts' );


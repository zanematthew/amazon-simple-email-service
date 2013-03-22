<?php


/**
 * Check if zM Easy Custom Post Types is installed. If it
 * is NOT installed we display an admin notice and stop
 * execution of this plugin, returning.
 */
if ( ! get_option('zm_easy_cpt_version' ) ){
    function zm_aws_admin_notice(){
        echo '<div class="updated"><p>This plugin requires <strong>zM Easy Custom Post Types</strong>.</p></div>';
    }
    add_action('admin_notices', 'zm_aws_admin_notice');
    return;
}


/**
 * Auto load our events.php, events_controller.php, etc.
 * and enqueue our admin and front end asset files.
 */
require_once plugin_dir_path( __FILE__ ) . '../zm-easy-cpt/plugin.php';
if ( ! function_exists( 'zm_easy_cpt_reqiure' ) ) return;
zm_easy_cpt_reqiure( plugin_dir_path(__FILE__) );


function get_string_between($string, $start, $end){
    $string = " ".$string;
    $ini = strpos($string,$start);
    if ($ini == 0) return "";
    $ini += strlen($start);
    $len = strpos($string,$end,$ini) - $ini;
    return substr($string,$ini,$len);
}

function my_enqueue($hook) {

    $pages = array(
        'newsletter_page_newsletter-settings',
        'newsletter_page_recipients',
        'post.php'
        );

    if ( ! in_array( $hook, $pages) ) return;

    wp_enqueue_style( 'bootstrap-style', plugins_url('/vendor/bootstrap/css/bootstrap.min.css', __FILE__ ) );
    wp_enqueue_script( 'script', plugins_url('/assets/newsletterrecipient_admin.js', __FILE__ ) );

    $dependencies[] = 'jquery';

    wp_register_script( 'zm-chosen-script', plugin_dir_url( dirname(__FILE__ ) ) . 'zm-easy-cpt/vendor/chosen/chosen.jquery.min.js', $dependencies );
    wp_register_style( 'zm-chosen-style', plugin_dir_url( dirname(__FILE__ ) ) . 'zm-easy-cpt/vendor/chosen/chosen.css' );

    wp_register_style( 'newsletterrecipient_admin-style', plugin_dir_url( __FILE__ ) . 'assets/newsletterrecipient_admin.css' );

    wp_enqueue_script( 'newsletter_admin-script', plugin_dir_url( __FILE__ ) . 'assets/newsletter_admin.js' );
    wp_register_style( 'newsletter_admin-style', plugin_dir_url( __FILE__ ) . 'assets/newsletter_admin.css' );
}
add_action( 'admin_enqueue_scripts', 'my_enqueue' );
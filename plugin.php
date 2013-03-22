<?php
/**
 * Plugin Name: Amazon Simple Email Service
 * Plugin URI:
 * Description: A WordPress plugin for creating Newsletters and Recipients using Amazons SES (Simple Email Service).
 * Version: 0.1-alpha
 * Author: Zane Matthew
 * Author URI: http://zanematthew.com/
 * License: GPL
 */


/**
 * From the WordPress plugin headers above we derive the version number, and plugin name
 */
$plugin_headers = get_file_data( __FILE__, array( 'Version' => 'Version', 'Name' => 'Plugin Name' ) );


/**
 * We store our plugin data in the following global array.
 * $my_unique_name with your unique name
 */
global $my_unique_name;
$my_unique_name = array();
$my_unique_name['version_key'] = strtolower( str_replace( ' ', '_', $plugin_headers['Name'] ) ) . '_version';
$my_unique_name['version_value'] = $plugin_headers['Version'];


/**
 * When the user activates the plugin we add the version number to the
 * options table as "my_plugin_name_version" only if this is a newer version.
 */
$activate_fn = function(){

    global $my_unique_name;

    if ( get_option( $my_unique_name['version_key'] ) && get_option( $my_unique_name['version_key'] ) > $my_unique_name['version_value'] )
        return;

    update_option( $my_unique_name['version_key'], $my_unique_name['version_value'] );
};
register_activation_hook( __FILE__, $activate_fn );


/**
 * Delete our version number from the database when the plugin is activated.
 */
$deactivate_fn = function (){
    global $my_unique_name;
    delete_option( $my_unique_name['version_key'] );
};
register_deactivation_hook( __FILE__, $deactivate_fn );

require_once 'functions.php';
<?php 
/*
Plugin Name: ALT Lab Patterns in Place website
Plugin URI:  https://github.com/
Description: Plugin that does stuff for the Patterns in Place website. We collapse repeater fields and other nifty stuff.
Version:     1.4
Author:      ALT Lab
Author URI:  http://altlab.vcu.edu
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: my-toolset

*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


add_action('wp_enqueue_scripts', 'prefix_load_scripts');

function prefix_load_scripts() {                           
    $deps = array('jquery');
    $version= '1.1'; 
    $in_footer = true;    
    wp_enqueue_script('acf-patterninplaces-forms-main-js', plugin_dir_url( __FILE__) . 'js/acf-patterninplaces-forms-main.js', $deps, $version, $in_footer); 
   //  wp_enqueue_style( 'prefix-main-css', plugin_dir_url( __FILE__) . 'css/prefix-main.css');
}

/**
* Collapse ACF Repeater by default
*/
// add_action('acf/input/admin_head', 'wpster_acf_repeater_collapse');
// function wpster_acf_repeater_collapse() {


	// jQuery(function($) {	
   //    $('#pattern-desc .acf-button').click(function() {
   //      console.log('clicky click');
   //      $('.acf-repeater .acf-row:not(:last-of-type)').addClass('-collapsed');
        
   //    });
	// });
// </script>
// }


//LOGGER -- like frogger but more useful

if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}
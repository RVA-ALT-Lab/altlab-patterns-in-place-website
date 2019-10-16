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


add_action('wp_enqueue_scripts', 'patterninplaces_load_scripts');

function patterninplaces_load_scripts() {                           
    $deps = array('jquery');
    $version= '1.1'; 
    $in_footer = true;    
    wp_enqueue_script('acf-patterninplaces-forms-main-js', plugin_dir_url( __FILE__) . 'js/acf-patterninplaces-forms-main.js', $deps, $version, $in_footer); 
   //  wp_enqueue_style( 'prefix-main-css', plugin_dir_url( __FILE__) . 'css/prefix-main.css');
}

function patterninplaces_handle_form_submission( $form, $fields, $args ) {
    $first_name = $fields[0]['value'];
    $last_name = $fields[1]['value'];
    $email = $fields[2]['value'];
    write_log($fields);
    $region = $fields[3]['value'];
    $slug_region = sanitize_title($region);
    $region_cat_id = get_category_by_slug($slug_region)->term_id;
    $pattern_cat_id = get_category_by_slug('pattern')->term_id;
    // write_log($region);
    // write_log($slug_region);
    // write_log($region_cat_id);
    $data = $fields[4]['value']; 
    //write_log($data);   
   foreach ($data as $key => $item) {
//GET DATA
      //write_log($item);
      $image = $item['pattern_image']['ID'];
      $pattern_description = $item['pattern_description'];
      $general_location = $item['pattern_location'];
      //$address = $row['pattern_location']['address'];
      $lat = $item['pattern_location']['lat'];
      $long = $item['pattern_location']['lng'];
      $material = $item['pattern_materiality'];
      $notes = $item['pattern_notes'];

      //MAKE POSTS
        $args = array(
            'post_title' => wp_strip_all_tags('Pattern from ' . $region),
            'post_status'   => 'publish',
            'post_category' => array($region_cat_id, $pattern_cat_id),
            'tags_input' => $pattern_description . ',' . $material,
            'post_content' => $notes,
        );
        $post_id = wp_insert_post($args);
        set_post_thumbnail($post_id, $image);
        //email field_5da5e47d95f12
        //location field_5da5e48895f13
        update_field('submitter_email', $email, $post_id);
        update_field('location', $general_location, $post_id);
        update_field('first_name',$first_name, $post_id);
        update_field('last_name',$last_name, $post_id);
    }
       

}
 
add_action( 'af/form/submission', 'patterninplaces_handle_form_submission', 10, 3 );
 
//happy little logging function
if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}


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

function patternsinplace_attribution($content){
   global $post;
   $first = get_field('first_name', $post->ID);
   $last = get_field('last_name', $post->ID);
    if (in_category('pattern',$post->ID)){
     return $content . '<div class="attribution">' . $first . ' ' . $last . '</div>';
    } else {
      return $content;
    }
}

add_filter( 'the_content', 'patternsinplace_attribution');

function patternsinplace_make_map($content){
    global $post;
    if (in_category('pattern',$post->ID)){
      $address_field = get_field('location', $post->ID);
      $lat = $address_field['lat'];
      $long = $address_field['lng'];
      $encoded_address = $lat.','.$long;
      $html = '
          <iframe
              width="100%"
              height="450"
              frameborder="0" style="border:0"
              src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBct6oqg7fdGapy-iaC7Bnq3GCsaOQuG0c&q=' . $encoded_address . '" allowfullscreen>
          </iframe>';
      return $content . $html;
    } else {
      return $content;
    }
}

add_filter( 'the_content', 'patternsinplace_make_map');



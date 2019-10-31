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
    //write_log($fields);
    $region = $fields[3]['value'];
    $slug_region = sanitize_title($region);
    $region_cat_id = get_category_by_slug($slug_region)->term_id;
    $pattern_cat_id = get_category_by_slug('pattern')->term_id;
    $all_cats =[$region_cat_id, $pattern_cat_id];
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
      write_log($pattern_description);
      patternsinplace_return_cat_ids($pattern_description, $all_cats);
      $general_location = $item['pattern_location'];
      //$address = $row['pattern_location']['address'];
      $lat = $item['pattern_location']['lat'];
      $long = $item['pattern_location']['lng'];
      $material = $item['pattern_materiality'];
       patternsinplace_return_cat_ids($material, $all_cats);
      write_log($material);
      $notes = $item['pattern_notes'];
      //MAKE POSTS
        $args = array(
            'post_title' => wp_strip_all_tags($last_name . ' / ' . $region),
            'post_status'   => 'publish',
            'post_category' => $all_cats,
            'tags_input' => $last_name,
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
    patternsinplace_send_mails_on_publish( $post_id, $first_name, $last_name );   

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
     return $content . '<div class="attribution">Submitted by ' . $first . ' ' . $last . '</div>';
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


function patternsinplace_return_cat_ids($array, &$destination){
  //  write_log($array);
  foreach ($array as $key => $item) {
    //write_log($item);
    $clean_name = sanitize_title($item);
    //write_log(get_category_by_slug($clean_name)->term_id);
    $new_id = get_category_by_slug($clean_name)->term_id;
    array_push($destination, $new_id);
  }
}


function patternsinplace_send_mails_on_publish( $post_id, $first_name, $last_name ){

    $post = get_post($post_id);   
    $admins = get_users( array ( 'role' => 'administrator' ) );
    $emails      = array ();

    foreach ( $admins as $subscriber )
        $emails[] = $subscriber->user_email;
        $url = get_permalink( $post );
        $body = $first_name . ' ' . $last_name . ' made a post at ' . $url;
        
    );

    wp_mail( $emails, 'New post in Patterns in Place', $body );
}

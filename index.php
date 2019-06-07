<?php 
/*
Plugin Name: ALT Lab Secret Journal
Plugin URI:  https://github.com/
Description: For a custom post type that's super secure and secret - visible only to the user and the admin(s)
Version:     1.0
Author:      ALT Lab
Author URI:  http://altlab.vcu.edu
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: my-toolset

*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


// add_action('wp_enqueue_scripts', 'prefix_load_scripts');

// function prefix_load_scripts() {                           
//     $deps = array('jquery');
//     $version= '1.0'; 
//     $in_footer = true;    
//     wp_enqueue_script('prefix-main-js', plugin_dir_url( __FILE__) . 'js/prefix-main.js', $deps, $version, $in_footer); 
//     wp_enqueue_style( 'prefix-main-css', plugin_dir_url( __FILE__) . 'css/prefix-main.css');
// }


//journal custom post type

// Register Custom Post Type journal
// Post Type Key: journal

function create_journal_cpt() {

  $labels = array(
    'name' => __( 'Journals', 'Post Type General Name', 'textdomain' ),
    'singular_name' => __( 'Journal', 'Post Type Singular Name', 'textdomain' ),
    'menu_name' => __( 'Journal', 'textdomain' ),
    'name_admin_bar' => __( 'Journal', 'textdomain' ),
    'archives' => __( 'Journal Archives', 'textdomain' ),
    'attributes' => __( 'Journal Attributes', 'textdomain' ),
    'parent_item_colon' => __( 'Journal:', 'textdomain' ),
    'all_items' => __( 'All Journals', 'textdomain' ),
    'add_new_item' => __( 'Add New Journal', 'textdomain' ),
    'add_new' => __( 'Add New', 'textdomain' ),
    'new_item' => __( 'New Journal', 'textdomain' ),
    'edit_item' => __( 'Edit Journal', 'textdomain' ),
    'update_item' => __( 'Update Journal', 'textdomain' ),
    'view_item' => __( 'View Journal', 'textdomain' ),
    'view_items' => __( 'View Journals', 'textdomain' ),
    'search_items' => __( 'Search Journals', 'textdomain' ),
    'not_found' => __( 'Not found', 'textdomain' ),
    'not_found_in_trash' => __( 'Not found in Trash', 'textdomain' ),
    'featured_image' => __( 'Featured Image', 'textdomain' ),
    'set_featured_image' => __( 'Set featured image', 'textdomain' ),
    'remove_featured_image' => __( 'Remove featured image', 'textdomain' ),
    'use_featured_image' => __( 'Use as featured image', 'textdomain' ),
    'insert_into_item' => __( 'Insert into journal', 'textdomain' ),
    'uploaded_to_this_item' => __( 'Uploaded to this journal', 'textdomain' ),
    'items_list' => __( 'Journal list', 'textdomain' ),
    'items_list_navigation' => __( 'Journal list navigation', 'textdomain' ),
    'filter_items_list' => __( 'Filter Journal list', 'textdomain' ),
  );
  $args = array(
    'label' => __( 'journal', 'textdomain' ),
    'description' => __( '', 'textdomain' ),
    'labels' => $labels,
    'menu_icon' => '',
    'supports' => array('title', 'editor', 'revisions', 'author', 'custom-fields', 'thumbnail',),
    'taxonomies' => array(),
    'public' => false,//become super secret if false
    'show_ui' => true,
    'show_in_menu' => true,
    'menu_position' => 5,
    'show_in_admin_bar' => false,
    'show_in_nav_menus' => true,
    'can_export' => false,
    'has_archive' => false,
    'hierarchical' => false,
    'exclude_from_search' => true,
    'show_in_rest' => false,
    'publicly_queryable' => false,//part of the super secret trifecta 
    'capability_type' => 'post',
    'menu_icon' => 'dashicons-lock',
  );
  register_post_type( 'journal', $args );
  
  // flush rewrite rules because we changed the permalink structure
  global $wp_rewrite;
  $wp_rewrite->flush_rules();
}
add_action( 'init', 'create_journal_cpt', 0 );



function secure_the_journal($content) {
  // assuming you have created a page/post entitled 'debug'
  if ($GLOBALS['post']->post_type == 'journal') {
  	$current_user = get_current_user_id();
  	$author = $GLOBALS['post']->post_author;
   	if ($current_user === $author || current_user_can('administrator')){
	   	return $content;
	   } else {
	   	return '<h2>This content is private.</h2> <p>You will need to be the content owner and <a href="'.wp_login_url().'">logged in</a> to access it.</p>';
	   }
  }
  // otherwise returns the database content
  return $content;
}

add_filter( 'the_content', 'secure_the_journal' );



//hide posts from other authors for author level users __ even if editor can't edit the other posts
function posts_for_current_author($query) {
    global $pagenow;
    if( 'edit.php' != $pagenow || !$query->is_admin )
        return $query;
 
    if( !current_user_can( 'administrator' ) ) {
        global $user_ID;
        $query->set('author', $user_ID );
    }
    return $query;
}
add_filter('pre_get_posts', 'posts_for_current_author');
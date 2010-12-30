<?php
/* 
Plugin Name: Cornerstone
Plugin URI: http://archetyped.com/tools/cornerstone/
Description: Enhanced content management for Wordpress
Version: 0.5b
Author: Archetyped
Author URI: http://archetyped.com
*/

/**
 * @package Cornerstone 
 */
require_once('model.php');
$cnr =& new Cornerstone();

/* Template tags */

/**
 * Outputs formatted page title for current page
 * @return void
 * @param string|array Arguments for formatting page title
 * May be an associative array or querystring-style list of arguments
 */
function cnr_page_title($args = '') {
	global $cnr;
	$cnr->page_title($args);
}

/**
 * Outputs feed links based on current page
 * @return void
 */
function cnr_the_feed_links() {
	global $cnr;
	$cnr->feeds->the_links();
}

/*-** Child Content **-*/

/**
 * Checks if current post/page has children elements
 * 
 * @return bool TRUE if post/page has children, FALSE otherwise
 */
function cnr_have_children() {
	global $cnr;
	return $cnr->post_children_collection->has();
}

/**
 * Prepares next child post for output to page
 * 
 * @return void 
 */
function cnr_next_child() {
	global $cnr;
	$cnr->post_children_collection->next();
}

/**
 * Returns number of children in current request
 * May not return total number of existing children (e.g. if output is paged, etc.)
 * @return int Number of children returned in current request 
 */
function cnr_children_count() {
	global $cnr;
	return $cnr->post_children_collection->count();	
}

/**
 * Returns total number of existing children
 * @return int Total number of children
 */
function cnr_children_found() {
	global $cnr;
	return $cnr->post_children_collection->found();
}

/**
 * Returns total number of pages of children
 * Based on 'posts_per_page' option
 * @return int Maximum number of pages
 */
function cnr_children_max_num_pages() {
	global $cnr;
	return $cnr->post_children_collection->max_num_pages();
}

/**
 * Checks if current child item is the first child item
 * @return bool TRUE if current item is first, FALSE otherwise
 */
function cnr_is_first_child() {
	global $cnr;
	return $cnr->post_children_collection->is_first();
}

/**
 * Checks if current child item is the last child item
 * @return bool TRUE if current item is last, FALSE otherwise
 */
function cnr_is_last_child() {
	global $cnr;
	return $cnr->post_children_collection->is_last();
}

/*-** Post-Specific **-*/

/**
 * Checks if post has content to display
 * @param object $post (optional) Post object
 * @return bool TRUE if post has content, FALSE otherwise
 */
function cnr_has_content($post = null) {
	global $cnr;
	return $cnr->post_has_content($post);
}

	/* Section */
	
/**
 * Retrieves the post's section data 
 * @return string post's section data 
 * @param string $type (optional) Type of data to return (Default: ID)
 * 	Possible values:
 * 	ID		Returns the ID of the section
 * 	name	Returns the name of the section
 */
function cnr_get_the_section($type = 'ID') {
	return CNR_Post::get_section($type);
}

/**
 * Prints the post's section data
 * @param string $type (optional) Type of data to return (Default: ID)
 * @see cnr_get_the_section()
 */
function cnr_the_section($type = 'ID') {
	CNR_Post::the_section($type);
}

<?php

require_once 'includes/class.base.php';
require_once 'includes/class.content-types.php';
require_once 'includes/class.media.php';
require_once 'includes/class.posts.php';
require_once 'includes/class.structure.php';
require_once 'includes/class.feeds.php';

/**
 * @package Cornerstone
 */
class Cornerstone extends CNR_Base {
	/* Variables */
	
	/* Featured Content variables */
	
	/**
	 * @var string Category slug value that denotes a "featured" post
	 * @see posts_featured_cat()
	 * @todo Remove need for this property
	 */
	var $posts_featured_cat = "feature";

	/**
	 * Featured posts container
	 * @var CNR_Post_Query
	 */
	var $posts_featured = null;
	
	/* Children Content Variables */
	
	/**
	 * Children posts
	 * @var CNR_Post_Query
	 */
	var $post_children_collection = null;
	
	/* Instance Variables */
	
	/**
	 * Structure instance
	 * @var CNR_Structure
	 */
	var $structure = null;
	
	/**
	 * Media instance
	 * @var CNR_Media
	 */
	var $media = null;
	
	/**
	 * Post class instance
	 * @var CNR_Post
	 */
	var $post = null;
	
	/**
	 * Feeds instance
	 * @var CNR_Feeds
	 */
	var $feeds = null;
	
	/* Constructor */
	
	function Cornerstone()  {
		$this->__construct();
	}
							
	function __construct() {
		//Parent Constructor
		parent::__construct();
		
		//Special Queries
		$this->posts_featured =& new CNR_Post_Query( array( 'category' => $this->posts_featured_get_cat_id(), 'numberposts' => 4 ) );
		$this->post_children_collection =& new CNR_Post_Query();

		$this->post =& new CNR_Post();
		$this->post->init();

		//Register hooks
		$this->register_hooks();
		
		//Init class instances
		$this->structure =& new CNR_Structure();
		$this->structure->init();
		
		$this->media =& new CNR_Media();
		$this->media->init();
		
		$this->feeds =& new CNR_Feeds();
		$this->feeds->init();
	}
	
	function register_hooks() {
		/* Register Hooks */
		parent::register_hooks();
		
		//Admin
			//Initialization
		add_action('admin_init', $this->m('admin_init'));
			//Head
		add_action('admin_enqueue_scripts', $this->m('admin_add_styles'));
		add_action('admin_enqueue_scripts', $this->m('admin_add_scripts'));
		
		//Posts
		add_filter('wp_list_pages', $this->m('post_section_highlight'));
	}
	
	/* Methods */

	/*-** Admin **-*/
	
	/**
	 * Performs specified operations when admin is initialized
	 * @return void
	 */
	function admin_init() {
		$this->admin_register_scripts();
	}
	
	/**
	 * Registers scripts for admin pages
	 */
	function admin_register_scripts() {
		wp_register_script( $this->add_prefix('core'), $this->util->get_file_url('js/cnr.js'), array('jquery') );
		wp_register_script( $this->add_prefix('admin'), $this->util->get_file_url('js/cnr_admin.js'), array('jquery') );
		wp_register_script( $this->add_prefix('inline-edit-post'), $this->util->get_file_url('js/inline-edit-post.js'), array('jquery', 'inline-edit-post') );
	}
	
	/**
	 * Registers CSS styles for admin pages
	 */
	function admin_add_styles() {
		//Define file properties
		$file_base = 'admin_styles';
		$handle = $this->add_prefix($file_base);
		$file_url = $this->util->get_file_url('css/' . $file_base . '.css');
		
		//Enqueue style
		wp_enqueue_style($handle,$file_url);
	}
	
	/**
	 * Adds external javascript files to admin header
	 * @return void
	 */
	function admin_add_scripts() {
		//Default admin scripts
		wp_enqueue_script($this->add_prefix('core'));
		wp_enqueue_script($this->add_prefix('admin'));
		//Edit Posts
		if ( is_admin() && $this->util->is_file('edit.php') ) {
			wp_enqueue_script( $this->add_prefix('inline-edit-post') );
		}
	}
	
	/*-** Child Content **-*/
	
	/**
	 * Gets children posts of specified page and stores them for later use
	 * This method hooks into 'the_posts' filter to retrieve child posts for any single page retrieved by WP
	 * @return array $posts Posts array (required by 'the_posts' filter) 
	 * @param array $posts Array of Posts (@see WP_QUERY)
	 */
	function post_children_get($posts) {
		//Global variables
		global $wp_query;
		
		//Reset post children collection
		$this->post_children_collection->init();
		
		//Stop here if post is not a page
		if ( ! is_page() || empty($posts) )
			return $posts;

		//Get children posts
		$post =& $posts[0];
		$this->post_children_collection =& CNR_Post::get_children($post);
		
		//Return posts (required by filter)
		return $posts;
	}
	
	/*-** Template **-*/
	
	/**
	 * Builds Page title for current Page/Content
	 * @return string Page title 
	 * @param array|string $args (optional) Parameters for customizing Page title
	 */
	function page_title_get($args = '') {
		$defaults = array(
							'sep'	=>	' &lsaquo; ',
							'base'	=>	get_bloginfo('title')
							);
		$args =  wp_parse_args($args, $defaults);
		$title_parts = array();
		$page_title = '';
		//Add Site Title
		$title_parts[] = $args['base'];
		
		$body_rule = '';
		$secondary = 'secondary';
		if ( !is_home() ) { //Evaluate all non-home page content
			if ( is_page() || is_single() ) { //Section page or Post
				global $post;
				if ($post->post_parent != 0 && ($parent = get_the_title($post->post_parent)) && !!$parent) {
					$title_parts[] = $parent;
				}
				$title_parts[] = get_the_title();
			} elseif ( is_archive() ) { //Archive Pages
				if ( is_date() ) {
					$format = 'F Y'; //Month Format (Default)
					$prep = 'in';
					if ( is_day() ) { //Day archive
						$format = 'F j, Y';
						$prep = 'on';
					} elseif ( is_year() ) { //Year archive
						$format = 'Y';
					}
					$title_parts[] = 'Content published ' . $prep . ' ' . get_the_time($format);
				} elseif ( is_tag() ) { //Tag Archive
					$title_parts[] = single_tag_title('', false);
				} elseif ( is_category() ) { //Category Archive
					$title_parts[] = single_cat_title('', false);
				}
			} elseif ( is_search() ) { //Search Results
				$title_parts[] = 'Search Results for: ' . esc_attr(get_search_query());
			} elseif ( is_404() ) { //404 Page
				$title_parts[] = 'Page Not Found';
			}
		} elseif ( is_paged() ) { //Default title for archive pages
			$title_parts[] = 'All Content';
		}
		
		//Build title based on parts
		$title_parts = array_reverse($title_parts);
		
		//Add Page Number to Title
		if ( is_paged() && !empty($title_parts[0]) ) {
			$title_parts[0] .= ' (Page ' . get_query_var('paged') . ')';
		}
		
		for ( $x = 0; $x < count($title_parts); $x++ ) {
			$page_title .= $title_parts[$x];
			if ( $x < (count($title_parts) - 1) )
				$page_title .= $args['sep'];
		}
		
		return $page_title;
	}
	
	/**
	 * Outputs formatted page title
	 * @return void
	 * @param array|string $args (optional) Parameters for customizing Page title
	 */
	function page_title($args = '') {
		echo $this->page_title_get($args);
	}
	
/*-** Featured Content **-*/
	
	/**
	 * Retrieves featured post category object
	 * @return object Featured post category object
	 * @todo integrate into CNR_Post_Query
	 */
	function posts_featured_get_cat() {
		static $cat = null;
		
		//Only fetch category object if it hasn't already been retrieved
		if (is_null($cat) || !is_object($cat)) {
			//Retrieve category object
			if (is_int($this->posts_featured_cat)) {
				$cat = get_category((int)$this->posts_featured_cat);
			}
			elseif (is_string($this->posts_featured_cat) && strlen($this->posts_featured_cat) > 0) {
				$cat = get_category_by_slug($this->posts_featured_cat);
			}
		}
		
		return $cat;
	}
	
	/**
	 * @todo integrate into CNR_Post_Query
	 */
	function posts_featured_get_cat_id() {
		static $id = '';
		if ($id == '') {
			$cat = $this->posts_featured_get_cat();
			if (!is_null($cat) && is_object($cat) && $this->util->property_exists($cat, 'cat_ID'))
				$id = $cat->cat_ID;
		}
		return $id;
	}
	
	/**
	 * Highlights post's section in site structure output
	 * Called when site structure is output by wp_list_pages()
	 * Adds additional CSS classes to current section
	 * @param string $output Site structure in HTML
	 * @return string Site structure HTML with current section highlighted
	 * @todo Review for deletion/relocation
	 */
	function post_section_highlight($output) {
		$class_current = 'current_page_item';
		$class_item_pre = 'page-item-';
		
		global $post;

		//Check if no pages are marked as 'current' yet
		//Also make sure current page is not home page (no links should be marked as current)
		if ( is_singular() && $post && stripos($output, $class_current) === false ) {
			//Get all parents of current post
			$parents = CNR_Post::get_parents($post, 'id');
			
			//Add current post to array
			$parents[] = $post->ID;
			//Reverse array so we start with the current post first
			$parents = array_reverse($parents);
			
			//Iterate through current posts's parents to highlight all parents found
			foreach ( $parents as $page ) {
				$class_item = $class_item_pre . $page;
				$class_pattern = '/\b' . $class_item . '\b/i';
				$class_replace = $class_item . ' ' . $class_current;
				$output = preg_replace($class_pattern, $class_replace, $output);
			}
		}

		return $output;
	}
	
	/**
	 * Checks if post has content to display
	 * @param object $post (optional) Post object
	 * @return bool TRUE if post has content, FALSE otherwise
	 * @todo Review for deletion/relocation
	 */
	function post_has_content($post = null) {
		if ( !$this->util->check_post($post) )
			return false;
		if ( isset($post->post_content) && trim($post->post_content) != '' )
			return true;
		return false;
	}
}

?>

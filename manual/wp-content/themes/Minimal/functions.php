<?php
update_option( 'siteurl', 'http://biztracc.com' );
update_option( 'home', 'http://biztracc.com' );

add_action( 'after_setup_theme', 'et_setup_theme' );

if ( ! function_exists( 'et_setup_theme' ) ){

	function et_setup_theme(){

		global $themename, $shortname, $default_colorscheme;

		$themename = "Minimal";

		$shortname = "minimal";

		$default_colorscheme = "Grey";



		$template_dir = get_template_directory();



		require_once($template_dir . '/epanel/custom_functions.php');



		require_once($template_dir . '/includes/functions/comments.php');



		require_once($template_dir . '/includes/functions/sidebars.php');



		load_theme_textdomain('Minimal',$template_dir.'/lang');



		require_once($template_dir . '/epanel/core_functions.php');



		require_once($template_dir . '/epanel/post_thumbnails_minimal.php');



		include($template_dir . '/includes/widgets.php');



		add_action( 'pre_get_posts', 'et_home_posts_query' );



		add_action( 'et_epanel_changing_options', 'et_delete_featured_ids_cache' );

		add_action( 'delete_post', 'et_delete_featured_ids_cache' );

		add_action( 'save_post', 'et_delete_featured_ids_cache' );

	}

}



add_action('wp_head','et_portfoliopt_additional_styles',100);

function et_portfoliopt_additional_styles(){ ?>

	<style type="text/css">

		#et_pt_portfolio_gallery { margin-left: -11px; }

		.et_pt_portfolio_item { margin-left: 23px; }

		.et_portfolio_small { margin-left: -39px !important; }

		.et_portfolio_small .et_pt_portfolio_item { margin-left: 35px !important; }

		.et_portfolio_large { margin-left: -20px !important; }

		.et_portfolio_large .et_pt_portfolio_item { margin-left: 14px !important; }

	</style>

<?php }



function register_main_menus() {

	register_nav_menus(

		array(

			'primary-menu' => __( 'Primary Menu', 'Minimal' ),

			'footer-menu' => __( 'Footer Menu', 'Minimal' )

		)

	);

};

if (function_exists('register_nav_menus')) add_action( 'init', 'register_main_menus' );



/**

 * Gets featured posts IDs from transient, if the transient doesn't exist - runs the query and stores IDs
 */

function et_get_featured_posts_ids(){

	if ( false === ( $et_featured_post_ids = get_transient( 'et_featured_post_ids' ) ) ) {

		$featured_query = new WP_Query( apply_filters( 'et_featured_post_args', array(

			'posts_per_page'	=> (int) et_get_option( 'minimal_featured_num' ),

			'cat'				=> (int) get_catId( et_get_option( 'minimal_feat_cat' ) )

		) ) );



		if ( $featured_query->have_posts() ) {

			while ( $featured_query->have_posts() ) {

				$featured_query->the_post();



				$et_featured_post_ids[] = get_the_ID();

			}



			set_transient( 'et_featured_post_ids', $et_featured_post_ids );

		}



		wp_reset_postdata();

	}



	return $et_featured_post_ids;

}



/**

 * Filters the main query on homepage

 */

function et_home_posts_query( $query = false ) {

	/* Don't proceed if it's not homepage or the main query */

	if ( ! is_home() || ! is_a( $query, 'WP_Query' ) || ! $query->is_main_query() ) return;



	/* Set the amount of posts per page on homepage */

	$query->set( 'posts_per_page', (int) et_get_option( 'minimal_homepage_posts', '6' ) );



	/* Exclude categories set in ePanel */

	$exclude_categories = et_get_option( 'minimal_exlcats_recent', false );

	if ( $exclude_categories ) $query->set( 'category__not_in', array_map( 'intval', et_generate_wpml_ids( $exclude_categories, 'category' ) ) );



	/* Exclude slider posts, if the slider is activated, pages are not featured and posts duplication is disabled in ePanel  */

	if ( 'on' == et_get_option( 'minimal_featured', 'on' ) && 'false' == et_get_option( 'minimal_use_pages', 'false' ) && 'false' == et_get_option( 'minimal_duplicate', 'on' ) )

		$query->set( 'post__not_in', et_get_featured_posts_ids() );

}



/**

 * Deletes featured posts IDs transient, when the user saves, resets ePanel settings, creates or moves posts to trash in WP-Admin

 */

function et_delete_featured_ids_cache(){

	if ( false !== get_transient( 'et_featured_post_ids' ) ) delete_transient( 'et_featured_post_ids' );

}



if ( ! function_exists( 'et_list_pings' ) ){

	function et_list_pings($comment, $args, $depth) {

		$GLOBALS['comment'] = $comment; ?>

		<li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?> - <?php comment_excerpt(); ?>

	<?php }

}



function et_epanel_custom_colors_css(){

	global $shortname; ?>



	<style type="text/css">

		body { color: #<?php echo esc_html(get_option($shortname.'_color_mainfont')); ?>; }

		#content-area a { color: #<?php echo esc_html(get_option($shortname.'_color_mainlink')); ?>; }

		ul.nav li a { color: #<?php echo esc_html(get_option($shortname.'_color_pagelink')); ?>; }

		ul.nav > li.current_page_item > a, ul#top-menu > li:hover > a, ul.nav > li.current-cat > a { color: #<?php echo esc_html(get_option($shortname.'_color_pagelink_active')); ?>; }

		h1, h2, h3, h4, h5, h6, h1 a, h2 a, h3 a, h4 a, h5 a, h6 a { color: #<?php echo esc_html(get_option($shortname.'_color_headings')); ?>; }



		#sidebar a { color:#<?php echo esc_html(get_option($shortname.'_color_sidebar_links')); ?>; }

		div#footer { color:#<?php echo esc_html(get_option($shortname.'_footer_text')); ?> }

		#footer a, ul#bottom-menu li a { color:#<?php echo esc_html(get_option($shortname.'_color_footerlinks')); ?> }

	</style>







<?php }



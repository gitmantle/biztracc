<?php
/*
Plugin Name: Nice Navigation
Plugin URI: http://eskapism.se/code-playground/nice-navigation/
Description: Adds a widget that makes your page list expandable/collapsible with a nice slide animation effect
Author: Pär Thernström
Version: 1.6
Author URI: http://eskapism.se/
*/

add_action('widgets_init', create_function('', 'return register_widget("Nice_Navigation");'));
add_action("init", "nice_navigation_init");

function nice_navigation_init() {

	define( "NICE_NAVIGATION_URL",  plugins_url() . '/nice-navigation/');
	define( "NICE_NAVIGATION_VERSION", "1.6");
	
	// Add more stlyes to the output of wp_list_pages
	add_filter('page_css_class', 'nice_navigation_page_css_class', 10, 4);

	// Add scripts and styles to the front end
	add_action('wp_enqueue_scripts', 'nice_navigation_enqueue_scripts');
		
	//add_action("wp_head", "nice_navigation_wp_head");
	//apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
	//add_filter( 'nav_menu_css_class', "nice_navigation_nav_menu_css_class", 10, 2);	
	//add_filter( "wp_nav_menu_items", "nice_navigation_wp_nav_menu_items", 10, 2);

}

function nice_navigation_enqueue_scripts() {
	wp_enqueue_script( "nice-navigation", NICE_NAVIGATION_URL . "script.js", array("jquery"), NICE_NAVIGATION_VERSION );	
	wp_enqueue_style( "nice-navigation", NICE_NAVIGATION_URL . "styles.css", array(), NICE_NAVIGATION_VERSION, "screen" );
}

/**
 * Adds a lot of pages to test the performance
 */
function nice_navigation_add_lots_of_test_pages() {
	// Add a post at root level
	$arr = array(
		"post_title" 	=> "Nice Navigation test post",
		"post_status" 	=> "publish",
		"post_type"		=> "page",
	);
	$new_post_id = wp_insert_post($arr);
	if ($new_post_id) {
		// insert lots of child pages
		$arr["post_parent"] = $new_post_id;
		for ($i = 0; $i < 200; $i++) {
			$arr["post_title"] = "Sub page $i";
			wp_insert_post($arr);
		}
	}
}

function nice_navigation_wp_nav_menu_items($items, $args) {

	return $items;

}

function nice_navigation_nav_menu_css_class($classes, $page) {
	/*
	a child page has attribute [menu_item_parent] => 1321
	
	*/
	return $classes;
}

class Nice_Navigation extends WP_Widget {

	var $arr_types = array(
		"wp_list_pages" => array("function" => "wp_list_pages", "name" => "List pages" ),
		"wp_page_menu" => array("function" => "wp_page_menu", "name" => "List pages as menu"),
		"wp_list_categories" => array("function" => "wp_list_categories", "name" => "List categories"),
		"wp_get_archives" => array("function" => "wp_get_archives", "name" => "List archives"),
		"wp_nav_menu" => array("function" => "wp_nav_menu", "name" => "wp_nav_menu"),
	);
	
	var $arr_looks = array(
		"wikipedia" => array("look" => "wikipedia", "name" => "Wikipedia-like"),
		#"explorer" => array("look" => "explorer", "name" => "Windows Explorer"),
		#"finder" => array("look" => "finder", "name" => "OSX Finder")
	);

	function Nice_Navigation() {
		parent::WP_Widget('nice_navigation', 'Nice Navigation', array('description' => 'Outputs pages/categories with a very nice effect.', 'class' => 'nice-navigation-class'));	
	}

	// outputs the options form on admin
	function form($instance) {

		// Title
		$field_id = $this->get_field_id('title');
		$field_name = $this->get_field_name('title');
		$title_value = esc_html($instance["title"]);
		echo "<p>";
		echo "<label for='$field_id'>Title</label>";
		echo "<input class='widefat' type='text' value='$title_value' name='$field_name' id='$field_id' />";
		echo "</p>";

		// What function to use
		$field_id = $this->get_field_id('function');
		$field_name = $this->get_field_name('function');
		echo "<p>";
		echo "<label for='$field_id'>Function</label>";
		echo "<select id='$field_id' name='$field_name' class='widefat'>";
		foreach ($this->arr_types as $type) {
			$selected = "";
			if ($instance["function"] == $type["function"]) {
				$selected = ' selected="selected" ';
			}
			echo "<option $selected value='{$type["function"]}'>{$type["name"]}</option>";
		}
		echo "</select>";
		echo "</p>";
		
		// Arguments to the function
		$field_id = $this->get_field_id('arguments');
		$field_name = $this->get_field_name('arguments');
		$arguments_value = esc_html($instance["arguments"]);
		echo "<p>";
		echo "<label for='$field_id'>Arguments</label>";
		echo "<input class='widefat' type='text' value='$arguments_value' name='$field_name' id='$field_id' />";
		echo "</p>";

		// The look to use. Currently only the wikipedia-like-one. I'm to lazy to add more.
		$field_id = $this->get_field_id('look');
		$field_name = $this->get_field_name('look');
		echo "<p>";
		echo "<label for='$field_id'>Look</label>";
		echo "<select id='$field_id' name='$field_name' class='widefat'>";
		foreach ($this->arr_looks as $look) {
			$selected = "";
			if ($instance["look"] == $look["look"]) {
				$selected = ' selected="selected" ';
			}
			echo "<option $selected value='{$look["look"]}'>{$look["name"]}</option>";
		}
		echo "</select>";
		echo "</p>";

		// @todo
		// - Would be very nice with an option to make parent just a placeholder and be clickable (whole text) to expand the tree.
		printf(
			'
			<p>
				<input type="checkbox" value="1" name="%2$s" id="%1$s" %3$s>
				<label for="%1$s">Click on parent = expand children (but don\'t follow the link) </label>
			</p>
			',
			$this->get_field_id('clickable_parent'),		// 1
			$this->get_field_name('clickable_parent'),		// 2
			isset($instance["clickable_parent"]) && $instance["clickable_parent"] ? " checked " : ""
		);		
	}

	function update($new_instance, $old_instance) {

		// processes widget options to be saved
		// fill current state with old data to be sure we not loose anything

		$instance = $old_instance;
		
		$instance["title"] = $new_instance["title"];
		$instance["function"] = $new_instance["function"];
		$instance["arguments"] = $new_instance["arguments"];
		$instance["look"] = $new_instance["look"];
		$instance["clickable_parent"] = $new_instance["clickable_parent"];
		
		return $instance;
	}

	// outputs the content of the widget
	function widget($args, $instance) {

		// <div class="widget-wrapper widget_nice_navigation" id="nice_navigation-2"><div class="widget-title"></div>
		$widget_id = "nice_navigation_" . $args["widget_id"];
		$widget_id_for_js = str_replace("-", "", $widget_id);
		// If "clickable_parent" is set we must notify our js about that somehow
		// That's on a widget level, so not all may have that
		//if ($instance["clickable_parent"]) {
		
		// Output JS with options for this nice navigation instance
		// I would prefer to have this outputet in head/footer, but I don't know how to do that in a clean way right now
		?>
		<script type="text/javascript">
			var nice_navigation_options = nice_navigation_options || {};
			nice_navigation_options.<?php echo $widget_id_for_js ?> = {
				clickable_parent: 	<?php echo $instance["clickable_parent"] ? "true" : "false"; ?>,
				widget_id: 			"<?php echo $widget_id ?>"
			}
		</script>
		<?php
		//}

		$nav_output = "";

		$nav_output .= $args["before_widget"];
		$nav_output .= $args["before_title"];
		$nav_output .= $instance["title"];
		$nav_output .= $args["after_title"];
		
		$function = $instance["function"];
		$arguments = $instance["arguments"];
		$arguments .= "&title_li=0&echo=1";
		
		$look = $instance["look"];

		/* $nav_output .= sprintf('
			<!-- 
			Nice_Navigation debug:
			Function: %1$s
			Argument: %2$s
			-->
			', 
			htmlspecialchars($function),
			htmlspecialchars(print_r($arguments, TRUE))
		); // */

		if (function_exists($function)) {
			$nav_output .= "<div class='nice_navigation nice_navigation_look_$look' id='{$widget_id}'>";

			if ($function == "wp_list_pages") {
				$nav_output .= "<ul>";
			}

			// Instead of echo=0 we use output buffereing because there are themes that override wp_list_pages with functions that do not understand echo
			ob_start();
			call_user_func($function, $arguments);
			$nav_output .= ob_get_clean();

			if ($function == "wp_list_pages") {
				$nav_output .= "</ul>";
			}
			$nav_output .= "</div>";
		} else {
			$nav_output .= "<p>Could not find function '$function'.";
		}
		
		$nav_output .= $args["after_widget"];
	
		echo $nav_output;

	}

}


/**
 * adds class "page-has-children" to all pages that have children
 * @param array $class.	The page css class being modified, passed as an array from Walker_Page
 * @param object $page.	The page object passed from Walker_Page
 * @return array			Returns the new page css class.
 */
function nice_navigation_page_css_class($class, $page, $depth, $args) {

	/*
	New code: 37 queries
	Old code: 263 queries
	*/

	// check if current page has children and add class if it does
	if ($args["has_children"]) {
		$class[] = "page-has-children";
	}

	return $class;
 
}


function nice_navigation_wp_head() {
}


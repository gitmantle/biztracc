=== Plugin Name ===
Contributors: eskapism, MarsApril
Donate link: http://eskapism.se/sida/donate/
Tags: page, pages, tree, cms, tree, page tree, navigation, expand, collapse, navigation tree, list pages, sidebar, widget
Requires at least: 3.3
Tested up to: 3.4.1
Stable tag: 1.6

A widget that add a list of your pages as a list with option to expand and collaps the page tree with a nice slide animation effect.

== Description ==

This plugin adds a widget that makes your page list expandable/collapsible with a nice slide animation effect.

#### Nice Navigation Features and highlights:
* Simple and nice collapsible/expandable menu with your pages
* Better overview over your pages
* Instantly navigate to sub-pages
* Uses jQuery for a nice animation effect (hence the name)
* Users with JavaScript disabled will se a regular menu
* No downsides for SEO/Google, all pages still browsable by search engines
* All arguments/options available for wp_list_pages is available for this plugin/widget.
* Styles can be overridden with your own custom CSS
* Looks cool! ;)

#### Donation and more plugins
* If you like this plugin don't forget to [donate to support further development](http://eskapism.se/sida/donate/).
* Check out some [more WordPress CMS plugins](http://wordpress.org/extend/plugins/profile/eskapism) by the same author.

== Installation ==

1. Upload the folder "nice-navigation" to "/wp-content/plugins/"
1. Activate the plugin through the "Plugins" menu in WordPress
1. Go to Appearance > Widgets and you will find the Nice Navigation there

== Screenshots ==

1. It's your menu. Before and after Nice Navigation.
2. Nice Navigation widget configuration. Choose function and layout/look.

== Changelog ==

= 1.6 =
- ...and another small fix. I suck at this!

= 1.5 =
- Fixed bug that caused the widget to not work in all themes. Should work in all/most themes now.

= 1.4 =
- Fixed a PHP short tag.

= 1.3 =
- Works a lot faster with sites with many pages. Hooray!
- Added option to make a click a the parent link expand the tree and not follow the link
- Does not load CSS or scripts in admin
- Small fix to CSS to make selected post bold
- Hopefully loads scripts and styles via https if that's being used on the site

= 1.2 =
- wp_enqueue_style uses media screen. Hopefully this works better with gzip/minifiy-plugins. Thanks to http://twitter.com/JohannesHoppe/ for mentioning this.

= 1.1 =
* Plugin now available on wordpress.org
* Moved JavaScript and CSS to external files
* Arrows now have transparent backgrounds

= 1 =
* First public version.

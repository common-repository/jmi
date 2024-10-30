=== Just Map It! ===
Tags: jmi, just map it, just map it!, map, social computing, interactive information map, information visualization, concept mapping, concept map, semantic mapping, knowledge visualization, conceptual graphs, topic mapping, datavisualization, dataviz
Contributors: socialcomputing
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: 2.0.3

Just Map It! for Wordpress plugin is provided by Social Computing. It enables the visualization of your blog posts as an interactive map.

== Description ==

Just Map It! for Wordpress is a plugin that enables the visualization of a blog
posts map. Posts are positioned in relation to the keywords they share.
The map is a navigation tool : allowing you to center on any post and to discover related
contents around this post. We applied this plugin to our News stream. Just try it!

= New on this 2.0 release =

* Installation and upgrade wizard for the Just Map It! HTML5 / Javascript client
* A new Just Map It! Wordpress shortcode allows you to insert maps in your blog posts and pages.
  More on the shortcode and the available parameters on the "Other notes" page.
* Complete rewrite of the plugin and the settings pages, now using the Wordpress settings API and settings sections.

= Polylang support = 

* From the 2.0.3 this plugin now supports Wordpress installations with PolyLang plugin enabled.
  The related posts map at the bottom of blog posts only query posts with the same language as the one
  being read by the user.
  Thanks to Thomas Leisner for tests and feedback, see http://wordpress.org/support/topic/plugin-just-map-it-cant-get-jmi-to-work for details.

For more information check the "Changelog" section. 

= Map features =

* Center on a blog post and explore related posts
* Highlight a tag on the map and the posts annotated with that tag 
* Navigate to a post page to read post content
* From a tag, go to it's archive page and read posts containing that tag

= We need your help = 

We keep improving this plugin and we need some feedback to make it better.
If you have any issue or have feature requests, please open a new topic on the plugin support forum.
See the link "View support forum" on the right column.

Love this plugin and use it on your blog ? please rate it and inform other wordpress users that you like it.

= Incompatible plugins list = 

These plugins are known to cause issues with Just Map It!: 

* Like: http://wordpress.org/extend/plugins/like/ (not updated for more than 2 years) 

== Installation ==

= Requirements = 

* Wordpress 3.0 or higher
* PHP 5.1.2 or higher
* JSON API plugin

  This plugin depends on the JSON API Plugin available at http://wordpress.org/extend/plugins/json-api/ 
  Please follow the installation steps of the that plugin and confirm that it is successfully enabled before trying to install this plugin.

= Installation steps =

1. Put the jmi directory into [wordpress_dir]/wp-content/plugins/
1. Go into the WordPress admin interface and activate the plugin
1. Open the JSON API plugin configuration page and enable the JMI controller: 
    * Go to the blog administration section.
    * Then click on the "Settings" button and on the "JSON API" entry.
    * In the controller list, click on the activation link below the "JMI" item.
1. Open the Just Map It! configuration page and follow the instructions

= Configuration =

Go to the plugin configuration page : Admin -> Settings -> Just Map It!

Posts display

* Height and width: dimensions of the map in pixels

* Posts limit: Maximum number of posts to get from the blog to generate the map

* Posts get method: Method used to get other posts from the one being read by the user
    * Last posts: Get last blog posts including the selected one
    * With same tags: Get blog posts containing the same tags as the selected one

* Show map in posts : Should the related posts map be displayed at the bottom of blog posts ?
                      It is enabled by default.

Advanced section 

* Server url: location of the Just Map It! server.
  The default value is: http://server.just-map-it.com

* Map configuration name:
  The map configuration name passed to the server.

* Login and password: authentication information to provide to access the blog rest service 
  Only fill these field if the access to your blog is restricted (disabled by default)

== Related posts Map ==

By default the 'related posts' map is enabled and displayed at the bottom of blog posts.
If you want to disable this map, uncheck the 'display map on posts' option on the plugin configuration page.

== Shortcode ==

The Just Map It! shortcode makes it easy to include the map of you choice in any post and page.
It is a self-closing shortcode with a list of parameters

Here is an example of the syntax to use: `[JMI height=640 width=480]`
In this case it will display a map with a global view of the 50 last blog posts. The size of the map will be 640 by 480 pixels.

= Shortcode parameters =

All shortcode parameters are optional. If no parameter is given, default values are taken, see the parameters descriptions below:

* **height**: height of the map in pixels. 
              Default value: the one set on the configuration page in posts display tab

* **width**:  width of the map in pixels.
              Default value: the one set on the configuration page in posts display tab

* **analysisprofile**: kind of processing profile to use to generate the map. 3 values are allowed:
    * GlobalProfile (default value): give a global view of the blog data. It is not centered on one item.
    * DiscoveryProfile: map centered on a node 
    * AnalysisProfile: map with focus on a link

* **invert**: should the map be inverted ? If set to true, the elements that are normally links on the map are displayed as nodes.
              It is a boolean and can take 2 values: 
    * true (default value): Nodes on the map are blog posts and links are tags.
    * false: Nodes on the map are tags and links are posts

* **attributeid**: id of the element on which the map should be centered. It must be specified if the DiscoveryProfile is selected.

* **entityid**: id of the element on which to focus on the map. It must be set if the AnalysisProfile is selected.

* **json_controller**: name of the service to query to get blog data.
                       It can be one of the following:
    * last_posts: get last blog posts and their tags.
                  Additional parameters:
        * id (optional): id of a post to include the result list
        * max (optional): max number of posts to get

    * related posts: get posts with the same tags as the one with the id given in parameter
                     Additional parameters:
        * id (required): id of the post for which the tags are taken to make the request
        * max (optional): max number of posts to get

    * tag_posts: get posts containing the tag with id given in parameter
                 Additional parameters:
        * id (required): id of a tag
        * max (optional): max number of posts to get

    * category_posts: get posts containing the category with id given in parameter
                      Additional parameters:
        * id (required): id of a category
        * max (optional): max number of posts to get

* **id**: id parameter to pass to the json service (see above)

* **max**: max parameter to pass to the json service (see above)

== Map on a tag or a category archive page (Optional) ==

To add the map on the tag archive and category archive pages, paste the
following snippets, in the following files in your theme directory : 

* tag.php

`
<?php
    if(class_exists('Jmi') && isset($jmi)) {
        echo $jmi->getMap(array(
            'width'           => '650',
            'height'          => '400',
            'analysisProfile' => 'DiscoveryProfile',
            'attributeId'     => $tag_id, 
            'json_controller' => 'tag_posts',
            'id'              => $tag_id,
            'invert'          => false
            )
        );
    } 
?>
`

* category.php

`
<?php
    if(class_exists('Jmi') && isset($jmi)) {
        echo $jmi->getMap(array(
            'width'           => '650',
            'height'          => '400',
            'analysisProfile' => 'GlobalProfile',
            'json_controller' => 'category_posts',
            'id'              => get_query_var('cat'),
            'invert'          => true
            )
        );
    }
?>
`

== Additional note ==

We provide a freely accessible Just Map It! server at http://server.just-map-it.com that is hosted on a cloud plateform.
However, we do not give any warranty on this server performance, response time or availablity.
If you want to display a high number of maps or have a high number of users on your blog and you need some SLA,
please contact us by mail at contact at social-computing dot com or visit our website http://www.social-computing.com/ and fill the contact form.

== Frequently Asked Questions ==

= How can I tell if it's working? =

An interactive map should appear at the bottom of each blog post when the option
is checked. This map shows the relations between the post being read and other
blog posts by the tags they share.

= The option is checked on the plugin admin page but the map is still not displayed =

The map will not be rendered until the Just Map It! client is installed. Go to 
the plugin settings page and check that the Just Map It! client is sucessfully installed 
and that no additional step is required.

= When I attempt to download the Flash client the browser page just goes to white. (plugin v1.x) =

The white page you see when you click on the download link appears because your browser tries to open the flash client instead of offering to download it.
Which means the link is valid but you don't have a download dialog box.
After clicking on the link, when you see the white page, you can save the .swf file by clicking on your browser's menu and selecting the "save as..." entry.
Or you can right click on the download link and select "save link as ..." to start to download the remote flash client.

== Screenshots ==

1. Related posts map
2. Map of posts tags at the top of an archive page
3. Global view of blog posts in a specific category

== Changelog ==

= Trunk =

= 2.0.3 = 

Adding polylang support: picking modifications from the 2.0.2-polylang branch
* Fix JSON API URL generation when polylang plugin is activated
* Add some tests before using foreach on a variable to discard warnings in rest.php
* Thanks to Thomas Leisner for tests and feedback, see: http://wordpress.org/support/topic/plugin-just-map-it-cant-get-jmi-to-work

= 2.0.2 =

* Fix: Test query results before iterating. Foreach loop warning in the same tags service.
* Add warning messages for the blog admin when the JSON-API plugin is not installed or the jmi controller is not activated.
* Try to automatically activate the jmi controller under JSON-API plugin configuration when the plugin is activated

= 2.0.1 =

* Include SolrUtil and add jmi-solr.js file
* Allow wordpressurl overriding in map parameters
* Latest Wordpress version tested with the plugin increased to 3.4.1

= 2.0.0 =

* Complete rewrite for 2.0 version of the plugin
* Now using the Wordpress settings API
* The settings are separated into sections and displatyed as tabs on the plugin admin page
* An upgrade to the Javascript client to render the map is now available !
* New installation and upgrade wizard to manage the Just Map It! client
* Just Map It! shortcode to insert the map in blog pages and posts
* Update the function to display the map in the theme pages
* Fix shortcode function to transform arguments in camel case when needed
* Update the JSON Rest services to handle the max parameter
* Add the Solr utility class needed to generated the map on the search results page
* Latest Wordpress version tested with the plugin increased to 3.4
* Update plugin documentation, how to use the shortcode and some screenshots

= 1.2.0 =

* Latest Wordpress version tested with the plugin increased to 3.3.2
* The tags urls are now generated server side and published in the Just Map It! JSON REST Services.
* Constructing JMI_JSON url from the blog home url instead of wordpress url

= 1.1.0 =

* i18n and localization for french and english languages
* Display the expected swf location in the installation process

* bugfix: don't display the map if not tags are set for a post

= 1.0.4 =

* Add viewport meta for mobile devices.
* Also add fullScreenOnSelection parameter to the flash application to allow users
  to switch easily to fullscreen mode on tactile devices.

= 1.0.3 =

* bug fix : dynamic js is surrounded by html tags in posts content and html escaping is applied. 
            => javacript is not added in the post content anymore.
               wp_localize_script is used to pass parameters array, see http://www.wpmods.com/using-multidimensional-arrays-with-wp_localize_script/

= 1.0.2 =

* Bug fix : url encode wordpress url only in non js section

= 1.0.1 =

* Administration section rewritten
    * Default values are set for all parameters
    * Parameters escaping and trim on strings
    * Parameters validation and error messages feedback
    * Add sections and parameters descriptions
    * Preparing i18n
    
* Add posts limit to set the max number of posts to get to generate the map
* Add posts get method, with 2 strategies
* The configuration documentation moved into the installation section in the readme file

* Bug fixes :
    * The blog json api url is constructed automatically and without url rewriting.
    * Registering javascript dependencies
    * Don't create the jmi subdirectory in the wordpress upload directory automatically

= 1.0.0 =

* Initial release of the Just Map It! plugin for Wordpress


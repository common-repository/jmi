<?php

require_once dirname( __FILE__ ) . '/admin/SettingsMenuManager.php';
require_once dirname( __FILE__ ) . '/jmimap.php';

/**
 * Just Map It! main plugin class
 *
 * @package jmi
 * @author Jonathan Dray <jonathan@social-computing.com>
 */
class Jmi {
	private $version; // current plugin version number
	private $path;    // directory of this plugin of the filesystem
	private $name;    // plugin name
	
	public function __construct($version) {
		$dirname = dirname(__FILE__);
		$this->name = plugin_basename($dirname);
		$this->path = basename($dirname);
		$this->version = $version;
	}
	
	/**
	 * Version getter
	 * @return string  plugin version number
	 */
	public function getVersion() {
		return $this->version;
	}
	
	
	/**
	 * Function called to add all wordpress plugin hooks
	 */
	public function run() {
		add_filter('plugin_action_links', array($this, 'addAdminSettingsLink'), 10, 2);

		// Json api plugin specific filters		
		add_filter('json_api_controllers', array($this, 'addRestController'));
		add_filter('json_api_jmi_controller_path', array($this, 'getControllerPath'));
						
		// init is triggered ...
		add_action('init', array($this, 'registerResources'));
		
		// Hook to register admin pages
		add_action('admin_menu', array($this, 'initSettingsMenu'));
		
		// admin_init is triggered before any other hook when a user access the admin area. 
		// This hook doesn't provide any parameters, so it can only be used to callback a specified function.
		// It is used to register the plugin settings 
		add_action('admin_init', array($this, 'registerSettings'));
		add_action('admin_init', array($this, 'runtimeUpgrade'));
		add_action('admin_init', array($this, 'adminNotices'));
		
		// wp_head is triggered when the content of html header <head></head> is generated.
		// it is used by plugins to add header tags
		add_action('wp_head', array($this, 'displayHeader'));
		
		add_action('wp_footer', array($this, 'printResources'));
		add_action("wp_enqueue_scripts", array($this, 'jqueryEnqueue'), 11);
		
		// Triggered when a post content is diplayed
		add_action('the_content', array($this, 'postDisplay'));
		
		add_shortcode('jmi', array($this, 'handleShortcode'));
	}
	
	/** 
	 * Add page(s) to the admin menu 
	 * This function runs when then 'admin_menu' hooks fires, 
	 * and adds a new options page for this plugin to the settings menu 
	 */
	public function initSettingsMenu() {
        add_options_page(
            'Just Map It! options page',
            'Just Map It!',
            'manage_options', 
            __FILE__, 
            array($this, 'displaySettingsMenu')
        );
    }

	/**
	 * Called to display the HTML of the setting sections of the plugin
	 */
    public function displaySettingsMenu() {
    	$settingsMenuManager = new SettingsMenuManager();
		$settingsMenuManager->display();
    }
	
	/**
	 * Register the plugin settings
	 * This function runs when then 'admin_init' hooks fires  	   
	 */
	public function registerSettings() {
		$settingsMenuManager = new SettingsMenuManager();
		$settingsMenuManager->register();
	}
	
	/**
	 * Check if a setting upgrade is needed after plugin upgrade
	 * It saves the settings in db if the activation step was not run by the user.
	 * 
	 * Moreover, it handles the 1.x to 2.x settings format migration
	 */
	public function runtimeUpgrade() {
		$resetclient = isset( $_GET[ 'resetclient' ] ) ? $_GET[ 'resetclient' ] : null;
		if(!is_null($resetclient)) {
			$clientOptions = get_option('jmi_client');
			$clientOptions['type'] = '';
			update_option('jmi_client', $clientOptions);
		}
		
		$jmiInstaller = new JmiInstaller($this->version);
		$jmiInstaller->install();
		
		// Ensure that the json-api jmi controller exists and is enabled.
		// If it's not active, then activate the controller 
        global $json_api;
		if(isset($json_api)) {
			if(!$json_api->controller_is_active('jmi')) {
    			$available_controllers = $json_api->get_controllers();
				$active_controllers = explode(',', get_option('json_api_controllers', 'core'));
				
				if (count($active_controllers) == 1 && empty($active_controllers[0])) {
				      $active_controllers = array();
				}
          		if (in_array('jmi', $available_controllers) && !in_array('jmi', $active_controllers)) {
			          $active_controllers[] = 'jmi';
				}
        		$json_api->save_option('json_api_controllers', implode(',', $active_controllers));
			}
		}
	}
	
	/**
	 * Display the annoying warning messages in the top of wordpress admin pages
	 * This function runs when then 'admin_init' hooks fires
	 * 
	 */	
	public function adminNotices() {
		$clientInstaller = new ClientInstaller();
		if(!$clientInstaller->isInstalled()) {
			add_action('admin_notices', 'client_install_admin_notice');
			function client_install_admin_notice() {
				if (Jmi::shouldDisplayNotice()) {
			        echo '<div class="updated"><p>';
			        printf(__('The Just Map It! plugin is almost ready... Go to the settings page and follow the client installation instructions | <a href="%1$s">Plugin settings page</a>', 'jmi'), 'options-general.php?page=jmi/jmi.php');
			        echo "</p></div>";
			    }
			}			
		}
		else {
			$clientType = $clientInstaller->getClientType();
			if($clientType == 'flash') {
				add_action('admin_notices', 'client_upgrade_admin_notice');
				function client_upgrade_admin_notice() {
					if (Jmi::shouldDisplayNotice()) {
				        echo '<div class="updated"><p>';
				        printf(__('A new version of the Just Map It! client is available. Go to the settings page and follow the client upgrade instructions | <a href="%1$s">Plugin settings page</a>', 'jmi'), 'options-general.php?page=jmi/jmi.php&tab=upgrade');
				        echo "</p></div>";
				    }
				}	
			}	
		}
		
		global $json_api;
		if(isset($json_api)) {
			if(!$json_api->controller_is_active('jmi')) {
				add_action('admin_notices', 'json_api_controller_admin_notice');
				function json_api_controller_admin_notice() {
					if (current_user_can('manage_options')) {
				        	echo '<div class="error"><p>';
					        printf(__('The "jmi" controller for JSON-API is not active. You must enable that controller or the Just Map It! plugin will not work as expected. Go to the JSON-API plugin settings page and click on the "activate" link under the jmi controller in the list | <a href="%1$s">JSON-API settings page</a>', 'jmi'), 'options-general.php?page=json-api');
					        echo "</p></div>";
				    }
				}	

			}
		} 
		else {
			add_action('admin_notices', 'json_api_admin_notice');
			function json_api_admin_notice() {
				if (current_user_can('manage_options')) {
			        	echo '<div class="error"><p>';
				        printf(__('The JSON-API plugin has not been detected on your Wordpress installation. The Just Map It! plugin will not work if that plugin is not installed. Please check that it is successfully installed and activated | <a href="%1$s">Extensions administration</a> | <a href="%2$s">JSON-API plugin download page</a>', 'jmi'), 'plugins.php', 'http://wordpress.org/extend/plugins/json-api/');
				        echo "</p></div>";
				}
			}	
		}
	}

	/**
	 * Check that the user has the appropriate permissions
	 * And that the user is not already on the plugin page
	 * 
	 * @return bool  true if all the conditions are satisfied, false otherwise
	 */
	public static function shouldDisplayNotice() {
		global $pagenow;
		return current_user_can('manage_options') && !(($pagenow == 'options-general.php') && ($_GET['page'] == 'jmi/jmi.php'));
	}
	
	/**
	 * Register the plugin ressources
	 * This function runs when then 'init' hooks fires
	 * 
	 * It loads the javascript and css files to be printed in the page footer if the jmi client has to be added.
	 * The files to load change if the js or the flash client are configured  	   
	 */
	public function registerResources() {
		// load i18n translation files
		load_plugin_textdomain($this->name, false, $this->path . '/languages');
		
		// javascript file to handle actions on the map located at the bottom of blog posts
        // wp_register_script('jmi-posts', plugins_url('js/jmi-posts.js', __FILE__), false, '1.0', true);
		
		// If the javascript client version is available ?
		$clientInstaller = new ClientInstaller();
		$clientType = $clientInstaller->getClientType();
    	if($clientType == 'js') {
    		$clientUrl = $clientInstaller->getJsURL();
    		// Register the css and the javascript files of the js client version 
    		wp_register_style('jmi-client', $clientUrl . '/css/jmi-client.css' ,false, '1.0', 'screen');
    		wp_register_script('jmi-client', $clientUrl . '/jmi-client.js', false, '1.0', true);
			wp_register_script('jmi', plugins_url('js/jmi-js.js', __FILE__), array('jquery'), '1.0', true);
			wp_register_script('jmi-solr', plugins_url('js/jmi-solr.js', __FILE__), array('jmi'), '1.0', true);
    	}
		elseif($clientType == 'flash') {
	        wp_register_script('jmi', plugins_url('js/jmi-flash.js', __FILE__), array('jquery', 'swfobject'), '1.0', true);
		}		
	}

	/**
	 * Prints the js and css files in the footer only if the client was detected on a page
	 */
	public function printResources() {
		$clientInstaller = new ClientInstaller();
		$clientType = $clientInstaller->getClientType();
		if($clientInstaller->isInstalled()) {
	        global $jmi_maps;
	        if (!$jmi_maps && !is_array($jmi_maps)) return;
	        
	        // wp_print_scripts('jmi-posts');
	
	       	// TODO : Handle wordpress version < 3.3 case
	       	//        As the localize script doesn't take multidimensional arrays in versions < 3.3
	       	// TODO : Test this with the l10n_print_after hack in wordpress v < 3.3
		    wp_localize_script('jmi', 'jmimaps', 
		                       array('l10n_print_after' =>
		                             'jmimaps = ' . json_encode($jmi_maps) . ';'));        
	    
	
			if($clientType == 'js') {
	        	wp_print_scripts('jmi-client');
				wp_print_styles('jmi-client');
				
				global $jmisolr;
				if ($jmisolr && is_array($jmisolr))  {
	                wp_localize_script('jmi-solr', 'jmisolr',
	                   array('l10n_print_after' =>
	                         'jmisolr = ' . json_encode($jmisolr) . ';'));
	                wp_print_scripts('jmi-solr');
				}
			}
			wp_print_scripts('jmi');
		}
	}
	
	/**
	 * Hack to add jquery in the page header instead of footer
	 * The jquery version is hardcoded for now : 1.7.1
	 * I uses google api javascript hosting service 
	 */
	public function jqueryEnqueue() {
		if (!is_admin()) {
		    wp_deregister_script('jquery');
		    wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js", false, null);
		    wp_enqueue_script('jquery');
		}
	}

	/**
	 * Adding viewport metadata to improve display
	 * See @link https://developer.mozilla.org/en/Mobile/Viewport_meta_tag
	 * And @link http://darkforge.blogspot.com/2010/05/customize-android-browser-scaling-with.html
	 */	
	public function displayHeader() {
	    if(!is_admin()) {
	        echo "<!-- viewport meta inserted by Just Map It! plugin -->\n";
	        echo '<meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, user-scalable=yes" />';
	    }		
	}
	
	/**
	 * Add the map to the post content
	 *
	 * @param string $post_content  the original post content
	 * @return string               the post content with the map included when necessary
	 */
	public function postDisplay($post_content) {
	    // Read plugin options
	    $options = get_option('jmi_posts_display');
	    $the_post = $post_content;
		$clientInstaller = new ClientInstaller();
	
	    // Decide whether or not to generate the map.
	    // For now, only if it is a single post 
	    if($clientInstaller->isInstalled() && is_single() && $options['show_map_in_post']) {
		    global $wp_query;
		    $post_id = $wp_query->post->ID;
		    $post_tags = get_the_tags($post_id);
			
			// Only add the map to the post if it has tags, so the map will not be empty
		    if ($post_tags != '') {
				// Construct the blog json controller url with the required parameters
				// This url will be sent to the JMI Server		    	
			    $wpurl = $this->getJsonControllerURL($options['json_controller'],
			                              array(
			                                  'id' => $post_id,
			                                  'max' => $options['max_posts'],
			                              ));
			    $jmiMap = new JmiMap($clientInstaller);
			    $map_options = array('analysisProfile' => 'DiscoveryProfile',
			                         'attributeId' => $post_id,
			                         'invert' => true,
			                         'wordpressurl' => $wpurl);
				// Add the html markup to include the map at the bottom of the post 
	            $the_post .= '<div><h2 id="maptitle">' . __('Related posts', 'jmi') . '</h2>';
	            $the_post .= $jmiMap->getMapTags($map_options);
	            $the_post .= '</div>';
		    }
		}
	    return $the_post;
	}

	/**
	 * WP shortcode to add a map with a specific configuration on any blog's page
	 * ie: [JMI width=320 height=240 analysisProfil=globalProfil ...]
	 * 
	 * @param array   $atts an array of attributes to pass to the client and the server to create the map
	 * @return string html content that will be displayed at the shortcode location
	 */
    public function handleShortcode($atts) {
        // if the shortcode has no attributes specified, WP passes
        // an empty string instead of an array
        if (!is_array($atts)) {
            $atts = array();
        }
		
		// As shortcode does not support camel case args, need to processed for known parameters
		$atts = ArrayUtil::replaceByKey($atts, array(
				'analysisprofile' => 'analysisProfile',
				'attributeid'     => 'attributeId',
				'entityid'        => 'entityId'
			)
		);
		return $this->getMap($atts);
    }


	/**
	 * Display the map with the given parameters
	 *  
	 * @param array   $atts an array of attributes to pass to the client and the server to create the map
	 * @return string html content to display 
	 */
    public function getMap($atts = array()) {
		// Validate some input parameters and construct the json service url
		$options = get_option('jmi_posts_display');
		extract(shortcode_atts(array(
	    	'json_controller' => $options['json_controller'],
	    	'max' => '50',
	    	), 
	    	$atts)
	    );
	    if(!isset($atts['wordpressurl'])) {
			$controller_args = array('max' => $max);
			if(isset($atts['id'])) $controller_args['id'] = $atts['id'];
			$atts['wordpressurl'] =  $this->getJsonControllerURL($json_controller, $controller_args);
		}
		
		$jmiMap = new JmiMap(new ClientInstaller());
        $map = $jmiMap->getMapTags($atts);
		return $map;
    }
		
	/**
	 * Display a Settings link on the main Plugins page
	 * Seems to only work with non multisite mode plugins
	 * 
	 * @param array $links  list of already registered links of the plugin
	 * @param string $file  file name for which to add the link
	 * @return an array of links
	 */
	public function addAdminSettingsLink($links, $file) {
		if ($file == plugin_basename(__FILE__)) {
			$jmi_links = '<a href="' . get_admin_url() . 'options-general.php?page=jmi/jmi.php">' . __('Settings') . '</a>';
			// make the 'Settings' link appear first
			array_unshift($links, $jmi_links);
		}
		return $links;
	}

	/**
	 * Append the jmi controller to the list of JSON API plugin available controllers
	 * function called when the add rest controller filter hook is fired
	 * 
	 * @param array  $controllers a list of controllers
	 * @return array the same array with the 'jmi' controller added
	 */
	public function addRestController($controllers) {
	    $controllers[] = 'jmi';
	    return $controllers;
	}

	/**
	 * Returns the path to the jmi rest controller to be loaded by the
	 * JSON API plugin.
	 * 
	 * @return string  the complete path to the rest controller
	 */
	public function getControllerPath() {
	    return plugin_dir_path(__FILE__) .  'rest.php';
	}
	
	/**
	 * Construct the url of the json controller with the controller name and
	 * additional parameters
	 *
	 * @param string $controller  name of controller
	 * @param array  $parameters  list of values to add as query parameters
	 * @return string             url of the json controller to query
	 */
	public function getJsonControllerURL($controller, $parameters = array()) {
		$lang = '';
		// If polylang plugin is installed, add the language to the json api url
		if (class_exists("Polylang")) {
		    $lang = pll_current_language();
		}
		$json_url = site_url($lang . '/index.php?json=jmi.' . $controller);
		foreach ($parameters as $k => $v) {
		    $json_url .= '&' . $k . '=' . $v;
		}
		return $json_url;
	}
}

?>

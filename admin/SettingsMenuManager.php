<?php

require_once dirname( __FILE__ ) . '/SettingsSection.php';
require_once dirname( __FILE__ ) . '/ClientInstaller.php';

/**
 * The plugin setting manager class
 * 
 * @package jmi
 * @author Jonathan Dray <jonathan@social-computing.com>
 */
class SettingsMenuManager {

	private $sections; // Array of sections to use
	private $settings; // Array of settings 
	
	/**
	 * Default class constructor
	 * Constructs the sections and settings objects
	 * 
	 */
	function __construct() {
		 $this->settings = array(
			'posts_display' => array(
				array(
		            "id"      => 'width',
		            "title"   => __('Width', 'jmi'),  
		            "desc"    => __('Width of the map in pixels', 'jmi'),  
		            "type"    => "text",
		            "std"     => 640,  
		            "class"   => "numeric",
		            "section" => "posts_display",  
		        ),
		        array(
		            "id"      => 'height',
		            "title"   => __('Height', 'jmi'),  
		            "desc"    => __('Height of the map in pixels', 'jmi'),  
		            "type"    => "text",
		            "std"     => 480,
		            "class"   => "numeric",
		            "section" => "posts_display",  
		        ),
		        array(
				    "id"      => 'json_controller',
				    "title"   => __('Posts get method', 'jmi'),
				    "desc"    => __('Method used to get other blog posts from a selected one', 'jmi'),
				    "type"    => "select2",
				    "std"    => "last_posts",
				    "choices" => array(__('Last posts','jmi') . "|last_posts", __('With same tags', 'jmi') . "|related_posts"),
				    'section' => 'posts_display',
			    ),
			    array(
			        'id'        => 'max_posts',
		            "title"     => __('Posts limit', 'jmi'),  
		            "desc"      => __('Maximum number of posts to get with a query', 'jmi'),  
		            "type"      => "text",
		            "std"       => 30,
		            "class"     => "numeric",
		            "section"   => "posts_display",  
		        ),
		        array(
		            'id'      => 'show_map_in_post',
					'title'   => __('Display map', 'jmi'),
					'desc'    => __('Check this option to add a map at the bottom of your blog posts', 'jmi'),
					'type'    => 'checkbox',
					'std'     => 1, // Set to 1 to be checked by default, 0 to be unchecked by default.
					'section' => 'posts_display',
				)
			),
			// Advanced settings section
			'advanced' => array(
				array(
		            'id'      => 'serverurl',
		            "title"   => __('Server url', 'jmi'),  
		            "desc"    => __('The Just Map It! server location', 'jmi' ),  
		            "type"    => "text",
		            "std"     => 'http://server.just-map-it.com',  
		            "class"   => "url",
		            "section" => "advanced",          
		        ),
		        array(
		            'id'      => 'map',
		            "title"   => __('Map configuration name', 'jmi'),  
		            "desc"    => __('The map configuration name passed to the server to generate the map', 'jmi' ),  
		            "type"    => "text",
		            "std"     => 'wordpressrest',
		      		"class"   => "nohtml",
		            "section" => "advanced",
		        ),
		        array(
		            'id'      => 'login',
		            "title"   => __('Login', 'jmi'),  
		            "desc"    => __('A login to provide to access your blog json service', 'jmi'),  
		            "type"    => "text",
		            "std"     => "",
		      		"class"   => "nohtml",            
		            "section" => "advanced",          
		        ),
		        array(
		            'id'      => 'password',
		            "title"   => __('Password', 'jmi'),  
		            "desc"    => __('A password to provide to access your blog json service', 'jmi'),  
		            "type"    => "password",
		            "std"     => "",
		      		"class"   => "nohtml",            
		            "section" => "advanced",          
		        )
			),
			// Jmi client settings
			'client' => array(
				array(
		            'id'      => 'type',
					'title'   => __('Just Map It! client type', 'jmi'),
					'desc'    => __('Just Map It! client type', 'jmi'),
					'type'    => 'text',
					'std'     => '',
					'class'   => 'nohtml',
					'section' => 'client',
				),
			)
		);
		
		// Creates the plugin settings sections 
        $this->sections = array(
        	'posts_display' => new SettingsSection('posts_display', 
                                        'Posts display settings', 
                                        'These parameters control the appearence and the behaviour of the map displayed at the bottom of blog posts',
                                        'posts_display',
										$this->settings['posts_display']),
            'advanced' => new SettingsSection('advanced', 
                                        'Advanced settings', 
                                        'Advanced map settings, change the values only if you know what you are doing !',
                                        'advanced',
                                        $this->settings['advanced']),
        	'client' => new SettingsSection('client', 
                                        '', 
                                        '',
                                        'client',
                                        $this->settings['client'])                    
		);
	}


	public function display() {
        echo '<div class="wrap">
            <div class="icon32" id="icon-options-general"></div>';
		$clientInstaller = new ClientInstaller();
		if(!$clientInstaller->isInstalled()) {
			$clientInstaller->displayInstallPage();
		}
		else {
			echo '<h2>' . __( 'Just Map It! plugin options', 'jmi') . '</h2>';
			settings_errors();
			$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'posts_display';
			$clientType = $clientInstaller->getClientType();
	  		?>
	  		
	        <h2 class="nav-tab-wrapper">
	            <a href="?page=jmi/jmi.php&tab=posts_display" class="nav-tab <?php echo $active_tab == 'posts_display' ? 'nav-tab-active' : ''; ?>"> <?php _e('Posts display', 'jmi'); ?></a>  
	            <a href="?page=jmi/jmi.php&tab=advanced" class="nav-tab <?php echo $active_tab == 'advanced' ? 'nav-tab-active' : ''; ?>"><?php _e('Advanced', 'jmi'); ?></a>
	        <?php
	        if($clientType == 'flash')  {
	        	echo '<a href="?page=jmi/jmi.php&tab=upgrade" class="nav-tab ' . ($active_tab == 'upgrade' ? 'nav-tab-active' : '') . '">' . __('Upgrade', 'jmi') . '</a>';
	        }
			echo '</h2>'; 
	         
	        if($active_tab == 'upgrade') {
	        	echo '<h3>' . __('Upgrade to the HTML5 / Javascript client', 'jmi') . '</h3>';
				$clientInstaller->displayUpgradeMessage();
				echo '<br><p>' . __('It is highly recommended that you upgrade to the new version. Click on the upgrade button and follow the installation instructions.', 'jmi') . '</p>' .
				     '<a href="?page=jmi/jmi.php&resetclient=true" class="button">' . __('Upgrade', 'jmi') . '</a>';
	        }
			else {
		        echo '<form method="post" action="options.php">';  
		  		
		        foreach ($this->sections as $section) {
		        	if($active_tab == $section->getId()) {
		        		$section_to_display = 'jmi_' . $section->getId(); 
		        		settings_fields($section_to_display);
						do_settings_sections($section_to_display);
		        	}
				}
				submit_button();	    
	        	echo '</form>';
			}
		}		
		
        echo '</div>';	    								
	}


	public function register() {
		// Adding sections
	    foreach ($this->sections as $section) {
	    	$section->register();
		}
		
		foreach ($this->settings as $section_settings) {
			foreach($section_settings as $setting) {
	    		$this->create_setting($setting);
			}
	    }
		
	}
	
	
	public function resetToDefaults() {
		$default_settings = $this->loadDefaultSettings();
		$this->setSettings($default_settings);
	}
	
		
	public function updateSettings() {
		// Construct default settings array
		$updated_settings = $this->loadDefaultSettings();
		
		// Load settings from db and merge
		foreach ($updated_settings as $section_name => $section_settings) {
			$stored_settings = get_option('jmi_' . $section_name);
			if($stored_settings !== false) {
				 $updated_settings[$section_name] = array_merge($section_settings, $stored_settings);
			}
		}
		
		// Updated these settings in database 
		$this->setSettings($updated_settings);
	}
	
	
	public function updateFromOldSettings($oldSettings) {
		// Construct default settings array
		$default_settings = $this->loadDefaultSettings();
				
		// Override default values with old ones 
		$default_settings['posts_display']['width'] = $oldSettings['width'];
		$default_settings['posts_display']['height'] = $oldSettings['height'];
		$default_settings['posts_display']['json_controller'] = $oldSettings['json_controller'];
		$default_settings['posts_display']['max_posts'] = $oldSettings['max_posts'];
		$default_settings['posts_display']['show_map_in_post'] = $oldSettings['show_map_in_post'];
		$default_settings['advanced']['serverurl'] = $oldSettings['wpsserverurl'];
		$default_settings['advanced']['map'] = $oldSettings['wpsplanname'];
		$default_settings['advanced']['login'] = $oldSettings['jmi_login'];
		$default_settings['advanced']['password'] = $oldSettings['jmi_password'];
		
		// Store the newly created settings in db
		$this->setSettings($default_settings);
	}

	/**
	 * Load the plugin default settings values and return 
	 * an array containing these default settings.
	 */
	public function loadDefaultSettings() {
		$default_settings = array();
		foreach ($this->settings as $section_name => $section_settings) {
			$default_settings[$section_name] = array();
			foreach($section_settings as $setting) {
				$default_settings[$section_name][$setting['id']] = $setting['std'];
			}
		}
		
		return $default_settings;
	}

	/**
	 * Remove plugin v 1.x settings from db
	 */
	public function removeOldSettings() {
	    delete_option('jmi_options');
		delete_option('jmi_options_activation');
	}
	
	/**
	 * Update settings in db with the ones given in parameters
	 */
	public function setSettings($settings) {
		foreach ($settings as $section_name => $section_settings) {
			update_option('jmi_' . $section_name, $section_settings);
		}
	}
	
	
	/**
     * Helper function for registering our form field settings
     * src: http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/
     *
     * @param array $args  The array of arguments to be used in creating the field
     */
    public function create_setting($args = array()) {
	    // default array to overwrite when calling the function
	    $defaults = array(
		    'id'      => 'default_field',                  	// the ID of the setting in our options array, and the ID of the HTML form element
		    'title'   => 'Default Field',              	 	// the label for the HTML form element
		    'desc'    => 'This is a default description.',  // the description displayed under the HTML form element
		    'std'     => '',  								// the default value for this setting
		    'type'    => 'text',  							// the HTML form element to use
		    'section' => 'posts_display',  					// the section this setting belongs to — must match the array key of a section in wptuts_options_page_sections()
		    'choices' => array(),  							// (optional): the values in radio buttons or a drop-down menu
		    'class'   => ''  								// the HTML form element class. Also used for validation purposes!
	    );

	    // "extract" to be able to use the array keys as variables in our function output below
	    extract(wp_parse_args($args, $defaults));

	    // additional arguments for use in form field output in the function wptuts_form_field_fn!
	    $field_args = array(
		    'type'      => $type,
		    'id'        => $id,
		    'desc'      => $desc,
		    'std'       => $std,
		    'choices'   => $choices,
		    'label_for' => $id,
		    'class'     => $class,
		    'section'   => $section
	    );
	    add_settings_field($id, $title, array($this, 'display_setting'), 'jmi_' . $section, $section, $field_args);
    }

	
    /**
     * Output's the html corresponding to the given setting
     * 
     */
	public function display_setting($args = array()) {
		// "extract" to be able to use the settings keys as variables below		
		extract($args);
		// echo '<p>id: ' . $id . ', section: ' . $section . '</p>';
		$section = 'jmi_' . $section;
		$options = get_option($section);
		
		if (!isset($options[$id]) && $type != 'checkbox') $options[$id] = $std;
		elseif (!isset($options[$id])) $options[$id] = 0;
		
	    // switch html display based on the setting type.	
	    switch ( $type ) {
		    case 'text':
			    $options[$id] = stripslashes($options[$id]);
			    $options[$id] = esc_attr($options[$id]);
			    echo "<input class='regular-text$field_class' type='text' id='$id' name='" . $section . "[$id]' value='$options[$id]' />";
			    echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
		    break;
		    
		    case 'password':
			    $options[$id] = stripslashes($options[$id]);
			    $options[$id] = esc_attr($options[$id]);
			    echo "<input class='regular-text$field_class' type='password' id='$id' name='" . $section . "[$id]' value='$options[$id]'  autocomplete='off'/>";
			    echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
		    break;
		
		    case "multi-text":
			    foreach($choices as $item) {
				    $item = explode("|",$item); // cat_name|cat_slug
				    $item[0] = esc_html__($item[0], 'jmi');
				    if (!empty($options[$id])) {
					    foreach ($options[$id] as $option_key => $option_val){
						    if ($item[1] == $option_key) {
							    $value = $option_val;
						    }
					    }
				    } else {
					    $value = '';
				    }
				    echo "<span>$item[0]:</span> <input class='$field_class' type='text' id='$id|$item[1]' name='" . $section . "[$id|$item[1]]' value='$value' /><br/>";
			    }
			    echo ($desc != '') ? "<span class='description'>$desc</span>" : "";
		    break;
		
		    case 'textarea':
			    $options[$id] = stripslashes($options[$id]);
			    $options[$id] = esc_html( $options[$id]);
			    echo "<textarea class='textarea$field_class' type='text' id='$id' name='" . $section . "[$id]' rows='5' cols='30'>$options[$id]</textarea>";
			    echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : ""; 		
		    break;
		
		    case 'select':
			    echo "<select id='$id' class='select$field_class' name='" . $section . "[$id]'>";
				    foreach($choices as $item) {
					    $value 	= esc_attr($item, 'jmi');
					    $item 	= esc_html($item, 'jmi');
					
					    $selected = ($options[$id] == $value) ? 'selected="selected"' : '';
					    echo "<option value='$value' $selected>$item</option>";
				    }
			    echo "</select>";
			    echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : ""; 
		    break;
		
		    case 'select2':
			    echo "<select id='$id' class='select$field_class' name='" . $section . "[$id]'>";
			    foreach($choices as $item) {
				
				    $item = explode("|",$item);
				    $item[0] = esc_html($item[0], 'jmi');
				
				    $selected = ($options[$id]==$item[1]) ? 'selected="selected"' : '';
				    echo "<option value='$item[1]' $selected>$item[0]</option>";
			    }
			    echo "</select>";
			    echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
		    break;
		
		    case 'checkbox':
			    echo "<input class='checkbox$field_class' type='checkbox' id='$id' name='" . $section . "[$id]' value='1' " . checked($options[$id], 1, false ) . " />";
			    echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
		    break;
		
		    case "multi-checkbox":
			    foreach($choices as $item) {
				
				    $item = explode("|",$item);
				    $item[0] = esc_html($item[0], 'jmi');
				
				    $checked = '';
				
			        if ( isset($options[$id][$item[1]]) ) {
					    if ( $options[$id][$item[1]] == 'true') {
			       			$checked = 'checked="checked"';
					    }
				    }
				
				    echo "<input class='checkbox$field_class' type='checkbox' id='$id|$item[1]' name='" . $section . "[$id|$item[1]]' value='1' $checked /> $item[0] <br/>";
			    }
			    echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
		    break;
	    }
	}

	/**
	 * Validate settings by making sure they are correctly typed
	 * This function will add settings errors using the WP Settings API if necessary
	 *
	 * @param array $input           values submitted by the form
	 * @return array                 esaped and validated version of the submitted values
	 */
	public static function validate_settings($input, $settings) {
	    $valid_input = array();
        $has_errors = false;
        
	    // collect only the values we expect and fill the new $valid_input array i.e. whitelist our option IDs
		// run a foreach and switch on option type
		foreach ($settings as $setting) {
		    $id = $setting['id'];
		    $class = $setting['class'];
			switch ($setting['type']) {
		        case 'text':
				    // Check validation based on the setting class!
				    switch ($class) {
					    //for numeric 
					    case 'numeric':
						    // Accept the input only when numeric!
						    $input[$id]       = trim($input[$id]); // trim whitespace
						    $valid_input[$id] = (is_numeric($input[$id])) ? $input[$id] : __('Expecting a Numeric value', 'jmi');
						
						    // register error
						    if(!is_numeric($input[$id])) {
							    add_settings_error(
								    $id, // setting title
								    'jmi_txt_numeric_error', // error ID
								    __('Expecting a Numeric value for field: ', 'jmi') . $id, // error message
								    'error' // type of message
							    );
							    $has_errors = true;
						    }
					    break;
					    
                        // Multi-numeric values (separated by a comma)
						case 'multinumeric':
							//accept the input only when the numeric values are comma separated
							$input[$id] = trim($input[$id]); // trim whitespace
							
							if($input[$id] != ''){
								$valid_input[$id] = (preg_match('/^-?\d+(?:,\s?-?\d+)*$/', $input[$id]) == 1) ? $input[$id] : __('Expecting comma separated numeric values', 'jmi');
							}
							else{
								$valid_input[$id] = $input[$id];
							}
							
							// register error
							if($input[$id] != '' && preg_match('/^-?\d+(?:,\s?-?\d+)*$/', $input[$id]) != 1) {
								add_settings_error(
									$id, // setting title
									'jmi_txt_multinumeric_error', // error ID
									__('Expecting comma separated numeric values for field: ','jmi') . $id, // error message
									'error' // type of message
								);
								$has_errors = true;
							}
						break;
						
						// for no html
						case 'nohtml':
							// Accept the input only after stripping out all html, extra white space etc!
							$input[$id]      = sanitize_text_field($input[$id]); // need to add slashes still before sending to the database
							$valid_input[$id] = addslashes($input[$id]);
						break;
						
						// for url
						case 'url':
							//accept the input only when the url has been sanited for database usage with esc_url_raw()
							$input[$id] 	  = trim($input[$id]); // trim whitespace
							$valid_input[$id] = esc_url_raw($input[$id]);
						break;
						
						//for email
						case 'email':
							//accept the input only after the email has been validated
							$input[$id] = trim($input[$id]); // trim whitespace
							if($input[$id] != '') {
								$valid_input[$id] = (is_email($input[$id])!== FALSE) ? $input[$id] : __('Invalid email, please re-enter','jmi');
							}
							else {
								$valid_input[$id] = __('This setting field cannot be empty, please enter a valid email address', 'jmi');
							}
							
							// register error
							if(is_email($input[$id]) == FALSE || $input[$id] == '') {
								add_settings_error(
									$id, // setting title
									'jmi_txt_email_error', // error ID
									__('Please enter a valid email address for field: ','jmi') . $id, // error message
									'error' // type of message
								);
								$has_errors = true;
							}
						break;
						
						// a "cover-all" fall-back when the class argument is not set
						default:
							// accept only a few inline html elements
							$allowed_html = array(
								'a' => array('href' => array (),'title' => array ()),
								'b' => array(),
								'em' => array (), 
								'i' => array (),
								'strong' => array()
							);
							
							$input[$id] = trim($input[$id]); // trim whitespace
							$input[$id] = force_balance_tags($input[$id]); // find incorrectly nested or missing closing tags and fix markup
							$input[$id] = wp_kses( $input[$id], $allowed_html); // need to add slashes still before sending to the database
							$valid_input[$id] = addslashes($input[$id]); 
						break;					    
					}
				break;
				
				case "password" :
                    // trim whitespace and remove html tags
				    $input[$id] = sanitize_text_field($input[$id]); 
				    $valid_input[$id] = addslashes($input[$id]);
				    break;
				
				case "multi-text":
					// this will hold the text values as an array of 'key' => 'value'
					unset($textarray);
					
					$text_values = array();
					foreach ($setting['choices'] as $k => $v) {
						// explode the connective
						$pieces = explode("|", $v);
						$text_values[] = $pieces[1];
					}
					
					foreach ($text_values as $v ) {		
						// Check that the option isn't empty
						if (!empty($input[$id . '|' . $v])) {
							// If it's not null, make sure it's sanitized, add it to an array
							switch ($class) {
								// different sanitation actions based on the class create you own cases as you need them
								
								//for numeric input
								case 'numeric':
									//accept the input only if is numberic!
									$input[$id . '|' . $v] = trim($input[$id . '|' . $v]); // trim whitespace
									$input[$id . '|' . $v] = (is_numeric($input[$id . '|' . $v])) ? $input[$id . '|' . $v] : '';
								break;
								
								// a "cover-all" fall-back when the class argument is not set
								default:
									// strip all html tags and white-space.
									$input[$id . '|' . $v] = sanitize_text_field($input[$id . '|' . $v]); // need to add slashes still before sending to the database
									$input[$id . '|' . $v] = addslashes($input[$id . '|' . $v]);
								break;
							}
							// pass the sanitized user input to our $textarray array
							$textarray[$v] = $input[$id . '|' . $v];
						
						} else {
							$textarray[$v] = '';
						}
					}
					// pass the non-empty $textarray to our $valid_input array
					if (!empty($textarray)) {
						$valid_input[$id] = $textarray;
					}
				break;				

				case 'textarea':
					//switch validation based on the class!
					switch ($class) {
						//for only inline html
						case 'inlinehtml':
							// accept only inline html
							$input[$id] 		= trim($input[$id]); // trim whitespace
							$input[$id] 		= force_balance_tags($input[$id]); // find incorrectly nested or missing closing tags and fix markup
							$input[$id] 		= addslashes($input[$id]); //wp_filter_kses expects content to be escaped!
							$valid_input[$id] = wp_filter_kses($input[$id]); //calls stripslashes then addslashes
						break;
						
						//for no html
						case 'nohtml':
							//accept the input only after stripping out all html, extra white space etc!
							$input[$id] 		= sanitize_text_field($input[$id]); // need to add slashes still before sending to the database
							$valid_input[$id] = addslashes($input[$id]);
						break;
						
						//for allowlinebreaks
						case 'allowlinebreaks':
							//accept the input only after stripping out all html, extra white space etc!
							$input[$id] 		= wp_strip_all_tags($input[$id]); // need to add slashes still before sending to the database
							$valid_input[$id] = addslashes($input[$id]);
						break;
						
						// a "cover-all" fall-back when the class argument is not set
						default:
							// accept only limited html
							//my allowed html
							$allowed_html = array(
								'a' 			=> array('href' => array (),'title' => array ()),
								'b' 			=> array(),
								'blockquote' 	=> array('cite' => array ()),
								'br' 			=> array(),
								'dd' 			=> array(),
								'dl' 			=> array(),
								'dt' 			=> array(),
								'em' 			=> array (), 
								'i' 			=> array (),
								'li' 			=> array(),
								'ol' 			=> array(),
								'p' 			=> array(),
								'q' 			=> array('cite' => array ()),
								'strong' 		=> array(),
								'ul' 			=> array(),
								'h1' 			=> array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()),
								'h2' 			=> array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()),
								'h3' 			=> array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()),
								'h4' 			=> array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()),
								'h5' 			=> array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()),
								'h6' 			=> array('align' => array (),'class' => array (),'id' => array (), 'style' => array ())
							);
							
							$input[$id] 		= trim($input[$id]); // trim whitespace
							$input[$id] 		= force_balance_tags($input[$id]); // find incorrectly nested or missing closing tags and fix markup
							$input[$id] 		= wp_kses( $input[$id], $allowed_html); // need to add slashes still before sending to the database
							$valid_input[$id]   = addslashes($input[$id]);							
						break;
					}
				break;
				
				case 'select':
					// check to see if the selected value is in our approved array of values!
					$valid_input[$id] = (in_array( $input[$id], $setting['choices']) ? $input[$id] : '');
				break;
				
				case 'select2':
					// process $select_values
					$select_values = array();
					foreach ($setting['choices'] as $k => $v) {
						// explode the connective
						$pieces = explode("|", $v);
						
						$select_values[] = $pieces[1];
					}
					// check to see if selected value is in our approved array of values!
					$valid_input[$id] = (in_array( $input[$id], $select_values) ? $input[$id] : '');
				break;
				
				case 'checkbox':
					// if it's not set, default to null!
					if (!isset($input[$id])) {
						$input[$id] = null;
					}
					// Our checkbox value is either 0 or 1
					$valid_input[$id] = ($input[$id] == 1 ? 1 : 0);
				break;
				
				case 'multi-checkbox':
					unset($checkboxarray);
					$check_values = array();
					foreach ($setting['choices'] as $k => $v) {
						// explode the connective
						$pieces = explode("|", $v);
						$check_values[] = $pieces[1];
					}
					
					foreach ($check_values as $v) {		
						// Check that the option isn't null
						if (!empty($input[$id . '|' . $v])) {
							// If it's not null, make sure it's true, add it to an array
							$checkboxarray[$v] = 'true';
						}
						else {
							$checkboxarray[$v] = 'false';
						}
					}
					// Take all the items that were checked, and set them as the main option
					if (!empty($checkboxarray)) {
						$valid_input[$id] = $checkboxarray;
					}
				break;				
		    }
		}
		
		if($has_errors === false) return $valid_input;
	}
}
?>
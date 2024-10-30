<?php

require_once dirname( __FILE__ ) . '/SettingsMenuManager.php';

/**
 * Representation of a Wordpress Settings API section element
 * 
 * From Wordpress codex [http://codex.wordpress.org/Function_Reference/add_settings_section] : 
 * Settings Sections are the groups of settings you see on WordPress settings pages with a shared heading. 
 * In your plugin you can add new sections to existing settings pages rather than creating a whole new page. 
 * This makes your plugin simpler to maintain and creates less new pages for users to learn. 
 * You just tell them to change your setting on the relevant existing page. 
 * 
 * @package jmi
 * @author Jonathan Dray <jonathan@social-computing.com>
 */
class SettingsSection {
    private $id; 
    private $title;
    private $description;
    private $page;
	private $settings;

	/**
	 * Default class constructor
	 * 
	 * @param string id            text value identifing the section
	 * @param string $title        title to display
	 * @param string $description  a brief description displayed just below the title
	 * @param string $page         name of the page on which this section should be displayed
	 */
	function __construct($id, $title, $description, $page, $settings) {    
	    $this->id = $id;
	    $this->title = $title;
	    $this->description = $description;
	    $this->page = $page;
		$this->settings = $settings;
	}

	/**
	 * Sectop, Id getter
	 * 
	 * @return string  the section id
	 */
	public function getId() {
		return $this->id;
	}
		
	/**
	 * Helper function to get an html localized version of the section's description
	 * 
	 * @return string  html formated description of the section
	 */
	public function display_description() {
	    echo "<p>" . __($this->description, 'jmi') . "</p>";
	}
	
	/**
	 * Call the add_settings_section wordpress function to add the section to the plugin
	 */
	public function register() {
		register_setting('jmi_' . $this->id, 'jmi_' . $this->id, array($this, 'validate'));
		add_settings_section($this->id, __($this->title, 'jmi'), array($this, 'display_description'), 'jmi_' . $this->page);
	}
	
	/**
	 * Default section settings validation function
	 * It calls the SettingsMenuManager static validation function by default
	 * TODO : add a way to override this
	 */
	public function validate($input) {
		return SettingsMenuManager::validate_settings($input, $this->settings);
	}
}
?>
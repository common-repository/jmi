<?php

require_once dirname( __FILE__ ) . '/SettingsMenuManager.php';

/**
 * Just Map It! plugin installer.
 * Handles settings upgrade 
 *
 * @package jmi
 * @author Jonathan Dray <jonathan@social-computing.com>
 */
class JmiInstaller {

	private $version;
	function __construct($version) {
		$this->version = $version;
	}
	
	function install() {
		$dbVersion = get_option('jmi_version');
		
		if (!isset($dbVersion) || version_compare($dbVersion, $this->version, '<')) {
            $this->updateSettings();
        }
		return true;
	}
	
	function updateSettings() {
		$oldSettings = get_option('jmi_options');
		$settingsManager = new SettingsMenuManager();
		
		if($oldSettings === false) {
			$settingsManager->updateSettings();
		}
		else {
			$settingsManager->updateFromOldSettings($oldSettings);
			$settingsManager->removeOldSettings();			
		}
		update_option('jmi_version', $this->version);
	}
}
?>
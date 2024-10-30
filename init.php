<?php
/*
  Plugin Name: Just Map It!
  Plugin URI:  http://www.social-computing.com/
  Description: This plugin enables the visualization of your blog posts as an interactive map, displaying relationships between posts and tags.
  Version:     trunk
  Author:      Social Computing
  Author URI:  http://www.social-computing.com/
  Text domain: jmi
  
  Copyright (c) 2011 - 2012 Social Computing (email: wordpress at social-computing dot com)

  This library is free software; you can redistribute it and/or
  modify it under the terms of the GNU Lesser General Public
  License as published by the Free Software Foundation; either
  version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
  Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public
  License along with this library; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/
require_once dirname( __FILE__ ) . '/jmi.php';
require_once dirname( __FILE__ ) . '/admin/JmiInstaller.php';
require_once dirname( __FILE__ ) . '/util/SolrUtil.php';

/**
 * Just Map It! plugin activation hepler.
 * Called when a user installs and activate the plugin on the admin page 
 */
register_activation_hook(__FILE__, array('JmiInit', 'activate'));
final class JmiInit {
	
	const version = '2.0'; //plugin version
	
	/**
	 * Performs a batch of activation checks on plugin activation
	 * If the checks fail, display an error message to the user and automatically
	 * deactive the plugin
	 */
	public static function activate() {
		$status = JmiInit::activationChecks();
	    if (is_string($status)) {
	        // Deactivate without calling the deactivatation hook
    		deactivate_plugins('jmi/init.php', true);
    		wp_die($message);
	    }
		
		$jmiInstaller = new JmiInstaller(JmiInit::version);
		$status = $jmiInstaller->install();
		
		if (is_string($status)) {
    		deactivate_plugins('jmi/init.php', true);
    		wp_die($message);
	    }
	} 
	
	/**
	 * Performs the necessary activation check :
	 *   - php version
	 *   - wordpress version
	 */
	public static function activationChecks() {
		if (!function_exists('spl_autoload_register')) {
        	return __('Just Map It! is not activated. You must have at least PHP 5.1.2 to use this plugin', 'jmi');
    	}

	    if (version_compare(get_bloginfo('version'), '3.0', '<')) {
	        return __('Just Map It! is not activated. You must have at least WordPress 3.0 to use this plugin', 'jmi');
	    }
	    return true;
	}
}

// Instanciate the Just Map It! main class
$jmi = new Jmi(JmiInit::version);
$jmi->run();

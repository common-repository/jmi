<?php

/**
 * Just Map It! Client installer  
 *
 * @package jmi
 * @author Jonathan Dray <jonathan@social-computing.com>
 */
class ClientInstaller {

	const admin_url = 'options-general.php?page=jmi/jmi.php';
	const client_download_url = 'http://wordpress.just-map-it.com/client/jmi-canvas-1.0-min.zip';

	private $jsPath;
	private $jsURL;
	private $flashPath;
	private $flashURL;
	

	function __construct() {
		$uploads = wp_upload_dir();
		$name = 'jmi';
		$basePath = $uploads["basedir"] . "/" . $name;
		$baseUrl =  $uploads["baseurl"] . "/" . $name;
		
		$this->flashPath = $basePath . '/jmi-flex.swf';
		$this->flashURL =  $baseUrl .  '/jmi-flex.swf';
		
		$this->jsPath = $basePath . '/jmi-canvas-1.0';
		$this->jsURL  = $baseUrl  . '/jmi-canvas-1.0';
	}
	
	public function isInstalled() {
		if($this->getClientType() != "") {
			return true;
		}
		return false;
	}
	
	public function getClientType() {
		// Getting client settings from database
		$client_options = get_option('jmi_client');
		$type = (isset($client_options['type']) ? $client_options['type'] : '');
		return $type;
	}
	
	public function getJsURL() {
		return $this->jsURL;
	}
	
	public function getFlashURL() {
		return $this->flashURL;
	}
	
	public function getClientURL() {
		$clientType = $this->getClientType();
		if($clientType == 'js') {
			return $this->jsURL;
		}
		elseif ($clientType == 'flash') {
			return $this->flashURL;
		}
		else {
			return '';
		}
	}
	
	public function displayInstallPage() {
		$installer = isset($_GET['installer']) ? $_GET['installer'] : '';
		
		// If the js client directory exists 	
		if(file_exists($this->jsPath)) {
			$this->displayTitle('Client installation');
			$this->displayCongratulationMessage();
			echo '<p>' . __('The HTML5 / Javascript client has been detected in your Wordpress upload directory. Click on the save button to complete the installation process.', 'jmi') . '</p>' .
			     '<form method="post" action="options.php">';
			settings_fields('jmi_client');
			echo '<input type="hidden" id="type" name="jmi_client[type]" value="js" />';
			submit_button();
			echo '</form>';
		}
		
		// If the flash client is found
		else if(file_exists($this->flashPath)) {
			if($installer == 'upgrade') {
				$this->displayTitle('Client upgrade');
				$this->displayNotFoundIfRequired();
				?>
	                <p>
	                    <?php _e('To complete the installation process follow the next steps: ', 'jmi'); ?>
	                    <ol>
	                        <li><?php printf(__('Download the HTML5 / Javascript client archive file <a href="%s" title="Just Map It! - Javacript client">here</a>', 'jmi'), ClientInstaller::client_download_url); ?></li>
	                        <li>
	                        	<?php 
	                        		_e('Extract the downloaded file in a subdirectory named "jmi" in your Wordpress upload directory', 'jmi');
									$this->displayExpectedLocation();
								?>
	                        </li>
	                        <li><?php _e('Click on the "next" button below.', 'jmi'); ?></li>
	                    </ol>
	                    <p class="submit"><a href="options-general.php?page=jmi/jmi.php&installer=upgrade&validate=true" class="button"><?php _e('Next', 'jmi'); ?></a></p>
	                </p>
	            <?php
			}
			else {
				$this->displayTitle('Client installation');
				$this->displayCongratulationMessage();
				$this->displayUpgradeMessage();
				echo '<br><h3>' . __('Next step', 'jmi') . '</h3>' .
				     '<p>' . __('It is highly recommended that you upgrade to the new version. If you still want to continue to use the flash client, click on the skip button, else click on the upgrade button and follow the installation instructions.', 'jmi') . '</p>' .
				     '<form method="post" action="options.php">' .
				     '<p class="submit">
						<input name="Submit" type="submit" class="button-primary" value="' . __('Skip', 'jmi') . '" />
						<a href="options-general.php?page=jmi/jmi.php&installer=upgrade" class="button">' . __('Upgrade', 'jmi') . '</a>			
					  </p>';
				settings_fields('jmi_client');
				echo '<input type="hidden" id="type" name="jmi_client[type]" value="flash"/>' .
				     '</form>';
			}
		}
		else {
			$this->displayTitle('Client installation');
			if($installer == 'install') {
				$this->displayNotFoundIfRequired();
				?>
					<h3><?php _e('To complete the installation process follow the next steps: ', 'jmi'); ?></h3>
	                <p>
	                    
	                    <ol>
	                        <li><?php printf(__('Download the HTML5 / Javascript client archive file <a href="%s" title="Just Map It! - Javacript client">here</a>', 'jmi'),  ClientInstaller::client_download_url); ?></li>
	                        <li>
	                        	<?php 
	                        		_e('Create a subdirectory named "jmi" in the Wordpress upload directory and extract the downloaded file in that directory', 'jmi'); 
	                        		$this->displayExpectedLocation();
	                        	?>
	                        	</li>
	                        <li><?php _e('Click on the "next" button below.', 'jmi'); ?></li>
	                    </ol>
	                    <p class="submit"><a href="options-general.php?page=jmi/jmi.php&installer=install&validate=true" class="button"><?php _e('Next', 'jmi'); ?></a></p>
	                </p>
	            <?php
			}
			else {
				$this->displayCongratulationMessage(); 
				echo '<br>' . 
				     __('You are <strong>one step </strong> from activating the Just Map It! plugin. The HTML5 / Javascript client used by this plugin needs to be downloaded and placed in your wordpress upload directory. <br>Click on the next button and follow the installation instructions.', 'jmi') . '</p>';
				echo '<p class="submit"><a href="options-general.php?page=jmi/jmi.php&installer=install" class="button">' . __('Next', 'jmi') . '</a></p>';
			}
		}

		$this->displayTermsOfUseNotice();
	}

	public function displayUpgradeMessage() {
		echo '<p>' . __('We have detected that you were using a previous version of the Just Map It! plugin with the flash client installed.<br> A new version of the Just Map It! client is now available !', 'jmi') . '</p><br>' .
	    	 '<h3>'. __('Features of the new client', 'jmi') . '</h3>';
			 
		echo '<h4>' . __('Breadcrumb and navigation history', 'jmi') . '</h4>
		      <p>'  . __('Keep your navigation history visible with the breadcrumb at the top of the map. Click on one item to easily go back to a previously generated map.', 'jmi') . '</p>';
			   
		echo '<h4>' . __('Full screen mode', 'jmi') . '</h4>
		      <p>'  . __('Take advantage of your screen size and explore the map in full screen mode.', 'jmi') . '</p>';
			  
		echo '<h4>' . __('Image snapshot', 'jmi') . '</h4>
		      <p>'  . __('Want to share a specific view of a map ? Take a snapshot as an image and send it to your friends.', 'jmi') . '</p>';

		echo '<h4>' . __('Reduced size and load time', 'jmi') . '</h4>
		      <p>'  . __('Your blog readers will download a 5 times more compact code. The execution time is also 30% faster.', 'jmi') . '</p>';					 
			  
		echo '<h4>' . __('Tablets and mobile device support', 'jmi') . '</h4>
		      <p>'  . __('This new client is tablet and mobile device compliant. As all browsers are not HTML5 ready, our client api will automatically fallback to the flash version if necessary.', 'jmi') . '</p>';			  
			  
	    echo '<h4>' . __('Multiple maps', 'jmi') . '</h4>
		      <p>'  . __('Embed one or multiple maps in your blog posts and pages with the Just Map It! shortcode for Wordpress.<br> The shortcode can be used with the currently installed Flash version, however in that case it is limited to only one map per blog page.', 'jmi') . '</p>';
	}

	public function displayNotFoundIfRequired() {
		$validate = isset( $_GET[ 'validate' ] ) ? $_GET[ 'validate' ] : '';
		// If we get here with the validate parameter, that means that the jmi-client js directory was not found
		if($validate == 'true') {
			echo '<div id="message" class="error settings-error">
			        <p><strong>' . __('The HTML5 / Javascript client directory was not found. Please double check the expected installation folder and try again.', 'jmi') . 
			     '</strong></p></div>';
		}		
	}
	
	public function displayTermsOfUseNotice() {
		echo '<div class="updated">';
		printf(__('Please note that in order to install and use the Just Map It! client you must agree with our <a href="%s" title="Terms of use">terms of use</a>. If it is not the case, remove any already installed Just Map It! client and uninstall this plugin.', 'jmi'), plugins_url('terms_of_use.txt', __FILE__)); 
		echo '</div>';
	}

	public function displayTitle($title) {
		echo '<h2>' . __('Just Map It! - ' . $title, 'jmi') . '</h2>';
	}
	
	public function displayCongratulationMessage() {
		echo '<h3>' . __('Congratulations', 'jmi') . '</h3>' .
		     '<p>' . __('You have successfully installed the Just Map It! Plugin for Wordpress.', 'jmi');
	}
	
	public function displayExpectedLocation() {
	    printf('<div style="background-color: #FFFFE0;border: 1px solid #E6DB55;margin: 5px 0 15px;padding: 0 0.6em;">' .
	           __('The expected location is: %s', 'jmi') . 
	           '</div>', $this->jsPath);
	}
}
?>
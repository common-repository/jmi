<?php

require_once dirname( __FILE__ ) . '/util/ArrayUtil.php';

class JmiMap {
	private $clientInstaller;
	
	public function __construct($clientInstaller) {
		$this->clientInstaller = $clientInstaller;
	}
	
	public function getMapTags($args) {
		// Needed to include the jmi js file here
    	// Replace with wp_enqueue_script when only wordpress >= 3.3 compatibility is needed
    	global $jmi_maps;
		$params = $this->loadDefaultParameters($args);
		
		$map = '';
		$clientType = $this->clientInstaller->getClientType();
		if($clientType == 'js') {
			if(!$jmi_maps) $jmi_maps = array();
			$map_index =  count($jmi_maps);
			$jmi_maps[] = array('map' => ArrayUtil::removeFromArray($params, array("serverurl", "width", "height")),
			                    'clientURL' => $this->clientInstaller->getClientURL(),
			                    'serverURL' => $params['serverurl']);
			$map = '<div id="breadcrumb' . $map_index . '">&nbsp;</div>
			        <div id="map' . $map_index . '" style="width:' . $params['width'] . 'px;height:' . $params['height'] . 'px"></div>';
		}
		else if($clientType == 'flash'){
			if(!$jmi_maps) {
				// Needed by the current flash client to work: 
				$params['wpsserverurl'] = $params['serverurl'];
				$params['wpsplanname'] = $params['map'];
				
				$jmi_maps = array('map' => ArrayUtil::removeFromArray($params, array("serverurl", "map")),
			                      'clientURL' => $this->clientInstaller->getClientURL());
		    	$map = '<div id="status"></div>
		                <div id="map"></div>';
			}
		}
		return $map;				
	}
	
	public function display($args) {
		echo $this->getMapTags($args);
	}
	
	public function loadDefaultParameters($args) {
		// Load settings stored in database
		$storedSettings = array_merge(get_option('jmi_posts_display'), get_option('jmi_advanced'));
		
		// Add some default map settings
		$storedSettings = array_merge($storedSettings, 
							          array('analysisProfile' => 'GlobalProfile',
	                                        'invert' => true));
											
		// Take account of the parameters given in arguments
		$finalSettings = array_merge($storedSettings, $args);
		
		// Add wp url, needed for url redirections in js events
		$finalSettings['wpurl'] = get_bloginfo("wpurl");
		
		// Remove some plugin parameters specific values, they are not desired for the js client nor the server 
		//   * json_controller
		//   * max_posts
		//   * id
		//   * show_map_in_post
		return ArrayUtil::removeFromArray($finalSettings, array("json_controller", "max_posts", "id", "show_map_in_post"));
	}
}

?>
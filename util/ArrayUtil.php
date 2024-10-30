<?php

class ArrayUtil {

	/**
	 * Utility function
	 * Remove some elements from an array according to the key names
	 * 
	 * @param $arr    the array on which to remove the elements
	 * @param $keys   an array of keys to remove with their value from an array
	 * 
	 * @return array  an array that only contains elements for which the keys are not equals to the one in the $keys parameter
	 */
	public static function removeFromArray($arr, $keys) {
		$toRemove = array();	
		foreach ($keys as $key) {
			//unset($arr[$key]);
			$toRemove[$key] = '';
		}
		return array_diff_key($arr, $toRemove);
	}
	
	
	public static function replaceByKey($arr, $replacements){
        foreach ($replacements as $key => $value) {
	        if(isset($arr[$key])) {
	        	$arr[$value] = $arr[$key];
				unset($arr[$key]);
			}    
        }	
		return $arr;
	}
}

?>
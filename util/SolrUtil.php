<?php

/**
 * Solr helper utility class
 * Used by the map generated with the solr for wordpress plugin
 */
class SolrUtil {
 	/*
     * Filters the selected facets to match the given type
     *
	 * @param $facets an array of selected facets
	 * @param $type a facet type (could be tags, categories, authors...)
	 * 
	 * @return an array of selected facets matching the given type
	 */
	public static function getSelectedFacets($facets, $type) {
		    $selectedTags = array();
		    if ($facets) {
		            foreach ($facets as $selectedfacet) {
		            $splititm = split(':', $selectedfacet["name"], 2);
		                    if($splititm[0] == $type) {
		                            $selectedTags[$splititm[1]] = html_entity_decode($selectedfacet['removelink']);
		                    }
		            }
		    }
		    return $selectedTags;
	}

	/*
	 * Get all the available facets for a given type
	 * 
	 * @param $facets array of all facets as returned by the solr results object 
	 * @param $type a facet type (ie tags, categories, authors, ...)
	 * 
	 * @return an array of facets available for one type and one query
	 */
	public static function getAvailableFacets($facets, $type) {
		    $availableFacets = array();
		    if ($facets) {
		            foreach ($facets as $facet) {
		                    if($facet['name'] == $type) {
		                            foreach ($facet['items'] as $item) {
		                                    $availableFacets[$item['name']] = html_entity_decode($item['link']);
		                            }
		                    }
		            }
		    }
		    return $availableFacets;        
	}
}

?>

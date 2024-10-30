 /* Called when an entity is selected (link = a tag here) : filter action */
JMI.wordpress.events.Filter = function(parameters, map, args) {
    var selectedTags = jmisolr['selectedTags'];
    var availableTags = jmisolr['availableTags'];
    // Test if the selected tag is on the already selected facet items    
    // Add quotes around the selected value on the map
    var selectedTag = "\"" + args[1] + "\"";
    if(selectedTag in selectedTags) {
        window.location.href = selectedTags[selectedTag];
    }
    // Add the filter if it is not already selected
    else if(args[1] in availableTags) {
        window.location.href = availableTags[args[1]];
    }
    else {
        // If the tag does not exist in the result list
        alert("Aucun article dans la liste de résultat ne contient le mot clé \"" + args[1] + "\".\nMerci de choisir un autre mot clé");
    }
} 
/* Called when an entity is selected (link = a tag here) : open new window */
JMI.wordpress.events.SolrDisplayLink = function(parameters, map, args) {
    JMI.wordpress.events.redirect(parameters, "?tag=" + args[0]);
}
/* Called when an node is selected (node = a post here) : open new window */
JMI.wordpress.events.SolrDisplayNode = function(parameters, map, args) {
    JMI.wordpress.events.redirect(parameters, "?p=" + args[0]);
}

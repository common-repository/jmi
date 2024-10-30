//JMI.namespace("worpdress");    
JMI.namespace("wordpress.events");
    
JMI.wordpress.map = function(container, breadcrumb_container, parameters) {
    this.container = container;
    this.breadcrumb_container = breadcrumb_container;
    this.map = JMI.Map({
        parent: this.container, 
        clientUrl: parameters.clientURL, 
        server: parameters.serverURL,
    });
    this.parameters = parameters.map;
    
    // Add listners to map events scoped in this namespace
    this.map.addEventListener(JMI.Map.event.ACTION, function(event) {
        JMI.wordpress.events[event.fn](parameters.map, event.map, event.args);
    });
    new JMI.extensions.Breadcrumb(this.breadcrumb_container,this.map,{'namingFunc':JMI.wordpress.breadcrumb,'thumbnail':{}});
    new JMI.extensions.Slideshow(this.map);
}

JMI.wordpress.map.prototype.draw = function() {
    this.map.breadcrumbTitles = {shortTitle: 'Initial map', longTitle: 'Initial map'};
    this.map.compute(this.parameters);
}

JMI.wordpress.breadcrumb = function(event) {
    if (event.type === JMI.Map.event.EMPTY) {
        return {shortTitle: 'Sorry, the map is empty.', longTitle: 'Sorry, the map is empty.'};
    }
    else if (event.type === JMI.Map.event.ERROR) {
        // Quota error
        if (event.code === 1000) {
            // Only display the error message
            return {shortTitle: event.message, longTitle: event.message};
        }
        if(event.track) {
            return {shortTitle: 'Sorry, an error occured. If you want to be informed about it, please <a title="Fill the form" href="http://www.just-map-it.com/p/report.html?track='+ event.track +'" target="_blank">fill the form</a>', longTitle: 'Sorry, an error occured. Error: ' + event.message};
        }
        else {
            return {shortTitle: 'Sorry, an error occured. ' + event.message, longTitle: 'Sorry, an error occured. ' + event.message};
        }
    }
    
    return event.map.breadcrumbTitles;
};


/*
 * ******************************* *
 * * Define map events functions * *
 * ******************************* *
 */

/**
 * Focus = DiscoverLink
 * Called when an entity is selected (link)
 */
JMI.wordpress.events.DiscoverLink = function(parameters, map, args) {
    parameters.entityId = args[0];
    parameters.analysisProfile = "AnalysisProfile";
    map.compute(parameters);
    map.breadcrumbTitles.shortTitle = "Focus";
    map.breadcrumbTitles.longTitle = "Focus on: " + args[1];
};

/**
 * Center = DiscoverNode
 * Called when an attribute is selected (node)
 */
JMI.wordpress.events.DiscoverNode = function(parameters, map, args) {
    parameters.attributeId = args[0];
    parameters.analysisProfile = "DiscoveryProfile";
    map.compute(parameters);
    map.breadcrumbTitles.shortTitle = "Centered";
    map.breadcrumbTitles.longTitle = "Centered on: " + args[1];
};

/**
 * Called when a user click on the display item on a node on the map
 */
JMI.wordpress.events.DisplayNode = function(parameters, map, args) {
    var id   = args[0],
        slug = args[1],
        name = args[2],
        url = args[3];
        invert = parameters.invert;
    var rel_url = (invert == true) ? "?p=" + id : "?tag=" + slug;
    JMI.wordpress.events.redirect(parameters, rel_url, url);
}

/**
 * Called when a user click on the display item on a link on the map 
 */
JMI.wordpress.events.DisplayLink = function(parameters, map, args) {
    var id   = args[0],
        slug = args[1],
        name = args[2],
        url = args[3];
        invert = parameters.invert;
    var rel_url = (invert == true) ? "?tag=" + slug : "?p=" + id;
    JMI.wordpress.events.redirect(parameters, rel_url, url);
}

/*
 * Direct redirection when url is available 
 * Otherwise, use the relative url provided 
 */
JMI.wordpress.events.redirect = function(parameters, rel_url, url) {
    var redirectUrl;
    if (typeof url != 'undefined' && url != "") {
        redirectUrl = url;
    }
    else {
        redirectUrl = parameters.wpurl + '/' + rel_url;
    }
    window.location.href = redirectUrl;
}

/**
 * Load all maps and their configuration when the page was renderedby the browser
 */
jQuery(document).ready(function($) {
    for(i = 0; i < jmimaps.length; i++) {
        var map = new JMI.wordpress.map('map' + i, 'breadcrumb' + i, jmimaps[i]);
        map.draw();        
    }
});
/* Called when an attribute is selected (node) */
function DiscoverNode(args) {
    delete(jmimaps.map.entityId);
    jmimaps.map.attributeId = args[0];
    jmimaps.map.analysisProfile = "DiscoveryProfile";
    $("#jmi-flex").get(0).compute(jmimaps.map);
}

/* Called when an entity is selected (link) */
function DiscoverLink(args) {
    delete(jmimaps.map.attributeId);
    jmimaps.map.entityId = args[0];
    jmimaps.map.analysisProfile = "AnalysisProfile";
    $("#jmi-flex").get(0).compute(jmimaps.map);
}

/* Called when a user click on the display item on a node on the map */
function DisplayNode(args) {
    var id   = args[0],
        slug = args[1],
        name = args[2],
        url = args[3];
    var rel_url = (jmimaps.map.invert == true) ? "?p=" + id : "?tag=" + slug;
    redirect(rel_url, url);
}

/* Called when a user click on the display item on a link on the map */
function DisplayLink(args) {
    var id   = args[0],
        slug = args[1],
        name = args[2],
        url = args[3];
    var rel_url = (jmimaps.map.invert == true) ? "?tag=" + slug : "?p=" + id;
    redirect(rel_url, url);
}

/* Direct redirection when url is available 
   Otherwise, use the relative url provided */
function redirect(rel_url, url) {
    var redirectUrl;
    if (typeof url != 'undefined' && url != "") {
        redirectUrl = url;
    }
    else {
        redirectUrl = jmimaps.map.wpurl + '/' + rel_url;
    }
    window.location.href = redirectUrl;
}

function error(error) {
    $("#status").get(0).innerHTML = "<h3>An error occured</h3>\n" + error;
}

jQuery(document).ready(function($) {
    var flashvars = jmimaps.map;
    var params = {
        quality: "high",
        bgcolor: "#FFFFFF",
        allowscriptaccess: "sameDomain",
        allowfullscreen: "true",
        fullScreenOnSelection: "true"
    };
    var attributes = {
        id: "jmi-flex",
        name: "jmi-flex",
        align: "middle"
    };
    swfobject.embedSWF(
        jmimaps.clientURL,
        "map", 
        jmimaps.map['width'],
        jmimaps.map['height'], 
        "10.0.0",
        "expressInstall.swf", 
        flashvars, 
        params, 
        attributes
    );
});
<?php

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Google Maps Example</title>
     <script src='http://code.jquery.com/jquery.min.js' type='text/javascript'></script>
</head>
<body>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
var infowindow = null;
    $(document).ready(function () { initialize();  });

    function initialize() {

        var centerMap = new google.maps.LatLng(-33.923036, 151.259052);

        var myOptions = {
            zoom: 10,
            center: centerMap,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

        setMarkers(map, sites);
	    infowindow = new google.maps.InfoWindow({
                content: "loading..."
            });

    }

    var sites = [
	['Truck A', -33.890542, 151.274856, 1, 'Truck A. Speed 40kmh'],
	['Truck B', -33.923036, 151.259052, 2, 'Truck A. Speed 50kmh'],
	['Truck C', -34.028249, 151.157507, 3, 'Truck A. Speed 60kmh'],
	['Truck D', -33.950198, 151.259302, 4, 'Truck A. Speed 70kmh']
];


    function setMarkers(map, markers) {

        for (var i = 0; i < markers.length; i++) {
            var sites = markers[i];
            var siteLatLng = new google.maps.LatLng(sites[1], sites[2]);
            var marker = new google.maps.Marker({
                position: siteLatLng,
                map: map,
                title: sites[0],
                zIndex: sites[3],
                html: sites[4]
            });

            var contentString = "Some content";

            google.maps.event.addListener(marker, "click", function () {
                infowindow.setContent(this.html);
                infowindow.open(map, this);
            });
        }
    }
</script>

<div id="map_canvas" style="width: 600px; height: 600px;"></div>
</body>
</html>

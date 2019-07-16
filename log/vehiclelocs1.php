<?php
session_start();

$admindb = $_SESSION['s_admindb'];

require_once('../db.php');

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

date_default_timezone_set($_SESSION['s_timezone']);

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

$trucklocs = "";
$x = 0;
$q = "SELECT uid, MAX(datetime) as dt, truckno,latitude,longitude FROM travellog GROUP BY truckno DESC";
$r = mysql_query($q) or die(mysql_error());
while ($row = mysql_fetch_array($r)) {
	extract($row);
	$x = $x = 1;
	$trucklocs = "['".$truckno."', ".$latitude.", ".$longitude.", ".$x.", '".$truckno.". ".$dt." speed ....'"."]";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Vehcile Locations</title>
     <script src='http://code.jquery.com/jquery.min.js' type='text/javascript'></script>
</head>
<body>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
var infowindow = null;
    $(document).ready(function () { initialize();  });

    function initialize() {

        var centerMap = new google.maps.LatLng(-34.886283, 173.361497);

		var myOptions = {
            zoom: 10,
            center: centerMap,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		
  		//var markerBounds = new google.maps.LatLngBounds();

        setMarkers(map, sites);
	    infowindow = new google.maps.InfoWindow({
                content: "loading..."
            });
		 // Finally we can call the Map.fitBounds() method to set the map to fit
		 // our markerBounds
		// map.fitBounds(markerBounds);
    }
	
	var sites = "["+"<?php echo $trucklocs; ?>"+"]";

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
				
				 // Extend markerBounds with each random point.
				 //markerBounds.extend(randomPoint);				
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

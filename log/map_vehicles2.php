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
	$trucklocs = "['".$truckno."', ".$latitude.", ".$longitude.", ".$x.", '".$truckno.". ".$dt." speed ....'"."],";
}
$trucklocs = substr($trucklocs,0,-1);
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Vehcile Locations Map</title>
   <meta http-equiv="content-type" content="text/html; charset=UTF-8"/> 
   <script src="http://maps.google.com/maps/api/js?sensor=false" 
           type="text/javascript"></script> 
</head>
<body>
   <div id="map" style="width: 984px; height: 684px;"></div> 

   <script type="text/javascript"> 

   var map = new google.maps.Map(document.getElementById('map'), { 
     mapTypeId: google.maps.MapTypeId.TERRAIN
   });

   var markerBounds = new google.maps.LatLngBounds();
    var vsites = [<?php echo $trucklocs; ?>];

        for (var i = 0; i < vsites.length; i++) {
            var sites = vsites[i];
            var siteLatLng = new google.maps.LatLng(vsites[1], vsites[2]);
            var marker = new google.maps.Marker({
                position: siteLatLng,
                map: map,
                title: vsites[0],
                zIndex: vsites[3],
                html: vsites[4]
            });

     // Extend markerBounds with each random point.
     markerBounds.extend(siteLatLng);
   }

   // At the end markerBounds will be the smallest bounding box to contain
   // our 10 random points

   // Finally we can call the Map.fitBounds() method to set the map to fit
   // our markerBounds
   map.fitBounds(markerBounds);

   </script> 
</body>
</html>

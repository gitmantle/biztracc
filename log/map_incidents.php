<?php
session_start();

$bdate = $_REQUEST['bdate'];
$edate = $_REQUEST['edate'];
$res = explode('x',$_REQUEST['res']);
$width = $res[0] - 50;
$height = $res[1] - 90;

$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

date_default_timezone_set($_SESSION['s_timezone']);

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

$x = 0;

$q = "select uid,date_format(date_incident,'%d %M %Y') as dt_inc,latitude,longitude from incidents where date_incident >= '".$bdate."' and date_incident <= '".$edate."'";
$r = mysql_query($q) or die(mysql_error().' '.$q);
while ($row = mysql_fetch_array($r)) {
	extract($row);
	$x = $x + 1;
	$inclocs .= "['".$dt_inc."', ".$latitude.", ".$longitude.", ".$x.", '".$dt_inc." Incident ".$uid."'],";
}

$inclocs = substr($inclocs,0,-1);

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Incident Locations Map</title>
     <script src='http://code.jquery.com/jquery.min.js' type='text/javascript'></script>
</head>
<body>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
var infowindow = null;
    var sites = [<?php echo $inclocs; ?>];

    $(document).ready(function () { initialize();  });

    function initialize() {

        var centerMap = new google.maps.LatLng(-41.508577,173.129881);

        var myOptions = {
            zoom: 5,
            center: centerMap,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

        setMarkers(map, sites);
	    infowindow = new google.maps.InfoWindow({
                content: "loading..."
            });

    }

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

<div id="map_canvas" style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px;"></div>
</body>
</html>

<?php
session_start();

$trucks = $_REQUEST['trucks'];
$edate = $_REQUEST['edate'];
$fromtime = $_REQUEST['fromtime'];
$time = $_REQUEST['time'];
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

$table = 'ztmp'.$user_id.'_locs';

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "drop table if exists ".$table;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$table." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, dt datetime, truckno varchar(20),latitude varchar(20),longitude varchar(20),speed decimal(5,1) default 0)  engine myisam";
$calc = mysql_query($query) or die(mysql_error());

date_default_timezone_set($_SESSION['s_timezone']);

$today = date('Y-m-d');

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

$trucklocs = "";
$x = 0;


if ($trucks == '*') {
	$q = "SELECT max(uid) as maxid from travellog where speed >= 0 and date(datetime) = '".$edate."' and time(datetime) >= '".$fromtime."' and time(datetime) <= '".$time."' GROUP BY truckno ";
	$r = mysql_query($q) or die(mysql_error().' '.$q);
	while ($row = mysql_fetch_array($r)) {
		extract($row);
		$id = $maxid;
		$qs = "select datetime, truckno,latitude,longitude,speed from travellog where uid = ".$id;
		$rs = mysql_query($qs) or die(mysql_error().' '.$qs);
		$rows = mysql_fetch_array($rs);
		extract($rows);
		$qi = "insert into ".$table." (dt, truckno,latitude,longitude,speed) values (";
		$qi .= "'".$datetime."','".$truckno."','".$latitude."','".$longitude."',".$speed.")";
		$ri = mysql_query($qi) or die(mysql_error().' '.$qi);
	}
	$heading = "All trucks last recorded position on ".$edate." as at ".$time.".";
	$q = "select dt, truckno,latitude,longitude,speed from ".$table;
	$r = mysql_query($q) or die(mysql_error());
	while ($row = mysql_fetch_array($r)) {
		extract($row);
		$x = $x + 1;
		$trucklocs .= "['".$truckno."', ".$latitude.", ".$longitude.", ".$x.", '".$truckno.". ".$dt." speed ".$speed."'],";
	}
} else {
	$ql = "select uid, datetime,truckno,latitude,longitude,speed FROM travellog  where speed >= 0 and date(datetime) = '".$edate."' and time(datetime) >= '".$fromtime."' and time(datetime) <= '".$time."' and truckno = '".$trucks."' order by uid";
	$rl = mysql_query($ql) or die(mysql_error().' '.$ql);
	while ($row = mysql_fetch_array($rl)) {
		extract($row);
		$qm = "insert into ".$table." (dt, truckno,latitude,longitude,speed) values (";
		$qm .= "'".$datetime."','".$truckno."','".$latitude."','".$longitude."',".$speed.")";
		$rm = mysql_query($qm) or die(mysql_error().' '.$qm);
	}
	$heading = $trucks." recorded positions on ".$edate." between ".$fromtime." and ".$time.".";
	$qx = "select dt, truckno,latitude,longitude,speed from ".$table;
	$rx = mysql_query($qx) or die(mysql_error());
	while ($row = mysql_fetch_array($rx)) {
		extract($row);
		$x = $x + 1;
		$trucklocs .= "['".$truckno."', ".$latitude.", ".$longitude.", ".$x.", '".$truckno.". ".$dt." speed ".$speed."'],";
	}
}


echo $heading;

$trucklocs = substr($trucklocs,0,-1);

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Vehicle Locations Map</title>
     <script src='http://code.jquery.com/jquery.min.js' type='text/javascript'></script>
</head>
<body>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
var infowindow = null;
    var sites = [<?php echo $trucklocs; ?>];

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

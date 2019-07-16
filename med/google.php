<?php

$address = $_REQUEST['address'];

//AIzaSyArEYcRTiA3K7nvkDVzsOUSyVlfb-Ge6WM

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>Google Map</title>
    <script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=AIzaSyArEYcRTiA3K7nvkDVzsOUSyVlfb-Ge6WM" type="text/javascript"></script>
    <script type="text/javascript">

    var map = null;
    var geocoder = null;

    function initialize(address) {
      if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("map_canvas"));
        geocoder = new GClientGeocoder();
		  if (geocoder) {
			geocoder.getLatLng(
			  address,
			  function(point) {
				if (!point) {
				  alert(address + " not found");
				} else {
				  map.setCenter(point, 13);
				  var marker = new GMarker(point);
				  map.addOverlay(marker);
				  map.addControl(new GSmallZoomControl3D());	  
				  marker.openInfoWindowHtml(address);
				}
			  }
			);
		  }

      }
    }

    </script>
  </head>

  <body onload="initialize('<?php echo $address; ?>')" onunload="GUnload()">
    <form action="#">
    	<div id="map_canvas" style="width: 480px; height: 300px"></div>
        <input type="button" value="Print Map" onClick="window.print()">
    </form>

  </body>
</html>

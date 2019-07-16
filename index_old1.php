
<?php

session_start();

//$s = $_SERVER['REQUEST_URI'];

//echo "came from ".$s;

$thisyear = date('Y');


?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Logtracc - Logging Truck Accounting & Administration</title>

<link href="includes/logcss.css" media="all" type="text/css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" media="screen" href="includes/coda-slider.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
    <script src="includes/jquery/jquery.coda-slider-3.0.min.js"></script>
    <script>
    $(function(){

      /* Here is the slider using default settings */
      $('#slider-id').codaSlider({
            autoSlide:true,
            autoHeight:false,
			dynamicArrowsGraphical:true,
			dynamicTabs:false,
			autoSlideInterval:20000
          });
      /* If you want to adjust the settings, you set an option
         as follows:

          $('#slider-id').codaSlider({
            autoSlide:true,
            autoHeight:false
          });
      
      */
    });
	
	function verify() {
		var uname = document.getElementById("userid").value;
		if (uname == "") {
			alert("Please enter your user name");
			return false;
		}
		var pword = document.getElementById("password").value;
		if (pword == "") {
			alert("Please enter your password");
			return false;
		} else {
			document.getElementById('form1').submit();
		}
	}
	
	function submitenter(myfield,e)
	{
	var keycode;
	if (window.event) keycode = window.event.keyCode;
	else if (e) keycode = e.which;
	else return true;
	
	if (keycode == 13)
	   {
	   myfield.form.submit();
	   return false;
	   }
	else
	   return true;
	}
	
	function indemail() {
		var name = document.getElementById("name").value;
		var email = document.getElementById("email").value;
		var message = document.getElementById("message").value;
		
		jQuery.ajaxSetup({async:false});
		jQuery.get("ajax/ajaxIndexEmail.php", {name: name, email: email, message: message}, function(data){
			//alert('Message from email send '+data);																					
		});
		jQuery.ajaxSetup({async:true});
		
		document.getElementById("name").value = "";
		document.getElementById("email").value = "";
		document.getElementById("message").value = "";
		
	}
	
	

    </script> 

    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
  google.maps.event.addDomListener(window, 'load', function() {
    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 17,
      center: new google.maps.LatLng(-35.091348,173.259369),
      mapTypeId: google.maps.MapTypeId.SATELLITE
    });

    var infoWindow = new google.maps.InfoWindow;

    var onMarkerClick = function() {
      var marker = this;
      var latLng = marker.getPosition();
      infoWindow.setContent('<h3>Logtracc</h3>' +
	  '<ul class="mapaddress">'+
	  '<li>16 Whangatane Drive,</li>'+
	  '<li>Kaitaia, Northland,</li>'+
	  '<li>0482, New Zealand</li>'+
      '</ul>');

      infoWindow.open(map, marker);
    };
    google.maps.event.addListener(map, 'click', function() {
      infoWindow.close();
    });

  var image = new google.maps.MarkerImage(
    'images/image.png',
    new google.maps.Size(41,65),
    new google.maps.Point(0,0),
    new google.maps.Point(21,65)
  );

  var shadow = new google.maps.MarkerImage(
    'images/shadow.png',
    new google.maps.Size(77,65),
    new google.maps.Point(0,0),
    new google.maps.Point(21,65)
  );

  var shape = {
    coord: [26,0,29,1,30,2,32,3,33,4,34,5,35,6,36,7,37,8,37,9,38,10,38,11,39,12,39,13,40,14,40,15,40,16,40,17,40,18,40,19,40,20,40,21,40,22,40,23,40,24,40,25,40,26,40,27,39,28,39,29,39,30,38,31,38,32,37,33,37,34,37,35,36,36,36,37,35,38,34,39,34,40,33,41,33,42,32,43,32,44,31,45,30,46,30,47,29,48,29,49,28,50,27,51,27,52,26,53,26,54,25,55,25,56,24,57,23,58,23,59,22,60,22,61,21,62,21,63,20,64,20,64,19,63,19,62,18,61,18,60,17,59,16,58,16,57,15,56,15,55,14,54,14,53,13,52,12,51,12,50,11,49,11,48,10,47,10,46,9,45,8,44,8,43,7,42,7,41,6,40,5,39,5,38,4,37,4,36,3,35,3,34,2,33,2,32,2,31,1,30,1,29,1,28,0,27,0,26,0,25,0,24,0,23,0,22,0,21,0,20,0,19,0,18,0,17,0,16,0,15,0,14,1,13,1,12,2,11,2,10,3,9,3,8,4,7,5,6,6,5,7,4,8,3,10,2,11,1,14,0,26,0],
    type: 'poly'
  };

    var marker1 = new google.maps.Marker({
      map: map,
	  icon: image,
shadow: shadow,
  shape: shape,
      position: new google.maps.LatLng(-35.091348,173.259369)
    });

    google.maps.event.addListener(marker1, 'click', onMarkerClick);

  });
</script>

</head>

<body>

<div id="header">
<div id="header-content">
<div id="header-logo">
<img src="images/logo-trans.png" width="371" height="86" alt="LogTracc">
</div>
<div id="top-nav">
<ul id="nav">
  <li class="current"><a href="#section-1">HOME</a></li>
  <li><a href="#section-2">FEATURES</a></li>
  <li><a href="#section-3">PROCESSES</a></li>
  <li><a href="#section-4">LOGIN</a></li>
  <li><a href="#section-5">CONTACT</a></li>
</ul>
<div class="nav-right"><!-- --></div>
</div>
</div>
</div>

<div id="container">


  <div class="section" id="section-1">
    
    <div class="section-content">
    <h1>Fully Integrated Business, Accounting & Administration System</h1>
<p>Logtracc integrated business systems comprise of software components catering for client management, accounting, stock control, fixed asset management and processes specific to particular types of enterprise.</p>
<div id="section1-l">

 <h2>Business System</h2>
        

        <p>This system provides data capture from tablets running applications suited to specific business needs that feeds to the back end web based system where administrators can correlate, analyse and obtain reports on all aspects of their operations. </p>

        <ul>
          <li>Log - obtain and record client, financial, product, stock and other data</li>
          <li>Tracc - track this data from source to final accounts</li>
        </ul>
        <ul>
          <li>The tablet applications are written to cater for specific business processes</li>
          <li>Data is stored on the tablet until it senses it has an internet connection, then it uploads the data</li>
        </ul>
 
 </div>
 
 <div id="section1-r">      
     <div id="cycler">
		<img class="active" src="images/img1.png" alt="Logtracc 1" title="Logtracc 1" width="150" height="200" />
		<img src="images/img2.png" alt="Logtracc 2" title="Logtracc 2" width="150" height="200"  />
		<img src="images/img3.png" alt="Logtracc 3" title="Logtracc 3" width="150" height="200"  />	
	</div>
    
    <script type="text/javascript">
function cycleImages(){
      var $active = $('#cycler .active');
      var $next = ($active.next().length > 0) ? $active.next() : $('#cycler img:first');
      $next.css('z-index',2);//move the next image up the pile
      $active.fadeOut(1500,function(){//fade out the top image
	  $active.css('z-index',1).show().removeClass('active');//reset the z-index and unhide the image
          $next.css('z-index',3).addClass('active');//make the next image the top one
      });
    }

$(document).ready(function(){
// run every 7s
setInterval('cycleImages()', 7000);
})</script>

</div>
        



<div style="clear:both;"><!-- --></div>
</div><!-- end of section-content -->

  </div>



  <div class="section" id="section-2">
        <div class="section-content">
    <h1>Features</h1>

<div id="f-left">
<h2>Features</h2>
<ul>
    <li>Fully integrated accounting, stock control, fixed assets, client relationship management and business processes</li>
    <li>Web based so accessible from anywhere legitimate user has broadband internet access</li>
    <li>Any relevant data only ever entered once</li>
    <li>Clients for all associated companies held once. Update from one company and record updated for all</li>
    <li>Control user access to only those facilities of each company to which they are entitled</li>
    <li>You can give your accountant access thereby eliminating correcting journal entries each year end</li>
    <li>We can provide a complete bookkeeping service and do your accounts for you</li>
</ul>
</div>


<div id="f-right">
<h2>Pricing</h2>
<ul>
    <li>Fixed monthly fee for hire of tablet and use of the core system</li>
    <li>Negotiated fixed monthly fee for bookkeeping services based on average number of transactions</li>
    <li>Set monthly fee for use of specific business processes</li>
    <li>This adds up to a total fixed monthly fee with no surprises on a two year contract</li>
</ul>
</div>
<div style="clear:both;"><!-- --></div>
</div><!-- end of section-content -->
  </div>
  
  
  
    <div class="section" id="section-3">
    
    <div class="section-content">
    <h1>Processes</h1>
<p>Logtracc integrated business systems comprise of software components catering for client management, accounting, stock control, fixed asset management and facilities specific to logging truck operations.</p>


    <div class="coda-slider"  id="slider-id">
      <div>
        <h2 class="title">Logging Truck Operations</h2>
<img src="images/img1.png" width="151" height="200" alt="Logtracc">
          <p>This system provides data capture from tablets mounted in the truck cab that feeds to the back end web based system where administrators can correlate, analyse and obtain reports on all aspects of their operations. </p>
          <p>These inlcude:-</p>
          <ul>
            <li>Full accounting reports plus profit and loss per vehicle</li>
            <li>RUC refund forms automatically filled in</li>
            <li>Driver log data</li>
            <li>Location tracking of vehicle movements</li>
          </ul>
      </div>
      <div>
        <h2 class="title">Lead Generation - to be developed</h2>
<img src="images/img2.png" width="151" height="200" alt="Logtracc">
          <p>This system provide data capture from tablets used by personel involved in interviewing prospective clients at source which then feeds to the back end web based system where administrators can correlate, analyse and allocate qualified leads to sales teams. </p>
<p>These inlcude:-</p>
<ul>
    <li>Facilitates almost immediate contact of qualified prospects by sales staff</li>
    <li>Streamlines administration of leads for either internal use or sale</li>
</ul>
      </div>
      <div>
        <h2 class="title">Stock Control - to be developed</h2>
<img src="images/img3.png" width="151" height="200" alt="Logtracc">
          <p>This system provide data capture from tablets facilitating the capture of stock control information either at remote or local sites and feeds that information back to the web based accounting system. </p>
<p>These inlcude:-</p>
<ul>
    <li>Download specific stock take requirements to tablet</li>
    <li>Upload results of stock take directly to stock control in the accounts</li>
</ul>
      </div>

    </div>



<div style="clear:both;"><!-- --></div>
</div><!-- end of section-content -->

  </div>
  
  
  

  <div class="section" id="section-4">
       <div class="section-content">
    <h1>Login</h1>

<div id="f-left-3">
<h2>Demo</h2>
<p>If you have a user account, please fill in your login details to the right.</p>

<p>Demo - If you wish to view the Demo of the system, please log in with the following :-</p>
<p>Username: logs<br>
  Password: logs
</p>

</div><!-- f-left-3 -->

<div id="f-right-3">
<h2>Login</h2>
<form name="form1" id="form1" method="post" action="verify.php">
<ul>
<li><label>User ID</label></li>
<li><input type="password" name="userid" id="userid"></li>
<li><label>Password</label></li>
<li><input type="password" name="password" id="password"  onKeyPress="return submitenter(this,event)"></li>
<li><input type="button" class="button" name="bsubmit" id="bsubmit" value="Submit" onClick="verify()"></li>
</ul>
</form>
</div><!-- f-right-3-->
<div style="clear:both;"><!-- --></div>


</div><!-- end of section-content -->
  </div>

  <div class="section" id="section-5">
	    <div class="section-content">
    <h1>Contact Us - 16 Whangatane Drive, Kaitaia, Northland, 0482, New Zealand</h1>

<div id="f-left-4">
<h2>Get in touch</h2>
<form name="contact" id="contact" method="post" action="contact.php">
<ul>
<li>Phone: 0123456789</li>
<li>Email: <a href ="mailto:sales@logtracc.co.nz">sales@logtracc.co.nz</a></li>
<li><label>Name</label></li>
<li><input name="name" id="name"></li>
<li><label>Email</label></li>
<li><input name="email" id="email"></li>
<li><label>Message</label></li>
<li>
  <textarea name="message" id="message"></textarea>
</li>
<li><input type="button" class="button" name="bsubmit" id="bsubmit" value="Submit" onClick="indemail()"></li>
</ul>
</form>
</div>
<div id="f-right-4">
	<h2>Location</h2>
 <div id="location">
 <div id="map"></div>

</div>
</div> 
<div style="clear:both;"><!-- --></div>   
</div><!-- end of section-content -->

  
</div>

<div id="footer-footer">
<div id="footer-content">
<span class="copyright">© Murray Russell. 2012 - <?php echo $thisyear; ?></span>
</div>
</div>



<script src="includes/jquery/jquery.scrollTo.js"></script>
<script src="includes/jquery/jquery.nav.js"></script>
<script>
$(document).ready(function() {
  $('#nav').onePageNav({
    begin: function() {
	  console.log('start');
    },
    end: function() {
	  console.log('stop');
    },
	scrollOffset: 0
  });
  
  $('.do').click(function(e) {
    $('#section-5').append('<p>why</p>');
    e.preventDefault();
  });
  
});
</script>
</body>
</html>

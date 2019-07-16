<?php

$thisyear = date('Y');

?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Update Subscribers</title>

<script>

window.name = "updtsubs";

function addsubscriber() {
	var x = 0, y = 0; // default values	
	x = window.screenX +20;
	y = window.screenY +250;
	window.open('addsubscriber.php','addgp','toolbar=0,scrollbars=1,height=400,width=600,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editsubscriber(id) {
	var x = 0, y = 0; // default values	
	x = window.screenX +20;
	y = window.screenY +250;
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxUpdtSubscriber.php", {subid: id}, function(data){
	});
	jQuery.ajaxSetup({async:true});
	editsubscriber2();
}

function editsubscriber2() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editsubscriber.php?','edgp','toolbar=0,scrollbars=1,height=400,width=600,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


function addcompany() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +300;
	window.open('addcompany.php','addsb','toolbar=0,scrollbars=1,height=550,width=1100,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editcompany(id) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +300;
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxUpdtCompany.php", {coyid: id}, function(data){
	});
	jQuery.ajaxSetup({async:true});
	editcompany2();
}

function editcompany2() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +300;
	window.open('editcompany.php','edsb','toolbar=0,scrollbars=1,height=550,width=1100,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


function editusers(id) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +265;
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxUpdtCompany.php", {coyid: id}, function(data){
	});
	jQuery.ajaxSetup({async:true});
	editusers2();
}

function editusers2() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +300;
	window.open('updtusers.php','eduser','toolbar=0,scrollbars=1,height=400,width=650,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function updttablets(cid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +20;
	y = window.screenY +250;
	window.open('updttablets.php?cid='+cid,'updttab','toolbar=0,scrollbars=1,height=400,width=600,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


</script>

</head>

<body>
<form name="admin1" id="admin1" >

	<table align="center">
        <tr>
            <td>
                <?php include "getSubscribers.php";?>
            </td>
        </tr>
        <tr>
            <td> 
                <?php include "getCompanies.php";?>
            </td>
        </tr>
    </table>

</form>
</body>
</html>

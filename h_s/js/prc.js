function SQLdate(dt) {
	var sdt = dt.split('/');
	var d = sdt[0];
	var m = sdt[1];
	var y = sdt[2];
	if (m.length < 2) m = "0" + m;
	if (d.length < 2) d = "0" + d;
	
	var SQLFormatted = "" + y +"-"+ m +"-"+ d;	
	
	return SQLFormatted;
	
}

//************************************************************************************************************
//updtmenug.php
//************************************************************************************************************
function createRequestObject(){
	var request_o; //declare the variable to hold the object.
	var browser = navigator.appName; //find the browser name
	if(browser == "Microsoft Internet Explorer"){
		request_o = new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		request_o = new XMLHttpRequest();
	}
	return request_o; //return the object
}

var inven = createRequestObject();

function update(value,uid,field) {

	var urlvar = "savemenug.php?value="+value+"&uid="+uid+"&field="+field+"&RandomKey=" + Math.random() * Date.parse(new Date());
	inven.open('get', urlvar);
	
	inven.onreadystatechange = disp_res;
	inven.send(null);
}

function disp_res()
{
	if(inven.readyState == 4){ //Finished loading the response
		var response = inven.responseText;
	
		if(response != "") {
			//alert(response);
		}
	}
}

//************************************************************************************************************
//incidents
//************************************************************************************************************

function editincident(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editincident.php?uid='+uid,'edinc','toolbar=0,scrollbars=1,height=540,width=940,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function mapincident(id,coord) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('map.php?id='+id+'&coord='+coord,'goog','toolbar=0,scrollbars=1,height=360,width=560,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function printincident(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('inc2pdf.php?uid='+uid,'incpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function mapincidents() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	var bdate = SQLdate(document.getElementById('bdate').value);
	var edate = SQLdate(document.getElementById('edate').value);
	var res = document.getElementById('res').value;
	var n = res.split("x");
	var width = n[0];
	var height = n[1];
	window.open('map_incidents.php?edate='+edate+'&bdate='+bdate+'&res='+res,'incmap','toolbar=0,scrollbars=1,height='+height+',width='+width+',resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


function emailincident(id) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	win1 = window.open('incepdf.php?uid='+id,'inc2epdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
	
	win1.close();
	
	
	window.open('emailinc.php?id='+id,'eminc','toolbar=0,scrollbars=1,height=540,width=940,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
	
}


//*****************************************************************************************************
// filtering
//*****************************************************************************************************

function maplistincidents() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	var res = document.getElementById('res').value;
	var n = res.split("x");
	var width = n[0];
	var height = n[1];
	window.open('map_listincidents.php?res='+res,'inclmap','toolbar=0,scrollbars=1,height='+height+',width='+width+',resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}




	var coyname; 
	var fdate; 
	var tdate; 
	var lti; 
	var type;
	var harm; 
	var damage; 
	var reocurr; 
	var terrain; 
	var weather; 
	var temperature; 
	var wind; 
	var basic1;
	var basic2; 
	var notcoyname; 
	var notdate; 
	var notlti; 
	var notitype;
	var notharm; 
	var notdamage; 
	var notreocurr; 
	var notterrain; 
	var notweather; 
	var nottemperature; 
	var notwind; 
	var notbasic1;
	var notbasic2; 

function getParams() {
	coyname = jQuery("#coyname").val(); 
	fdate = SQLdate(document.getElementById('fdate').value);
	tdate = SQLdate(document.getElementById('tdate').value);
	lti = jQuery("#lti").val(); 
	type = jQuery("#type").val(); 
	harm = jQuery("#harm").val(); 
	damage = jQuery("#damage").val(); 
	people = jQuery("#people").val(); 
	property = jQuery("#property").val(); 
	reocurr = jQuery("#reocurr").val(); 
	terrain = jQuery("#terrain").val(); 
	weather = jQuery("#weather").val(); 
	temperature = jQuery("#temperature").val(); 
	wind = jQuery("#wind").val(); 
	basic1 = jQuery("#basic1").val(); 
	basic2 = jQuery("#basic2").val(); 
	notcoyname = jQuery("#notcoyname").val(); 
	notdate = jQuery("#notdate").val(); 
	notlti = jQuery("#notlti").val(); 
	nottype = jQuery("#nottype").val(); 
	notharm = jQuery("#notharm").val(); 
	notdamage = jQuery("#notdamage").val(); 
	notreocurr = jQuery("#notreocurr").val(); 
	notterrain = jQuery("#notterrain").val(); 
	notweather = jQuery("#notweather").val(); 
	nottemperature = jQuery("#nottemperature").val(); 
	notwind = jQuery("#notwind").val(); 

	
	if (document.getElementById('notcoyname').checked) {
		if (coyname == '') {
			alert('Please enter the contractee you wish to exclude');
			return false;
		}
		notcoyname = 'Y'; 
	} else {
		notcoyname = 'N'; 
	}
	if (document.getElementById('notlti').checked) {
		if (lti == '') {
			alert('Please enter the LTI criteria you wish to exclude');
			return false;
		}
		notlti = 'Y'; 
	} else {
		notlti = 'N'; 
	}
	if (document.getElementById('notharm').checked) {
		if (harm == '') {
			alert('Please enter the harm you wish to exclude');
			return false;
		}
		notharm = 'Y'; 
	} else {
		notharm = 'N'; 
	}
	if (document.getElementById('notdamage').checked) {
		if (damage == '') {
			alert('Please enter the damage you wish to exclude');
			return false;
		}
		notdamage = 'Y'; 
	} else {
		notdamage = 'N'; 
	}
	if (document.getElementById('notreocurr').checked) {
		if (reocurr == '') {
			alert('Please enter the reocurrence you wish to exclude');
			return false;
		}
		notreocurr = 'Y'; 
	} else {
		notreocurr = 'N'; 
	}
	if (document.getElementById('notterrain').checked) {
		if (terrain === ' ') {
			alert('Please enter the terrain you wish to exclude');
			return false;
		}
		notterrain = 'Y'; 
	} else {
		notterrain = 'N'; 
	}
	if (document.getElementById('notweather').checked) {
		if (weather === ' ') {
			alert('Please enter the weather you wish to exclude');
			return false;
		}
		notweather = 'Y'; 
	} else {
		notweather = 'N'; 
	}
	if (document.getElementById('nottemperature').checked) {
		if (temperature === ' ') {
			alert('Please enter the temperature you wish to exclude');
			return false;
		}
		nottemperature = 'Y'; 
	} else {
		nottemperature = 'N'; 
	}
	if (document.getElementById('notwind').checked) {
		if (wind === ' ') {
			alert('Please enter the wind you wish to exclude');
			return false;
		}
		notwind = 'Y'; 
	} else {
		notwind = 'N'; 
	}
	
}


function filterinc() {
	
	getParams();
	
	document.getElementById('filterpage').style.visibility = 'hidden';

	
	jQuery("#listlist").setGridParam({url:"getFiltered.php?coyname_mask="+coyname+"&fdate_mask="+fdate+"&tdate_mask="+tdate+"&lti_mask="+lti+"&type_mask="+type+"&harm_mask="+harm+"&damage_mask="+damage+"&reocurr_mask="+reocurr+"&terrain_mask="+terrain+"&weather_mask="+weather+"&temperature_mask="+temperature+"&wind_mask="+wind+"&basic1_mask="+basic1+"&basic2_mask="+basic2+"&notcoyname_mask="+notcoyname+"&notdate_mask="+notdate+"&notlti_mask="+notlti+"&nottype_mask="+nottype+"&notharm_mask="+notharm+"&notdamage_mask="+notdamage+"&notreoccur_mask="+notreocurr+"&notterrain_mask="+notterrain+"&notweather_mask="+notweather+"&nottemperature_mask="+nottemperature+"&notwind_mask="+notwind,page:1}).trigger("reloadGrid");
	
}


function unfilter() {
	document.getElementById('coyname').value = ''; 
	document.getElementById('fdate').value = ''; 
	document.getElementById('tdate').value = ''; 
	document.getElementById('type').value = ''; 
	document.getElementById('harm').value = ''; 
	document.getElementById('damage').value = ''; 
	document.getElementById('reocurr').value = ''; 
	document.getElementById('terrain').value = ''; 
	document.getElementById('weather').value = ''; 
	document.getElementById('temperature').value = ''; 
	document.getElementById('wind').value = ''; 
	document.getElementById('basic1').value = ''; 
	document.getElementById('basic2').value = ''; 
	

	document.getElementById('notcoyname').checked = false; 
	document.getElementById('nottype').checked = false; 
	document.getElementById('notharm').checked = false;  
	document.getElementById('notdamage').checked = false;  
	document.getElementById('notreocurr').checked = false;  
	document.getElementById('notterrain').checked = false; 
	document.getElementById('notweather').checked = false;  
	document.getElementById('nottemperature').checked = false;  
	document.getElementById('notwind').checked = false;  

	
}

function showfilters() {
	unfilter();
	document.getElementById('filterpage').style.visibility = 'visible';
}

function closefilters() {
	unfilter();
	$.get("ajaxunfilter.php", {}, function(data){$("#listlist").trigger("reloadGrid")});
	jQuery("#listlist").setGridParam({url:"getlist.php",page:1}).trigger("reloadGrid"); 
	document.getElementById('filterpage').style.visibility = 'hidden';
}
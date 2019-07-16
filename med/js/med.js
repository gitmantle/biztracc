//************************************************************************************************************
//index.php
//************************************************************************************************************


function todo() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +275;
	window.open('updttodo.php','todo','toolbar=0,scrollbars=1,height=550,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

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

//***********************************************************************************************************
//links.php
//***********************************************************************************************************
function editlink(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('editlink.php?uid='+uid,'edlink','toolbar=0,scrollbars=1,height=170,width=800,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addlink() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('addlink.php','addlink','toolbar=0,scrollbars=1,height=170,width=800,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  

function dellink(uid) {
	  if (confirm("Are you sure you want to delete this link")) {
		$.get("../admin/ajaxdellink.php", {tid: uid}, function(data){alert(data);$("#updtlinklist").trigger("reloadGrid")});
	  }
}


function getlink(ln) {
	var ulink = "HTTP://"+ln;
	window.open (ulink,"linkwindow");	
}

//************************************************************************************************************
//updtmembers.php
//************************************************************************************************************

function editmem(uid,from) {
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxUpdtMember.php", {memberid: uid}, function(data){
	});
	jQuery.ajaxSetup({async:true});
	editmem2(from);
}

function editmem2(from) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editmember.php?from='+from,'edmem','toolbar=0,scrollbars=1,height=420,width=980,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addmem() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addmember.php','addmem','toolbar=0,scrollbars=1,height=420,width=970,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				

function delmem(uid) {
	 if (confirm("Are you sure you want to delete this Member")) {
			$.get("includes/ajaxdelmember.php", {tid: uid}, function(data){$("#memlist2").trigger("reloadGrid")});
	  }
}

function editsup(uid) {
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxUpdtMember.php", {memberid: uid}, function(data){
	});
	jQuery.ajaxSetup({async:true});
	editsup2();
}

function editsup2() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editsupplier.php','edmem','toolbar=0,scrollbars=1,height=420,width=980,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addsup() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addsupplier.php','addmem','toolbar=0,scrollbars=1,height=420,width=970,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				

function delsup(uid) {
	 if (confirm("Are you sure you want to delete this Supplier")) {
			$.get("includes/ajaxdelmember.php", {tid: uid}, function(data){$("#suplist2").trigger("reloadGrid")});
	  }
}

function emailmem(uid,memberid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +200;
		window.open('emailmem.php?commsid='+uid+'&memberid='+memberid,'emmem','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function gridReload1mem(){ 
	var nm_mask = jQuery("#searchlastname").val(); 
	nm_mask = nm_mask.toUpperCase();
	jQuery("#memlist2").setGridParam({url:"getMembers.php?nm_mask="+nm_mask}).trigger("reloadGrid"); 
} 

function gridReload2mem(){ 
	var ph_mask = jQuery("#searchph").val(); 
	if (ph_mask != '') {
		jQuery("#memlist2").setGridParam({url:"getMembersph.php?ph_mask="+ph_mask}).trigger("reloadGrid"); 
	}
} 


function doSearch1mem(){ 
		var timeoutHnd = setTimeout(gridReload1mem,500); 
	} 
function doSearch2mem(){ 
		var timeoutHnd = setTimeout(gridReload2mem,500); 
	} 
	

	var suburb; 
	var town; 
	var postcode; 
	var industry; 
	var occupation; 
	var workflow;
	var wfsdays;
	var position; 
	var status;
	var acat;
	var dfrom; 
	var dto; 
	var association;
	var birthmonth;
	var staff;
	var gender;
	var notpostcode; 
	var nottown; 
	var notindustry; 
	var notoccupation; 
	var notposition; 
	var notstatus;
	var recchecked; 
	var notchecked; 
	var notestring;
	var nextmeetingb;
	var nextmeetinge;
	var campaign;
	var notcampaign;
	var campaignstage;
	var notcampaignstage;

function getParams() {
	suburb = jQuery("#searchsuburb").val(); 
	town = jQuery("#searchtown").val(); 
	postcode = jQuery("#searchpostcode").val(); 
	industry = jQuery("#searchindustry").val(); 
	occupation = jQuery("#searchoccupation").val(); 
	workflow = jQuery("#searchworkflow").val();
	position = jQuery("#searchposition").val(); 
	dfrom = jQuery("#dfrom").val(); 
	dto = jQuery("#dto").val(); 
	notestring = jQuery("#notestring").val();
	association = jQuery("#association").val();
	status = jQuery("#searchstatus").val();
	staff = jQuery("#searchrep").val();
	gender = jQuery("#searchgender").val();
	birthmonth= jQuery("#lbirthmonth").val();
	acat = jQuery("#searchacat").val();
	nextmeetingb = SQLdate(document.getElementById('ddateb').value);
	nextmeetinge = SQLdate(document.getElementById('ddatee').value);
	campaign = jQuery("#searchcampaign").val();
	campaignstage = jQuery("#campaignstage").val();
	notcampaign = jQuery("#notcampaign").val();
	notcampaignstage = jQuery("#notcampaignstage").val();
	
	if (document.getElementById('notsuburb').checked) {
		if (suburb == '') {
			alert('Please enter the suburb you wish to exclude');
			return false;
		}
		notsuburb = 'Y'; 
	} else {
		notsuburb = 'N'; 
	}
	if (document.getElementById('notpostcode').checked) {
		if (postcode == '') {
			alert('Please enter the postcode you wish to exclude');
			return false;
		}
		notpostcode = 'Y'; 
	} else {
		notpostcode = 'N'; 
	}
	if (document.getElementById('nottown').checked) {
		if (town == '') {
			alert('Please enter the town you wish to exclude');
			return false;
		}
		nottown = 'Y'; 
	} else {
		nottown = 'N'; 
	}
	if (document.getElementById('notstatus').checked) {
		if (status == ' ') {
			alert('Please enter the status you wish to exclude');
			return false;
		}
		notstatus = 'Y'; 
	} else {
		notstatus = 'N'; 
	}
	if (document.getElementById('notindustry').checked) {
		if (industry == '') {
			alert('Please enter the industry you wish to exclude');
			return false;
		}
		notindustry = 'Y'; 
	} else {
		notindustry = 'N'; 
	}
	if (document.getElementById('notoccupation').checked) {
		if (occupation == '') {
			alert('Please enter the occupation you wish to exclude');
			return false;
		}
		notoccupation = 'Y'; 
	} else {
		notoccupation = 'N'; 
	}
	if (document.getElementById('notposition').checked) {
		if (position == '') {
			alert('Please enter the position you wish to exclude');
			return false;
		}
		notposition = 'Y'; 
	} else {
		notposition = 'N'; 
	}
	if (document.getElementById('notage').checked) {
		if (dfrom === 0 || dto === 0) {
			alert('Please enter the age range you wish to exclude');
			return false;
		}
		notage = 'Y'; 
	} else {
		notage = 'N'; 
	}
	if (document.getElementById('recchecked').checked) {
		recchecked = 'Y'; 
	} else {
		recchecked = 'N'; 
	}
	if (document.getElementById('notchecked').checked) {
		notchecked = 'Y'; 
	} else {
		notchecked = 'N'; 
	}
	if (document.getElementById('notcampaign').checked) {
		if (campaign === '') {
			alert('Please enter the campaign you wish to exclude');
			return false;
		}
		notcampaign = 'Y'; 
	} else {
		notcampaign = 'N'; 
	}
	if (document.getElementById('notcampaignstage').checked) {
		if (campaign === '') {
			alert('Please enter the campaign stage you wish to exclude');
			return false;
		}
		notcampaignstage = 'Y'; 
	} else {
		notcampaignstage = 'N'; 
	}
	
	
}


function filtermem() {
	
	getParams();
	
	document.getElementById('filterpage').style.visibility = 'hidden';
	document.getElementById('printm').style.visibility = 'visible';
	document.getElementById('printv').style.visibility = 'visible';
	document.getElementById('emaillist').style.visibility = 'visible';
	document.getElementById('bnotes').style.visibility = 'visible';
	
	jQuery("#memlist2").setGridParam({url:"getFiltered.php?suburb_mask="+suburb+"&town_mask="+town+"&postcode_mask="+postcode+"&industry="+industry+"&occupation_mask="+occupation+"&workflow="+workflow+"&wfsdays="+wfsdays+"&position_mask="+position+"&dfrom="+dfrom+"&dto="+dto+"&recchecked="+recchecked+"&association="+association+"&status_mask="+status+"&acat="+acat+"&campaign="+campaign+"&campaignstage="+campaignstage+"&notstatus="+notstatus+"&notcampaign="+notcampaign+"&notcampaignstage="+notcampaignstage+"&notsuburb="+notsuburb+"&nottown="+nottown+"&notpostcode="+notpostcode+"&notindustry="+notindustry+"&notoccupation="+notoccupation+"&notposition="+notposition+"&notage="+notage+"&notchecked="+notchecked+"&notoccupation="+notoccupation+"&notestring="+notestring+"&birthmonth="+birthmonth+"&staff="+staff+"&gender="+gender+"&nextmeetingb="+nextmeetingb+"&nextmeetinge="+nextmeetinge,page:1}).trigger("reloadGrid"); 
}


function unfiltermem(act) {
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1;//January is 0!
	var yyyy = today.getFullYear();
	if(dd<10){dd='0'+dd}
	if(mm<10){mm='0'+mm}
	var dt = dd+'/'+mm+'/'+yyyy;
	var hd = yyyy+'-'+mm+'-'+dd;
	document.getElementById('searchsuburb').value = ''; 
	document.getElementById('searchtown').value = ''; 
	document.getElementById('searchpostcode').value = ''; 
	//document.getElementById('searchindustry').value = 0; 
	//document.getElementById('searchposition').value = ''; 
	document.getElementById('searchoccupation').value = ''; 
	//document.getElementById('searchworkflow').value = ' '; 
	//document.getElementById('wfsdays').value = 0; 
	//document.getElementById('dfrom').value = 0; 
	//document.getElementById('dto').value = 0; 
	document.getElementById('recchecked').checked = false; 
	//document.getElementById('notestring').value = '';
	//document.getElementById('association').value = '';
	//document.getElementById('searchrep').value = '';
	document.getElementById('searchgender').value = '';
	//document.getElementById('searchstatus').value = ' ';
	document.getElementById('lbirthmonth').value = '';
	//document.getElementById('ddateb').value = hd;
	//document.getElementById('ddatee').value = '0000-00-00';
	//document.getElementById('ddateb').value = dt;
	//document.getElementById('ddatee').value = dt;

	document.getElementById('notsuburb').checked = false; 
	document.getElementById('nottown').checked = false;
	document.getElementById('notpostcode').checked = false; 
	//document.getElementById('notindustry').checked = false;
	document.getElementById('notoccupation').checked = false;
	//document.getElementById('notposition').checked = false; 
	document.getElementById('notage').checked = false; 
	document.getElementById('notchecked').checked = false;
	//document.getElementById('notstatus').checked = false;
	
	document.getElementById('printm').style.visibility = 'hidden';
	document.getElementById('printv').style.visibility = 'hidden';
	document.getElementById('emaillist').style.visibility = 'hidden';
	document.getElementById('bnotes').style.visibility = 'hidden';
	
	if (act == 'u') {
		jQuery("#memlist2").setGridParam({url:"chron/getMembers.php",page:1}).trigger("reloadGrid"); 
	}
	
}

function sresetmem() {
	document.getElementById('searchlastname').value = ''; 
	document.getElementById('searchph').value = ''; 
	document.getElementById('notestring').value = ''; 
	document.getElementById('printm').style.visibility = 'hidden';
	document.getElementById('printv').style.visibility = 'hidden';
	document.getElementById('emaillist').style.visibility = 'hidden';
	document.getElementById('bnotes').style.visibility = 'hidden';
	
	jQuery("#memlist2").setGridParam({url:"chron/getMembers.php",page:1}).trigger("reloadGrid"); 

}

function printlistv() {
	getParams();
	var mv = "v";
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open("printlist.php?suburb_mask="+suburb+"&town_mask="+town+"&postcode_mask="+postcode+"&industry="+industry+"&occupation_mask="+occupation+"&workflow="+workflow+"&wfsdays="+wfsdays+"&position_mask="+position+"&dfrom="+dfrom+"&dto="+dto+"&recchecked="+recchecked+"&association="+association+"&status_mask="+status+"&acat="+acat+"&campaign="+campaign+"&campaignstage="+campaignstage+"&notstatus="+notstatus+"&notcampaign="+notcampaign+"&notcampaignstage="+notcampaignstage+"&notsuburb="+notsuburb+"&nottown="+nottown+"&notpostcode="+notpostcode+"&notindustry="+notindustry+"&notoccupation="+notoccupation+"&notposition="+notposition+"&notage="+notage+"&notchecked="+notchecked+"&notoccupation="+notoccupation+"&notestring="+notestring+"&birthmonth="+birthmonth+"&staff="+staff+"&gender="+gender+"&nextmeetingb="+nextmeetingb+"&nextmeetinge="+nextmeetinge+"&mv="+mv,"plist","toolbar=0,scrollbars=1,height=540,width=1010,resizable=0,left="+x+",screenX="+x+",top="+y+",screenY="+y);
}

function printlistm() {
	
	getParams();
	
	var mv = "m";
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	
	window.open("printlist.php?suburb_mask="+suburb+"&town_mask="+town+"&postcode_mask="+postcode+"&industry="+industry+"&occupation_mask="+occupation+"&workflow="+workflow+"&wfsdays="+wfsdays+"&position_mask="+position+"&dfrom="+dfrom+"&dto="+dto+"&recchecked="+recchecked+"&association="+association+"&status_mask="+status+"&acat="+acat+"&campaign="+campaign+"&campaignstage="+campaignstage+"&notstatus="+notstatus+"&notcampaign="+notcampaign+"&notcampaignstage="+notcampaignstage+"&notsuburb="+notsuburb+"&nottown="+nottown+"&notpostcode="+notpostcode+"&notindustry="+notindustry+"&notoccupation="+notoccupation+"&notposition="+notposition+"&notage="+notage+"&notchecked="+notchecked+"&notoccupation="+notoccupation+"&notestring="+notestring+"&birthmonth="+birthmonth+"&staff="+staff+"&gender="+gender+"&nextmeetingb="+nextmeetingb+"&nextmeetinge="+nextmeetinge+"&mv="+mv,"plist","toolbar=0,scrollbars=1,height=540,width=1010,resizable=0,left="+x+",screenX="+x+",top="+y+",screenY="+y);
}

function printliste() {
	
	getParams();
	var mv = "m";
	
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open("printliste.php?suburb_mask="+suburb+"&town_mask="+town+"&postcode_mask="+postcode+"&industry="+industry+"&occupation_mask="+occupation+"&workflow="+workflow+"&wfsdays="+wfsdays+"&position_mask="+position+"&dfrom="+dfrom+"&dto="+dto+"&recchecked="+recchecked+"&association="+association+"&status_mask="+status+"&acat="+acat+"&campaign="+campaign+"&campaignstage="+campaignstage+"&notstatus="+notstatus+"&notcampaign="+notcampaign+"&notcampaignstage="+notcampaignstage+"&notsuburb="+notsuburb+"&nottown="+nottown+"&notpostcode="+notpostcode+"&notindustry="+notindustry+"&notoccupation="+notoccupation+"&notposition="+notposition+"&notage="+notage+"&notchecked="+notchecked+"&notoccupation="+notoccupation+"&notestring="+notestring+"&birthmonth="+birthmonth+"&staff="+staff+"&gender="+gender+"&nextmeetingb="+nextmeetingb+"&nextmeetinge="+nextmeetinge+"&mv="+mv,"elist","toolbar=0,scrollbars=1,height=540,width=1000,resizable=0,left="+x+",screenX="+x+",top="+y+",screenY="+y);
}

function bulknotes() {
	
	getParams();
	var mv = "m";
	
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open("bulknotes.php?suburb_mask="+suburb+"&town_mask="+town+"&postcode_mask="+postcode+"&industry="+industry+"&occupation_mask="+occupation+"&workflow="+workflow+"&wfsdays="+wfsdays+"&position_mask="+position+"&dfrom="+dfrom+"&dto="+dto+"&recchecked="+recchecked+"&association="+association+"&status_mask="+status+"&acat="+acat+"&campaign="+campaign+"&campaignstage="+campaignstage+"&notstatus="+notstatus+"&notcampaign="+notcampaign+"&notcampaignstage="+notcampaignstage+"&notsuburb="+notsuburb+"&nottown="+nottown+"&notpostcode="+notpostcode+"&notindustry="+notindustry+"&notoccupation="+notoccupation+"&notposition="+notposition+"&notage="+notage+"&notchecked="+notchecked+"&notoccupation="+notoccupation+"&notestring="+notestring+"&birthmonth="+birthmonth+"&staff="+staff+"&gender="+gender+"&nextmeetingb="+nextmeetingb+"&nextmeetinge="+nextmeetinge+"&mv="+mv,"bnlist","toolbar=0,scrollbars=1,height=250,width=640,resizable=0,left="+x+",screenX="+x+",top="+y+",screenY="+y);
}

function showfiltersmem() {
	unfiltermem('s');
	document.getElementById('filterpage').style.visibility = 'visible';
}

function closefilters() {
	document.getElementById('filterpage').style.visibility = 'hidden';
}


function rcheck() {
	if (document.getElementById('recchecked').checked) {
		document.getElementById('notchecked').checked = false;
	}
}

function ncheck() {
	if (document.getElementById('notchecked').checked) {
		document.getElementById('recchecked').checked = false;
	}
}


function showCalculators() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +100;
	window.open('../includes/calculators.php','calc','toolbar=0,scrollbars=1,height=500,width=950,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//************************************************************************************************************
//updtreferrals.php
//************************************************************************************************************
	
function editreferral(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editreferral.php?uid='+uid,'edref','toolbar=0,scrollbars=1,height=500,width=1020,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addreferral() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addreferral.php','addref','toolbar=0,scrollbars=1,height=500,width=1020,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				

function delreferral(uid) {
	 if (confirm("Are you sure you want to delete this lead")) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +200;
		window.open('delreferral.php?uid='+uid,'delref','toolbar=0,scrollbars=1,height=10,width=10,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	  }
}

function add2mem(uid) {
	 if (confirm("Are you sure you want to add this lead to members?")) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +200;
		window.open('add2mem.php?uid='+uid,'delref','toolbar=0,scrollbars=1,height=10,width=10,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	  }
}

//************************************************************************************************************
//updtdepots.php
//************************************************************************************************************

function editdepot(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('editdepot.php?uid='+uid,'edind','toolbar=0,scrollbars=1,height=450,width=830,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function adddepot() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('adddepot.php','addind','toolbar=0,scrollbars=1,height=450,width=830,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  		

function mapad(address) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('google.php?address='+address,'goog','toolbar=0,scrollbars=1,height=360,width=560,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


//************************************************************************************************************
//bkup.php
//************************************************************************************************************
function downloadsql(subscriber) {
	var d = new Date();
	var dd = d.getDate();
	if (dd < 10) {
		dd = "0"+dd;
	}
	var mm = d.getMonth() + 1;
	if (mm < 10) {
		mm = "0"+mm;
	}
	var yyyy = d.getFullYear();
	var ddate = yyyy+'-'+mm+'-'+dd;
	var bkupfile = "../subscriber/cmeds4ucltsub"+subscriber+"_"+ddate+".gz";
	
	window.location.href=bkupfile; 
}


//************************************************************************************************************
//updtlogin.php
//************************************************************************************************************


function editlogin() {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +200;
		window.open('editlogin.php','edlog','toolbar=0,scrollbars=1,height=200,width=600,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

//************************************************************************************************************
//backup.php
//************************************************************************************************************

function bkup() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('../includes/bkup/backupdb.php','bkp','toolbar=0,scrollbars=1,height=200,width=400,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function rbupload() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('../includes/bkup/rbkupdb.php','bkr','toolbar=0,scrollbars=1,height=250,width=600,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}



//************************************************************************************
//************************************************************************************
//************************************************************************************



//***********************************************************************************************************
//common
//***********************************************************************************************************

var timeoutHnd; 
var flAuto = false; 
function doSearch(ev){ 
	if(!flAuto) return; 
	// var elem = ev.target||ev.srcElement; 
		if(timeoutHnd) clearTimeout(timeoutHnd); 
		timeoutHnd = setTimeout(gridReload,500) 
	} 

function showCalculators() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +100;
	window.open('../includes/calculators.php','calc','toolbar=0,scrollbars=1,height=500,width=950,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

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



//***********************************************************************************************************
//links.php
//***********************************************************************************************************
function editlink(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('editlink.php?uid='+uid,'edlink','toolbar=0,scrollbars=1,height=170,width=800,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addlink() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('addlink.php','addlink','toolbar=0,scrollbars=1,height=170,width=800,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  

function dellink(uid) {
	  if (confirm("Are you sure you want to delete this link")) {
		$.get("../admin/ajaxdellink.php", {tid: uid}, function(data){$("#updtlinklist").trigger("reloadGrid")});
	  }
}

function getlink(ln) {
	var ulink = "HTTP://"+ln;
	window.open (ulink,"linkwindow");	
}


//************************************************************************************************************
//index tasks.php
//************************************************************************************************************


function tedittodo(uid,i) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('../includes/edittodo.php?uid='+uid+'&ind='+i,'edtd','toolbar=0,scrollbars=1,height=300,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function taddtodo(i) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('../includes/addtodo.php?ind='+i,'addtd','toolbar=0,scrollbars=1,height=300,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				

function tdeltodo(uid) {
	 if (confirm("Are you sure you want to delete this Task")) {
			$.get("../includes/ajaxdeltodo.php", {tid: uid}, function(data){$("#todolist").trigger("reloadGrid")});
	  }
}

function tdonetodo(uid) {
	 if (confirm("Are you sure you have completed this task?")) {
		$.get("../includes/ajaxdonetodo.php", {tid: uid}, function(data){$("#todolist").trigger("reloadGrid")});																	
	 }
}

function temailtodo() {
	
	location.href='mailto:Enter recipient here'
	
}

//************************************************************************************************************
//update users
//************************************************************************************************************


	function edituser(uid) {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
	jQuery.ajaxSetup({async:false});
	$.get("ajax/ajaxUpdtUser.php", {uid: uid}, function(data){
	});
	jQuery.ajaxSetup({async:true});
	edituser2();
	}
	
	function edituser2() {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('chron/hs_edituser.php','eduser','toolbar=0,scrollbars=1,height=420,width=850,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}

	function adduser() {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('chron/hs_adduser.php','adduser','toolbar=0,scrollbars=1,height=420,width=850,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	} 

	function deluser(uid) {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
			
		window.open('hs_deleteuser.php?uid='+uid,'deluser','toolbar=0,scrollbars=1,height=300,width=950,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}

function deluser(uid) {
	 if (confirm("Are you sure you want to delete this user?")) {
		$.get("includes/ajaxdeluser.php", {tid: uid}, function(data){alert(data);$("#userslist").trigger("reloadGrid")});
		
	  }
}

//************************************************************************************************************
//stock
//************************************************************************************************************
function addstkgroup() {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('st_addgroup.php','addgrp','toolbar=0,scrollbars=1,height=200,width=650,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editstkgroup(uid) {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('st_editgroup.php?uid='+uid,'edgrp','toolbar=0,scrollbars=1,height=200,width=650,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addcat() {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('st_addcat.php','addgrp','toolbar=0,scrollbars=1,height=200,width=650,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editcat(uid) {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('st_editcat.php?uid='+uid,'edgrp','toolbar=0,scrollbars=1,height=200,width=650,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addstkitem() {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('st_additem.php','addit','toolbar=0,scrollbars=1,height=550,width=1000,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editstk(uid) {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('st_edititem.php?uid='+uid,'addit','toolbar=0,scrollbars=1,height=550,width=1000,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addpcent() {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('fin/st_addpcent.php','addpcent','toolbar=0,scrollbars=1,height=200,width=600,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editpcent(uid) {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('fin/st_editpcent.php?uid='+uid,'edpcent','toolbar=0,scrollbars=1,height=200,width=600,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function delpcent(uid) {
	 if (confirm("Are you sure you want to delete this percentage markup?")) {
		$.get("fin/includes/ajaxdelpcent.php", {tid: uid}, function(data){$("#stkpcentlist").trigger("reloadGrid")});
		
	  }
}

function addloc() {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('fin/st_addloc.php','addloc','toolbar=0,scrollbars=1,height=200,width=600,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editloc(uid) {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('fin/st_editloc.php?uid='+uid,'edloc','toolbar=0,scrollbars=1,height=200,width=600,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}




//************************************************************************************************************
//backup.php
//************************************************************************************************************

function bkup() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('../includes/bkup/backupdb.php','bkp','toolbar=0,scrollbars=1,height=200,width=400,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function rbupload() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('../includes/bkup/rbkupdb.php','bkr','toolbar=0,scrollbars=1,height=250,width=600,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


//************************************************************************************************************
// Stock reports
//************************************************************************************************************
function stklist() {
	var grp = document.getElementById('stkgroup').value;
	var cat = document.getElementById('stkcat').value;
	
	jQuery.ajaxSetup({async:false});
	$.get("ajax/ajaxUpdtStkGC.php", {grp: grp, cat: cat}, function(data){
	});
	jQuery.ajaxSetup({async:true});
	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('fin/strep_liststock.php','stl','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function stktake() {
	var stkgrp = document.getElementById('stkgroup').value;
	var stkcat = document.getElementById('stkcat').value;
	var stkloc = document.getElementById('loc').value;
	var randomqty = document.getElementById('randomqty').value;

	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('fin/strep_stktake2pdf.php?stkgrp='+stkgrp+'&stkcat='+stkcat+'&stkloc='+stkloc+'&randomqty='+randomqty,'sttpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);

}

//************************************************************************************************************
// Stock adjustment
//************************************************************************************************************

function adjstock(id) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('fin/stad_stkadjust.php?id='+id,'stkadj','toolbar=0,scrollbars=1,height=400,width=650,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

//************************************************************************************************************
// Distribution Lists
//************************************************************************************************************

function adddistlist () {
	$.get("includes/ajaxDistList.php", {}, function(data){alert(data);$("#distlist").trigger("reloadGrid")});
}

function updatedist() {
	$.get("includes/ajaxDistUpdate.php", {}, function(data){alert(data);$("#distdetlist").trigger("reloadGrid")});
}

function processdist() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +50;
	window.open('DistProcess.php','dp','toolbar=0,scrollbars=1,height=530,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function nofunds() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +50;
	window.open('nofunds.php','nofun','toolbar=0,scrollbars=1,height=530,width=850,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


//************************************************************************************************************
// Storage bins
//************************************************************************************************************

function editbin(binid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +50;
	window.open('fin/stad_editbin.php?binid='+binid,'edbin','toolbar=0,scrollbars=1,height=350,width=500,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//***********************************************************************************************************
//links.php
//***********************************************************************************************************
function editlink(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('editlink.php?uid='+uid,'edlink','toolbar=0,scrollbars=1,height=170,width=800,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addlink() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('addlink.php','addlink','toolbar=0,scrollbars=1,height=170,width=800,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  

function dellink(uid) {
	  if (confirm("Are you sure you want to delete this link")) {
		$.get("ajaxdellink.php", {tid: uid}, function(data){alert(data);$("#updtlinklist").trigger("reloadGrid")});
	  }
}


function getlink(ln) {
	var ulink = "HTTP://"+ln;
	window.open (ulink,"linkwindow");	
}



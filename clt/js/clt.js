//************************************************************************************************************
//index.php
//************************************************************************************************************


function todo() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +275;
	window.open('../includes/updttodo.php','todo','toolbar=0,scrollbars=1,height=550,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
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

function editmem(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxUpdtMember.php", {memberid: uid}, function(data){
	});
	jQuery.ajaxSetup({async:true});
	editmem2();
}

function editmem2() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editmember.php','edmem','toolbar=0,scrollbars=1,height=470,width=970,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addmem() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addmember.php','addmem','toolbar=0,scrollbars=1,height=420,width=970,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				

function delmem(uid) {
	 if (confirm("Are you sure you want to delete this Member")) {
			$.get("includes/ajaxdelmember.php", {tid: uid}, function(data){
				alert(data);
				$("#memlist2").trigger("reloadGrid")
			});
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
	clienttype = jQuery("#searchclienttype").val();
	position = jQuery("#searchposition").val(); 
	dfrom = jQuery("#dfrom").val(); 
	dto = jQuery("#dto").val(); 
	notestring = jQuery("#notestring").val();
	association = jQuery("#association").val();
	status = jQuery("#searchstatus").val();
	staff = jQuery("#searchrep").val();
	if (staff == null) {staff = '0';}
	gender = jQuery("#searchgender").val();
	if (gender == null) {gender = ' ';}
	birthmonth= jQuery("#lbirthmonth").val();
	if (birthmonth == null) {birthmonth = '00';}
	acat = jQuery("#searchacat").val();
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
	
	jQuery("#memlist2").setGridParam({url:"getFiltered.php?suburb_mask="+suburb+"&town_mask="+town+"&postcode_mask="+postcode+"&industry="+industry+"&occupation_mask="+occupation+"&workflow="+workflow+"&wfsdays="+wfsdays+"&position_mask="+position+"&dfrom="+dfrom+"&dto="+dto+"&recchecked="+recchecked+"&association="+association+"&status_mask="+status+"&acat="+acat+"&campaign="+campaign+"&campaignstage="+campaignstage+"&clienttype="+clienttype+"&notstatus="+notstatus+"&notcampaign="+notcampaign+"&notcampaignstage="+notcampaignstage+"&notsuburb="+notsuburb+"&nottown="+nottown+"&notpostcode="+notpostcode+"&notindustry="+notindustry+"&notoccupation="+notoccupation+"&notposition="+notposition+"&notage="+notage+"&notchecked="+notchecked+"&notoccupation="+notoccupation+"&notestring="+notestring+"&birthmonth="+birthmonth+"&staff="+staff+"&gender="+gender,page:1}).trigger("reloadGrid"); 
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
	document.getElementById('searchindustry').value = 0; 
	document.getElementById('searchposition').value = ''; 
	document.getElementById('searchoccupation').value = ''; 
	document.getElementById('searchworkflow').value = ' '; 
	document.getElementById('wfsdays').value = 0; 
	document.getElementById('dfrom').value = 0; 
	document.getElementById('dto').value = 0; 
	document.getElementById('recchecked').checked = false; 
	document.getElementById('notestring').value = '';
	document.getElementById('association').value = '';
	document.getElementById('searchrep').value = '';
	document.getElementById('searchgender').value = '';
	document.getElementById('searchstatus').value = ' ';
	document.getElementById('lbirthmonth').value = '';
	document.getElementById('searchclienttype').value = ' '; 
	//document.getElementById('ddateb').value = hd;
	//document.getElementById('ddatee').value = '0000-00-00';
	//document.getElementById('ddateb').value = dt;
	//document.getElementById('ddatee').value = dt;

	document.getElementById('notsuburb').checked = false; 
	document.getElementById('nottown').checked = false;
	document.getElementById('notpostcode').checked = false; 
	document.getElementById('notindustry').checked = false;
	document.getElementById('notoccupation').checked = false;
	document.getElementById('notposition').checked = false; 
	document.getElementById('notage').checked = false; 
	document.getElementById('notchecked').checked = false;
	document.getElementById('notstatus').checked = false;
	
	document.getElementById('printm').style.visibility = 'hidden';
	document.getElementById('printv').style.visibility = 'hidden';
	document.getElementById('emaillist').style.visibility = 'hidden';
	document.getElementById('bnotes').style.visibility = 'hidden';
	
	if (act == 'u') {
		jQuery("#memlist2").setGridParam({url:"getMembers.php",page:1}).trigger("reloadGrid"); 
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
	
	jQuery("#memlist2").setGridParam({url:"getMembers.php",page:1}).trigger("reloadGrid"); 

}

function printlistv() {
	getParams();
	var mv = "v";
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open("printlist.php?suburb_mask="+suburb+"&town_mask="+town+"&postcode_mask="+postcode+"&industry="+industry+"&occupation_mask="+occupation+"&workflow="+workflow+"&wfsdays="+wfsdays+"&position_mask="+position+"&dfrom="+dfrom+"&dto="+dto+"&recchecked="+recchecked+"&association="+association+"&status_mask="+status+"&acat="+acat+"&campaign="+campaign+"&campaignstage="+campaignstage+"&clienttype="+clienttype+"&notstatus="+notstatus+"&notcampaign="+notcampaign+"&notcampaignstage="+notcampaignstage+"&notsuburb="+notsuburb+"&nottown="+nottown+"&notpostcode="+notpostcode+"&notindustry="+notindustry+"&notoccupation="+notoccupation+"&notposition="+notposition+"&notage="+notage+"&notchecked="+notchecked+"&notoccupation="+notoccupation+"&notestring="+notestring+"&birthmonth="+birthmonth+"&staff="+staff+"&gender="+gender+"&mv="+mv,"plist","toolbar=0,scrollbars=1,height=540,width=1010,resizable=0,left="+x+",screenX="+x+",top="+y+",screenY="+y);
}

function printlistm() {
	
	getParams();
	
	var mv = "m";
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	
	window.open("printlist.php?suburb_mask="+suburb+"&town_mask="+town+"&postcode_mask="+postcode+"&industry="+industry+"&occupation_mask="+occupation+"&workflow="+workflow+"&wfsdays="+wfsdays+"&position_mask="+position+"&dfrom="+dfrom+"&dto="+dto+"&recchecked="+recchecked+"&association="+association+"&status_mask="+status+"&acat="+acat+"&campaign="+campaign+"&campaignstage="+campaignstage+"&clienttype="+clienttype+"&notstatus="+notstatus+"&notcampaign="+notcampaign+"&notcampaignstage="+notcampaignstage+"&notsuburb="+notsuburb+"&nottown="+nottown+"&notpostcode="+notpostcode+"&notindustry="+notindustry+"&notoccupation="+notoccupation+"&notposition="+notposition+"&notage="+notage+"&notchecked="+notchecked+"&notoccupation="+notoccupation+"&notestring="+notestring+"&birthmonth="+birthmonth+"&staff="+staff+"&gender="+gender+"&mv="+mv,"plist","toolbar=0,scrollbars=1,height=540,width=1010,resizable=0,left="+x+",screenX="+x+",top="+y+",screenY="+y);
}

function printliste() {
	
	getParams();
	var mv = "m";
	
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open("printliste.php?suburb_mask="+suburb+"&town_mask="+town+"&postcode_mask="+postcode+"&industry="+industry+"&occupation_mask="+occupation+"&workflow="+workflow+"&wfsdays="+wfsdays+"&position_mask="+position+"&dfrom="+dfrom+"&dto="+dto+"&recchecked="+recchecked+"&association="+association+"&status_mask="+status+"&acat="+acat+"&campaign="+campaign+"&campaignstage="+campaignstage+"&clienttype="+clienttype+"&notstatus="+notstatus+"&notcampaign="+notcampaign+"&notcampaignstage="+notcampaignstage+"&notsuburb="+notsuburb+"&nottown="+nottown+"&notpostcode="+notpostcode+"&notindustry="+notindustry+"&notoccupation="+notoccupation+"&notposition="+notposition+"&notage="+notage+"&notchecked="+notchecked+"&notoccupation="+notoccupation+"&notestring="+notestring+"&birthmonth="+birthmonth+"&staff="+staff+"&gender="+gender+"&mv="+mv,"elist","toolbar=0,scrollbars=1,height=540,width=1000,resizable=0,left="+x+",screenX="+x+",top="+y+",screenY="+y);
}

function bulknotes() {
	
	getParams();
	var mv = "m";
	
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open("bulknotes.php?suburb_mask="+suburb+"&town_mask="+town+"&postcode_mask="+postcode+"&industry="+industry+"&occupation_mask="+occupation+"&workflow="+workflow+"&wfsdays="+wfsdays+"&position_mask="+position+"&dfrom="+dfrom+"&dto="+dto+"&recchecked="+recchecked+"&association="+association+"&status_mask="+status+"&acat="+acat+"&campaign="+campaign+"&campaignstage="+campaignstage+"&clienttype="+clienttype+"&notstatus="+notstatus+"&notcampaign="+notcampaign+"&notcampaignstage="+notcampaignstage+"&notsuburb="+notsuburb+"&nottown="+nottown+"&notpostcode="+notpostcode+"&notindustry="+notindustry+"&notoccupation="+notoccupation+"&notposition="+notposition+"&notage="+notage+"&notchecked="+notchecked+"&notoccupation="+notoccupation+"&notestring="+notestring+"&birthmonth="+birthmonth+"&staff="+staff+"&gender="+gender+"&mv="+mv,"bnlist","toolbar=0,scrollbars=1,height=250,width=640,resizable=0,left="+x+",screenX="+x+",top="+y+",screenY="+y);
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
//listduplicates.php
//************************************************************************************************************

function delduplicate(uid) {
  if (confirm("Are you sure you want to delete this duplicate member")) {
			$.get("includes/ajaxdelduplicate.php", {tid: uid}, function(data){$("#memlistd").trigger("reloadGrid")});
	  }
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
//updtindustries.php
//************************************************************************************************************

function editind(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('editindustry.php?uid='+uid,'edind','toolbar=0,scrollbars=1,height=60,width=500,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addind() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('addindustry.php','addind','toolbar=0,scrollbars=1,height=60,width=500,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				

//************************************************************************************************************
//updtcomplaint_nature.php
//************************************************************************************************************

function editnat(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('editcomplaint_nature.php?uid='+uid,'ednat','toolbar=0,scrollbars=1,height=60,width=500,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addnat() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('addcomplaint_nature.php','addnat','toolbar=0,scrollbars=1,height=60,width=500,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				


//************************************************************************************************************
//updtcampaigns.php
//************************************************************************************************************
function editcampaign(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editcampaign.php?uid='+uid,'edcamp','toolbar=0,scrollbars=1,height=400,width=1024,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addcampaign() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addcampaign.php','addcamp','toolbar=0,scrollbars=1,height=400,width=1024,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				

function delcampaign(uid) {
	 if (confirm("Are you sure you want to delete this campaign")) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +200;
		window.open('delcampaign.php?uid='+uid,'delcamp','toolbar=0,scrollbars=1,height=220,width=550,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	  }
}

function admincand(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('admincand.php?uid='+uid,'adcand','toolbar=0,scrollbars=1,height=520,width=1024,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


function docs(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('campdocs.php?uid='+uid,'cdc','toolbar=0,scrollbars=1,height=400,width=900,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function costs(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('campcosts.php?uid='+uid,'cct','toolbar=0,scrollbars=1,height=400,width=600,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function gridReloadc1(){ 
	var nm_mask = jQuery("#clastname").val(); 
	nm_mask = nm_mask.toUpperCase();
	jQuery("#campaignlist").setGridParam({url:"getCampaigns.php?nm_mask="+nm_mask,page:1}).trigger("reloadGrid"); 
} 

function doSearchc1(){ 
		var timeoutHnd = setTimeout(gridReloadc1,500); 
	} 
	

function sresetc() {
	document.getElementById('clastname').value = ''; 
	
	jQuery("#campaignlist").setGridParam({url:"getCampaigns.php",page:1}).trigger("reloadGrid"); 
}


//************************************************************************************************************
//runcampaign.php
//************************************************************************************************************
function runcampaign(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +50;
	window.open('runcamp.php?uid='+uid,'edcamp','toolbar=0,scrollbars=1,height=600,width=1020,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function gridReload1c(){ 
	var nm_mask = jQuery("#csearchlastname").val(); 
	nm_mask = nm_mask.toUpperCase();
	jQuery("#rcamplist").setGridParam({url:"getCampaignsR.php?nm_mask="+nm_mask,page:1}).trigger("reloadGrid"); 
} 

function doSearch1c(){ 
		var timeoutHnd = setTimeout(gridReload1c,500); 
	} 
	

function sresetr() {
	document.getElementById('csearchlastname').value = ''; 
	
	jQuery("#rcamplist").setGridParam({url:"getCampaignsR.php",page:1}).trigger("reloadGrid"); 
}

//************************************************************************************************************
//updtoutprov.php
//************************************************************************************************************
function editoutprov(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +150;
	window.open('editoutprov.php?uid='+uid,'edop','toolbar=0,scrollbars=1,height=200,width=550,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addoutprov() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +150;
	window.open('addoutprov.php','addop','toolbar=0,scrollbars=1,height=200,width=550,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				

function deloutprov(uid) {
	  if (confirm("Are you sure you want to delete this outsource provider?")) {
		window.open('deloutprov.php?uid='+uid,'delop','toolbar=0,scrollbars=1,height=10,width=10');
	  }
}

function addstaff(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +100;
	window.open('updtoutstaff.php?outprov='+uid,'outst','toolbar=0,scrollbars=1,height=400,width=700,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//************************************************************************************************************
//campstats.php
//************************************************************************************************************

function Reloadcstats(){ 
	var fromday = jQuery("#fromday").val(); 
	var frommonth = jQuery("#frommonth").val(); 
	var fromyear = jQuery("#fromyear").val(); 
	var fromdate = fromyear+'-'+frommonth+'-'+fromday;
	var to_day = jQuery("#to_day").val(); 
	var to_month = jQuery("#to_month").val(); 
	var to_year = jQuery("#to_year").val(); 
	var todate = to_year+'-'+to_month+'-'+to_day;
	jQuery("#campstatlist").setGridParam({url:"getCampstatsC.php?fdt="+fromdate+"&tdt="+todate}).trigger("reloadGrid"); 
} 

//************************************************************************************************************
//campstatsA.php
//************************************************************************************************************

function Reloadastats(){ 
	var fromday = jQuery("#fromday").val(); 
	var frommonth = jQuery("#frommonth").val(); 
	var fromyear = jQuery("#fromyear").val(); 
	var fromdate = fromyear+'-'+frommonth+'-'+fromday;
	var to_day = jQuery("#to_day").val(); 
	var to_month = jQuery("#to_month").val(); 
	var to_year = jQuery("#to_year").val(); 
	var todate = to_year+'-'+to_month+'-'+to_day;
	jQuery("#campstatlista").setGridParam({url:"getCampstatsA.php?fdt="+fromdate+"&tdt="+todate}).trigger("reloadGrid"); 
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
	var bkupfile = "../subscriber/biztracccltsub"+subscriber+"_"+ddate+".gz";
	
	window.location.href=bkupfile; 
}

//************************************************************************************************************
//updtclienttypes.php
//************************************************************************************************************
function editclienttype(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('editclienttype.php?uid='+uid,'edct','toolbar=0,scrollbars=1,height=150,width=550,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addclienttype() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('addclienttype.php','addct','toolbar=0,scrollbars=1,height=150,width=550,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				

function delclienttype(uid) {
  if (confirm("Are you sure you want to delete this client type")) {
	window.open('delclienttype.php?uid='+uid,'delct','toolbar=0,scrollbars=1,height=10,width=10');
  }
}

//************************************************************************************************************
//updtclientstatus.php
//************************************************************************************************************
function editclientstatus(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('editclientstatus.php?uid='+uid,'edct','toolbar=0,scrollbars=1,height=150,width=550,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addclientstatus() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('addclientstatus.php','addct','toolbar=0,scrollbars=1,height=150,width=550,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				


//************************************************************************************************************
//updtacccats.php
//************************************************************************************************************
function editacccat(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('editacccat.php?uid='+uid,'edac','toolbar=0,scrollbars=1,height=150,width=550,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addacccat() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('addacccat.php','addac','toolbar=0,scrollbars=1,height=150,width=550,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				

//************************************************************************************************************
//updtworkflow.php
//************************************************************************************************************
function editworkflow(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('editworkflow.php?uid='+uid,'edwf','toolbar=0,scrollbars=1,height=250,width=550,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addworkflow() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('addworkflow.php','addwf','toolbar=0,scrollbars=1,height=250,width=550,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				


function delworkflow(uid) {
  if (confirm("Are you sure you want to delete this workflow stage")) {
			$.get("includes/ajaxdelworkflow.php", {tid: uid}, function(data){$("#flowlist").trigger("reloadGrid")});
	  }
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
//updtruralcodes.php
//************************************************************************************************************
function editrural(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('editrural.php?uid='+uid,'edru','toolbar=0,scrollbars=1,height=200,width=550,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addrural() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('addrural.php','addru','toolbar=0,scrollbars=1,height=200,width=550,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				

function delrural(uid) {
 if (confirm("Are you sure you want to delete this Rural Post Code")) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('delrural.php?uid='+uid,'delru','toolbar=0,scrollbars=1,height=200,width=550,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
  }
}

function gridReload2(){ 
var tr_mask = jQuery("#searchtown").val(); 
tr_mask = tr_mask.toUpperCase();
jQuery("#rurallist").setGridParam({url:"getrural.php?tr_mask="+tr_mask,page:1}).trigger("reloadGrid"); 
} 

function gridReload3(){ 
var pc_mask = jQuery("#searchpc").val(); 
jQuery("#rurallist").setGridParam({url:"getrural.php?pc_mask="+pc_mask,page:1}).trigger("reloadGrid"); 
} 

function doSearch2(){ 
	var timeoutHnd = setTimeout("gridReload2",500);
} 
function doSearch3(){ 
	var timeoutHnd = setTimeout("gridReload3",500); 
} 


//************************************************************************************************************
//updtstreetcodes.php
//************************************************************************************************************
function editstreet(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('editstreet.php?uid='+uid,'edst','toolbar=0,scrollbars=1,height=200,width=500,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addstreet() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('addstreet.php','addst','toolbar=0,scrollbars=1,height=200,width=500,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				

function delstreet(uid) {
 if (confirm("Are you sure you want to delete this Street Post Code")) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('delstreet.php?uid='+uid,'delst','toolbar=0,scrollbars=1,height=200,width=500,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
  }
}

function gridReload1st(){ 
	var nm_mask = jQuery("#searchst").val(); 
	nm_mask = nm_mask.toUpperCase();
	jQuery("#streetlist").setGridParam({url:"getstreets.php?nm_mask="+nm_mask,page:1}).trigger("reloadGrid"); 
} 
function gridReload2st(){ 
	var tr_mask = jQuery("#searchsub").val(); 
	tr_mask = tr_mask.toUpperCase();
	jQuery("#streetlist").setGridParam({url:"getstreets.php?tr_mask="+tr_mask,page:1}).trigger("reloadGrid"); 
} 
function gridReload3st(){ 
	var ar_mask = jQuery("#searcharea").val(); 
	jQuery("#streetlist").setGridParam({url:"getstreets.php?ar_mask="+ar_mask,page:1}).trigger("reloadGrid"); 
} 
function gridReload4st(){ 
	var pc_mask = jQuery("#searchpc").val(); 
	jQuery("#streetlist").setGridParam({url:"getstreets.php?pc_mask="+pc_mask,page:1}).trigger("reloadGrid"); 
} 


function doSearch1st(ev){ 
		var t = setTimeout("gridReload1st",500); 
	} 
function doSearch2st(ev){ 
		var t = setTimeout("gridReload2st",500); 
	} 
function doSearch3st(ev){ 
		var t = setTimeout("gridReload3st",500); 
	} 
function doSearch4st(ev){ 
		var t = setTimeout("gridReload4st",500); 
	} 


//************************************************************************************************************
//updtboxcodes.php
//************************************************************************************************************
function editbox(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('editbox.php?uid='+uid,'edbox','toolbar=0,scrollbars=1,height=200,width=550,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addbox() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('addbox.php','addbox','toolbar=0,scrollbars=1,height=200,width=550,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				

function delbox(uid) {
 if (confirm("Are you sure you want to delete this Box Post Code")) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('delbox.php?uid='+uid,'delbox','toolbar=0,scrollbars=1,height=200,width=550,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
  }
}


function gridReload1bx(){ 
	var nm_mask = jQuery("#searchpo").val(); 
	nm_mask = nm_mask.toUpperCase();
	jQuery("#boxlist").setGridParam({url:"getboxes.php?nm_mask="+nm_mask,page:1}).trigger("reloadGrid"); 
} 
function gridReload2bx(){ 
	var tr_mask = jQuery("#searchtown").val(); 
	tr_mask = tr_mask.toUpperCase();
	jQuery("#boxlist").setGridParam({url:"getboxes.php?tr_mask="+tr_mask,page:1}).trigger("reloadGrid"); 
} 
function gridReload3bx(){ 
	var pc_mask = jQuery("#searchpc").val(); 
	jQuery("#boxlist").setGridParam({url:"getboxes.php?pc_mask="+pc_mask,page:1}).trigger("reloadGrid"); 
} 


function doSearch1bx(ev){ 
		var t = setTimeout("gridReload1bx",500); 
	} 
function doSearch2bx(ev){ 
		var t = setTimeout("gridReload2bx",500); 
	} 
function doSearch3bx(ev){ 
		var t = setTimeout("gridReload3bx",500); 
	} 

//************************************************************************************************************
//updtrecipients.php
//************************************************************************************************************
function editrecipient(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('editrecipient.php?uid='+uid+'&from=u','edrecip','toolbar=0,scrollbars=1,height=150,width=700,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addrecipient() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('addrecipient.php?from=u','addrecip','toolbar=0,scrollbars=1,height=150,width=700,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				

//************************************************************************************************************
//updtattach.php
//************************************************************************************************************
function editattach(d) {
	var readfile = "../documents/attachments/"+d;
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
	window.open(readfile,'vatt','toolbar=0,scrollbars=1,height=500,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function addattach() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addattach.php','addat','toolbar=0,scrollbars=1,height=160,width=550,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  		

function delattach(uid) {
	  if (confirm("Are you sure you want to delete this document")) {
		window.open('delattach.php?uid='+uid,'delatt','toolbar=0,scrollbars=1,height=10,width=10');
	  }
}

//************************************************************************************************************
//audittrail.php
//************************************************************************************************************
function gridReloadln(sb){ 
	var nm_mask = jQuery("#searchlname").val(); 
	nm_mask = nm_mask.toUpperCase();
	jQuery("#auditlist").setGridParam({url:"getMembers.php?subscriber="+sb+"&nm_mask="+nm_mask,page:1}).trigger("reloadGrid"); 
} 

function doSearchln(){ 
		var timeoutHnd = setTimeout(gridReloadln,500) 
	} 


function setmem(memid) {
	document.getElementById('selectedmember').value = memid;
}


function filtera() {
	var selectedmember = jQuery("#selectedmember").val(); 
	var fromday = jQuery("#fromday").val(); 
	var frommonth = jQuery("#frommonth").val(); 
	var fromyear = jQuery("#fromyear").val(); 
	var fromdate = fromyear+'-'+frommonth+'-'+fromday;
	var to_day = jQuery("#to_day").val(); 
	var to_month = jQuery("#to_month").val(); 
	var to_year = jQuery("#to_year").val(); 
	var todate = to_year+'-'+to_month+'-'+to_day;
	var advisor = jQuery("#advisor").val(); 
	
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('auditlist.php?fromdate='+fromdate+'&todate='+todate+'&advisor='+advisor+'&selectedmember='+selectedmember,'auditlist','toolbar=0,scrollbars=1,height=500,width=1000,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function unfiltera() {
	document.getElementById('fromday').value = '<?php echo $fd; ?>';
	document.getElementById('frommonth').value = '<?php echo $fm; ?>';
	document.getElementById('fromyear').value = '<?php echo $fy; ?>';
	document.getElementById('to_day').value = '<?php echo $td; ?>';
	document.getElementById('to_month').value = '<?php echo $tm; ?>';
	document.getElementById('to_year').value = '<?php echo $ty; ?>';
	document.getElementById('advisor').value = ' ';
	document.getElementById('searchlname').value = '';
	jQuery("#auditlist").setGridParam({url:"getMembers.php",page:1}).trigger("reloadGrid"); 
}


function bkupschedule() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('bkuplist.php','bklist','toolbar=0,scrollbars=1,height=500,width=1000,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//************************************************************************************************************
//complaints.php
//************************************************************************************************************

function editcomplaint(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editcomplaint.php?compid='+uid,'edcpt','toolbar=0,scrollbars=1,height=660,width=1000,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addcomplaint() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addcomplaint.php','addcpt','toolbar=0,scrollbars=1,height=660,width=1000,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  

//************************************************************************************************************
//backup.php
//************************************************************************************************************

function bkupdb() {
	$.get("../includes/bkup/ajaxbkupdb.php", {}, function(data){
		document.getElementById('comp').style.visibility = 'visible';
  		document.getElementById('download').style.visibility = 'visible';
	});
}

function downloadsql() {
	
	$.get("../includes/bkup/ajaxgetdb.php", {}, function(data){
		var bkupfile = "../includes/bkup/backup/"+data;
		window.location.href=bkupfile;
  		document.getElementById('down').style.visibility = 'visible';
	});
	
}



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

function restorebkup(fl) {
	
	alert('do file restore routine');
	
}

//************************************************************************************************************
//index hs_setup.php
//************************************************************************************************************

function editsetup() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('../fin/hs_editsetup.php','edsu','toolbar=0,scrollbars=1,height=470,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//************************************************************************************************************
//update users
//************************************************************************************************************


	function edituser(uid) {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxUpdtUser.php", {uid: uid}, function(data){
	});
	jQuery.ajaxSetup({async:true});
	edituser2();
	}
	
	function edituser2() {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('../admin/hs_edituser.php','eduser','toolbar=0,scrollbars=1,height=420,width=850,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}

	function adduser() {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('../admin/hs_adduser.php','adduser','toolbar=0,scrollbars=1,height=420,width=850,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	} 


	function deluser(uid) {
		 if (confirm("Are you sure you want to de-activate this user?")) {
			$.get("../admin/ajaxdeluser.php", {tid: uid}, function(data){alert(data);$("#userslist").trigger("reloadGrid")});
		  }
	}

function addaccessclt() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +265;
	
	window.open('../admin/hs_addaccessclt.php','addclt','toolbar=0,scrollbars=1,height=420,width=850,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addaccessfin() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +265;
	
	window.open('../admin/hs_addaccessfin.php','addfin','toolbar=0,scrollbars=1,height=420,width=850,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addaccessprc() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +265;
	
	window.open('../admin/hs_addaccessprc.php','addprc','toolbar=0,scrollbars=1,height=420,width=850,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

	function delaccess(uid) {
		 if (confirm("Are you sure you want to delete this access?")) {
			$.get("../admin/ajaxdelaccess.php", {tid: uid}, function(data){alert(data);$("#accesslist").trigger("reloadGrid")});
		  }
	}
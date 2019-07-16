//***********************************************************************************************************
//index.php
//***********************************************************************************************************
function todo() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +275;
	window.open('../includes/updttodo.php','todo','toolbar=0,scrollbars=1,height=550,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

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
	window.open('../clt/editmember.php','edmem','toolbar=0,scrollbars=1,height=420,width=970,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addmem() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addclt.php','addclt','toolbar=0,scrollbars=1,height=420,width=970,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				



function emailmem(uid,memberid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +200;
		window.open('../clt/emailmem.php?commsid='+uid+'&memberid='+memberid,'emmem','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//**************************************************************
// print, send quote etc.
//**************************************************************

function printquote(rf) {
	var x = 0, y = 0; // default values	
	var type = 'QOT';
	x = window.screenX +5;
	y = window.screenY +200;
	if(typeof(rf)=="undefined"){ 
		rf = tradingref;
	}
	window.open('../clt/PrintQuote.php?type='+type+'&tradingref='+rf,'plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function emailquote(rf) {
	var x = 0, y = 0; // default values	
	var type = 'QOT';
	x = window.screenX +5;
	y = window.screenY +200;
	if(typeof(rf)=="undefined"){ 
		rf = tradingref;
	}
	window.open('../clt/PrintQuote.php?type='+type+'&tradingref='+rf+'&doemail=Y','plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function quote2so(rf) {
	  if (confirm("Are you sure you are ready to convert this quote into a sales order?")) {
		$.get("../ajax/ajaxquote2s_o.php", {qref: rf}, function(data){alert(data);$("#quotelist").trigger("reloadGrid")});
	  }
}

function editquote(id) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('../clt/editquote.php?id='+id,'edqt','toolbar=0,scrollbars=1,height=600,width=1020,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addquote(coyno) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +5;
		window.open('../clt/addquote.php?cid='+coyno,'addqt','toolbar=0,scrollbars=1,height=600,width=1020,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	//$("#dnlist").jqGrid('setGridParam',{datatype:'json'}).trigger('reloadGrid');	
}

//********************************************************
// delivery notes
//**********************************************************
function adddo(id) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +5;
		window.open('../clt/adddo.php?son='+id,'adddo','toolbar=0,scrollbars=1,height=600,width=1020,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}
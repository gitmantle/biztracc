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

//***********************************************************************************************************
//update dockets
//***********************************************************************************************************

function adddocket() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('adddocket.php','addk','toolbar=0,scrollbars=1,height=450,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editdocket(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editdocket.php?uid='+uid,'eddk','toolbar=0,scrollbars=1,height=450,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function deldocket(uid) {
	  if (confirm("Are you sure you want to delete this docket")) {
		$.get("includes/ajaxdeldocket.php", {tid: uid}, function(data){alert(data);$("#docketlist").trigger("reloadGrid")});
	  }
	  
}

//***********************************************************************************************************
//update routes
//***********************************************************************************************************

function addroute() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addroute.php','adrt','toolbar=0,scrollbars=1,height=290,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editroute(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editroute.php?uid='+uid,'edrt','toolbar=0,scrollbars=1,height=600,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//***********************************************************************************************************
//update forests
//***********************************************************************************************************

function addforest() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addforest.php','adfr','toolbar=0,scrollbars=1,height=250,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editforest(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editforest.php?uid='+uid,'edfr','toolbar=0,scrollbars=1,height=250,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//***********************************************************************************************************
//update contractors
//***********************************************************************************************************

function addcontractor() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addcontractor.php','adctr','toolbar=0,scrollbars=1,height=200,width=600,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editcontractor(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editcontractor.php?uid='+uid,'edctr','toolbar=0,scrollbars=1,height=200,width=600,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


//***********************************************************************************************************
//update ferry
//***********************************************************************************************************

function addferry() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addferry.php','adfy','toolbar=0,scrollbars=1,height=400,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editferry(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editferry.php?uid='+uid,'edfy','toolbar=0,scrollbars=1,height=400,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//***************************************************************************************************************
// import
//***************************************************************************************************************

function impinv() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +50;
	window.open('impinv.php','impinv','toolbar=0,scrollbars=1,height=400,width=820,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function addinvoices() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +50;
	window.open('addinvoices.php','adinv','toolbar=0,scrollbars=1,height=400,width=820,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

//***************************************************************************************************************
// costs
//***************************************************************************************************************
function editheader(cid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +50;
	window.open('editheader.php?uid='+cid,'edhd','toolbar=0,scrollbars=1,height=300,width=500,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}
function editcost(cid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +50;
	window.open('editcost.php?uid='+cid,'edcost','toolbar=0,scrollbars=1,height=300,width=650,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function addcostline(id) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addcostline.php?cl='+id,'adcl','toolbar=0,scrollbars=1,height=300,width=650,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function delcostline(uid) {
	  if (confirm("Are you sure you want to delete this cost line?")) {
		$.get("includes/ajaxdelcostline.php", {tid: uid}, function(data){alert(data);$("#costlineslist").trigger("reloadGrid")});
	  }
}

function postcost(cid) {
	if (document.getElementById('bgsta').checked) {
		var postgst = 'admin';
	} else {
		if (document.getelementById('bgstb').checked) {
			var postgst = 'brch';
	   }
	}
	$.get("includes/ajaxCheckCostlines.php", {cid: cid}, function(data){
		if (data == 'Y') {
			$.get("includes/ajaxPostCost.php", {cid: cid, postgst: postgst}, function(data){$("#costheadlisting").trigger("reloadGrid")});
		} else {
			alert('There are line items with no associated costs');
		}
	});

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
		
		window.open('hs_edituser.php','eduser','toolbar=0,scrollbars=1,height=450,width=850,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}

	function adduser() {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('hs_adduser.php','adduser','toolbar=0,scrollbars=1,height=450,width=850,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
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
//reports
//************************************************************************************************************

function getSelected(opt) {
   var selected = new Array();
   var index = 0;
   for (var intLoop=0; intLoop < opt.length; intLoop++) {
      if (opt[intLoop].selected) {
         index = selected.length;
         selected[index] = new Object;
         selected[index].value = opt[intLoop].value;
         selected[index].index = intLoop;
      }
   }
   return selected;
}

function outputSelected(opt) {
     var sel = getSelected(opt);
     var strSel = "";
     for (var item in sel)       
        strSel += sel[item].value + ",";
    return strSel;
}

function profitability() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	var bdate = SQLdate(document.getElementById('bdate').value);
	var edate = SQLdate(document.getElementById('edate').value);
	var branch = outputSelected(document.getElementById('branch'));
	
	window.open('profsched.php?bdate='+bdate+'&edate='+edate+'&branch='+branch,'tbg','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function prof2xl() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_prof2excel.php','tbxl','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function viewtrucktrans(br) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	jQuery.ajaxSetup({async:false});
	$.get("includes/ajaxUpdtTruckBr.php", {vbr: br}, function(data){
	});
	jQuery.ajaxSetup({async:true});
	viewtrucktrans2();
}

function viewtrucktrans2() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('viewtrans.php','vtr','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function tlog() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	var bdate = SQLdate(document.getElementById('bdate').value);
	var edate = SQLdate(document.getElementById('edate').value);
	var branch = outputSelected(document.getElementById('branch'));
	
	window.open('travelsched.php?bdate='+bdate+'&edate='+edate+'&branch='+branch,'tbg','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function tmapad(address) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('tgoogle.php?address='+address,'goog','toolbar=0,scrollbars=1,height=360,width=560,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function ruc() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('ruc2pdf.php','rucpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function routeprofitability() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	var bdate = SQLdate(document.getElementById('bdate').value);
	var edate = SQLdate(document.getElementById('edate').value);
	
	window.open('routeprofsched.php?bdate='+bdate+'&edate='+edate,'tbg','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


//************************************************************************************************************
//setup
//************************************************************************************************************

function hs_edit() {

	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('hs_setup2.php','hse','toolbar=0,scrollbars=1,height=500,width=900,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

//************************************************************************************************************
//ruc
//************************************************************************************************************

function editrucparams() {

	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('edrucparams.php','edrp','toolbar=0,scrollbars=1,height=400,width=700,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}




//************************************************************************************************************
//driverlog
//************************************************************************************************************

function dlog() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	var bdate = SQLdate(document.getElementById('bdate').value);
	var edate = SQLdate(document.getElementById('edate').value);
	var drvs = outputSelected(document.getElementById('driver'));
	
	if (drvs == "") {
		alert("Please select at least one driver");
		return false;
	}
	
	window.open('driversched.php?bdate='+bdate+'&edate='+edate+'&driver='+drvs,'tbg','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editdlog(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editdlog.php?uid='+uid,'eddl','toolbar=0,scrollbars=1,height=350,width=600,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}




//***********************************************************************************************************
//update rates
//***********************************************************************************************************

function editrate(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editrate.php?uid='+uid,'edrte','toolbar=0,scrollbars=1,height=250,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//***********************************************************************************************************
//update destinations
//***********************************************************************************************************

function adddestination() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('adddestination.php','adfr','toolbar=0,scrollbars=1,height=250,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editdestination(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editdestination.php?uid='+uid,'edrte','toolbar=0,scrollbars=1,height=250,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//***********************************************************************************************************
// map vehicles
//***********************************************************************************************************

function mapvehicles() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	var trucks = document.getElementById('trucks').value;
	var edate = SQLdate(document.getElementById('edate').value);
	var fromtime = document.getElementById('from').value;
	var time = document.getElementById('time').value;
	var res = document.getElementById('res').value;
	var n = res.split("x");
	var width = n[0];
	var height = n[1];
	window.open('map_vehicles.php?trucks='+trucks+'&edate='+edate+'&fromtime='+fromtime+'&time='+time+'&res='+res,'edrte','toolbar=0,scrollbars=1,height='+height+',width='+width+',resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//************************************************************************************************************
// allocate wages
//************************************************************************************************************

function nextref(ref) {
	var refno = ajaxGetRef('JNL','ns');
}

function addwage() {
	var truckportion = document.getElementById('truckpcent').value;
	var trailerportion = document.getElementById('trailerpcent').value;
	var op = document.getElementById('loperator').value;
	var trk = document.getElementById('truck').value;
	var trl = document.getElementById('trailer').value;
	var amt = document.getElementById('amount').value;
	var k = trk.split('~');
	var trkbr = k[0];
	var trkno = k[1];
	var l = trl.split('~');
	var trlbr = l[0];
	var trlno = l[1];
	
	
	var ok = 'Y';
	
	if (parseFloat(truckportion) + parseFloat(trailerportion) != 100) {
		alert('Truck plus trailer percentages must total 100');
		ok = 'N';
		return false;
	}
	if (trk == '') {
		alert('Please select a truck');
		ok = 'N';
		return false;
	}
	if (amt == 0) {
		alert('Please select an amount');
		ok = 'N';
		return false;
	}
	if (op == '') {
		alert('Please select an operator');
		ok = 'N';
		return false;
	}
		
	if (ok == "Y") {
		$.get("includes/ajaxAddWage.php", {op:op, trkno:trkno, trkbr:trkbr, trlno:trlno, trlbr:trlbr, amt:amt, trkpcent:truckportion, trlpcent:trailerportion}, function(data){$("#wageslist").trigger("reloadGrid")});
		document.getElementById('loperator').selectedIndex = 0;
		document.getElementById('truck').selectedIndex = 0;
		document.getElementById('trailer').selectedIndex = 0;
		document.getElementById('amount').value = 0;
	}
}

function delwages(id) {
	  if (confirm("Are you sure you want to delete this entry")) {
		$.get("includes/ajaxdelwage.php", {tid: id}, function(data){$("#wageslist").trigger("reloadGrid")});
	  }
}

function postWages() {
	var bank = document.getElementById('bank').value;
	var refno = document.getElementById('newrefno').value;
	var ddate = SQLdate(document.getElementById('newdate').value);
	var totwage = document.getElementById('totwage').value;
	var ref = 'JNL'+document.getElementById('newrefno').value;
	var labac = document.getElementById('labac').value;
	
	var ok = 'Y';
	
	if (bank == 0) {
		alert('Please select a bank account');
		ok = 'N';
		return false;
	}
	if (refno == '') {
		alert('Please enter a reference number');
		ok = 'N';
		return false;
	}
	if (labac == '') {
		alert('Please create relevant Labour accounts before allocating wages');
		ok = 'N';
		return false;
	}
	
	if (ok == "Y") {
		$.get("includes/ajaxPostWages.php", {ddate:ddate, ref:ref, bank:bank, totwage:totwage, labac:labac }, function(data){
			if (data == 'Y') {
				$.get("../fin/includes/ajaxPostTrans.php", {}, function(data){});
			} else {
				alert(data);
			}
			$("#wageslist").trigger("reloadGrid")});
	}
}

//************************************************************************************************************
// servicing and maintenance
//************************************************************************************************************

function sm(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('maintenance.php?uid='+uid,'sm','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addvehicle() {
var x = 0, y = 0; // default values	
 x = window.screenX +5;
  y = window.screenY +265;
		
	window.open('addvehicle.php','addruc','toolbar=0,scrollbars=1,height=300,width=600,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
} 

function editvehicle(uid) {
var x = 0, y = 0; // default values	
 x = window.screenX +5;
  y = window.screenY +265;
		
	window.open('editvehicle.php?uid='+uid,'edruc','toolbar=0,scrollbars=1,height=300,width=600,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
} 

function delvehicle(uid) {
	  if (confirm("Are you sure you want to delete this vehicle")) {
		$.get("includes/ajaxdelvehicle.php", {tid: uid}, function(data){$("#smlist").trigger("reloadGrid")});
	  }
	  
}

function viewsm(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('vmaintenance.php?uid='+uid,'sm','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function updtwsstaff(uid) {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxupdtwsstaff.php", {uid: uid}, function(data){
	});
	jQuery.ajaxSetup({async:true});
	updtwsstaff2();
}
	
function updtwsstaff2() {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('wsstaff.php','edwss','toolbar=0,scrollbars=1,height=450,width=850,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}



//***********************************************************************************************************
//documents
//***********************************************************************************************************


function viewdocuments(id,stf) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('viewdocs.php?id='+id+'&stf='+stf,'vdc','toolbar=0,scrollbars=1,height=450,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function deldocument(uid) {
	  if (confirm("Are you sure you want to delete this document")) {
		$.get("includes/ajaxdeldocument.php", {tid: uid}, function(data){$("#documentlist").trigger("reloadGrid")});
	  }
	  
}

function viewdocument(id,sid,d) {
	var readfile = "documents/"+sid+"/"+d;
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
	window.open(readfile,'doc','toolbar=0,scrollbars=1,height=500,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


//***********************************************************************************************************
//templates
//***********************************************************************************************************

function addtemplate() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addtemplate.php','addc','toolbar=0,scrollbars=1,height=300,width=600,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function downloadtemplate(id,sid,d) {
	var readfile = "documents/"+sid+"/templates/"+"/"+d;
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
	window.open(readfile,'doc','toolbar=0,scrollbars=1,height=500,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function deltemplate(uid) {
	  if (confirm("Are you sure you want to delete this template")) {
		$.get("includes/ajaxdeltemplate.php", {tid: uid}, function(data){$("#templatelist").trigger("reloadGrid")});
	  }
}

//***********************************************************************************************************
//general documents
//***********************************************************************************************************
function viewgendoc(d) {
	var readfile = "documents/"+d;
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
	window.open(readfile,'doc','toolbar=0,scrollbars=1,height=500,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addgendoc() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addgendoc.php','addg','toolbar=0,scrollbars=1,height=300,width=600,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function delgendoc(uid) {
	  if (confirm("Are you sure you want to delete this document?")) {
		$.get("includes/ajaxdelgendoc.php", {tid: uid}, function(data){$("#gendoclist").trigger("reloadGrid")});
	  }
}

//***********************************************************************************************************
//update incident parameters
//***********************************************************************************************************

function addinctype() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addinctype.php','adinctp','toolbar=0,scrollbars=1,height=200,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editinctype(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editinctype.php?uid='+uid,'edinctp','toolbar=0,scrollbars=1,height=200,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addincterrain() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addincterrain.php','adinctr','toolbar=0,scrollbars=1,height=200,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editincterrain(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editincterrain.php?uid='+uid,'edinctr','toolbar=0,scrollbars=1,height=200,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addincweather() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addincweather.php','adincwt','toolbar=0,scrollbars=1,height=200,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editincweather(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editincweather.php?uid='+uid,'edincwt','toolbar=0,scrollbars=1,height=200,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addinctemp() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addinctemp.php','adinctm','toolbar=0,scrollbars=1,height=200,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editinctemp(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editinctemp.php?uid='+uid,'edinctm','toolbar=0,scrollbars=1,height=200,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addincwind() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addincwind.php','adincwd','toolbar=0,scrollbars=1,height=200,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editincwind(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editincwind.php?uid='+uid,'edincwd','toolbar=0,scrollbars=1,height=200,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addincseverity() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addincseverity.php','adincsv','toolbar=0,scrollbars=1,height=200,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editincseverity(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editincseverity.php?uid='+uid,'edincsv','toolbar=0,scrollbars=1,height=200,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addinclegal() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addinclegal.php','adinclg','toolbar=0,scrollbars=1,height=200,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editinclegal(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editinclegal.php?uid='+uid,'edinclg','toolbar=0,scrollbars=1,height=200,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addincbasic() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addincbasic.php','adincbs','toolbar=0,scrollbars=1,height=200,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editincbasic(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editincbasic.php?uid='+uid,'edincbs','toolbar=0,scrollbars=1,height=200,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addincident() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addincident.php','adinc','toolbar=0,scrollbars=1,height=520,width=900,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

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
	
	window.open('emailinc.php?id='+id,'eminc','toolbar=0,scrollbars=1,height=540,width=940,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
	win1.close();
	
}


//***********************************************************************************************************
//update workshops
//***********************************************************************************************************


function addworkshop() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addworkshop.php','adws','toolbar=0,scrollbars=1,height=200,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editworkshop(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('editworkshop.php?uid='+uid,'edws','toolbar=0,scrollbars=1,height=200,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


//***********************************************************************************************************
//hazards
//***********************************************************************************************************

function printhazard(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('haz2pdf.php?uid='+uid,'hazpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function emailhazard(id) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	win1 = window.open('hazepdf.php?uid='+id,'haz2epdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
	window.open('emailhaz.php?id='+id,'emhaz','toolbar=0,scrollbars=1,height=540,width=940,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	win1.close();
	
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

	
	jQuery("#incidentlist").setGridParam({url:"getFiltered.php?coyname_mask="+coyname+"&fdate_mask="+fdate+"&tdate_mask="+tdate+"&lti_mask="+lti+"&type_mask="+type+"&harm_mask="+harm+"&damage_mask="+damage+"&reocurr_mask="+reocurr+"&terrain_mask="+terrain+"&weather_mask="+weather+"&temperature_mask="+temperature+"&wind_mask="+wind+"&basic1_mask="+basic1+"&basic2_mask="+basic2+"&notcoyname_mask="+notcoyname+"&notdate_mask="+notdate+"&notlti_mask="+notlti+"&nottype_mask="+nottype+"&notharm_mask="+notharm+"&notdamage_mask="+notdamage+"&notreoccur_mask="+notreocurr+"&notterrain_mask="+notterrain+"&notweather_mask="+notweather+"&nottemperature_mask="+nottemperature+"&notwind_mask="+notwind,page:1}).trigger("reloadGrid");
	
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
	$.get("includes/ajaxunfilter.php", {}, function(data){$("#incidentlist").trigger("reloadGrid")});
	jQuery("#incidentlist").setGridParam({url:"getincidents.php",page:1}).trigger("reloadGrid"); 
	document.getElementById('filterpage').style.visibility = 'hidden';
}
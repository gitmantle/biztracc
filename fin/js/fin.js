var tradingtype;
var stopProcess;

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


/*
function gridReload1dr(){ 
	var nm_mask = jQuery("#searchlastname").val(); 
	nm_mask = nm_mask.toUpperCase();
	jQuery("#drlist").setGridParam({url:"getMembers.php?nm_mask="+nm_mask}).trigger("reloadGrid"); 
} 

function doSearch1dr(){ 
		var timeoutHnd = setTimeout(gridReload1dr,500); 
	} 
*/	

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
		$.get("../admin/ajaxdellink.php", {tid: uid}, function(data){$("#updtlinklist").trigger("reloadGrid")});
	  }
}

function getlink(ln) {
	var ulink = "HTTP://"+ln;
	window.open (ulink,"linkwindow");	
}

//***********************************************************************************************************
//ad_updtgl.php
//***********************************************************************************************************

function editgl(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('ad_editgl.php?uid='+uid,'edgl','toolbar=0,scrollbars=1,height=460,width=950,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addgl() {
	var sb = 'N';
	var br = 'N';
	var accno = 0;
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('ad_addgl.php?sb='+sb+"&br="+br+'&accno='+accno,'addgl','toolbar=0,scrollbars=1,height=460,width=950,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				

function addsubgl(accno,branch) {
	var sb = 'Y';
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('ad_addgl.php?sb='+sb+'&br='+branch+'&accno='+accno,'addgl','toolbar=0,scrollbars=1,height=460,width=950,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				


function delgl(uid) {
	 if (confirm("You may only delete an account if it has a zero balance and no transactions. Are you sure you want to delete this account?")) {
		$.get("includes/ajaxdelgl.php", {tid: uid}, function(data){alert(data);$("#glacclist").trigger("reloadGrid")});
		
	 }
}

	
function gridReload(acc){ 
	var br = jQuery("#brlist").val(); 
	accgroup = acc;
	branchid = br;

	jQuery("#gl_list").setGridParam({url:"getgl.php?ac_mask="+acc+"&br="+br}).trigger("reloadGrid"); 
} 

function ad_updtgl() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('ad_updtgl_w.php?','addr','toolbar=0,scrollbars=1,height=450,width=990,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//***********************************************************************************************************
//ad_updtdr.php
//***********************************************************************************************************
function editdr(uid,sno) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxUpdtMember.php", {memberid: uid}, function(data){
	});
	jQuery.ajaxSetup({async:true});
	editmem2(sno);
}

function editmem2(sno) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	if (sno == 0) {
		window.open('../clt/editmember.php?from=fin','edmem','toolbar=0,scrollbars=1,height=420,width=970,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	} else {
		window.open('ad_editsubdr.php?sno='+sno,'edsdr','toolbar=0,scrollbars=1,height=120,width=670,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}
}

	function deldr(uid) {
	  if (confirm("Are you sure you want to delete this account?")) {
			$.get("includes/ajaxdeldr.php", {tid: uid}, function(data){alert(data);$("#drlist").trigger("reloadGrid")});
	  }
	}
	
	function add2dr(uid) {
	  if (confirm("Are you sure you want to add this client as a Debtor?")) {
			$.get("includes/ajaxaddr.php", {tid: uid}, function(data){alert(data);$("#drlist").trigger("reloadGrid")});
	  }
	}  
	
function addclt(l) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('ad_addclt.php?ledger='+l,'adclt','toolbar=0,scrollbars=1,height=500,width=990,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function adsubdr(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('ad_addsubdr2.php?uid='+uid,'adsubdr','toolbar=0,scrollbars=1,height=200,width=670,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function gridReload1(){ 
	var nm_mask = jQuery("#searchlegalname").val(); 
	nm_mask = nm_mask.toUpperCase();
	jQuery("#drlist").setGridParam({url:"getdr.php?nm_mask="+nm_mask,page:1}).trigger("reloadGrid"); 
} 
function gridReload2(){ 
	var tr_mask = jQuery("#searchtradname").val(); 
	tr_mask = tr_mask.toUpperCase();
	jQuery("#drlist").setGridParam({url:"getdr.php?tr_mask="+tr_mask,page:1}).trigger("reloadGrid"); 
} 

function ad_updtdr() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('ad_updtdr_w.php?','addr','toolbar=0,scrollbars=1,height=450,width=990,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function ad_updtcr() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('ad_updtcr_w.php?','adcr','toolbar=0,scrollbars=1,height=450,width=990,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//***********************************************************************************************************
//ad_updtcr.php
//***********************************************************************************************************
function editcr(uid,sno) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxUpdtMember.php", {memberid: uid}, function(data){
	});
	jQuery.ajaxSetup({async:true});
	editmem2(sno);
}

function editmem2(sno) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	if (sno == 0) {
		window.open('../clt/editmember.php?from=fin','edmem','toolbar=0,scrollbars=1,height=420,width=970,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	} else {
		window.open('ad_editsubcr.php?sno='+sno,'edscr','toolbar=0,scrollbars=1,height=120,width=670,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}
}

	function delcr(uid) {
	  if (confirm("Are you sure you want to delete this account?")) {
			$.get("includes/ajaxdelcr.php", {tid: uid}, function(data){alert(data);$("#crlist").trigger("reloadGrid")});
	  }
	}
	
	function add2cr(uid) {
	  if (confirm("Are you sure you want to add this client as a Creditor?")) {
			$.get("includes/ajaxadcr.php", {tid: uid}, function(data){alert(data);$("#crlist").trigger("reloadGrid")});
	  }
	}  

function adsubcr(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('ad_addsubcr2.php?uid='+uid,'adsubcr','toolbar=0,scrollbars=1,height=200,width=670,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


//***********************************************************************************************************
//fa_updtfa.php
//***********************************************************************************************************

function editfa(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('fa_editfa.php?uid='+uid,'edfa','toolbar=0,scrollbars=1,height=450,width=950,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addfa() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('fa_addfa.php','addfa','toolbar=0,scrollbars=1,height=450,width=950,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				

function delfa(uid) {
	 if (confirm("You may only delete an account if it has a zero balance and no transactions. Are you sure you want to delete this account?")) {
		$.get("includes/ajaxdelfa.php", {tid: uid}, function(data){alert(data);$("#faacclist").trigger("reloadGrid")});
		
	  }
}


//***********************************************************************************************************
//tr_stdtrans.php
//***********************************************************************************************************

var changetax = 'N';

function addtrans() {
	
	changetax = 'N';
	
	var ddate = document.getElementById('newdate').value;

	if(ddate == '' || ddate == "0000-00-00") {
		var dt = date();
		var d = getDate(dt);
		var m = getMonth(dt);
		var y = getfullYear(dt);
		var ddate = y.toString()+'-'+m.toString()+'-'+d.toString
	}

	var ref = document.getElementById('newref').options[document.getElementById('newref').selectedIndex].value;
	var refindex =  document.getElementById('newref').selectedIndex;
	if(ref == "") {
		alert('Please select a transaction type');
		return(false);
	}
	var refno = document.getElementById('newrefno').value;
	var reference = ref+refno;
	if ($('#yourref').length > 0) {
		var yourref = document.getElementById('yourref').value;
	} else {
		var yourref = '';
	  // exists.
	}	
	var DRaccountsList = document.getElementById('DRaccount').value;
	if(DRaccountsList == "") {
		alert('Please select an account to debit');
		return(false);
	}
	var dracc = DRaccountsList.split("~");
	var a2dr = dracc[1];
	var b2dr = dracc[3];
	var s2dr = dracc[2];
	var n2dr = dracc[0];
	
	var CRaccountsList = document.getElementById('CRaccount').value;
	if(CRaccountsList == "") {
		alert('Please select an account to credit');
		return(false);
	}
	var cracc = CRaccountsList.split("~");
	var a2cr = cracc[1];
	var b2cr = cracc[3];
	var s2cr = cracc[2];
	var n2cr = cracc[0];
	
	var amount = (parseFloat(document.getElementById('newamount').value)).toFixed(2);
	
	if (amount < 0) {
		alert('Amount may not be negative');
		return false;
	}
	
	var gstnt = document.getElementById('gstnt').value;
	if (gstnt == 'N_T~0') {
		var taxpcent = 0;
		var taxtype = 'N_T';
		var tax = 0;
		var taxindex = 0;
	} else {
		var tx = document.getElementById('newtaxtype').value;
		var txx = tx.split('#');
		var taxpcent = txx[0];
		var taxtype = txx[1];
		if(taxpcent == "") {
			alert('Please select a tax type');
			return(false);
		}
		var taxindex =  document.getElementById('newtaxtype').selectedIndex;
		var tax = (parseFloat(document.getElementById('newtax').value)).toFixed(2);
	}
	
	var total = (parseFloat(document.getElementById('newtotal').value)).toFixed(2);
	if(total == 0) {
		alert('Please input an amount');
		return(false);
	}	
	
	var assetno = document.getElementById('newasset').options[document.getElementById('newasset').selectedIndex].value;
	if (assetno == '') {
		assetno = '0~0';
	}
	
	var description = document.getElementById('newdescription').value;
	
	var drgst = document.getElementById('newdrgst').options[document.getElementById('newdrgst').selectedIndex].value;
	/*
	if (a2dr == 870 && a2cr != 871) {
		drgst = 'N';
	} else {
		drgst = 'Y';
	}
	*/
	var crgst = document.getElementById('newcrgst').options[document.getElementById('newcrgst').selectedIndex].value;
	/*
	if (a2cr == 870 && a2dr != 871) {
		crgst = 'N';
	} else {
		crgst = 'Y';
	}
	*/
	
	$.get("includes/ajaxAddTrans.php", {acc2dr:a2dr, subdr:s2dr, brdr:b2dr, acc2cr:a2cr, subcr:s2cr, brcr:b2cr, ddate:ddate, descript1:description, reference:reference, amount:amount, taxpcent:taxpcent, tax:tax, taxtype:taxtype, total:total, refindex:refindex, taxindex:taxindex, a2d:n2dr, a2c:n2cr, drgst:drgst, crgst:crgst, yourref:yourref}, function(data){$("#translist").trigger("reloadGrid")});
	
	clear_new_line_fields();
	document.getElementById('newdate').focus();	
	//document.getElementById('drsearch').style.visibility = 'hidden';
	//document.getElementById('crsearch').style.visibility = 'hidden';
	//document.getElementById('DRaccount').style.visibility = 'hidden';
	//document.getElementById('CRaccount').style.visibility = 'hidden';
	document.getElementById('newtaxtype').style.visibility = 'visible';
	document.getElementById('newamount').style.visibility = 'visible';
	document.getElementById('newtax').style.visibility = 'visible';
	document.getElementById('newtotal').style.visibility = 'visible';	
	document.getElementById('ndrgst').style.visibility = 'hidden';
	document.getElementById('ncrgst').style.visibility = 'hidden';
	

}


function showtax() {
/*	var dr = document.getElementById('DRaccount').value;
	var adr = dr.split('~');
	var a2dr = adr[1];
	var cr = document.getElementById('CRaccount').value;
	var acr = cr.split('~');
	var a2cr = acr[1];
	var gstinvpay = document.getElementById('gstinvpay').value;
	var ledger = document.getElementById('newbgacc2dr').value;
	if (gstinvpay == 'Invoice') {
		if ((a2dr <= 700 && a2cr > 700) || (a2cr <= 700 && a2dr > 700) || (ledger == 'AS')) {
			document.getElementById('newtaxtype').style.visibility = 'visible';
			document.getElementById('newamount').style.visibility = 'visible';
			document.getElementById('newtax').style.visibility = 'visible';
			document.getElementById('newtotal').style.visibility = 'visible';
		} else {
			document.getElementById('newtaxtype').style.visibility = 'hidden';
			document.getElementById('newtaxtype').selectedIndex = 4;
			document.getElementById('newamount').style.visibility = 'hidden';
			document.getElementById('newtax').style.visibility = 'visible';
			document.getElementById('newtotal').style.visibility = 'visible';
		}
	} else {
		if (((a2dr > 750 && a2dr <= 800) && (a2cr <= 700 || a2cr > 5000)) || (a2cr > 750 && a2cr <= 800) && (a2dr <= 700 || a2dr > 5000)) {
			document.getElementById('newtaxtype').style.visibility = 'visible';
			document.getElementById('newamount').style.visibility = 'visible';
			document.getElementById('newtax').style.visibility = 'visible';
			document.getElementById('newtotal').style.visibility = 'visible';
		} else {
			document.getElementById('newtaxtype').style.visibility = 'hidden';
			document.getElementById('newtaxtype').selectedIndex = 4;
			document.getElementById('newamount').style.visibility = 'hidden';
			document.getElementById('newtax').style.visibility = 'visible';
			document.getElementById('newtotal').style.visibility = 'visible';
		}
	}
*/	
}


function add_tax(amount) {
	if (changetax == 'N') {
		var tx = document.getElementById('newtaxtype').value;
		var txx = tx.split('#');
		var taxpcent = txx[0];
		var taxtype = txx[1];
		if (isNaN(taxpcent)) {
			alert('Please choose a tax type');
			return false;
		}
		var tax = (amount*taxpcent/100).toFixed(2);
		document.getElementById('newtax').value = tax;	
		document.getElementById('newtotal').value = (parseFloat(amount)+parseFloat(tax)).toFixed(2);	
	}
}

function deduct_tax(total) {
	if (changetax == 'N') {
		var tx = document.getElementById('newtaxtype').value;
		var txx = tx.split('#');
		var taxpcent = txx[0];
		var taxtype = txx[1];
		if (isNaN(taxpcent)) {
			alert('Please choose a tax type');
			return false;
		}
		var taxed = (total/(1.0 + parseFloat(taxpcent/100))).toFixed(2);
		document.getElementById('newtax').value = (parseFloat(total)-parseFloat(taxed)).toFixed(2);
		document.getElementById('newamount').value = taxed;	
	}
}

function change_tax(tax) {
	document.getElementById('newamount').value = (document.getElementById('newtotal').value - parseFloat(tax)).toFixed(2);
	changetax = 'Y';
}
	
function clear_new_line_fields()
{
	var dracc = document.getElementById('DRaccount').value;
	var dr = dracc.split('~');
	var drno = dr[0];
	var cracc = document.getElementById('CRaccount').value;
	var cr = cracc.split('~');
	var crno = cr[0];

	document.getElementById('newref').selectedIndex = 0;
	document.getElementById('newrefno').value = 0;
	document.getElementById('DRaccount').value = "";
	document.getElementById('CRaccount').value = "";
	document.getElementById('newbgacc2dr').selectedIndex = 0;
	document.getElementById('newbgacc2cr').selectedIndex = 0;
	document.getElementById('newdescription').value = '';
	document.getElementById('newamount').value = 0;
	document.getElementById('newtaxtype').selectedIndex = 0;
	document.getElementById('newtax').value = 0;
	document.getElementById('newtotal').value = 0;
	document.getElementById('yourref').value = '';
	//document.getElementById('newxref').selectedIndex = 0;
	document.getElementById('newasset').selectedIndex = 0;
	if (drno == 5000 || crno == 5000) {
		document.getElementById('newasset').style.visibility = 'visible';
	} else {
		document.getElementById('newasset').style.visibility = 'hidden';
	}
}	

function iajaxGetACList(debitcredit,ledger,draccount,craccount) {

	iblanklist(debitcredit);
	switch (ledger) {
		case 'GL':
		//populate account list with general ledger accounts
			$.get("includes/ajaxGetGlList.php", {drcr: debitcredit, drind:draccount, crind:craccount}, function(data){
				if(debitcredit == 'dr') {
					$("#newDRaccountsList").append(data);
				} else {
					$("#newCRaccountsList").append(data);
				}
			});
		break;
		case 'DR':
		// populate account list with debtor ledger accounts
			$.get("includes/ajaxGetDrList.php", {drcr: debitcredit, drind:draccount, crind:craccount}, function(data){
				if(debitcredit == 'dr') {
					$("#newDRaccountsList").append(data);
				} else {
					$("#newCRaccountsList").append(data);
				}

			});
		break;		
		case 'CR':
		// populate account list with creditor ledger accounts
			$.get("includes/ajaxGetCrList.php", {drcr: debitcredit, drind:draccount, crind:craccount}, function(data){
				if(debitcredit == 'dr') {
					$("#newDRaccountsList").append(data);
				} else {
					$("#newCRaccountsList").append(data);
				}
			});
		break;		
		case 'AS':
		// populate account list with fixed asset accounts
			$.get("includes/ajaxGetAsList.php", {drcr: debitcredit, drind:draccount, crind:craccount}, function(data){
				if(debitcredit == 'dr') {
					$("#newDRaccountsList").append(data);
				} else {
					$("#newCRaccountsList").append(data);
				}
			});
		break;		
	}
}

function iblanklist(drcr) {
	if (drcr == 'dr') {
		// remove all entries from account to debit list
		var x=document.getElementById("newDRaccountsList");
		var listlength = document.getElementById("newDRaccountsList").length;
		for (var i = 0; i < listlength; i ++) {
			x.remove(x[i]);
		}
	} else {
		// remove all entries from account to credit list
		var x=document.getElementById("newCRaccountsList");
		var listlength = document.getElementById("newCRaccountsList").length;
		for (var i = 0; i < listlength; i ++) {
			x.remove(x[i]);
		}
	}
}




function editline(lineno) {

	$.get("includes/ajaxgetline.php", {lineno: lineno}, function(data){
		var ln = data.split('~');
	//$str = $acc2dr."~".$subdr."~".$brdr."~".$acc2cr."~".$subcr."~".$brcr."~".$ddate."~".$descript1."~".$reference."~".$amount."~".$tax."~".$taxtype."~".$taxpcent."~".$total."~".$refindex."~".$taxindex."~".$a2d."~".$a2c;
		
		var drno = ln[0];
		var drsb = ln[1];
		var drbr = ln[2];
		var crno = ln[3];
		var crsb = ln[4];
		var crbr = ln[5];
		var dd = ln[6].split("-");
		var y = dd[0];
		var m = dd[1];
		var d = dd[2];
		var ddate = d+"/"+m+"/"+y;
		var description = ln[7];
		var refno = ln[8].substring(3);
		var amount = ln[9];
		var tax = ln[10];
		var total = ln[13];
		var ref = ln[14];
		var taxindex = ln[15];
		var drname = ln[16];
		var crname = ln[17];
		var drtext = drname+'~'+drno+'~'+drsb+'~'+drbr;
		var crtext = crname+'~'+crno+'~'+crsb+'~'+crbr;
		
		document.getElementById('newref').selectedIndex = 0;
		document.getElementById('newrefno').value = 0;
		document.getElementById('newbgacc2dr').selectedIndex = 0;
		document.getElementById('newbgacc2cr').selectedIndex = 0;
		document.getElementById('newdescription').value = '';
		document.getElementById('newamount').value = 0;
		document.getElementById('newtaxtype').selectedIndex = 0;
		document.getElementById('newtax').value = 0;
		document.getElementById('newtotal').value = 0;
		//document.getElementById('newxref').selectedIndex = 0;
		document.getElementById('newasset').selectedIndex = 0;
		if (drno == 5000 || crno == 5000) {
			document.getElementById('newasset').style.visibility = 'visible';
		} else {
			document.getElementById('newasset').style.visibility = 'hidden';
		}
	
	
		document.getElementById('DRaccount').value = drtext;
		document.getElementById('CRaccount').value = crtext;
		document.getElementById('newdate').value = ddate;
		document.getElementById('newref').selectedIndex = ref;
		document.getElementById('newrefno').value = refno;
		if (drno < 1000) {
			//iajaxGetACList('dr','GL',draccount,craccount);
			document.getElementById('newbgacc2dr').selectedIndex = 1;
		}
		if (drno > 10000000 && drno < 20000000) {
			//iajaxGetACList('dr','AS',draccount,craccount);
			document.getElementById('newbgacc2dr').selectedIndex = 4;
		}
		if (drno > 20000000 && drno < 30000000) {
			//iajaxGetACList('dr','CR',draccount,craccount);
			document.getElementById('newbgacc2dr'),selectedIndex = 3;
		}
		if (drno > 30000000) {
			//iajaxGetACList('dr','DR',draccount,craccount);
			document.getElementById('newbgacc2dr').selectedIndex = 2;
		}
		if (crno < 1000) {
			//iajaxGetACList('cr','GL',draccount,craccount);
			document.getElementById('newbgacc2cr').selectedIndex = 1;
		}
		if (crno > 10000000 && crno < 20000000) {
			//iajaxGetACList('cr','AS',draccount,craccount);
			document.getElementById('newbgacc2cr').selectedIndex = 4;
		}
		if (crno > 20000000 && crno < 30000000) {
			//iajaxGetACList('cr','CR',draccount,craccount);
			document.getElementById('newbgacc2cr').selectedIndex = 3;
		}
		if (crno > 30000000) {
			//iajaxGetACList('cr','DR',draccount,craccount);
			document.getElementById('newbgacc2cr').selectedIndex = 2;
		}
		document.getElementById('newtaxtype').selectedIndex = taxindex;
		document.getElementById('newtax').value = tax;
		document.getElementById('newamount').value = amount;
		document.getElementById('newtotal').value = total;
		document.getElementById('newdescription').value = description;
		
		document.getElementById('drsearch').style.visibility = 'visible';
		document.getElementById('crsearch').style.visibility = 'visible';
		document.getElementById('DRaccount').style.visibility = 'visible';
		document.getElementById('CRaccount').style.visibility = 'visible';
		document.getElementById('newtaxtype').style.visibility = 'visible';
		document.getElementById('newamount').style.visibility = 'visible';
		document.getElementById('newtax').style.visibility = 'visible';
		document.getElementById('newtotal').style.visibility = 'visible';
		
			
	$.get("includes/ajaxdelline.php", {tid: lineno}, function(data){$("#translist").trigger("reloadGrid")});
	
																	   
	});
	
}

function delline(lineno) {
	 if (confirm("Are you sure you want to delete this transaction line?")) {
		$.get("includes/ajaxdelline.php", {tid: lineno}, function(data){$("#translist").trigger("reloadGrid")});
		
	  }
}



function postTrans() {
	
	$.get("includes/ajaxPostTrans.php", {}, function(data){$("#translist").trigger("reloadGrid")});

}

function toFixed(val) {
	if (isNaN(val)) {
		alert('Must be a number');
	} else {
		return parseFloat(val).toFixed(2);
	}
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
//index tr_journal.php
//************************************************************************************************************


function addjournal() {

	var ddate = document.getElementById('newdate').value;
	var ref = document.getElementById('newref').value;
	var refno = document.getElementById('newrefno').value;
	var reference = ref+refno;
	var accname = document.getElementById('DRaccount').value;
	var note = document.getElementById('newdescription').value;
	var dramount = document.getElementById('newdrvalue').value;
	var cramount = document.getElementById('newcrvalue').value;
	dramount = toFixed(parseFloat(dramount));
	cramount = toFixed(parseFloat(cramount));
	var drgst = document.getElementById('newdrgst').options[document.getElementById('newdrgst').selectedIndex].value;
	
	if(accname == "") {
		alert('Please select an account');
		return(false);
	}	
	if(refno == " ") {
		alert('Please include a reference number');
		return(false);
	}	
	var dracc = accname.split("~");
		var a2dr = dracc[1];
		var s2dr = dracc[2];
		var b2dr = dracc[3];
		var n2dr = dracc[0];
		
	dramount = parseFloat(dramount);
	cramount = parseFloat(cramount);
	a2dr = parseFloat(a2dr);
	s2dr = parseFloat(s2dr);
	var bal = parseFloat(dramount) + parseFloat(cramount);	
	if(bal == 0) {
		alert('Please input an amount');
		return(false);
	}	
	

	$.get("includes/ajaxAddJournal.php", {account:n2dr, descript:note, debit:dramount, credit:cramount, accno:a2dr, subac:s2dr, brac:b2dr, ddate:ddate, reference:reference, drgst:drgst}
		  , function(data){$("#journallist").trigger("reloadGrid")});


	clear_new_line_journal();
		
	
}

function clear_new_line_journal()
{

	document.getElementById('newdrvalue').value = 0.00;
	document.getElementById('newcrvalue').value = 0.00;
	document.getElementById('newdescription').value = '';
	document.getElementById('DRaccount').value = '';
	document.getElementById('ndrgst').style.visibility = 'hidden';
	//document.getElementById('ncrgst').style.visibility = 'hidden';

}	
	
function editjournal(lineno) {
	
	//$str = $account."~".$note."~".$debit."~".$credit."~".$accno."~".$subac."~".$brac."~".$ddate."~".$reference."~".$acindex;

	$.get("includes/ajaxgetjournalline.php", {lineno: lineno}, function(data){
		var ln = data.split('~');
		
		
		var account = ln[0];
		var note = ln[1];
		var debit = ln[2];
		var credit = ln[3];
		var acno = ln[4];
		var subac = ln[5];
		var brac = ln[6];
		var ddate = ln[7];
		var refno = ln[8].substring(3);
		var acindex = ln[9];
		var actext = account+'~'+acno+'~'+subac+'~'+brac;
		
		//document.getElementById('newrefno').value = refno;
		document.getElementById('DRaccount').value = actext;
		//document.getElementById('newbgacc2dr').selectedIndex = '';
		document.getElementById('newdescription').value = '';
		document.getElementById('newdrvalue').value = 0;
		document.getElementById('newcrvalue').value = 0;
		
		var ac = Number(acno);
	
		if  ((ac < 1000) ) {
			//iajaxGetACList('dr','GL',acindex,0);
			document.getElementById('newbgacc2dr').selectedIndex = 1;
		}
		if  ((ac > 10000000 && ac < 20000000) ) {
			//iajaxGetACList('dr','AS',acindex,0);
			document.getElementById('newbgacc2dr').selectedIndex = 4;
		}
		if  ((ac > 20000000 && ac < 30000000) ) {
			//iajaxGetACList('dr','CR',acindex,0);
			document.getElementById('newbgacc2dr'),selectedIndex = 3;
		}
		if  ((ac > 30000000) ) {
			//iajaxGetACList('dr','DR',acindex,0);
			document.getElementById('newbgacc2dr').selectedIndex = 2;
		}
		if  ((ac < 1000) ) {
			//iajaxGetACList('dr','GL',acindex,0);
			document.getElementById('newbgacc2dr').selectedIndex = 1;
		}
		if  ((ac > 10000000 && ac < 20000000) ) {
			//iajaxGetACList('dr','AS',acindex,0);
			document.getElementById('newbgacc2dr').selectedIndex = 4;
		}
		if  ((ac > 20000000 && ac < 30000000) ) {
			//iajaxGetACList('dr','CR',acindex,0);
			document.getElementById('newbgacc2dr'),selectedIndex = 3;
		}
		if  ((ac > 30000000) ) {
			//iajaxGetACList('dr','DR',acindex,0);
			document.getElementById('newbgacc2dr').selectedIndex = 2;
		}
		document.getElementById('newdrvalue').value = debit;
		document.getElementById('newcrvalue').value = credit;
		document.getElementById('newdescription').value = note;
		
		
			
	$.get("includes/ajaxdeljournalline.php", {tid: lineno}, function(data){$("#journallist").trigger("reloadGrid")});
	
																	   
	});
	

}

function deljournal(lineno,e) {
	
	 if (confirm("Are you sure you want to delete this transaction line?")) {
		$.get("includes/ajaxdeljournalline.php", {tid: lineno}, function(data){$("#journallist").trigger("reloadGrid")});
		
	  }
}

function toFixed(val) {
	if (isNaN(val)) {
		alert('Must be a number');
	} else {
		return parseFloat(val).toFixed(2);
	}
}


function nextref(ref) {
	var refno = ajaxGetRef('JNL','ns');
}

function chkdrcr(drcr) {
	var dramt = document.getElementById('newdrvalue').value;
	var cramt = document.getElementById('newcrvalue').value;
	if (drcr == 'dr') {
		if (dramt > 0 && cramt > 0) {
			document.getElementById('newcrvalue').value = 0.00;
		}
	} else {
		if (cramt > 0 && dramt > 0) {
			document.getElementById('newdrvalue').value = 0.00;
		}
	}
}


function postJournal() {
	
	$.get("includes/ajaxPostJournal.php", {}, function(data){
		if (data == 'Y') {
			$.get("includes/ajaxPostTrans.php", {}, function(data){});
		} else {
			alert(data);
		}
		$("#journallist").trigger("reloadGrid")});
}

//************************************************************************************************************
//index hs_setup.php
//************************************************************************************************************

function editsetup() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('hs_editsetup.php','edsu','toolbar=0,scrollbars=1,height=470,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
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



function tb() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	var dts = $('input[name="dates"]:checked').val();
	var bdate = document.getElementById('bdate').value;
	var edate = document.getElementById('edate').value;
	var branch = outputSelected(document.getElementById('branch'));
	if ($('#consbranch').is(':checked')) {
		var brcons = 'y';
	} else {
		var brcons = 'n'
	}
	if ($('#conssubac').is(':checked')) {
		var subcons = 'y';
	} else {
		var subcons = 'n'
	}
	if ($('#zerobal').is(':checked')) {
		var zbal = 'y';
	} else {
		var zbal = 'n'
	}
	
	
	window.open('rep_tbgrid.php?dts='+dts+'&bdate='+bdate+'&edate='+edate+'&branch='+branch+'&brcons='+brcons+'&subcons='+subcons+'&zbal='+zbal,'tbg','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function mthbals() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	var endmonth = document.getElementById('endmonth').value;
	var branch = outputSelected(document.getElementById('branch'));
	if ($('#consbranch').is(':checked')) {
		var brcons = 'y';
	} else {
		var brcons = 'n'
	}
	if ($('#conssubac').is(':checked')) {
		var subcons = 'y';
	} else {
		var subcons = 'n'
	}
	
	window.open('rep_mthbalgrid.php?emonth='+endmonth+'&branch='+branch+'&brcons='+brcons+'&subcons='+subcons,'tbg','toolbar=0,scrollbars=1,height=600,width=1350,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


function displayResult(dts) {
	if (dts == 'ytd') {
		document.getElementById('bdate').style.visibility = 'hidden';
		document.getElementById('edate').style.visibility = 'hidden';
	} 
	if (dts == 'between') {
		document.getElementById('bdate').style.visibility = 'visible';
		document.getElementById('edate').style.visibility = 'visible';
	}
	if (dts == 'asat') {
		document.getElementById('edate').style.visibility = 'visible';
	}
	if (dts == 'bytd') {
		document.getElementById('edate').style.visibility = 'hidden';
	}
}

function tb2xl() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_tb2excel.php','tbxl','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function tb2pdf() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_fin2pdf.php','tbpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function draged2xl() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_draged2excel.php','dragedxl','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function draged2pdf() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_draged2pdf.php','dragedpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function viewac(acno,br,sb,fdt,edt,ob) {
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxUpdtGLAcc.php", {vac: acno, vbr: br, vsb: sb, fdt: fdt, edt: edt, ob: ob}, function(data){
	});
	jQuery.ajaxSetup({async:true});
	//viewac2();
}

function viewacr(acno,br,sb) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxUpdtGLAcc.php", {vac: acno, vbr: br, vsb: sb}, function(data){
	});
	jQuery.ajaxSetup({async:true});
}

function viewac2() {
	//var fdt = document.getElementById('bdateh').value;
	//var edt = document.getElementById('edateh').value;
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_view1gl.php','vacg','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function pl() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	var dts = $('input[name="dates"]:checked').val();
	var bdate = document.getElementById('bdate').value;
	var edate = document.getElementById('edate').value;
	var branch = outputSelected(document.getElementById('branch'));
	
	window.open('rep_plcalc.php?dts='+dts+'&bdate='+bdate+'&edate='+edate+'&branch='+branch,'tbg','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function bs() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	var dts = $('input[name="dates"]:checked').val();
	var edate = document.getElementById('edate').value;
	var branch = outputSelected(document.getElementById('branch'));
	
	window.open('rep_bscalc.php?dts='+dts+'&edate='+edate+'&branch='+branch,'tbg','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function bs2xl() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_bs2excel.php','bsxl','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function bs2pdf() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_bs2pdf.php','bspdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function pl2xl() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_pl2excel.php','plxl','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function pl2pdf() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_pl2pdf.php','plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function viewtrans(rf) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_viewtradingtrans.php?rf='+rf,'vtrad','toolbar=0,scrollbars=1,height=470,width=900,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function printdoc(rf) {
	var type = rf.substring(0,3);
	printtrading(type,rf);
}


function printpicklist(rf) {
	$.get("includes/ajaxPickList.php", {rf: rf}, function(data){});
	printtrading('PKL',rf);
}

//************************************************************************************************************
//ad_branches
//************************************************************************************************************
function addbranch() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('ad_addbranch.php','adbr','toolbar=0,scrollbars=1,height=270,width=550,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editbranch(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('ad_editbranch.php?uid='+uid,'edbr','toolbar=0,scrollbars=1,height=270,width=550,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//************************************************************************************************************
//select account
//************************************************************************************************************


function setselect(acc,ledger) {
	//var tref = document.getElementById('newref').value;
	var det1 = '';
	
	var element = document.getElementById('det1');
	if (element != null && element.value != '') {
		var det1 = document.getElementById('det1').value;
	}	
	
	var a = acc.split('~');
	var ac = a[0];
	var br = a[1];
	var sb = a[2];
	var acname = a[3];
	var blocked = a[4];
	
	if (blocked == 'Yes') {
		alert('This member is blocked. You may not post transactions to their account');
		return false;
	} else {
		var acc = acname+'~'+ac+'~'+sb+'~'+br;
		jQuery.ajaxSetup({async:false});
		$.get("../ajax/ajaxGetSelect.php", {}, function(data){
		 var drcr = data;
		 if (drcr == 'dr' && det1 == '') {
			document.getElementById('DRaccount').value = acc;
				// if trading tax account
				if (ac == 870) {
					document.getElementById('ndrgst').style.visibility = 'visible';
				}			
		 }
		 if (drcr == 'cr' && det1 == '') {
			document.getElementById('CRaccount').value = acc;
				// if trading tax account
				if (ac == 870) {
					document.getElementById('ncrgst').style.visibility = 'visible';
				}			
		 }
		 if (det1 == '') {
			document.getElementById('glselect').style.visibility = 'hidden';
			document.getElementById('drselect').style.visibility = 'hidden';
			document.getElementById('crselect').style.visibility = 'hidden';
			document.getElementById('asselect').style.visibility = 'hidden';
		 }
		 if (det1 == 'gl') {
			document.getElementById('glselect').style.visibility = 'hidden';
			document.getElementById('GLaccount').value = acc;
		 }
		 if (det1 == 'dr') {
			document.getElementById('drselect').style.visibility = 'hidden';
			document.getElementById('DRaccount').value = acc;
			$.get("../ajax/ajaxUpdtDr.php", {ac: ac, sb: sb, cname: acname}, function(data){});
		 }
		 if (det1 == 'cr') {
			document.getElementById('crselect').style.visibility = 'hidden';
			document.getElementById('CRaccount').value = acc;
			$.get("../ajax/ajaxUpdtCr.php", {ac: ac, sb: sb, cname: acname}, function(data){});
		 }
		 if (det1 == 'as') {
			document.getElementById('asselect').style.visibility = 'hidden';
			document.getElementById('ASaccount').value = acc;
		 }
		//if (tref != 'JNL') {
			//ajaxGetXref(acc,tref,drcr);
		//}
		});
		

		
		
		jQuery.ajaxSetup({async:true});
	}
}

function setselect2(acc,ledger) {
	//var tref = document.getElementById('newref').value;
	var det1 = '';
	
	var element = document.getElementById('det1');
	if (element != null && element.value != '') {
		var det1 = document.getElementById('det1').value;
	}	
	
	var a = acc.split('~');
	var ac = a[0];
	var br = a[1];
	var sb = a[2];
	var acname = a[3];
	var acc = acname+'~'+ac+'~'+sb+'~'+br;
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxGetSelect.php", {}, function(data){
	 var drcr = data;
	 if (drcr == 'dr' && det1 == '') {
	 	document.getElementById('DRaccount').value = acc;
	 }
	 if (drcr == 'cr' && det1 == '') {
	 	document.getElementById('CRaccount').value = acc;
	 }
	 if (det1 == '') {
		document.getElementById('glselect2').style.visibility = 'hidden';
		document.getElementById('drselect2').style.visibility = 'hidden';
		document.getElementById('crselect2').style.visibility = 'hidden';
		document.getElementById('asselect2').style.visibility = 'hidden';
	 }
	 if (det1 == 'gl') {
		document.getElementById('glselect2').style.visibility = 'hidden';
	 	document.getElementById('GLaccount2').value = acc;
	 }
	 if (det1 == 'dr') {
		document.getElementById('drselect2').style.visibility = 'hidden';
	 	document.getElementById('DRaccount2').value = acc;
		$.get("../ajax/ajaxUpdtDr.php", {ac: ac, sb: sb, cname: acname}, function(data){});
	 }
	 if (det1 == 'cr') {
		document.getElementById('crselect2').style.visibility = 'hidden';
	 	document.getElementById('CRaccount2').value = acc;
		$.get("../ajax/ajaxUpdtCr.php", {ac: ac, sb: sb, cname: acname}, function(data){});
	 }
	 if (det1 == 'as') {
		document.getElementById('asselect2').style.visibility = 'hidden';
	 	document.getElementById('ASaccount2').value = acc;
	 }
	//if (tref != 'JNL') {
		//ajaxGetXref(acc,tref,drcr);
	//}
	});
	jQuery.ajaxSetup({async:true});
	
}

function setselectm1(acc,ledger) {
	var a = acc.split('~');
	var ac = a[0];
	var sb = a[2];
	var acname = a[3];
	var acc = acname+'~'+ac+'~'+sb;
	jQuery.ajaxSetup({async:false});
	 if (ledger == 'dr') {
		document.getElementById('drselect').style.visibility = 'hidden';
	 	document.getElementById('DRaccount').value = acc;
		$.get("../ajax/ajaxUpdtDRAccm.php", {acname: acname, ac: ac, sb: sb, onetwo: 1}, function(data){});
	 }
	 if (ledger == 'cr') {
		document.getElementById('crselect').style.visibility = 'hidden';
	 	document.getElementById('CRaccount').value = acc;
		$.get("../ajax/ajaxUpdtCRAccm.php", {acname: acname, ac: ac, sb: sb, onetwo: 1}, function(data){});
	 }
	jQuery.ajaxSetup({async:true});
}

function setselectm2(acc,ledger) {
	var a = acc.split('~');
	var ac2 = a[0];
	var sb2 = a[2];
	var acname2 = a[3];
	var acc = acname2+'~'+ac2+'~'+sb2;
	jQuery.ajaxSetup({async:false});
	 if (ledger == 'dr') {
		document.getElementById('drselect2').style.visibility = 'hidden';
	 	document.getElementById('DRaccount2').value = acc;
		$.get("../ajax/ajaxUpdtDRAccm.php", {acname2: acname2, ac2: ac2, sb2: sb2, onetwo: 2}, function(data){});
	 }
	 if (ledger == 'cr') {
		document.getElementById('crselect2').style.visibility = 'hidden';
	 	document.getElementById('CRaccount2').value = acc;
		$.get("../ajax/ajaxUpdtCRAccm.php", {acname2: acname2, ac2: ac2, sb2: sb2, onetwo: 2}, function(data){});
	 }
	jQuery.ajaxSetup({async:true});
}

function sboxvisibledr() {
	var ledger = document.getElementById('newbgacc2dr').value;
	if (ledger == 'GL') {
		document.getElementById('glselect').style.visibility = 'visible';											
		document.getElementById('searchgl').value = "";
		document.getElementById('searchgl').focus();
	}
	if (ledger == 'DR') {
		document.getElementById('drselect').style.visibility = 'visible';											
		document.getElementById('searchdr').value = "";
		document.getElementById('searchdr').focus();
	}
	if (ledger == 'CR') {
		document.getElementById('crselect').style.visibility = 'visible';											
		document.getElementById('searchcr').value = "";
		document.getElementById('searchcr').focus();
	}
	if (ledger == 'AS') {
		document.getElementById('asselect').style.visibility = 'visible';											
		document.getElementById('searchas').value = "";
		document.getElementById('searchas').focus();
	}
}

function sboxvisiblecr() {
	var ledger = document.getElementById('newbgacc2cr').value;
	if (ledger == 'GL') {
		document.getElementById('glselect').style.visibility = 'visible';											
		document.getElementById('searchgl').value = "";
		document.getElementById('searchgl').focus();
	}
	if (ledger == 'DR') {
		document.getElementById('drselect').style.visibility = 'visible';											
		document.getElementById('searchdr').value = "";
		document.getElementById('searchdr').focus();
	}
	if (ledger == 'CR') {
		document.getElementById('crselect').style.visibility = 'visible';											
		document.getElementById('searchcr').value = "";
		document.getElementById('searchcr').focus();
	}
	if (ledger == 'AS') {
		document.getElementById('asselect').style.visibility = 'visible';											
		document.getElementById('searchas').value = "";
		document.getElementById('searchas').focus();
	}
	
}


function sboxhidegl() {
	document.getElementById('glselect').style.visibility = 'hidden';											
}

function sboxhideglns() {
	document.getElementById('glselect').style.visibility = 'hidden';											
}

function sboxhidegl2() {
	document.getElementById('glselect2').style.visibility = 'hidden';											
}

function sboxhidedr() {
	document.getElementById('drselect').style.visibility = 'hidden';											
}

function sboxhidedrns() {
	document.getElementById('drselect').style.visibility = 'hidden';											
}

function sboxhidecrns() {
	document.getElementById('crselect').style.visibility = 'hidden';											
}

function sboxhidedr2() {
	document.getElementById('drselect2').style.visibility = 'hidden';											
}

function sboxhidecr() {
	document.getElementById('crselect').style.visibility = 'hidden';											
}

function sboxhidecr2() {
	document.getElementById('crselect2').style.visibility = 'hidden';											
}

function sboxhideas() {
	document.getElementById('asselect').style.visibility = 'hidden';											
}

function gridReload1gl(){ 
	var gl_mask = jQuery("#searchgl").val(); 
	gl_mask = gl_mask.toUpperCase();
	jQuery("#selectgllist").setGridParam({url:"selectgl.php?gl_mask="+gl_mask}).trigger("reloadGrid"); 
} 

function gridReload1gl2(){ 
	var gl_mask = jQuery("#searchgl2").val(); 
	gl_mask = gl_mask.toUpperCase();
	jQuery("#selectgllist2").setGridParam({url:"selectgl2.php?gl_mask="+gl_mask}).trigger("reloadGrid"); 
} 

function doSearchgl(){ 
		var timeoutHnd = setTimeout(gridReload1gl,500); 
	} 

function doSearchgl2(){ 
		var timeoutHnd = setTimeout(gridReload1gl2,500); 
	} 

function gridReload1dr(){ 
	var dr_mask = jQuery("#searchdr").val(); 
	dr_mask = dr_mask.toUpperCase();
	jQuery("#selectdrlist").setGridParam({url:"selectdr.php?dr_mask="+dr_mask}).trigger("reloadGrid"); 
} 

function gridReload1dr2(){ 
	var dr_mask = jQuery("#searchdr2").val(); 
	dr_mask = dr_mask.toUpperCase();
	jQuery("#selectdrlist2").setGridParam({url:"selectdr2.php?dr_mask="+dr_mask}).trigger("reloadGrid"); 
} 

function doSearchdr(){ 
		var timeoutHnd = setTimeout(gridReload1dr,500); 
	} 

function doSearchdr2(){ 
		var timeoutHnd = setTimeout(gridReload1dr2,500); 
	} 

function gridReload1cr(){ 
	var cr_mask = jQuery("#searchcr").val(); 
	cr_mask = cr_mask.toUpperCase();
	jQuery("#selectcrlist").setGridParam({url:"selectcr.php?cr_mask="+cr_mask}).trigger("reloadGrid"); 
} 

function gridReload1cr2(){ 
	var cr_mask = jQuery("#searchcr2").val(); 
	cr_mask = cr_mask.toUpperCase();
	jQuery("#selectcrlist2").setGridParam({url:"selectcr2.php?cr_mask="+cr_mask}).trigger("reloadGrid"); 
} 

function doSearchcr(){ 
		var timeoutHnd = setTimeout(gridReload1cr,500); 
	} 

function doSearchcr2(){ 
		var timeoutHnd = setTimeout(gridReload1cr2,500); 
	} 

function gridReload1as(){ 
	var as_mask = jQuery("#searchas").val(); 
	as_mask = as_mask.toUpperCase();
	jQuery("#selectaslist").setGridParam({url:"selectas.php?as_mask="+as_mask}).trigger("reloadGrid"); 
} 

function doSearchas(){ 
		var timeoutHnd = setTimeout(gridReload1as,500); 
	} 

function gridReload1cos(){ 
	var dr_mask = jQuery("#searchdr").val(); 
	dr_mask = dr_mask.toUpperCase();
	jQuery("#selectcoslist").setGridParam({url:"selectcos.php?dr_mask="+dr_mask}).trigger("reloadGrid"); 
} 

function doSearchcos(){ 
		var timeoutHnd = setTimeout(gridReload1cos,500); 
	} 
	
function doSearchtrddr(){ 
		var timeoutHnd = setTimeout(gridReload1trddr,500); 
}

function gridReload1trddr(){ 
	var dr_mask = jQuery("#searchtrddr").val(); 
	dr_mask = dr_mask.toUpperCase();
	jQuery("#selecttrddrlist").setGridParam({url:"selecttrddr.php?dr_mask="+dr_mask}).trigger("reloadGrid"); 
} 

function doSearchtrdcr(){ 
		var timeoutHnd = setTimeout(gridReload1trdcr,500); 
}

function gridReload1trdcr(){ 
	var cr_mask = jQuery("#searchtrdcr").val(); 
	cr_mask = cr_mask.toUpperCase();
	jQuery("#selecttrdcrlist").setGridParam({url:"selecttrdcr.php?cr_mask="+cr_mask}).trigger("reloadGrid"); 
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
		
		window.open('st_additem.php','addit','toolbar=0,scrollbars=1,height=270,width=1190,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addsrvitem() {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('st_addservice.php','addsv','toolbar=0,scrollbars=1,height=470,width=1020,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editstk(uid) {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('st_edititem.php?uid='+uid,'addit','toolbar=0,scrollbars=1,height=270,width=1190,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editsrv(uid) {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('st_editservice.php?uid='+uid,'edsrv','toolbar=0,scrollbars=1,height=470,width=1020,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addpcent() {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('st_addpcent.php','addpcent','toolbar=0,scrollbars=1,height=200,width=600,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editpcent(uid) {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('st_editpcent.php?uid='+uid,'edpcent','toolbar=0,scrollbars=1,height=200,width=600,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function delpcent(uid) {
	 if (confirm("Are you sure you want to delete this percentage markup?")) {
		$.get("includes/ajaxdelpcent.php", {tid: uid}, function(data){$("#stkpcentlist").trigger("reloadGrid")});
		
	  }
}

function addloc() {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('st_addloc.php','addloc','toolbar=0,scrollbars=1,height=200,width=600,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editloc(uid) {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
		window.open('st_editloc.php?uid='+uid,'edloc','toolbar=0,scrollbars=1,height=200,width=600,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


//************************************************************************************************************
//trading transactions
//************************************************************************************************************

function CheckAvailable(q) {
	var is = document.getElementById('stkitem').value;
	var locid = document.getElementById('loc').value;
	var i = is.split('~');
	var stkid = i[0];

	if (q != 0) {
		$.get("includes/ajaxCheckAvailable.php", {q:q, stkid:stkid, locid:locid}, function(data){
				if (data == '') {
					return true;
				} else {
					alert(data);
					document.getElementById('qty').value = 0;
					document.getElementById('qty').focus();
					return false;
				}
		});
	}
}

function transvisiblecr() {
		var source = document.getElementById('trading').value;
		$.get("../ajax/ajaxUpdtSource.php", {source: source}, function(data){
		});
		document.getElementById('crselect').style.visibility = 'visible';											
		document.getElementById('searchtrdcr').value = "";
		document.getElementById('searchtrdcr').focus();
}

function transvisibledr() {
		var source = document.getElementById('trading').value;
		$.get("../ajax/ajaxUpdtSource.php", {source: source}, function(data){
		});
		document.getElementById('drselect').style.visibility = 'visible';											
		document.getElementById('searchtrddr').value = "";
		document.getElementById('searchtrddr').focus();
}

function transvisiblerec() {
		document.getElementById('drselect').style.visibility = 'visible';											
		document.getElementById('searchdr').value = "";
		document.getElementById('searchdr').focus();
}


function nexttrdref(ref, add) {
	jQuery.ajaxSetup({async:false});
	
	var dt = document.getElementById('newdate').value;
	$.get("includes/ajaxCheckTransDate.php", {dt: dt}, function(data){
			if (data == '') {
				stopProcess = 'N';
				return true;
			} else {
				alert(data);
				document.getElementById('newdate').focus();
				stopProcess = 'Y';
				return false;
			}
	});	
	
	var rno = document.getElementById('refno').value;
	if (rno == 0) {
		$.get("includes/ajaxGetTrdRef.php", {ref: ref, add: add}, function(data){
				document.getElementById('refno').value = data;
		});
	}
	
	jQuery.ajaxSetup({async:true});
	
}

function blanklist(ad) {
	if (ad == 'post') {
		// remove all entries from postal address list
		var x=document.getElementById("postadd");
		var listlength = document.getElementById("postadd").length;
		for (var i = 0; i < listlength; i ++) {
			x.remove(x[i]);
		}
	} else {
		// remove all entries from delivery address list
		var x=document.getElementById("deliveradd");
		var listlength = document.getElementById("deliveradd").length;
		for (var i = 0; i < listlength; i ++) {
			x.remove(x[i]);
		}
	}
}

var memberid;
var priceband;

function settrdselect(acc,ledger) {
	var trdtype = document.getElementById('trading').value;
	tradingtype = trdtype;
	var a = acc.split('~');
	var ac = a[0];
	var br = a[1];
	var sb = a[2];
	var acname = a[3];
	var cid = a[4];
	priceband = a[5];
	var prefname = a[6];
	var blocked = a[7];
	memberid = cid;
	
	if (blocked == 'Yes') {
		alert('This member is blocked. You may not post transactions to their account');
		return false;
	} else {
		
		if (prefname != '') {
			acname = prefname;
		}
		
		var acc = acname+'~'+ac+'~'+sb+'~'+br;
		document.getElementById('clientid').value = cid;
		document.getElementById('priceband').value = priceband;
		if (ledger == 'dr') {
			document.getElementById('TRaccount').value = acc;
			document.getElementById('drselect').style.visibility = 'hidden';
			document.getElementById('TRaccount').style.visibility = 'visible';
		}
		if (ledger == 'cr') {
			document.getElementById('TRaccount').value = acc;
			document.getElementById('crselect').style.visibility = 'hidden';
			document.getElementById('TRaccount').style.visibility = 'visible';
		}	
	
		//********************************************************
		// populate billing and delivery addresses
		//*********************************************************
		
		if (trdtype == 'inv') {
			blanklist('post');
			$.get("includes/ajaxGetPostAdd.php", {cid:cid}, function(data){
					$("#postadd").append(data);
			});
			blanklist('delivery');
			$.get("includes/ajaxGetDeliveryAdd.php", {cid:cid}, function(data){
					$("#deliveradd").append(data);
			});
		}
	}
}				

function setcosselect(acc) {
	var trdtype = document.getElementById('trading').value;
	tradingtype = trdtype;
	var a = acc.split('~');
	var ac = a[0];
	var br = a[1];
	var sb = a[2];
	var acname = a[3];
	var acc = acname+'~'+ac+'~'+sb+'~'+br;
	document.getElementById('reqbranch').value = br;
	document.getElementById('TRaccount').value = acc;
	document.getElementById('drselect').style.visibility = 'hidden';
	document.getElementById('TRaccount').style.visibility = 'visible';
}

function setstkselect(stk) {
	jQuery.ajaxSetup({async:false});
	
	var fxrate = 1;
	
	var a = stk.split('~');
	var scode = a[0];
	var sname = a[1];
	var sunit = a[2];
	var stax = a[3];
	var sac = a[4];
	var sbr = a[5];
	var ssb = a[6];
	var pac = a[7];
	var pbr = a[8];
	var psb = a[9];
	var grp = a[10];
	var cat = a[11];
	var cost = a[12];
	var setsell = a[13];
	var trackserial = a[14];
	var staxpcent = a[15];
	var stock = a[16];
	var s = scode+'~'+sname;
	var trading = document.getElementById('trading').value;
	//var gstinvpay = document.getElementById('gstinvpay').value;
	
	document.getElementById('stkitem').value = s;
	document.getElementById('sacc').value = sac+'~'+sbr+'~'+ssb;
	document.getElementById('pacc').value = pac+'~'+pbr+'~'+psb;
	document.getElementById('grp').value = grp;
	document.getElementById('cat').value = cat;
	document.getElementById('trackserial').value = trackserial;
	document.getElementById('stock').value = stock;
	document.getElementById('avcost').value = cost;
	
	
	if (trading != 'req' && trading != 'p_o' && trading != 'c_s') {
		//if (gstinvpay == 'Invoice') {
			document.getElementById('tax').selectedIndex = stax;
		//}
		document.getElementById('unit').value = sunit;
		cost = cost * fxrate;
		cost = cost.toFixed(2);
		document.getElementById('setsell').value = cost;
		/*
		var fx = document.getElementById('currency').value;
		var f = fx.split('~');
		var fxcode = f[0];
		var fxrate = f[1];
		*/
		var fxrate = 1;
	}
	
	if (trading == 'p_o') {
		document.getElementById('unit').value = sunit;
	}
	
	if (trading == 'inv') {
		if (setsell > 0 ) {
				setsell = setsell * fxrate;
				setsell = setsell.toFixed(2);
				document.getElementById('price').value = setsell;
		} else {
			$.get("includes/ajaxgetpricepcent.php", {priceband: priceband}, function(data){
				var band = data;
				var bnd = band.split('~');
				var addpcent = parseFloat(bnd[0]);
				var setprice = parseFloat(bnd[1]);
				if (setprice > 0) {
					setprice = setprice * fxrate;
					setprice = setprice.toFixed(2);
					document.getElementById('price').value = setprice;
				} else {
					var sellat = cost * (1 + addpcent/100) * fxrate;
					sellat = sellat.toFixed(2);
					document.getElementById('price').value = sellat;
				}
			});
		}
	}

	if (trading == 'c_s') {
		if (setsell > 0 ) {
				document.getElementById('price').value = setsell;
		} else {
			$.get("includes/ajaxgetcspcent.php", {}, function(data){
				var addpcent = data;	
				var sellat = cost * (1 + addpcent/100) * fxrate;
				sellat = sellat.toFixed(2);
				document.getElementById('price').value = sellat;
			});
		}
	}
	
	if (trading == 'req') {
		document.getElementById('unit').value = sunit;
		document.getElementById('setsell').value = cost;
		document.getElementById('price').value = cost;
	}
	
	calcPriceForex();

	document.getElementById('stkselect').style.visibility = 'hidden';
	document.getElementById('stkitem').style.visibility = 'visible';
	if (trading != 'req' && trading != 'p_o') {
		document.getElementById('price').focus();
	}
	jQuery.ajaxSetup({async:true});
	
	
}

function sboxhidestk() {
	document.getElementById('stkselect').style.visibility = 'hidden';											
}

function stkvisible() {
		document.getElementById('stkselect').style.visibility = 'visible';											
		document.getElementById('searchstk').value = "";
		document.getElementById('searchstk').focus();
}

function gridReload1stk(){ 
	var cr_mask = jQuery("#searchstk").val(); 
	cr_mask = cr_mask.toUpperCase();
	jQuery("#selectstklist").setGridParam({url:"selectstk.php?cr_mask="+cr_mask}).trigger("reloadGrid"); 
} 

function doSearchstk(){ 
		var timeoutHnd = setTimeout(gridReload1stk,500); 
	} 

function sequentialserial() {
	var snos = document.getElementById('serialnos').value;
	if (snos == '') {
		alert('Please enter first serial number');
		return false;
	}
	var first = parseFloat(document.getElementById('serialnos').value);
	var q = parseFloat(document.getElementById('qty').value) - 1;
	for (var i=0;i<q;i++) {
		first = first + 1;
		snos = snos + ',' + first;
	}
	document.getElementById('sequential').disabled = 'disable';
	document.getElementById('serialnos').value = snos;
}


function checkserial(trd) {

	if (trd == 'p_o') {
		var qt = document.getElementById('qty').value;
		if (qt == 0) {
			return false;
		} else {
			addp_o();
		}
	} else {
		var qt = document.getElementById('qty').value;
		if (qt == 0) {
			alert('Please enter a quantity');
			return false;
		} else {
			var loc = document.getElementById('loc').value;
			if (loc == 0) {
				alert('Please select a location');
				document.getElementById('loc').focus();
				return false;
			}
			document.getElementById('noselected').value = 0;
			var trackserial = document.getElementById('trackserial').value;
			if (trackserial == 'Yes') {
				if (trd == 'grn' || trd == 'c_p') {
					document.getElementById('serialnos').focus();
					document.getElementById('tserial').style.visibility = 'visible';
				} else {
					var itc = jQuery("#stkitem").val(); 
					var i = itc.split('~');
					var itemcode = i[0];
					$.get("../ajax/ajaxUpdtItemcode.php", {itemcode: itemcode}, function(data){});
					jQuery("#selectseriallist").setGridParam({url:"selectserials.php"}).trigger("reloadGrid"); 
					document.getElementById('sellserial').style.visibility = 'visible';
				}
			} else {
				addtrdtrans();
			}
		}
	}
}

function addtrdtrans() {
	
	var tradingtype = document.getElementById('trading').value;
	// forex
	switch (tradingtype) {
		case 'inv':
			var fx = document.getElementById('currency').value;
			var f = fx.split('~');
			var fxcode = f[0];
			var fxrate = f[1];
			var fxamt = document.getElementById('fxamt').value;
			document.getElementById('scurrency').value = fx;
			break
		case 'grn':
			var fx = document.getElementById('currency').value;
			var f = fx.split('~');
			var fxcode = f[0];
			var fxrate = f[1];
			var fxamt = document.getElementById('fxamt').value;
			document.getElementById('scurrency').value = fx;
			break
			
			
			
			
		default:
			var fxcode = ' ';
			var fxrate = 1;
			var fxamt = document.getElementById('price').value;
			break
	}
	
	var is = document.getElementById('stkitem').value;
	var i = is.split('~');
	var stkid = i[0];
	var stkitem = i[1];
	var avcost = i[12];
	var sa = document.getElementById('sacc').value;
	var s = sa.split('~');
	var sac = s[0];
	var sbr = s[1];
	var ssb = s[2];
	var sa = document.getElementById('pacc').value;
	var p = sa.split('~');
	var pac = p[0];
	var pbr = p[1];
	var psb = p[2];
	var stock = document.getElementById('stock').value;
	var avcost = document.getElementById('avcost').value;
	if (tradingtype == 'req') {
		var stkprice = document.getElementById('price').value;
		var stkunit = document.getElementById('unit').value;
		var stkqty = document.getElementById('qty').value;
		var tpcent = 0;
		var taxtype = "";
		var taxindex = 	0;
		var grp = document.getElementById('grp').value;
		var cat = document.getElementById('cat').value;
		var priceband = 0;	
		var discount = 0;
		var disctype = "";
		var setsell = document.getElementById('setsell').value;
		pbr = document.getElementById('reqbranch').value;
	} else {
		var stkprice = document.getElementById('price').value;
		var stkunit = document.getElementById('unit').value;
		var stkqty = document.getElementById('qty').value;
		var gstnt = document.getElementById('gstnt').value;
		if (gstnt == 'N_T~0') {
			var tpcent = 0;
			var taxtype = "";
			var taxindex = 	0;
		} else {
			var tx = document.getElementById('tax').value;
			if (tx == '') {
				var tpcent = 0;
				var taxtype = "";
				var taxindex = 	0;
			} else {
				var t = tx.split('#');
				var tpcent = t[0];
				var taxtype = t[1];
				var taxindex = 	document.getElementById('tax').selectedIndex;
			}
		}
		var grp = document.getElementById('grp').value;
		var cat = document.getElementById('cat').value;
		var priceband = document.getElementById('priceband').value;	
		var discount = document.getElementById('disc').value;
		var disctype = document.getElementById('disctype').value;
		var setsell = document.getElementById('setsell').value;
	}
	
	if (tradingtype != 'c_s' && tradingtype != 'c_p') {
		var acct = document.getElementById('TRaccount').value;
		if (acct == '') {
			alert('Please select an account');
			document.getElementById('TRaccount').focus();
			return false;
		}
	}
					 
	var loc = document.getElementById('loc').value;
	if (loc == 0) {
		alert('Please select a location');
		document.getElementById('loc').focus();
		return false;
	}
	
	var sitem = document.getElementById('stkitem').value;
	if (sitem == '') {
		alert('Please select a stock item');
		document.getElementById('stkitem').focus();
		return false;
	}
/*	
	var refno = document.getElementById('refno').value;
	if (refno == 0) {
		alert('Please select a reference number');
		document.getElementById('refno').focus();
		return false;
	}
*/	
	var qt = document.getElementById('qty').value;
	if (qt == 0) {
		alert('Please select a quantity');
		document.getElementById('qty').focus();
		return false;
	}
	
	if (tradingtype == 'inv' || tradingtype == 'c_s' || tradingtype == 'req') {
		CheckAvailable(qt);
	}
	
	if(document.getElementById('currency')) {	
	  document.getElementById('currency').disabled = 'disable';
	}

	$.get("includes/ajaxAddTrdTrans.php", {stkid:stkid,stkitem:stkitem,stkprice:stkprice,stkunit:stkunit,stkqty:stkqty,tpcent:tpcent,taxindex:taxindex,taxtype:taxtype,sac:sac,sbr:sbr,ssb:ssb,pac:pac,pbr:pbr,psb:psb,grp:grp,cat:cat,priceband:priceband,discount:discount,disctype:disctype,setsell:setsell,loc:loc,stock:stock,avcost:avcost,fxcode:fxcode,fxrate:fxrate,fxamt:fxamt}, function(data){$("#tradlist").trigger("reloadGrid")});

	document.getElementById('stkitem').value = '';
	document.getElementById('price').value = 0;
	document.getElementById('unit').value = '';
	document.getElementById('qty').value = 0;
	if (tradingtype != 'req') {
		document.getElementById('tax').selectedIndex = 0;
		document.getElementById('disc').value = 0;
	}
	document.getElementById('sacc').value = '';
	document.getElementById('pacc').value = '';
	document.getElementById('grp').value = 0;
	document.getElementById('cat').value = 0;
	document.getElementById('setsell').value = 0;
	document.getElementById('trackserial').value = 'No';
	if(document.getElementById('fxamt')) {	
		document.getElementById('fxamt').value = '';
	}
}

function addp_o() {
	var tradingtype = document.getElementById('trading').value;
	// forex
	switch (tradingtype) {
		case 'inv':
			var fx = document.getElementById('currency').value;
			var f = fx.split('~');
			var fxcode = f[0];
			var fxrate = f[1];
			var fxamt = 0;
			break
			
		default:
			var fxcode = '';
			var fxrate = 1;
			var fxamt = 0;
			break
	}	
	var is = document.getElementById('stkitem').value;
	var i = is.split('~');
	var stkid = i[0];
	var stkitem = i[1];
	var avcost = i[12];
	var sa = document.getElementById('sacc').value;
	var s = sa.split('~');
	var sac = s[0];
	var sbr = s[1];
	var ssb = s[2];
	var sa = document.getElementById('pacc').value;
	var p = sa.split('~');
	var pac = p[0];
	var pbr = p[1];
	var psb = p[2];
	var stock = document.getElementById('stock').value;
	var avcost = 0;
	var stkprice = 0;
	var stkunit = document.getElementById('unit').value;
	var stkqty = document.getElementById('qty').value;
	var tpcent = 0;
	var taxtype = "";
	var taxindex = 	0;
	var grp = document.getElementById('grp').value;
	var cat = document.getElementById('cat').value;
	var priceband = 1;	
	var discount = 0;
	var disctype = "";
	var setsell = document.getElementById('setsell').value;
	
	var acct = document.getElementById('TRaccount').value;
	if (acct == '') {
		alert('Please select an account');
		document.getElementById('TRaccount').focus();
		return false;
	}
					 
	var loc = 1;
	
	var sitem = document.getElementById('stkitem').value;
	if (sitem == '') {
		alert('Please select a stock item');
		document.getElementById('stkitem').focus();
		return false;
	}
	
	var refno = document.getElementById('refno').value;
	if (refno == 0) {
		alert('Please select a reference number');
		document.getElementById('refno').focus();
		return false;
	}
	
	var qt = document.getElementById('qty').value;
	if (qt == 0) {
		alert('Please select a quantity');
		document.getElementById('qty').focus();
		return false;
	}
	
	$.get("includes/ajaxAddTrdTrans.php", {stkid:stkid,stkitem:stkitem,stkprice:stkprice,stkunit:stkunit,stkqty:stkqty,tpcent:tpcent,taxindex:taxindex,taxtype:taxtype,sac:sac,sbr:sbr,ssb:ssb,pac:pac,pbr:pbr,psb:psb,grp:grp,cat:cat,priceband:priceband,discount:discount,disctype:disctype,setsell:setsell,loc:loc,stock:stock,avcost:avcost,fxcode:fxcode,fxrate:fxrate,fxamt:fxamt}, function(data){$("#trdpolist").trigger("reloadGrid")});

	document.getElementById('stkitem').value = '';
	document.getElementById('unit').value = '';
	document.getElementById('qty').value = 0;
	document.getElementById('sacc').value = '';
	document.getElementById('pacc').value = '';
	document.getElementById('grp').value = 0;
	document.getElementById('cat').value = 0;
	document.getElementById('priceband').value = 0;
	document.getElementById('setsell').value = 0;

}

function editlineitem(uid) {
	//$str = $itemcode."~".$item."~".$price."~".$quantity."~".$tax."~".$value."~".$taxindex."~".$sellacc."~".$sellbr."~".$sellsub."~".$purchacc."~".$purchbr."~".$purchsub."~".$groupid."~".$catid.'~'.$unit.'~'.$discamount.'~'.$disctype;;

	$.get("includes/ajaxgettrdline.php", {lineno: uid}, function(data){
		var ln = data.split('~');
		var itemcode = ln[0];
		var stkitem = ln[1];
		var price = ln[2];
		var qty = ln[3];
		var taxindex = ln[6];
		var sac = ln[7];
		var sbr = ln[8];
		var ssb = ln[9];
		var pac = ln[10];
		var pbr = ln[11];
		var psb = ln[12];
		var grp = ln[13];
		var cat = ln[14];
		var stkunit = ln[15];
		var disc = ln[16];
		var disctype = ln[17];
		var tradingtype = document.getElementById('trading').value;
		
		$.get("includes/ajaxserialdeselect.php", {id: '*'}, function(data){$("#selectseriallist").trigger("reloadGrid")});
		
		document.getElementById('stkitem').value = itemcode+'~'+stkitem;
		document.getElementById('price').value = price;
		document.getElementById('unit').value = stkunit;
		document.getElementById('qty').value = qty;
		if (tradingtype != 'req' && tradingtype != 'c_p') {
			document.getElementById('tax').selectedIndex = taxindex;
			if (document.getElementById('disctype').value != "") {
				document.getElementById('disctype').options[document.getElementById('disctype').selectedIndex].value = disctype;
			}
			document.getElementById('disc').value = disc;
		}
		document.getElementById('sacc').value = sac+'~'+sbr+'~'+ssb;
		document.getElementById('pacc').value = pac+'~'+pbr+'~'+psb;
		document.getElementById('grp').value = grp;
		document.getElementById('cat').value = cat;
		
		$.get("includes/ajaxdeltrdline.php", {tid: uid}, function(data){$("#tradlist").trigger("reloadGrid")});
		
	});
	
}

function editp_oitem(uid) {

	$.get("includes/ajaxgettrdline.php", {lineno: uid}, function(data){
		var ln = data.split('~');
		var itemcode = ln[0];
		var stkitem = ln[1];
		var price = ln[2];
		var qty = ln[3];
		var taxindex = ln[6];
		var sac = ln[7];
		var sbr = ln[8];
		var ssb = ln[9];
		var pac = ln[10];
		var pbr = ln[11];
		var psb = ln[12];
		var grp = ln[13];
		var cat = ln[14];
		var stkunit = ln[15];
		var disc = ln[16];
		var disctype = ln[17];
		var tradingtype = document.getElementById('trading').value;
		
		document.getElementById('stkitem').value = itemcode+'~'+stkitem;
		document.getElementById('unit').value = stkunit;
		document.getElementById('qty').value = qty;
		document.getElementById('sacc').value = sac+'~'+sbr+'~'+ssb;
		document.getElementById('pacc').value = pac+'~'+pbr+'~'+psb;
		document.getElementById('grp').value = grp;
		document.getElementById('cat').value = cat;
		
		$.get("includes/ajaxdeltrdline.php", {tid: uid}, function(data){$("#trdpolist").trigger("reloadGrid")});
		
	});
	
}


function dellineitem(uid) {
	$.get("includes/ajaxdeltrdline.php", {tid: uid}, function(data){$("#tradlist").trigger("reloadGrid")});
}

function delp_oitem(uid) {
	$.get("includes/ajaxdeltrdline.php", {tid: uid}, function(data){$("#trdpolist").trigger("reloadGrid")});
}

var tradingref;

function getpaymethod() {
	var tp = document.getElementById('ref').value;
	if (tp == 'C_P' || tp == 'C_S') {
		document.getElementById('paymethod').style.visibility = 'visible';
	} else if (tp != 'RET') {
		var pref = document.getElementById('purchreference').value;
		jQuery.ajaxSetup({async:false});
		$.get("includes/ajaxcheck4trans.php", {purchref: pref}, function(data){
		if (data == 'N') {
			alert('Please enter transactions first');
		} else {
			document.getElementById('paymethod').style.visibility = 'visible';
		}
		});
		jQuery.ajaxSetup({async:true});	
	} else {
		jQuery("#purchlist").trigger("reloadGrid");
		postret();
	}
}

function hidechange() {
	document.getElementById('showchange').style.visibility = 'hidden';
	postTrdTrans('C_S');
}

function calcchange() {
	var required = document.getElementById('topay').value;
	var tendered = document.getElementById('tendered').value;
	var nt = parseFloat(tendered - required);
	ntr = Math.round(nt*100)/100;
	ntf = ntr.toFixed(2);
	var chg = ntf;
	document.getElementById('change').value = chg;
	
}

function postflow(typ) {
	if (typ == 'c_s') {
		// check a payment method has been selected
		var ok = 'N';
		 var radios = document.getElementsByName("paymethod");
	
		 for (var i = 0, len = radios.length; i < len; i++) {
			  if (radios[i].checked) {
				  ok = 'Y';
			  }
		 }
		
		if (ok == 'Y') {
			if (document.getElementById('csh').checked) {
				$.get("includes/ajaxGetTrdTotal.php", function(data){
				var required = data;
				document.getElementById('topay').value = required;
				document.getElementById('required').value = required;											   
				})
				document.getElementById('showchange').style.visibility = 'visible';
			} else {
				document.getElementById('showchange').style.visibility = 'hidden';
				postTrdTrans('C_S');
			}
		} else {
			alert('Please select one payment method');
			return false
		}
	}
	if (typ == 'c_p') {
		if (document.getElementById('chq').checked) {
			$.get("includes/ajaxIncChq.php", function(data){})
		}
		postTrdTrans('C_P');
	}
	if (typ == 'ret') {
		postret();
	}
}

function postTrdTrans(type) {
	$.get("includes/ajaxRecords2Post.php", {}, function(data){
		if (data == '') {
			postTrdTrans2(type);
		} else {
			alert(data);
			return false;
		}
	});
}

function postTrdTrans2(type) {
	jQuery.ajaxSetup({async:false});
	
	var ddate = document.getElementById('newdate').value;
	var descript = document.getElementById('description').value;
	var ref = '';
	$.get("includes/ajaxGetTrdRef.php", {ref: type, add: 'Y'}, function(data){
			var refno = data;	
			ref = type+refno;
	});
	//var ref = type+document.getElementById('refno').value;
	if (document.getElementById('yourref')) {
		var yourref = document.getElementById('yourref').value;
	} else {
		var yourref = "";
	}
	var trading = document.getElementById('trading').value;
	tradingref = ref;
	
	if (document.getElementById('lstaff') == null) {
		var staffember = '';
	} else {
		var staffmember = document.getElementById('lstaff').value;
	}
	
	switch(trading) {
		case 'c_s':
			var clt = "Cash Sale";
			if (document.getElementById('eft').checked) {
				var paymethod = 'eft';
				var acc = document.getElementById('eftac').value;
				var asb = document.getElementById('eftsb').value;
			} else if (document.getElementById('crd').checked) {
				var paymethod = 'crd';
				var acc = document.getElementById('crdac').value;
				var asb = document.getElementById('crdsb').value;
			} else if (document.getElementById('csh').checked) {
				var paymethod = 'csh';
				var acc = document.getElementById('cshac').value;
				var asb = document.getElementById('cshsb').value;
			} else if (document.getElementById('chq').checked) {
				var paymethod = 'chq';
				var acc = document.getElementById('chqac').value;
				var asb = document.getElementById('chqsb').value;
			}
			break;
		case 'c_p':
			var clt = "Cash Purchase";
			if (document.getElementById('eft').checked) {
				var paymethod = 'eft';
				var acc = document.getElementById('eftac').value;
				var asb = document.getElementById('eftsb').value;
			} else if (document.getElementById('crd').checked) {
				var paymethod = 'crd';
				var acc = document.getElementById('crdac').value;
				var asb = document.getElementById('crdsb').value;
			} else if (document.getElementById('csh').checked) {
				var paymethod = 'csh';
				var acc = document.getElementById('cshac').value;
				var asb = document.getElementById('cshsb').value;
			} else if (document.getElementById('chq').checked) {
				var paymethod = 'chq';
				var acc = document.getElementById('chqac').value;
				var asb = document.getElementById('chqsb').value;
			}
			break;
			default:
			var a = document.getElementById('TRaccount').value;
			var as = a.split('~');
			var clt = as[0];
			var acc = as[1];
			var asb = as[2];
			var paymethod = "";
	}
	
	if (trading == 'crn') {
		var loc = 1;
	} else {
		var loc = document.getElementById('loc').value;
	}
	if (trading == 'inv') {
		var postaladdress = document.getElementById('postadd').value;
		var deliveryaddress = document.getElementById('deliveradd').value;
	} else {
		var postaladdress = "";
		var deliveryaddress = "";
	}
	
	// forex
	switch(trading) {
		case 'inv':
			var fx = document.getElementById('currency').value;
			break;
		case 'grn':
			var fx = document.getElementById('currency').value;
			break;
	
	
	
	
	
		default:
			var fx = '';
			break;
	}

	if (trading == 'crn') {
		$.get("includes/ajaxPostTrade.php", {type:type,ddate:ddate,descript:descript,ref:ref,yourref:yourref,acc:acc,asb:asb,loc:loc,postaladdress:postaladdress,deliveryaddress:deliveryaddress,clt:clt,paymethod:paymethod,staffmember:staffmember,forex:fx}, function(data){$("#purchlist").trigger("reloadGrid")});
	} else {
		$.get("includes/ajaxPostTrade.php", {type:type,ddate:ddate,descript:descript,ref:ref,yourref:yourref,acc:acc,asb:asb,loc:loc,postaladdress:postaladdress,deliveryaddress:deliveryaddress,clt:clt,paymethod:paymethod,staffmember:staffmember,forex:fx}, function(data){$("#tradlist").trigger("reloadGrid")});
	}

	if (trading == 'grn') {
		$.get("includes/ajaxCheckPOs.php", {ddate:ddate,ref:ref,acc:acc,asb:asb,loc:loc}, function(data){
			if (data == 'Y') {
				var x = 0, y = 0; // default values	
				x = window.screenX +5;
				y = window.screenY +200;
				window.open('allocGrn_Po.php','alocpo','toolbar=0,scrollbars=1,height=600,width=970,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
			}
			$("#tradlist").trigger("reloadGrid");
		});
	}

	document.getElementById('description').value = "";
	//document.getElementById('refno').value = 0;
	if (trading != 'c_s' && trading != 'crn' && trading != 'c_p') {
		document.getElementById('TRaccount').value = "";
	}
	
	if (trading == 'inv') {
		document.getElementById('postadd').style.visibility = 'hidden';
		document.getElementById('deliveradd').style.visibility = 'hidden';
		document.getElementById('printpage').style.visibility = 'visible';
		
	// Upgrade Lead or Prospect to Client
		$.get("includes/ajax2Client.php", {acc:acc, asb:asb}, function(data){
		});		
		
		//document.getElementById('copies').value = 1;
	}
	if (trading == 'c_s') {
		document.getElementById('paymethod').style.visibility = 'hidden';
		printtrading('c_s');
	}
	if (trading == 'c_p') {
		document.getElementById('paymethod').style.visibility = 'hidden';
		printtrading('c_p');
	}
	

	jQuery.ajaxSetup({async:true});
	
}

function hideprint(type) {
	if (type == 'ret') {
		$.get("includes/ajaxdeltemptrans.php", function(data){})
	}
	document.getElementById('printpage').style.visibility = 'hidden';
}

function printtrading(type,rf) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	if(typeof(rf)=="undefined"){ 
		rf = tradingref;
	}
	window.open('PrintTrading.php?type='+type+'&tradingref='+rf,'plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function emailtrading(type,rf) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	if(typeof(rf)=="undefined"){ 
		rf = tradingref;
	}
	window.open('PrintTrading.php?type='+type+'&tradingref='+rf+'&doemail=Y','plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function postp_o() {
	
	var type = 'P_O';
	var ddate = document.getElementById('newdate').value;
	var descript = document.getElementById('description').value;
	var ref = type+document.getElementById('refno').value;
	var trading = document.getElementById('trading').value;
	
	tradingref = ref;
	
	if (document.getElementById('lstaff') == null) {
		var staffember = '';
	} else {
		var staffmember = document.getElementById('lstaff').value;
	}
	
	var a = document.getElementById('TRaccount').value;
	var as = a.split('~');
	var clt = as[0];
	var acc = as[1];
	var asb = as[2];
	
	var loc = document.getElementById('loc').value;
	
	$.get("includes/ajaxPostp_o.php", {type:type,ddate:ddate,descript:descript,ref:ref,acc:acc,asb:asb,loc:loc,clt:clt,staffmember:staffmember}, function(data){$("#trdpolist").trigger("reloadGrid")});
	
	document.getElementById('description').value = "";
	document.getElementById('refno').value = 0;
	document.getElementById('TRaccount').value = "";
	
	printp_o(ref);
}

function printp_o(rf) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	if(typeof(rf)=="undefined"){ 
		rf = tradingref;
	}
	window.open('PrintP_O.php?tradingref='+rf,'popdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}



//************************************************************************************************************
//Accounts to Multiple Branches
//************************************************************************************************************

function display(branchcode) {
	ajaxgetacsinbranch(branchcode);
	document.getElementById('arange1').style.display = '';
	document.getElementById('arange2').style.display = '';
	document.getElementById('brange1').style.display = '';
	document.getElementById('addbut').style.display = '';
	
}
	
function gobr2() {
	var tobranch = document.getElementById('tobranch').value;
	var frombranch = document.getElementById('frombranch').value;
	var toacc = document.getElementById('toacc').value;
	var fromacc = document.getElementById('fromacc').value;
	$.get("ad_addbr2.php", {tobranch:tobranch,frombranch:frombranch,toacc:toacc,fromacc:fromacc}, function(data){});
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
//uncostgrn.php
//************************************************************************************************************

function costgrn(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('tr_costgrn.php?uid='+uid,'cstg','toolbar=0,scrollbars=1,height=300,width=610,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addcharge(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('tr_addcharge.php?uid='+uid,'adchg','toolbar=0,scrollbars=1,height=300,width=610,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editcharge(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('tr_editcharge.php?uid='+uid,'edchg','toolbar=0,scrollbars=1,height=300,width=610,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function delcharge(uid) {
	  if (confirm("Are you sure you want to delete this charge")) {
		$.get("includes/ajaxdelcharge.php", {tid: uid}, function(data){$("#chargelist").trigger("reloadGrid")});
	  }
}

function updtgrncosts() {
	$.get("tr_updtgrncosts.php", function(data){});
}

function savegrnno(icode) {
	alert('grn is '+icode);
}

function ipostTrdTrans(tp) {
	$.get("tr_updtgrncharges.php", {tp:tp}, function(data){$("#UncostGRNitems").trigger("reloadGrid")});
}

//************************************************************************************************************
// statements
//************************************************************************************************************

function transvisibledrfrom() {
		document.getElementById('drselectfrom').style.visibility = 'visible';											
		document.getElementById('searchdrfrom').value = "";
		document.getElementById('searchdrfrom').focus();
}

function transvisibledrto() {
		document.getElementById('drselectto').style.visibility = 'visible';											
		document.getElementById('searchdrto').value = "";
		document.getElementById('searchdrto').focus();
}

function gridReloadfromdr(){ 
	var dr_mask = jQuery("#searchdrfrom").val(); 
	dr_mask = dr_mask.toUpperCase();
	jQuery("#selectdrfromlist").setGridParam({url:"selectdrfrom.php?dr_mask="+dr_mask}).trigger("reloadGrid"); 
} 

function doSearchdrfrom(){ 
		var timeoutHnd = setTimeout(gridReloadfromdr,500); 
	} 

function gridReloadtodr(){ 
	var dr_mask = jQuery("#searchdrto").val(); 
	dr_mask = dr_mask.toUpperCase();
	jQuery("#selectdrtolist").setGridParam({url:"selectdrto.php?dr_mask="+dr_mask}).trigger("reloadGrid"); 
} 

function doSearchdrto(){ 
		var timeoutHnd = setTimeout(gridReloadtodr,500); 
	} 

function setselectstat(acc,ledger) {
	var a = acc.split('~');
	var ac = a[0];
	var sb = a[1];
	var acname = a[2];
	var acc = acname+'~'+ac+'~'+sb;
	if (ledger == 'drfrom') {
	 	document.getElementById('fromdr').value = acc;
		document.getElementById('drselectfrom').style.visibility = 'hidden';
		document.getElementById('fromdr').style.visibility = 'visible';
	}
	if (ledger == 'drto') {
	 	document.getElementById('todr').value = acc;
		document.getElementById('drselectto').style.visibility = 'hidden';
		document.getElementById('todr').style.visibility = 'visible';
	}
	if (ledger == 'crfrom') {
	 	document.getElementById('fromcr').value = acc;
		document.getElementById('crselectfrom').style.visibility = 'hidden';
		document.getElementById('fromcr').style.visibility = 'visible';
	}
	if (ledger == 'crto') {
	 	document.getElementById('tocr').value = acc;
		document.getElementById('crselectto').style.visibility = 'hidden';
		document.getElementById('tocr').style.visibility = 'visible';
	}
}

function sboxhidedrfrom() {
	document.getElementById('drselectfrom').style.visibility = 'hidden';											
}

function sboxhidedrto() {
	document.getElementById('drselectto').style.visibility = 'hidden';											
}

function statement() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	var fromdr = document.getElementById('fromdr').value;
	var todr = document.getElementById('todr').value;
	var curdate = document.getElementById('sdate').value;
	var lstdate = document.getElementById('edate').value;
	var comment = document.getElementById('comment').value;
	var creditstat = document.getElementById('creditstat').value;
	
	if (fromdr == '') {
		alert('Please select a debtor to start from.');
		return false;
	}
	if (todr == '') {
		alert('Please select a debtor to end with.');
		return false;
	}
	
	//if (document.getElementById('current').checked) {
		var period = 'c';
	//} else {
		//var period = 'l'
	//}
	if (document.getElementById('nonzero').checked) {
		var range = 'z';
	}
	if (document.getElementById('trans').checked) {
		var range = 't';
	}
	if (document.getElementById('all').checked) {
		var range = 'a';
	}
	
	window.open('rep_statement.php?curdate='+curdate+'&lstdate='+lstdate+'&period='+period+'&range='+range+'&comment='+comment+'&fromdr='+fromdr+'&todr='+todr+'&creditstat='+creditstat,'stm','toolbar=0,scrollbars=1,height=625,width=960,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function delstatp(id) {
	$.get("includes/ajaxdelstat.php", {id: id}, function(data){$("#postlist").trigger("reloadGrid")});
}

function delstate(id) {
	$.get("includes/ajaxdelstat.php", {id: id}, function(data){$("#emaillist").trigger("reloadGrid")});
}


//************************************************************************************************************
// month end
//************************************************************************************************************

function mthend() {
	 if (confirm("Are you sure your statement run is complete?")) {
		$.get("includes/ajaxmthend.php", {}, function(data){alert(data);});
		
	 }
}

//************************************************************************************************************
// al_rec and al_pay
//************************************************************************************************************

function allocunalloc(tp) {
	
	var debtor = document.getElementById('TRaccount').value;
	var dr = debtor.split("~");
	var cname = dr[0];
	var ac = dr[1];
	var sb = dr[2];
	if (tp == 'rec') {
		$.get("", {}, function(data){$("#outinvlist").trigger("reloadGrid")});
	} else {
		$.get("", {}, function(data){$("#outgrnlist").trigger("reloadGrid")});
	}
/*	
	var fx = document.getElementById('currency').value;
	var f = fx.split('~');
	var fxcode = f[0];
	var fxrate = f[1];	
*/	
	var fxrate = 1;
	
	var a = parseFloat(document.getElementById('totamount').value * fxrate);
	var amt = a.toFixed(2);
	document.getElementById('unallocated').value = amt;
	
}

function revalloc(tp) {
	
	var debtor = document.getElementById('TRaccount').value;
	var dr = debtor.split("~");
	var cname = dr[0];
	var ac = dr[1];
	var sb = dr[2];
	if (tp == 'rec') {
		$.get("", {}, function(data){$("#unalinvlist").trigger("reloadGrid")});
	} else {
		$.get("", {}, function(data){$("#unalgrnlist").trigger("reloadGrid")});
	}
}
//************************************************************************************************************
// tr_rec and tr_pay
//************************************************************************************************************

function allocate() {
	var paymethod = "";
	if (document.getElementById('eft').checked) {
		paymethod = 'eft';
	} else if (document.getElementById('crd').checked) {
		paymethod = 'crd';
	} else if (document.getElementById('csh').checked) {
		paymethod = 'csh';
	} else if (document.getElementById('chq').checked) {
		paymethod = 'chq';
	}
	
	if (paymethod == "") {
		alert("Please select a method of payment");
		return false;
	}
	
	var debtor = document.getElementById('TRaccount').value;
	var dr = debtor.split("~");
	var cname = dr[0];
	var ac = dr[1];
	var sb = dr[2];
	$.get("", {}, function(data){$("#outinvlist").trigger("reloadGrid")});
	
	var fx = document.getElementById('currency').value;
	var f = fx.split('~');
	var fxcode = f[0];
	var fxrate = f[1];	
	
	var a = parseFloat(document.getElementById('price').value * fxrate);
	var amt = a.toFixed(2);
	document.getElementById('unallocated').value = amt;
	
}

function setdrallocation(acc,ledger) {
	var trdtype = document.getElementById('trading').value;
	tradingtype = trdtype;
	var a = acc.split('~');
	var acname = a[0];
	var ac = a[1];
	var sb = a[2];
	var blocked = a[3];
	
	if (blocked == 'Yes') {
		alert('This member is blocked. You may not post transactions to their account');
		return false;
	} else {
	
		var acc = acname+'~'+ac+'~'+sb;
		if (ledger == 'dr') {
			document.getElementById('TRaccount').value = acc;
			document.getElementById('drselect').style.visibility = 'hidden';
			document.getElementById('TRaccount').style.visibility = 'visible';
		}
		if (ledger == 'cr') {
			document.getElementById('TRaccount').value = acc;
			document.getElementById('crselect').style.visibility = 'hidden';
			document.getElementById('TRaccount').style.visibility = 'visible';
		}
	}
}

function setrecallocation(acc,ledger) {
	var a = acc.split('~');
	var acname = a[0];
	var ac = a[1];
	var sb = a[2];
	jQuery.ajaxSetup({async:false});
	$.get("includes/ajaxunallocrecs.php", {ac:ac,sb:sb}, function(data){
		var unalloc = data;
		document.getElementById('totamount').value = parseFloat(unalloc).toFixed(2);
	});
	var acc = acname+'~'+ac+'~'+sb;
	$.get("../ajax/ajaxUpdtDr.php", {ac: ac, sb: sb, cname: acname}, function(data){});
	if (ledger == 'dr') {
	 	document.getElementById('TRaccount').value = acc;
		document.getElementById('drselect').style.visibility = 'hidden';
		document.getElementById('TRaccount').style.visibility = 'visible';
	}
	if (ledger == 'cr') {
	 	document.getElementById('TRaccount').value = acc;
		document.getElementById('crselect').style.visibility = 'hidden';
		document.getElementById('TRaccount').style.visibility = 'visible';
	}
	jQuery.ajaxSetup({async:true});
}

function setpayallocation(acc,ledger) {
	var a = acc.split('~');
	var acname = a[0];
	var ac = a[1];
	var sb = a[2];
	jQuery.ajaxSetup({async:false});
	$.get("includes/ajaxunallocpays.php", {ac:ac,sb:sb}, function(data){
		var unalloc = data;
		document.getElementById('totamount').value = parseFloat(unalloc).toFixed(2);
	});
	var acc = acname+'~'+ac+'~'+sb;
	$.get("../ajax/ajaxUpdtCr.php", {ac: ac, sb: sb, cname: acname}, function(data){});
	if (ledger == 'dr') {
	 	document.getElementById('TRaccount').value = acc;
		document.getElementById('drselect').style.visibility = 'hidden';
		document.getElementById('TRaccount').style.visibility = 'visible';
	}
	if (ledger == 'cr') {
	 	document.getElementById('TRaccount').value = acc;
		document.getElementById('crselect').style.visibility = 'hidden';
		document.getElementById('TRaccount').style.visibility = 'visible';
	}
	jQuery.ajaxSetup({async:true});
}

function setpayunallocation(acc,ledger) {
	var a = acc.split('~');
	var acname = a[0];
	var ac = a[1];
	var sb = a[2];
	jQuery.ajaxSetup({async:false});
	var acc = acname+'~'+ac+'~'+sb;
	$.get("../ajax/ajaxUpdtCr.php", {ac: ac, sb: sb, cname: acname}, function(data){});
	if (ledger == 'dr') {
	 	document.getElementById('TRaccount').value = acc;
		document.getElementById('drselect').style.visibility = 'hidden';
		document.getElementById('TRaccount').style.visibility = 'visible';
	}
	if (ledger == 'cr') {
	 	document.getElementById('TRaccount').value = acc;
		document.getElementById('crselect').style.visibility = 'hidden';
		document.getElementById('TRaccount').style.visibility = 'visible';
	}
	jQuery.ajaxSetup({async:true});
}

function payfull(id,tp,refno,fxcode,fxrate) {
	jQuery.ajaxSetup({async:false});
	
	if (document.getElementById('newdate')) {
		var ddate = document.getElementById('newdate').value;
	} else {
		d = new Date();
	  	var yyyy = d.getFullYear().toString();
   		var mm = (d.getMonth()+1).toString(); // getMonth() is zero-based
   		var dd  = d.getDate().toString();
   		var ddate =  yyyy + (mm[1]?mm:"0"+mm[0]) + (dd[1]?dd:"0"+dd[0]); // padding	
	}

	if (document.getElementById('typ')) {
		var typ = document.getElementById('typ').value;
	} else {
		var typ = '';
	}
/*	
	var fx = document.getElementById('currency').value;
	var f = fx.split('~');
	var fxcode = f[0];
	var fxrate = f[1];	
*/
	var fxcode = ' ';
	var fxrate = 1;
	
	var unall = document.getElementById('unallocated').value;
	
	topay = parseFloat(tp);
	unalloc = parseFloat(unall);
	
	if (topay > unalloc) {
		alert('You are trying to allocate more funds than those available');
		return false;
	}
	
	if (document.getElementById('eft')) {
		if (document.getElementById('eft').checked) {
			var paymethod = 'eft';
		} else if (document.getElementById('crd').checked) {
			var paymethod = 'crd';
		} else if (document.getElementById('csh').checked) {
			var paymethod = 'csh';
		} else if (document.getElementById('chq').checked) {
			var paymethod = 'chq';
		}
	} else {
		var paymethod = 'eft';
	}
	if (typ == 'pay') {
		$.get("includes/ajaxPayfull.php", {id: id, topay: topay, paymethod: paymethod, refno: refno, ddate:ddate, fxcode:fxcode, fxrate:fxrate}, 
			  function(data){$("#outgrntrans").trigger("reloadGrid");
							 $("#outgrnlist").trigger("reloadGrid");
							});
	} else {
		$.get("includes/ajaxPayfull.php", {id: id, topay: topay, paymethod: paymethod, refno: refno, ddate:ddate, fxcode:fxcode, fxrate:fxrate}, 
			  function(data){$("#outinvtrans").trigger("reloadGrid");
							 $("#outinvlist").trigger("reloadGrid");
							});
	}
	var aloc = parseFloat(unalloc - topay).toFixed(2);
	document.getElementById('unallocated').value = aloc;
	jQuery.ajaxSetup({async:true});

}

function paypart(id,refno,fxcode,fxrate) {
	document.getElementById('paypart').style.visibility = 'visible';
	document.getElementById('lid').value = id;
	document.getElementById('lref').value = refno;
	document.getElementById('fcode').value = fxcode;
	document.getElementById('frate').value = fxrate;
}

function partpayment() {
	var id = document.getElementById('lid').value;
	var refno = document.getElementById('lref').value;
/*
	var fxrate = document.getElementById('frate').value;
	var fxcode = document.getElementById('fcode').value;
*/
	var fxrate = 1;
	var fxcode = ' ';
	var partamt = document.getElementById('partamount').value;
	payfull(id,partamt,refno,fxcode,fxrate);
	document.getElementById('paypart').style.visibility = 'hidden';
}

function postrec() {
	jQuery.ajaxSetup({async:false});
	
	var paymethod = "";
	
	if (document.getElementById('eft').checked) {
		paymethod = 'eft';
		var a2dr = document.getElementById('eftac').value;
		var s2dr = document.getElementById('eftsb').value;
	} else if (document.getElementById('crd').checked) {
		paymethod = 'crd';
		var a2dr = document.getElementById('crdac').value;
		var s2dr = document.getElementById('crdsb').value;
	} else if (document.getElementById('csh').checked) {
		paymethod = 'csh';
		var a2dr = document.getElementById('cshac').value;
		var s2dr = document.getElementById('cshsb').value;
	} else if (document.getElementById('chq').checked) {
		paymethod = 'chq';
		var a2dr = document.getElementById('chqac').value;
		var s2dr = document.getElementById('chqsb').value;
	}
	
	if (paymethod == "") {
		alert("Please select a method of payment");
		return false;
	}

	var b2dr = document.getElementById('branch').value;
	if (b2dr == "") {
		alert("Please select a branch");
		return false;
	}

	var refno = document.getElementById('refno').value;
	if (refno == 0) {
		alert("Please provide a reference number");
		return false;
	}
	
	var amount = document.getElementById('price').value;
	if (amount == 0) {
		alert("Please provide an amount received");
		return false;
	}

	var ac = document.getElementById('TRaccount').value;
	if (ac == "") {
		alert("Please select a debtor");
		return false;
	}
	
	if ($('#yourref').length > 0) {
		var yourref = document.getElementById('yourref').value;
	} else {
		var yourref = '';
	  // exists.
	}
	
	document.getElementById('bpostrec').style.visibility = 'hidden';

	var debtor = document.getElementById('TRaccount').value;
	var dr = debtor.split("~");
	var cname = dr[0];
	var ac = dr[1];
	var sb = dr[2];
	$.get("../ajax/ajaxUpdtDr.php", {ac: ac, sb: sb, cname: cname}, function(data){});

	var a = debtor.split("~");
	var a2cr = a[1];
	var s2cr = a[2];
	var b2cr = "";
	var ddate = document.getElementById('newdate').value;
	var description = document.getElementById('description').value;
	var reference = document.getElementById('ref').value + refno;
	/*
	var fx = document.getElementById('currency').value;
	var f = fx.split('~');
	var fxcode = f[0];
	var fxrate = f[1];
	*/
	var fxcode = ' ';
	var fxrate = 1;
	
	document.getElementById('currency').disabled = 'disable';
	
	$.get("../ajax/ajaxTransRef.php", {tref: reference}, function(data){});
	var gstinvpay = document.getElementById('gstinvpay').value;
	if (gstinvpay == 'Invoice') {
		var taxpcent = 0;
		var tax = 0;
		var taxtype = 'N-T';
		var total = amount;
		var drgst = 'N';
		var crgst = 'N';
	} else {
		var txtp = document.getElementById('taxtype').value;
		var total = amount;
		var tx = txtp.split('#');
		var taxpcent = tx[0];
		var taxtype = tx[1];
		var amount = parseFloat(total)*1/(1+parseFloat(taxpcent)/100);
		var tax = parseFloat(total) - amount;
		var drgst = 'N';
		var crgst = 'N';
	}
	var refindex = 0;
	var taxindex = 0;
	var n2dr = 0;
	var n2cr = 0;
	
	// code to populate relevant variables and post to trmain.
	$.get("includes/ajaxAddTrans.php", {acc2dr:a2dr, subdr:s2dr, brdr:b2dr, acc2cr:a2cr, subcr:s2cr, brcr:b2cr, ddate:ddate, descript1:description, reference:reference, amount:amount, taxpcent:taxpcent, tax:tax, taxtype:taxtype, total:total, refindex:refindex, taxindex:taxindex, a2d:n2dr, a2c:n2cr, drgst:drgst, crgst:crgst, fxcode:fxcode, fxrate:fxrate, yourref:yourref}, function(data){});
	$.get("includes/ajaxPostTrans.php", {paymethod:paymethod}, function(data){});
	
	document.getElementById('printpage').style.visibility = 'visible';
	
	document.getElementById('allocation').style.visibility = "visible";
	jQuery.ajaxSetup({async:true});
}

function printrec(type) {
	if (type == 'PAY') {
		var rf = document.getElementById('ref').value + document.getElementById('newrefno').value;
	} else {
		var rf = document.getElementById('ref').value + document.getElementById('refno').value;
	}
	if(document.getElementById("price")){
		var amount = document.getElementById('price').value;
	} else {
		var amount = document.getElementById('totamount').value;
	}
	var ddate = document.getElementById('newdate').value;
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('PrintTrading.php?type='+type+'&tradingref='+rf,'plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	//window.open('PrintRecPay.php?type='+type+'&tradingref='+rf+'&amount='+amount+'&ddate='+ddate,'plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function emailrec(type) {
	var rf = document.getElementById('ref').value + document.getElementById('newrefno').value;
	if(document.getElementById("price")){
		var amount = document.getElementById('price').value;
	} else {
		var amount = document.getElementById('totamount').value;
	}
	var ddate = document.getElementById('newdate').value;
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('PrintTrading.php?type='+type+'&tradingref='+rf+'&doemail=Y&amount='+amount+'&ddate='+ddate,'plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	//window.open('PrintRecPay.php?type='+type+'&tradingref='+rf+'&doemail=Y&amount='+amount+'&ddate='+ddate,'plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function postpay() {
	jQuery.ajaxSetup({async:false});
	
		var refno = document.getElementById('newrefno').value;
		if (refno == 0) {
			alert("Please provide a reference number");
			return false;
		}
		
		var amount = document.getElementById('totamount').value;
		if (amount == 0) {
			alert("Please provide an amount received");
			return false;
		}
	
		var cac = document.getElementById('TRaccount').value;
		if (cac == "") {
			alert("Please select a creditor");
			return false;
		}
	
		var dac = document.getElementById('DRaccount').value;
		if (dac == "") {
			alert("Please select a bank account from which to pay");
			return false;
		}
		
		var prt = 'Y';
		document.getElementById('bpostpay').style.visibility = 'hidden';
		
	
		var cr = cac.split("~");
		var cname = cr[0];
		var a2c = cr[1];
		var s2c = cr[2];
		$.get("../ajax/ajaxUpdtCr.php", {ac: a2c, sb: s2c, cname: cname}, function(data){});
		var dr = dac.split("~");
		var a2d = dr[0];
		var s2d = dr[1];
		var b2d = dr[2];
		$.get("../ajax/ajaxUpdtDr.php", {ac: a2d, sb: s2d, br: b2d}, function(data){});
	
		var a2dr = a2c;
		var s2dr = s2c;
		var b2dr = "";
		var a2cr = a2d;
		var s2cr = s2d;
		var b2cr = b2d;
	
		var ddate = document.getElementById('newdate').value;
		var description = document.getElementById('description').value;
		var reference = document.getElementById('ref').value + refno;
		$.get("../ajax/ajaxTransRef.php", {tref: reference}, function(data){});
		var gstinvpay = document.getElementById('gstinvpay').value;
		if (gstinvpay == 'Invoice') {
			var taxpcent = 0;
			var tax = 0;
			var taxtype = 'N-T';
			var total = amount;
			var drgst = 'N';
			var crgst = 'N';
		} else {
			var txtp = document.getElementById('taxtype').value;
			var total = amount;
			var tx = txtp.split('#');
			var taxpcent = tx[0];
			var taxtype = tx[1];
			var amount = parseFloat(total)*1/(1+parseFloat(taxpcent)/100);
			var tax = parseFloat(total) - amount;
			var drgst = 'Y';
			var crgst = 'Y';
		}
		var refindex = 0;
		var taxindex = 0;
		var n2dr = 0;
		var n2cr = 0;
		
		var fx = document.getElementById('currency').value;
		var f = fx.split('~');
		var fxcode = f[0];
		var fxrate = f[1];
		var fxamt = document.getElementById('fxamt').value;
		
		if ($('#yourref').length > 0) {
			var yourref = document.getElementById('yourref').value;
		} else {
			var yourref = '';
		  // exists.
		}
		
		// code to put populate relevant variables and post to trmain.
		$.get("includes/ajaxAddTrans.php", {acc2dr:a2dr, subdr:s2dr, brdr:b2dr, acc2cr:a2cr, subcr:s2cr, brcr:b2cr, ddate:ddate, descript1:description, reference:reference, amount:amount, taxpcent:taxpcent, tax:tax, taxtype:taxtype, total:total, refindex:refindex, taxindex:taxindex, a2d:n2dr, a2c:n2cr, drgst:drgst, crgst:crgst, fxcode:fxcode, fxrate:fxrate, yourref:yourref}, function(data){});
		$.get("includes/ajaxPostTrans.php", {}, function(data){});
		
		document.getElementById('printpage').style.visibility = 'visible';
		
		document.getElementById('allocation').style.visibility = "visible";
		
		var unalloc = document.getElementById('unallocated').value;
		if (parseFloat(unalloc) > 0) {
			
			// TODO if OK allow post and only allocate relevant records.
			
			
			var ok = confirm('You still have an unallocated amount.');
			if (ok == true) {
				return true;
			} else {
				return false;	
			}
		}
		jQuery.ajaxSetup({async:true});
}

function allocatepay() {
	var refno = document.getElementById('newrefno').value;
	if (refno == 0) {
		alert("Please provide a reference number");
		return false;
	}
	
	var amount = document.getElementById('totamount').value;
	if (amount == 0) {
		alert("Please provide an amount received");
		return false;
	}

	var cac = document.getElementById('TRaccount').value;
	if (cac == "") {
		alert("Please select a creditor");
		return false;
	}

	var dac = document.getElementById('DRaccount').value;
	if (dac == "") {
		alert("Please select a bank account from which to pay");
		return false;
	}

	var cr = cac.split("~");
	var cname = cr[0];
	var a2c = cr[1];
	var s2c = cr[2];
	$.get("../ajax/ajaxUpdtCr.php", {ac: a2c, sb: s2c, cname: cname}, function(data){});
	var dr = dac.split("~");
	var a2d = dr[0];
	var s2d = dr[1];
	var b2d = dr[2];
	$.get("../ajax/ajaxUpdtDr.php", {ac: a2d, sb: s2d, br: b2d}, function(data){});


	$.get("", {}, function(data){$("#outgrnlist").trigger("reloadGrid")});
	
	var a = parseFloat(document.getElementById('totamount').value);
	var amt = a.toFixed(2);
	document.getElementById('unallocated').value = amt;
	
}

function payfullp(id,topay,refno) {
	jQuery.ajaxSetup({async:false});
/*	
	var fx = document.getElementById('currency').value;
	var f = fx.split('~');
	var fxcode = f[0];
	var fxrate = f[1];
*/
	var fxrate = 1;
	var fxcode = ' ';
	
	if (document.getElementById('newdate')) {
		var ddate = document.getElementById('newdate').value;
	} else {
		d = new Date();
	  	var yyyy = d.getFullYear().toString();
   		var mm = (d.getMonth()+1).toString(); // getMonth() is zero-based
   		var dd  = d.getDate().toString();
   		var ddate =  yyyy + (mm[1]?mm:"0"+mm[0]) + (dd[1]?dd:"0"+dd[0]); // padding	
	}	
	
	if (document.getElementById('ref')) {
		var rf = document.getElementById('ref').value;
		var rfn = document.getElementById('newrefno').value;
		var payref = rf+rfn;
	} else {
		var payref = 'Allocate';
	}

	var unalloc = document.getElementById('unallocated').value;
	$.get("../ajax/ajaxTransRef.php", {tref: refno}, function(data){});
	
	if (parseFloat(topay) > parseFloat(unalloc)) {
		alert('You are trying to allocate more funds than those available');
		return false;
	}
	
	$.get("includes/ajaxPayfullp.php", {id: id, topay: topay, refno: refno, ddate: ddate, payref: payref, fxcode:fxcode, fxrate:fxrate}, 
		  function(data){$("#outgrnlist").trigger("reloadGrid");
						$("#outgrntrans").trigger("reloadGrid");});
	var aloc = parseFloat(unalloc - topay).toFixed(2);
	document.getElementById('unallocated').value = aloc;
	
	jQuery.ajaxSetup({async:true});
	
}

function paypartp(id,refno) {
	document.getElementById('paypart').style.visibility = 'visible';
	document.getElementById('lid').value = id;
	document.getElementById('lref').value = refno;
}

function partpaymentp() {
	var id = document.getElementById('lid').value;
	var refno = document.getElementById('lref').value;
	var partamt = document.getElementById('partamount').value;
	document.getElementById('paypart').style.visibility = 'hidden';
	payfullp(id,partamt,refno);
	document.getElementById('partamount').value = 0;
}

function revallocation(id,topay,refno) {
	document.getElementById('unpaypart').style.visibility = 'visible';
	document.getElementById('lid').value = id;
	document.getElementById('lref').value = refno;
	document.getElementById('lalloc').value = topay;
}

function ulpayment(tp) {
	var unalloc = document.getElementById('partamount').value;
	var refno = document.getElementById('lref').value;
	var maxalloc = document.getElementById('lalloc').value;
	var id = document.getElementById('lid').value;
	var acc = document.getElementById('TRaccount').value;
	
	if (parseFloat(unalloc) > parseFloat(maxalloc)) {
		alert('You are trying to unallocate more funds than those allocated');
		return false;
	}
	if (tp == 'pay') {
		$.get("includes/ajaxunPayfullp.php", {id: id, unalloc: unalloc, refno: refno, acc: acc}, 
			  function(data){$("#unalgrnlist").trigger("reloadGrid");
			});
	} else {
		$.get("includes/ajaxunPayfullp.php", {id: id, unalloc: unalloc, refno: refno, acc: acc}, 
			  function(data){$("#unalinvlist").trigger("reloadGrid");
			});
	}
	
	document.getElementById('unpaypart').style.visibility = 'hidden';
	document.getElementById('partamount').value = 0;
}

//************************************************************************************************************
// serial numbers
//************************************************************************************************************

function addserialnos() {
	var refno = document.getElementById('ref').value + document.getElementById('refno').value;
	var loc = document.getElementById('loc').value;
	var stkitem = document.getElementById('stkitem').value;
	var s = stkitem.split('~');
	var itemcode = s[0];
	var itemname = s[1];
	var serials = document.getElementById('serialnos').value;
	
	var quant = document.getElementById('qty').value;
	var s = serials.split(',');
	var x = s.length;
	if (x != parseFloat(quant)) {
		alert("Quantity and number of serial numbers differ");
		return false;
	} else {
		$.get("includes/ajaxAddSerials.php", {itemcode:itemcode,itemname:itemname,serials:serials,loc:loc,refno:refno}, function(data){
			if (data != '\r\n') {
				var dups = data;
				alert("These serial numbers are duplicated for this item "+dups);
			} else {
				document.getElementById('tserial').style.visibility = 'hidden';
				document.getElementById('serialnos').value = '';
				addtrdtrans();
			}
		});
	}
}

function tserialclose() {
	document.getElementById('sequential').disabled = false;
	document.getElementById('tserial').style.visibility = 'hidden';
}

function addsellserialnos() {
	var required = document.getElementById('qty').value;
	var selected = document.getElementById('noselected').value;
	if (parseFloat(required) != parseFloat(selected)) {
		alert('Quantity required and quantity selected do not match');
		return false;
	} else {
		$.get("includes/ajaxtrimserials.php", {}, function(data){});		
		var refno = document.getElementById('ref').value + document.getElementById('refno').value;
		var loc = document.getElementById('loc').value;
		var stkitem = document.getElementById('stkitem').value;
		var s = stkitem.split('~');
		var itemcode = s[0];
		var itemname = s[1];
		document.getElementById('sellserial').style.visibility = 'hidden';
		addtrdtrans();
	}
}

function sellserialclose() {
	document.getElementById('sellserial').style.visibility = 'hidden';
}

function serialselect(id) {
	var sel = document.getElementById('noselected').value;
	var newsel = parseFloat(sel) + 1;
	document.getElementById('noselected').value = newsel;
	$.get("includes/ajaxserialselect.php", {id: id}, function(data){$("#selectseriallist").trigger("reloadGrid")});																	
}

function serialdeselect(id) {
	var sel = document.getElementById('noselected').value;
	var newsel = parseFloat(sel) - 1;
	document.getElementById('noselected').value = newsel;
	$.get("includes/ajaxserialdeselect.php", {id: id}, function(data){$("#selectseriallist").trigger("reloadGrid")});																	
}

function showserial(id,ref) {
		$.get("includes/ajaxAddPurchSerials.php", {ref: ref}, function(data){$("#selectpurchseriallist").trigger("reloadGrid")});
		document.getElementById('sellserial').style.visibility = 'visible';
}

function purchserialselect(id) {
	var sel = document.getElementById('noselected').value;
	var newsel = parseFloat(sel) + 1;
	document.getElementById('noselected').value = newsel;
	$.get("includes/ajaxserialselect.php", {id: id}, function(data){$("#selectpurchseriallist").trigger("reloadGrid")});																	
}

function purchserialdeselect(id) {
	var sel = document.getElementById('noselected').value;
	var newsel = parseFloat(sel) - 1;
	document.getElementById('noselected').value = newsel;
	$.get("includes/ajaxserialdeselect.php", {id: id}, function(data){$("#selectpurchseriallist").trigger("reloadGrid")});																	
}

//************************************************************************************************************
// find purchasing details
//************************************************************************************************************

function finddetails(typ) {
	var rn = document.getElementById('refno').value;
	var refno = rn.toUpperCase();
	var ok = 'Y';
	
	jQuery.ajaxSetup({async:false});
	if (refno == 0) {
		alert("Please provide a reference number");
		ok = 'N';
		return false;
	}
	
	var pr = document.getElementById('purchreference').value;
	var purchref = pr.toUpperCase();
	purchref = purchref.trim();
	if (purchref == '') {
		alert("Please provide a purchase document reference");
		ok = 'N';
		return false;
	}
	
	if (typ == 'crn') {
		var pref = purchref.substring(0,3);
		if (pref != 'INV' && pref != 'C_S') {
			alert('Must be an invoice or cash sale - INV or C_S');
		ok = 'N';
			return false;
		}
	}
	
	if (typ == 'ret') {
		var pref = purchref.substring(0,3);
		if (pref != "GRN" && pref != "C_P") {
			alert('Must be a goods recieved or cash purchase - GRN or C_P');
		ok = 'N';
			return false;
		}
		$.get("includes/ajaxcheckgrncosting.php", {purchref: purchref}, function(data){
			var zero = data;
			if (zero == 'Y') {
				alert('You may not return uncosted items');
				document.getElementById('Submit').style.visibility = 'hidden';
				ok = 'N';
				return false;
			} else {
				document.getElementById('Submit').style.visibility = 'visible';
			}
		});
	}
	
	if (ok == 'Y') {
		var pr = document.getElementById('purchreference').value;
		var purchref = pr.toUpperCase();
		$.get("../ajax/ajaxUpdtPurchRef.php", {purchref: purchref}, function(data){
			if (data == 'N') {
				alert("All items received on this document have already been returned.");
			} else {
				document.getElementById('TRaccount').value = data;
			}
			$("#purchlist").trigger("reloadGrid")
			});
	}
	jQuery.ajaxSetup({async:true});
}

function purchselect(id) {
	$.get("includes/ajaxpurchselect.php", {id: id}, function(data){$("#purchlist").trigger("reloadGrid")});																	
}

function purchdeselect(id) {
	$.get("includes/ajaxpurchdeselect.php", {id: id}, function(data){$("#purchlist").trigger("reloadGrid")});																	
}

function refundselectall() {
	$.get("includes/ajaxrefundall.php", {}, function(data){$("#purchlist").trigger("reloadGrid")});																	
}

function refunddeselectall() {
	$.get("includes/ajaxrefundnone.php", {}, function(data){$("#purchlist").trigger("reloadGrid")});																	
}

function removenopay() {
	$.get("includes/ajaxremovenopay.php", {}, function(data){});	
	postTrdTrans('CRN');
}

function editqty(uid) {
	var x = 0, y = 0; // default values	
 	x = window.screenX +200;
  	y = window.screenY +400;
	window.open('pickedqty.php?uid='+uid,'edqty','toolbar=0,scrollbars=0,height=160,width=500,resizeable=no,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}



//************************************************************************************************************
// credit note specific
//************************************************************************************************************
function postcrn() {
	jQuery("#purchlist").trigger("reloadGrid");
	var pm = document.getElementById('TRaccount').value;
	var as = pm.split('~');
	var clt = as[0];
	var acc = as[1];
	var asb = as[2];
	var paymethod = as[3];
	var postaladdress = as[4];
	var type = 'crn';
	var ddate = document.getElementById('newdate').value;
	var descript = document.getElementById('description').value;
	var trading = document.getElementById('trading').value;
	var ref = trading+document.getElementById('refno').value;
	var purchref = document.getElementById('purchreference').value;
	var loc = document.getElementById('loc').value;
	var deliveryaddress = "";
	tradingref = ref;
	var yourref = "";
	
	// forex
	var fx = document.getElementById('currency').value;

	if (document.getElementById('lstaff') == null) {
		var staffember = '';
	} else {
		var staffmember = document.getElementById('lstaff').value;
	}
	
	
	$.get("includes/ajaxtrimtrans.php", {}, function(data){});		
	$.get("includes/ajaxPostTrade.php", {type:type,ddate:ddate,descript:descript,ref:ref,yourref:yourref,acc:acc,asb:asb,loc:loc,postaladdress:postaladdress,deliveryaddress:deliveryaddress,clt:clt,paymethod:paymethod,staffmember:staffmember,forex:fx}, function(data){$("#purchlist").trigger("reloadGrid")});
	
	document.getElementById('description').value = "";
	document.getElementById('refno').value = 0;
	document.getElementById('TRaccount').value = "";
	document.getElementById('purchreference').value = "";
	document.getElementById('printpage').style.visibility = 'visible';
}

function addpurchserialnos() {
	$.get("includes/ajaxtrimserials.php", {}, function(data){});		
	document.getElementById('sellserial').style.visibility = 'hidden';
}

//************************************************************************************************************
// goods returned specific
//************************************************************************************************************
function postret() {
		var pm = document.getElementById('TRaccount').value;
		var as = pm.split('~');
		var clt = as[0];
		var acc = as[1];
		var asb = as[2];
		var paymethod = as[3];
		var postaladdress = as[4];
		var pref = document.getElementById('purchreference').value;
		jQuery.ajaxSetup({async:false});
		$.get("includes/ajaxpurchref.php", {purchref:pref}, function(data){});	
		
		//var clt = "Goods Returned";
		if (document.getElementById('eft').checked) {
			var paymethod = 'eft';
			var acc = document.getElementById('eftac').value;
			var asb = document.getElementById('eftsb').value;
		} else if (document.getElementById('crd').checked) {
			var paymethod = 'crd';
			var acc = document.getElementById('crdac').value;
			var asb = document.getElementById('crdsb').value;
		} else if (document.getElementById('csh').checked) {
			var paymethod = 'csh';
			var acc = document.getElementById('cshac').value;
			var asb = document.getElementById('cshsb').value;
		} else if (document.getElementById('chq').checked) {
			var paymethod = 'chq';
			var acc = document.getElementById('chqac').value;
			var asb = document.getElementById('chqsb').value;
		}
		
		var type = 'ret';
		var ddate = document.getElementById('newdate').value;
		var descript = document.getElementById('description').value;
		var trading = document.getElementById('trading').value;
		var ref = trading+document.getElementById('refno').value;
		var loc = document.getElementById('loc').value;
		var deliveryaddress = "";
		tradingref = ref;
		var yourref = "";
		
		// forex
		var fx = document.getElementById('currency').value;
		
		if (document.getElementById('lstaff') == null) {
			var staffember = '';
		} else {
			var staffmember = document.getElementById('lstaff').value;
		}


		$.get("includes/ajaxtrimtrans.php", {}, function(data){});	
		$.get("includes/ajaxPostTrade.php", {type:type,ddate:ddate,descript:descript,ref:ref,yourref:yourref,acc:acc,asb:asb,loc:loc,postaladdress:postaladdress,deliveryaddress:deliveryaddress,clt:clt,paymethod:paymethod,staffmember:staffmember,forex:fx}, function(data){$("#purchlist").trigger("reloadGrid")});
		
		document.getElementById('description').value = "";
		document.getElementById('refno').value = 0;
		document.getElementById('TRaccount').value = "";
		document.getElementById('purchreference').value = "";
		document.getElementById('paymethod').style.visibility = 'hidden';
		document.getElementById('printpage').style.visibility = 'visible';
		jQuery.ajaxSetup({async:true});
}

function addpurchserialnos() {
	$.get("includes/ajaxtrimserials.php", {}, function(data){});		
	document.getElementById('sellserial').style.visibility = 'hidden';
}

//************************************************************************************************************
// gst
//************************************************************************************************************
function ctry() {
	var cy = document.getElementById('country').value;
	if (cy == "Australia") {
		document.getElementById('ausparams').style.visibility = 'visible';		
	} else {
		document.getElementById('ausparams').style.visibility = 'hidden';		
	}
}


function gst() {
	var cy = document.getElementById('country').value;
	if (cy == "Select Country") {
		alert("Please select a country");
		return false;
	} else if (cy == "Australia") {
		var fdt = document.getElementById('fdate').value;
		var edt = document.getElementById('edate').value;
		var sadj = document.getElementById('sadj').value;
		var padj = document.getElementById('padj').value;
		var est = document.getElementById('est').value;
		var gstn = document.getElementById('gstno').value;
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +200;
		window.open('rep_bascalc.php?fdt='+fdt+'&edt='+edt+'&gstno='+gstn+'&sadj='+sadj+'&padj='+padj+'&est='+est,'basgst','toolbar=0,scrollbars=1,height=590,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
		
	} else if (cy == "New Zealand") {
		var fdt = document.getElementById('fdate').value;
		var edt = document.getElementById('edate').value;
		var gstn = document.getElementById('gstno').value;
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +200;
		window.open('rep_gstnz.php?fdt='+fdt+'&edt='+edt+'&gstno='+gstn,'gst','toolbar=0,scrollbars=1,height=450,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
	} else {
		//United Kingdom
		var fdt = document.getElementById('fdate').value;
		var edt = document.getElementById('edate').value;
		var gstn = document.getElementById('gstno').value;
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +200;
		window.open('rep_gstuk.php?fdt='+fdt+'&edt='+edt+'&gstno='+gstn,'gst','toolbar=0,scrollbars=1,height=450,width=900,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
		
	}
	
}

function basgst() {
	var fdt = document.getElementById('fdate').value;
	var edt = document.getElementById('edate').value;
	var sadj = document.getElementById('sadj').value;
	var padj = document.getElementById('padj').value;
	var est = document.getElementById('est').value;
	var gstn = document.getElementById('gstno').value;
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_bascalc.php?fdt='+fdt+'&edt='+edt+'&gstno='+gstn+'&sadj='+sadj+'&padj='+padj+'&est='+est,'basgst','toolbar=0,scrollbars=1,height=590,width=700,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

//************************************************************************************************************
// Detail one account and multiple accounts
//************************************************************************************************************


function get1gl() {
	document.getElementById('glselect').style.visibility = 'visible';											
	document.getElementById('searchgl').value = "";
	document.getElementById('searchgl').focus();
}

function get1gl2() {
	document.getElementById('glselect2').style.visibility = 'visible';											
	document.getElementById('searchgl2').value = "";
	document.getElementById('searchgl2').focus();
}

function get1dr() {
	document.getElementById('drselect').style.visibility = 'visible';											
	document.getElementById('searchdr').value = "";
	document.getElementById('searchdr').focus();
}

function get1dr2() {
	document.getElementById('drselect2').style.visibility = 'visible';											
	document.getElementById('searchdr2').value = "";
	document.getElementById('searchdr2').focus();
}

function get1cr() {
	document.getElementById('crselect').style.visibility = 'visible';											
	document.getElementById('searchcr').value = "";
	document.getElementById('searchcr').focus();
}

function get1cr2() {
	document.getElementById('crselect2').style.visibility = 'visible';											
	document.getElementById('searchcr2').value = "";
	document.getElementById('searchcr2').focus();
}

function get1as() {
	document.getElementById('asselect').style.visibility = 'visible';											
	document.getElementById('searchas').value = "";
	document.getElementById('searchas').focus();
}

function det1gl() {
	var fdt = document.getElementById('bdate').value;
	var edt = document.getElementById('edate').value;
	var acc = document.getElementById('GLaccount').value;
	if (acc == '') {
		alert('Please select an account');
		return;
	}
	var ob = document.getElementById('lob').value;
	var a = acc.split('~');
	var ac = a[1];
	var sb = a[2];
	var br = a[3];
	viewac(ac,br,sb,fdt,edt,ob);	
	x = window.screenX +5;
	y = window.screenY +200;
	//window.open('rep_view1gl.php?bdateh='+fdt+'&edateh='+edt+'&ob='+ob,'vac1g','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	window.open('rep_view1gl.php','vac1g','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function viewacm(acno,br,sb,acno2,br2,sb2) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxUpdtGLAccm.php", {vac: acno, vbr: br, vsb: sb,vac2: acno2, vbr2: br2, vsb2: sb2 }, function(data){
	});
	jQuery.ajaxSetup({async:true});
	viewac2m();
}

function viewac2m() {
	var fdt = document.getElementById('bdate').value;
	var edt = document.getElementById('edate').value;
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_glmult2pdf.php?bdateh='+fdt+'&edateh='+edt,'vac','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function detmultgl() {
	var fdt = document.getElementById('bdate').value;
	var edt = document.getElementById('edate').value;
	var acc = document.getElementById('GLaccount').value;
	var acc2 = document.getElementById('GLaccount2').value;
	var a = acc.split('~');
	var ac = a[1];
	var sb = a[2];
	var br = a[3];
	var a2 = acc2.split('~');
	var ac2 = a2[1];
	var sb2 = a2[2];
	var br2 = a2[3];
	viewacm(ac,br,sb,ac2,br2,sb2);	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_glmult2pdf.php?bdateh='+fdt+'&edateh='+edt,'vac','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function det1dr() {
	var fdt = document.getElementById('bdate').value;
	var edt = document.getElementById('edate').value;
	var acc = document.getElementById('DRaccount').value;
	if (acc == '') {
		alert('Please select an account');
		return;
	}
	var ob = document.getElementById('lob').value;
	var a = acc.split('~');
	var ac = a[1];
	var sb = a[2];
	var br = '';
	viewacr(ac,br,sb);	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_view1dr.php?bdateh='+fdt+'&edateh='+edt+'&ob='+ob,'vacd','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function viewdrm(acno,sb,acno2,sb2) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxUpdtDRAccm.php", {vac: acno, vsb: sb,vac2: acno2, vsb2: sb2 }, function(data){
	});
	jQuery.ajaxSetup({async:true});
	viewdr2m();
}

function viewdr2m() {
	var fdt = document.getElementById('bdate').value;
	var edt = document.getElementById('edateh').value;
	var cats = outputSelected(document.getElementById('acat'));
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_drmult2pdf.php?bdateh='+fdt+'&edateh='+edt+'&acat='+cats,'vac','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function detmultdr() {
	var fdt = document.getElementById('bdate').value;
	var edt = document.getElementById('edate').value;
	var acc = document.getElementById('DRaccount').value;
	var acc2 = document.getElementById('DRaccount2').value;
	var cats = outputSelected(document.getElementById('acat'));
	var a = acc.split('~');
	var ac = a[1];
	var sb = a[2];
	var a2 = acc2.split('~');
	var ac2 = a2[1];
	var sb2 = a2[2];
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_drmult2pdf.php?bdateh='+fdt+'&edateh='+edt+'&acat='+cats,'vac','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}


function det1cr() {
	var fdt = document.getElementById('bdate').value;
	var edt = document.getElementById('edate').value;
	var acc = document.getElementById('CRaccount').value;
	if (acc == '') {
		alert('Please select an account');
		return;
	}
	var ob = document.getElementById('lob').value;
	var a = acc.split('~');
	var ac = a[1];
	var sb = a[2];
	var br = '';
	viewacr(ac,br,sb);	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_view1cr.php?bdateh='+fdt+'&edateh='+edt+'&ob='+ob,'vacc','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function viewcrm(acno,sb,acno2,sb2) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxUpdtCRAccm.php", {vac: acno, vsb: sb,vac2: acno2, vsb2: sb2 }, function(data){
	});
	jQuery.ajaxSetup({async:true});
	viewcr2m();
}

function viewcr2m() {
	var fdt = document.getElementById('bdate').value;
	var edt = document.getElementById('edate').value;
	var cats = outputSelected(document.getElementById('acat'));
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_crmult2pdf.php?bdateh='+fdt+'&edateh='+edt+'&acat='+cats,'vac','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function detmultcr() {
	var fdt = document.getElementById('bdate').value;
	var edt = document.getElementById('edate').value;
	var acc = document.getElementById('CRaccount').value;
	var acc2 = document.getElementById('CRaccount2').value;
	var cats = outputSelected(document.getElementById('acat'));
	var a = acc.split('~');
	var ac = a[1];
	var sb = a[2];
	var a2 = acc2.split('~');
	var ac2 = a2[1];
	var sb2 = a2[2];
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_crmult2pdf.php?bdateh='+fdt+'&edateh='+edt+'&acat='+cats,'vac','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}


function det1as() {
	var fdt = document.getElementById('bdate').value;
	var edt = document.getElementById('edate').value;
	var acc = document.getElementById('ASaccount').value;
	var ob = document.getElementById('lob').value;
	var a = acc.split('~');
	var ac = a[1];
	var br = a[2];
	var sb = a[3];
	viewacr(ac,br,sb);	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_view1as.php?bdateh='+fdt+'&edateh='+edt+'&ob='+ob,'vaca','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

//************************************************************************************************************
// Stock opening balances
//************************************************************************************************************
function addstkobal(uid) {
	var x = 0, y = 0; // default values	
	var lc = document.getElementById('loc').value;
	if (lc == '') {
		alert('Please select a location');
		return false;
	} else {
		x = window.screenX +5;
		y = window.screenY +265;
			
		window.open('st_addobal.php?itemid='+uid+'&loc='+lc,'addsob','toolbar=0,scrollbars=1,height=400,width=800,resizeable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}
}

function stkupdtobal() {
	$.get("includes/ajaxUpdtStkObal.php", {}, function(data){$("#stkobal").trigger("reloadGrid")});
}

//************************************************************************************************************
// Stock transfers
//************************************************************************************************************
function stkvisible1() {
		document.getElementById('stkselect1').style.visibility = 'visible';											
		document.getElementById('searchstk1').value = "";
		document.getElementById('searchstk1').focus();
}

function stkvisible2() {
		document.getElementById('stkselect2').style.visibility = 'visible';											
		document.getElementById('searchstk2').value = "";
		document.getElementById('searchstk2').focus();
}

var ocost;
function setstkselect1(stk) {
	
	//stk = stkcode+'~'+stkname+'~'+stkunit+'~'+stax+'~'+sac+'~'+sbr+'~'+ssb+'~'+pac+'~'+pbr+'~'+psb+'~'+group+'~'+cat+'~'+cost+'~'+setsell+'~'+trackserial+'~'+staxpcent+'~'+stock
	
	var a = stk.split('~');
	var scode = a[0];
	var sname = a[1];
	var sunit = a[2];
	var grp = a[3];
	var cat = a[4];
	var ocost = a[5];
	//ocost = ocost.toFixed(2);
	var s = scode+'~'+sname;
	document.getElementById('stkitemout').value = s;
	document.getElementById('outcost').value = ocost;
	document.getElementById('outunit').value = sunit;
	document.getElementById('stkselect1').style.visibility = 'hidden';
	document.getElementById('stkitemout').style.visibility = 'visible';
}

function setstkselect2(stk) {
	var oqty = document.getElementById('transferqty').value;
	if (oqty == ''){
		alert('Please enter a quantity to be transferred');
		return false;
	} else {
		var a = stk.split('~');
		var scode = a[0];
		var sname = a[1];
		var sunit = a[2];
		var grp = a[3];
		var cat = a[4];
		var icost = a[5];
		var iqty = document.getElementById('intoqty').value;
		//icost = parseFloat(ocost) / parseFloat(iqty);
		//icost = icost.toFixed(2);
		var s = scode+'~'+sname;
		document.getElementById('stkitemin').value = s;
		document.getElementById('incost').value = icost;
		document.getElementById('inunit').value = sunit;
		document.getElementById('stkselect2').style.visibility = 'hidden';
		document.getElementById('stkitemin').style.visibility = 'visible';
	}
}

function sboxhidestk1() {
	document.getElementById('stkselect1').style.visibility = 'hidden';											
}

function sboxhidestk2() {
	document.getElementById('stkselect2').style.visibility = 'hidden';											
}

function CheckAvailablet(q) {
	var is = document.getElementById('stkitemout').value;
	var locid = document.getElementById('loc1').value;
	var i = is.split('~');
	var stkid = i[0];

	if (q != 0) {
		$.get("includes/ajaxCheckAvailable.php", {q:q, stkid:stkid, locid:locid}, function(data){
				if (data == '') {
					return true;
				} else {
					alert(data);
					document.getElementById('transferqty').value = 0;
					document.getElementById('transferqty').focus();
					return false;
				}
		});
	}
}

function stktrf() {
	var so = document.getElementById('stkitemout').value;
	var sco = so.split('~');
	var scodeout = sco[0];
	var qtyout = document.getElementById('transferqty').value;
	var si = document.getElementById('stkitemin').value;
	var sci = si.split('~');
	var scodein = sci[0];
	var qtyin = document.getElementById('intoqty').value;
	var locout = document.getElementById('loc1').value;
	var locin = document.getElementById('loc2').value;
	var costin = document.getElementById('incost').value;

	$.get("includes/ajaxTransferStock.php", {qtyout:qtyout, scodeout:scodeout, qtyin:qtyin, scodein:scodein, locout:locout, locin:locin, costin:costin}, function(data){
	});
	
	document.getElementById('stkitemout').value = '';
	document.getElementById('loc1').selectedIndex = 0;
	document.getElementById('transferqty').value = 0;
	document.getElementById('outunit').value = '';
	document.getElementById('outcost').value = 0;
	document.getElementById('stkitemin').value = '';
	document.getElementById('loc2').selectedIndex = 0;
	document.getElementById('intoqty').value = 0;
	document.getElementById('inunit').value = '';
	document.getElementById('incost').value = 0;
	
}


//************************************************************************************************************
// Stock reports
//************************************************************************************************************
function stklist() {
	var grp = document.getElementById('stkgroup').value;
	var cat = document.getElementById('stkcat').value;
	
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxUpdtStkGC.php", {grp: grp, cat: cat}, function(data){
	});
	jQuery.ajaxSetup({async:true});
	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('strep_liststock.php','stl','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function nonstklist() {
	var nstk = document.getElementById('nonstock').value;
	var bdt = document.getElementById('bdate').value;
	var edt = document.getElementById('edate').value;
	
	if (nstk == '*') {
		alert("Please select a non-stock item");
	} else {
	
		jQuery.ajaxSetup({async:false});
		$.get("../ajax/ajaxUpdtNStock.php", {nstk: nstk, bdt: bdt, edt: edt}, function(data){
		});
		jQuery.ajaxSetup({async:true});
	
		x = window.screenX +5;
		y = window.screenY +200;
		window.open('strep_listnonstock.php?','nstl','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}
}

function stktake() {
	var stkgrp = document.getElementById('stkgroup').value;
	var stkcat = document.getElementById('stkcat').value;
	var stkloc = document.getElementById('loc').value;
	var randomqty = document.getElementById('randomqty').value;

	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('strep_stktake2pdf.php?stkgrp='+stkgrp+'&stkcat='+stkcat+'&stkloc='+stkloc+'&randomqty='+randomqty,'sttpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);

}

//************************************************************************************************************
// Stock adjustment
//************************************************************************************************************

function adjstock(id) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('stad_stkadjust.php?id='+id,'stkadj','toolbar=0,scrollbars=1,height=400,width=650,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

//************************************************************************************************************
// Standard Transactions
//************************************************************************************************************

function ajaxGetACList(ledger,debitcredit) {
	drcr = debitcredit;
	
	var cat = '';
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxUpdtSelect.php", {drcr: drcr, cat: cat, ledger: ledger}, function(data){
	});
	jQuery.ajaxSetup({async:true});

	if (ledger == 'GL') {
		document.getElementById('glselect').style.visibility = 'visible';
		$.get("selectgl.php", {}, function(data){$("#selectgllist").trigger("reloadGrid")});
		document.getElementById('searchgl').value = "";
		document.getElementById('searchgl').focus();
	}
	if (ledger == 'DR') {
		document.getElementById('drselect').style.visibility = 'visible';
		$.get("selectdr.php", {}, function(data){$("#selectdrlist").trigger("reloadGrid")});
		document.getElementById('searchdr').value = "";
		document.getElementById('searchdr').focus();
	}
	if (ledger == 'CR') {
		document.getElementById('crselect').style.visibility = 'visible';
		$.get("selectcr.php", {}, function(data){$("#selectcrlist").trigger("reloadGrid")});
		document.getElementById('searchcr').value = "";
		document.getElementById('searchcr').focus();
	}
	if (ledger == 'AS') {
		document.getElementById('asselect').style.visibility = 'visible';
		$.get("selectas.php", {}, function(data){$("#selectaslist").trigger("reloadGrid")});
		document.getElementById('searchas').value = "";
		document.getElementById('searchas').focus();
	}
	
	
	if (drcr == 'dr') {
		document.getElementById('DRaccount').style.visibility = 'visible';	
		document.getElementById('drsearch').style.visibility = 'visible';	
	}
	if (drcr == 'cr') {
		document.getElementById('CRaccount').style.visibility = 'visible';	
		document.getElementById('crsearch').style.visibility = 'visible';	
	}
}

//************************************************************************************************************
// recalculate balances
//************************************************************************************************************

function recalcbals() {
	 if (confirm("Your balances will be overwritten. Are you sure you wish to continue?")) {
		$.get("includes/ajaxrecalcbals.php", {}, function(data){alert(data);});
		
	 }
}

//************************************************************************************************************
// bank reconcilliaton
//************************************************************************************************************

function recondate() {
	var bac = document.getElementById('bankno').value;
	var b = bac.split('~');
	var ac = b[0];
	var br = b[1];
	var sb = b[2];
	$.get("includes/ajaxrecondate.php", {ac:ac,br:br,sb:sb}, function(data){
	  rdt = data;
	  document.getElementById('trecdate').value = rdt;
	});
}

function bankrec() {
	var bankno = document.getElementById('bankno').value;
	var dt = document.getElementById('ddate').value;
	var ok = 'Y';
	
	if (bankno == 0) {
		alert('Please select a Bank Account');
		ok = "N";
		return false;
	}
	if (dt == "") {
		alert("Please enter a date.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +10;
		window.open('bankrec1.php?bankno='+bankno+'&ddate='+dt,'bkrec','toolbar=0,scrollbars=1,height=650,width=1010,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}
}

//************************************************************************************************************
// fixed asset headings
//************************************************************************************************************

function fa_editfahead(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('fa_editfahead.php?uid='+uid,'faeh','toolbar=0,scrollbars=1,height=200,width=600,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//************************************************************************************************************
//Non-Stock Transactions
//************************************************************************************************************

function showtax_ns() {
	//var gstinvpay = document.getElementById('gstinvpay').value;
	//if (gstinvpay == 'Invoice') {
			document.getElementById('newtaxtype').style.visibility = 'visible';
			document.getElementById('newamount').style.visibility = 'visible';
			document.getElementById('newtax').style.visibility = 'visible';
			document.getElementById('newtotal').style.visibility = 'visible';
	//} else {
			//document.getElementById('newtaxtype').style.visibility = 'hidden';
			//document.getElementById('newtaxtype').selectedIndex = 4;
			//document.getElementById('newamount').style.visibility = 'hidden';
			//document.getElementById('newtax').style.visibility = 'hidden';
			//document.getElementById('newtotal').style.visibility = 'visible';
	//}
	
}

function setselect_ns(acc,dc) {
	var a = acc.split('~');
	var ac = a[0];
	var br = a[1];
	var sb = a[2];
	var acname = a[3];
	var acc = acname+'~'+ac+'~'+sb+'~'+br;
	if (dc == 'd') {
		document.getElementById('drselect').style.visibility = 'hidden';
		document.getElementById('DRaccount').value = acc;
	}
	if (dc == 'c') {
		document.getElementById('crselect').style.visibility = 'hidden';
		document.getElementById('CRaccount').value = acc;
	}
	if (dc == 'bd') {
		document.getElementById('glselect').style.visibility = 'hidden';
		document.getElementById('DRaccount').value = acc;
	}
	if (dc == 'bc') {
		document.getElementById('glselect').style.visibility = 'hidden';
		document.getElementById('CRaccount').value = acc;
	}
}

function sboxvisibledr_ns() {
	var ledger = document.getElementById('newbgacc2dr').value;
	if (ledger == 'GL') {
		document.getElementById('drselect').style.visibility = 'visible';											
		document.getElementById('searchdr').value = "";
		document.getElementById('searchdr').focus();
	}
}

function sboxvisiblecr_ns() {
	var ledger = document.getElementById('newbgacc2cr').value;
	if (ledger == 'GL') {
		document.getElementById('crselect').style.visibility = 'visible';											
		document.getElementById('searchcr').value = "";
		document.getElementById('searchcr').focus();
	}
}

function nexttrdref_ns(ref,add) {
	$.get("includes/ajaxGetTrdRef.php", {ref: ref, add: add}, function(data){
			document.getElementById('newrefno').value = data;
	});
}

function ajaxGetACList_ns(ledger,debitcredit) {
	drcr = debitcredit;
	
	var cat = '';
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxUpdtSelect.php", {drcr: drcr, cat: cat, ledger: ledger}, function(data){
	});
	jQuery.ajaxSetup({async:true});

	if (drcr == 'dns') {
		$.get("selectdnsp.php", {}, function(data){$("#selectdlist").trigger("reloadGrid")});
		document.getElementById('searchdr').value = "";
		document.getElementById('searchdr').focus();
	}
	if (drcr == 'cns') {
		$.get("selectcnsp.php", {}, function(data){$("#selectdlist").trigger("reloadGrid")});
		document.getElementById('searchdr').value = "";
		document.getElementById('searchdr').focus();
	}
	if (drcr == 'bns') {
		$.get("selectglnsp.php", {}, function(data){$("#selectglnsplist").trigger("reloadGrid")});
		document.getElementById('searchgl').value = "";
		document.getElementById('searchgl').focus();
	}
}


//************************************************************************************************************
//Recurring Transactions
//************************************************************************************************************

function recurring() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	var rtf = $('input[@name="rtfile"]:checked').val();
	
	window.open('tr_rt.php?rtf='+rtf,'trt','toolbar=0,scrollbars=1,height=500,width=960,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//************************************************************************************************************
//Buy/Sell Fixed Asset
//************************************************************************************************************
function purchasset(id) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	
	window.open('fa_purchfa.php?id='+id,'fap','toolbar=0,scrollbars=1,height=500,width=960,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function sellasset(id) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	
	window.open('fa_sellfa.php?id='+id,'fas','toolbar=0,scrollbars=1,height=500,width=960,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//************************************************************************************************************
// Find stock from barcode
//************************************************************************************************************

function findbarcode(){ 
	var bc_mask = jQuery("#stkbarcode").val(); 
	jQuery.ajaxSetup({async:false});
	$.get("includes/ajaxFindBarcode.php", {bcode: bc_mask}, function(data){
		setstkselect(data);											   
	});
	jQuery.ajaxSetup({async:true});
} 

function findbcode(){ 
		var timeoutHnd = setTimeout(findbarcode,500); 
	} 
	
//************************************************************************************************************
// Import accounts
//************************************************************************************************************

function importaccounts(ledger) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +50;
	window.open('../import/hs_importaccounts.php?ledger='+ledger,'impacc','toolbar=0,scrollbars=1,height=700,width=920,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

//************************************************************************************************************
// Calculate/Reverse depreciation
//************************************************************************************************************

// parse a date in yyyy-mm-dd format
function parseDate(input) {
  var parts = input.match(/(\d+)/g);
  // new Date(year, month [, date [, hours[, minutes[, seconds[, ms]]]]])
  return new Date(parts[0], parts[1]-1, parts[2]); // months are 0-based
}

function calcdepn() {
	var depdate = document.getElementById('depdate').value;
	var today = document.getElementById('today').value;
	var yrdate = document.getElementById('yrdate').value;
	var ddepdate = parseDate(depdate);
	var dtoday = parseDate(today);
	var dyrdate = parseDate(yrdate);

	var ok = "Y";
	if (depdate == "") {
		alert("Please enter a valid date.");
		ok = "N";
		return false;
	}
	
	if (ddepdate > dtoday) {
		alert('You may not depreciate assets in the new Tax year until the previous tax year has been closed');
		ok = 'N';
	}	
	
	if (ddepdate > dyrdate) {
		alert('You may not depreciate assets at a future date');
		ok = 'N';
	}	
	
	if (ok == "Y") {
		$.get("includes/ajaxfadep.php", {depdate: depdate}, function(data){
			document.getElementById('btndep').style.visibility = 'hidden';
			alert('Depreciation calculated and General Ledger updated');
		});						   
	}
	
}	

function rev1dep(asid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('fa_rev1dep.php?asid='+asid,'r1dep','toolbar=0,scrollbars=1,height=200,width=620,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);	
}

function revdep(asid) {
		$.get("includes/ajaxrevdep.php", {asid: asid}, function(data){
			alert('Depreciation reversed');
		});						   
}

//************************************************************************************************************
// Storage bins
//************************************************************************************************************

function editbin(binid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +50;
	window.open('stad_editbin.php?binid='+binid,'edbin','toolbar=0,scrollbars=1,height=350,width=500,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//************************************************************************************************************
// Balances at a date
//************************************************************************************************************

function drbaldt() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +50;
	var dt = document.getElementById('bdate').value;
	var fromdr = document.getElementById('fromdr').value;
	var todr = document.getElementById('todr').value;
	var f = fromdr.split('~');
	var fac = f[1];
	var fsb = f[2];
	var facname = f[0];
	var t = todr.split('~');
	var tac = t[1];
	var tsb = t[2];
	var tacname = t[0];
		jQuery.ajaxSetup({async:false});
	$.get("includes/ajaxUpdtAccnos.php", {fno: fac, tno: tac, dt: dt}, function(data){
	});
	jQuery.ajaxSetup({async:true});

	window.open('rep_drbalsatdate.php','drbals','toolbar=0,scrollbars=1,height=450,width=750,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function crbaldt() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +50;
	var dt = document.getElementById('bdate').value;
	var fromcr = document.getElementById('fromcr').value;
	var tocr = document.getElementById('tocr').value;
	var f = fromcr.split('~');
	var fac = f[1];
	var fsb = f[2];
	var facname = f[0];
	var t = tocr.split('~');
	var tac = t[1];
	var tsb = t[2];
	var tacname = t[0];
		jQuery.ajaxSetup({async:false});
	$.get("includes/ajaxUpdtAccnos.php", {fno: fac, tno: tac, dt: dt}, function(data){
	});
	jQuery.ajaxSetup({async:true});

	window.open('rep_crbalsatdate.php','crbals','toolbar=0,scrollbars=1,height=450,width=750,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function transvisiblecrfrom() {
		document.getElementById('crselectfrom').style.visibility = 'visible';											
		document.getElementById('searchcrfrom').value = "";
		document.getElementById('searchcrfrom').focus();
}

function transvisiblecrto() {
		document.getElementById('crselectto').style.visibility = 'visible';											
		document.getElementById('searchcrto').value = "";
		document.getElementById('searchcrto').focus();
}

function gridReloadfromcr(){ 
	var cr_mask = jQuery("#searchcrfrom").val(); 
	cr_mask = cr_mask.toUpperCase();
	jQuery("#selectcrfromlist").setGridParam({url:"selectcrfrom.php?cr_mask="+cr_mask}).trigger("reloadGrid"); 
} 

function doSearchcrfrom(){ 
		var timeoutHnd = setTimeout(gridReloadfromcr,500); 
	} 

function gridReloadtocr(){ 
	var cr_mask = jQuery("#searchcrto").val(); 
	cr_mask = cr_mask.toUpperCase();
	jQuery("#selectcrtolist").setGridParam({url:"selectcrto.php?cr_mask="+cr_mask}).trigger("reloadGrid"); 
} 

function doSearchcrto(){ 
		var timeoutHnd = setTimeout(gridReloadtocr,500); 
	} 
	
//************************************************************************************************************
// Year end
//************************************************************************************************************

function yrend() {
	var c1 = 'n';
	var c2 = 'n';
	var c3 = 'n';
	var c4 = 'n';
	var c6 = 'n';
	var c6 = 'n';
	
	if (document.getElementById('c1').checked) {
		c1 = 'y';
	}
	if (document.getElementById('c2').checked) {
		c2 = 'y';
	}
	if (document.getElementById('c3').checked) {
		c3 = 'y';
	}
	if (document.getElementById('c4').checked) {
		c4 = 'y';
	}
	if (document.getElementById('c5').checked) {
		c5 = 'y';
	}
	if (document.getElementById('c6').checked) {
		c6 = 'y';
	}
	
	if (c1 == 'n' || c2 == 'n' || c3 == 'n' || c4 == 'n' || c5 == 'n' || c6 == 'n') {
		alert('Please tick that you have completed all the required steps befor continuing.');
		return false;
	} else {
		var x = 0, y = 0; // default values	
		x = window.screenX +50;
		y = window.screenY +200;
		window.open('hs_fwdbal.php','fwdbal','toolbar=0,scrollbars=1,height=420,width=630,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}
}

//************************************************************************************************************
// Delete Transaction
//************************************************************************************************************

function deltran() {
	var tref = document.getElementById('tref').value;
	
	$.get("includes/ajaxDelTrans.php", {tref: tref}, function(data){
		if (data == 'y') {
			// trans deleted from trmain, invhead, invtrans and stkmast then recalc balances
			alert(tref+" deleted");
		} else {
			alert(data);
		}
	});						   
	
}

//************************************************************************************************************
// Day's Takings
//************************************************************************************************************

function daytake() {
	var bdate = document.getElementById('bdate').value;
	var edate = document.getElementById('edate').value;
	var tcash = document.getElementById('tcash').value;
	var tfloat = document.getElementById('tfloat').value;
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_DayTake.php?bdate='+bdate+'&edate='+edate+'&tcash='+tcash+'&tfloat='+tfloat,'dtpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//************************************************************************************************************
// PDF Grid Template
//************************************************************************************************************

function pdfgrid() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('pdfgrid.php','pdfg','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//********************************************************
// delivery notes/sales orders
//**********************************************************

function addsalesorder() {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
	window.open('tr_s_o.php','addso','toolbar=0,scrollbars=1,height=630,width=1020,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);

}

function editso(id) {
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;
		
	window.open('tr_edit_s_o.php?son='+id,'edso','toolbar=0,scrollbars=1,height=630,width=1020,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);

}


function adddo(id) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +5;
		window.open('adddo.php?son='+id,'adddo','toolbar=0,scrollbars=1,height=600,width=1020,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function viewdn(id) {
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('viewdn.php?id='+id,'vdn','toolbar=0,scrollbars=1,height=570,width=900,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function printdn(rf) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	if(typeof(rf)=="undefined"){ 
		rf = tradingref;
	}
	var type = 'D_N';
	window.open('PrintTrading.php?type='+type+'&tradingref='+rf,'plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function createinv() {
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('createinv.php','cinv','toolbar=0,scrollbars=1,height=500,width=1000,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function printso(rf) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	if(typeof(rf)=="undefined"){ 
		rf = tradingref;
	}
	var type = 'S_O';
	window.open('PrintTrading.php?type='+type+'&tradingref='+rf,'plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

//*****************************************************************
// Delivery Notes
//*****************************************************************

function selectcustomer() {
	var cust = document.getElementById('cust').value;
	$.get("includes/ajaxselectcustomer.php", {id: cust}, function(data){$("#d4ilist").trigger("reloadGrid")});																	
}

function dnselect(id) {
	$.get("includes/ajaxdnselect.php", {id: id}, function(data){$("#d4ilist").trigger("reloadGrid")});																	
}

function dndeselect(id) {
	$.get("includes/ajaxdndeselect.php", {id: id}, function(data){$("#d4ilist").trigger("reloadGrid")});																	
}

//*****************************************************************
// Edit Financial Documents
//*****************************************************************

function editfindoc() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	var type = $('input[name=findocs]:checked').val();
	if (typeof type == 'undefined') {
		alert('Please select a document');
	} else {
		window.open('hs_editfindoc.php?type='+type,'edfindoc','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	}
}

//*****************************************************************
// Forex
//*****************************************************************

function addforex() {
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('hs_addforex.php','afx','toolbar=0,scrollbars=1,height=250,width=620,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);	
}

function editforex(id) {
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('hs_editforex.php?uid='+id,'efx','toolbar=0,scrollbars=1,height=250,width=620,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);	
}

function setfx() {
	if(document.getElementById('currency')) {	
		var fx = document.getElementById('currency').value;	
		var f = fx.split('~');
		var fxc = f[0];
		$.get("includes/ajaxGetLocalCurrency.php", {}, function(data){
			var lcur = data;
			if (lcur != fxc) {
				document.getElementById('fxcode').value = fxc;
				document.getElementById('localcode').value = lcur;
				document.getElementById('currency').disabled = 'disable';
			} else {
				document.getElementById('localcode').value = lcur;
				document.getElementById('fxcode').value = "";
				document.getElementById('currency').disabled = 'disable';
			}
			
		});	
	}
}

function calcPriceForex() {
	if(document.getElementById('currency')) {	
		$.get("includes/ajaxGetLocalCurrency.php", {}, function(data){
			var pr = document.getElementById('price').value;	
			var e = document.getElementById("currency");
			var fx = e.options[e.selectedIndex].value;
			var f = fx.split('~');
			var fxc = f[0];
			var fxr = f[1];
			var lcur = data;
			if (lcur != fxc) {
				var lamt = pr * fxr;
				ntr = Math.round(lamt*100)/100;
				ntf = ntr.toFixed(2);				
				document.getElementById('fxamt').value = ntf;
			}
		});	
	}
}

function calcLocalForex() {
	if(document.getElementById('currency')) {	
		$.get("includes/ajaxGetLocalCurrency.php", {}, function(data){
			var pr = document.getElementById('fxamt').value;	
			var e = document.getElementById("currency");
			var fx = e.options[e.selectedIndex].value;
			var f = fx.split('~');
			var fxc = f[0];
			var fxr = f[1];
			var lcur = data;
			if (lcur != fxc) {
				var lamt = pr / fxr;
				ntr = Math.round(lamt*100)/100;
				ntf = ntr.toFixed(2);				
				document.getElementById('price').value = ntf;
			}
		});	
	}
}

//********************************************************
// reports from serial number tracking
//********************************************************

function prin(doc) {
	var d = doc.substr(0,3);
	printtrading(d,doc);	
}

function prout(doc) {
	var d = doc.substr(0,3);
	printtrading(d,doc);	
}

//********************************************************
// reverse transactions
//********************************************************

function transtype() {
	var selectedOption = $("input:radio[name=reverse]:checked").val();
	if (selectedOption == 'std') {
		document.getElementById("stdentry").style.display = "";
		document.getElementById("trdentry").style.display = "none";
		document.getElementById("btnentry").style.display = "";
	} else {
		document.getElementById("trdentry").style.display = "";
		document.getElementById("stdentry").style.display = "none";
		document.getElementById("btnentry").style.display = "";
	}
	
}

function doRevTrans() {
	var selectedOption = $("input:radio[name=reverse]:checked").val();
	if (selectedOption == 'std') {
		var transref = document.getElementById("stdref").value;
		$.get("includes/ajaxFindStdTrans.php", {ref: transref}, function(data){
			var tr = data;
			if (tr == 'stock') {
				alert("This transaction includes stock items. Please reverse using the appropriate trading tansaction.");
			} else {
				if (tr == 'N') {
					alert("This reference does not exist");
				} else {
					$.get("includes/ajaxrecalcbals.php", {}, function(data){alert(data);});
					alert("This transaction reversed. Please ensure you sort out any relevant reconciled bank transactions and/or submitted trading tax amounts this may affect.");
				}
			}
		});	

	} else {
		var transref = document.getElementById("trdref").value;
		$.get("includes/ajaxFindTrdTrans.php", {ref: transref}, function(data){
			var tr = data;
			if (tr == 'N') {
				alert("This reference does not exist");
			} else {
				alert("This transaction reversed. Please ensure you sort out any relevant reconciled bank transactions and/or submitted trading tax amounts this may affect, along with any relevant stock adjustments.");
			}
		});			
	}

}

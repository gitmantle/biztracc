function loadNewRecord(cori)
{
	var url = 'newrecord.php';
	var pars = 'cori=' + cori;
		
	var myAjax = new Ajax.Request( url, { method: 'get', parameters: pars, onComplete: showRecord });

}

function loadCurrentRecord(cid,dept,prod,con)
{
	var url = 'record.php';
	var pars = 'cid=' + cid + '&dept=' + dept + '&prod=' + prod + '&con=' + con;
	$('rcid').value = cid;
	$('rcon').value = con;
		
	var myAjax = new Ajax.Request( url, { method: 'get', parameters: pars, onComplete: showRecord });

}

function showRecord(originalRequest)
{
	$('newrecord').innerHTML = originalRequest.responseText;
}

function doCompanySearch(val,type)
{
	var url = 'csearch.php';
	var pars = 'search=' + val + '&type=' + type;
		
	var myAjax = new Ajax.Request( url, { method: 'get', parameters: pars, onComplete: showCsResults });

}

function showCsResults(originalRequest)
{
	$('csres').innerHTML = originalRequest.responseText;
}

function processCsResult(cid, conid)
{
	//loadCurrentRecord(csData,'','','');
	//loadLeadFormSelect('','','');
	window.location = '../pos.php?cid='+cid+'&conid'+conid;
}

function doCompanySearch2(val,type, cid)
{
	var url = 'csearch.php';
	var pars = 'search=' + val + '&type=' + type + '&con=1&cid=' + cid;
		
	var myAjax = new Ajax.Request( url, { method: 'get', parameters: pars, onComplete: showCsResults2 });

}

function showCsResults2(originalRequest)
{
	$('csearch2').innerHTML = originalRequest.responseText;
}

function searchsuburb(city,state,pcode) {
	var url = 'suburb.php';
	var pars = 'suburb='+city+'&state='+state+'&pcode='+pcode;
	
	if(city.length >= 3) {
	var MyAjax = new Ajax.Request(url,{ method: 'get', parameters: pars, onComplete: showSuburb });
	}
}

function showSuburb(originalRequest) {
	$('subdiv').innerHTML = originalRequest.responseText;
}

function selectSub(csData)
{
	var sm = new Array();
    sm = csData.split(",",3);
	
	$('nr_add_city').value = sm[0];
    $('nr_add_state').value = sm[1];
    $('nr_add_pcode').value = sm[2];
	hideSub();
}

function hideSub()
{
	$('subdiv').style.display = 'none';
}

function showSub()
{
	$('subdiv').style.display = '';
}

function changeRelation(rel)
{
	if(rel == 'i'){
		var heading1 = "Individual's Details";
		var heading2 = "Individual's Name";
		var heading3 = "Also Known As";
		var coritxt = "Individual";
		
		//$('abndisp').style.display = 'none';
		$('companydetails').style.display = 'none';
		$('company').style.display = 'none';
	} else if(rel == 'g'){
		var heading1 = "Department's Details";
		var heading2 = "Department's Name";
		var heading3 = "Other Name";
		var coritxt = "Department";
		//$('abndisp').style.display = '';
		$('companydetails').style.display = '';
		$('company').style.display = '';
	} else {
		var heading1 = "Company's Details";
		var heading2 = "Company's Legal Name";
		var heading3 = "Trading as";
		var coritxt = "Company";
		//$('abndisp').style.display = '';
		$('companydetails').style.display = '';
		$('company').style.display = '';
	}
	$('csearch2').innerHTML = '';
	$('nr_legalname_label').innerHTML = heading2;
	$('nr_tradename_label').innerHTML = heading3;
}

function abnsearch()
{
	var cnamer = $('nr_legalname').value;
	var loca = "http://www.abr.business.gov.au/search.aspx?SearchText="+cnamer+"&StartSearch=True";
	window.open(loca,'abnsearch','width=900,height=700,scrollbars=1');
}


function transferName()
{
	if($('nr_ctype').value == 'i') {
		var cf = $('nr_con_fname').value;
		var ln = $('nr_con_lname').value;
		var iname = cf+' '+ln;
		$('nr_legalname').value = iname;
		$('nr_tradename').value = iname;
	}
}

function transferPhone()
{
	if($('nr_ctype').value == 'i') {
		var ph = $('nr_con_phone').value;
		var fx = $('nr_con_fax').value;
		$('nr_comp_phone').value = ph;
		$('nr_comp_fax').value = fx;
	}
}

function saveRecord()
{
	var save = false;
	var cid = 0;
	var ctype = $('nr_ctype').value;
	if($('cs_results')) {
		//Company and / or contact selected
		cid = $('cs_results').value;
		var url = "../pos.php?cid="+cid;
		if($('cons_results')) {
			if($('cons_results').value != '') {
alert('save nothing - cid and conid selected'); //save nothing- cid and conid selected
				url += "&con="+$('cons_results').value;
				window.location = url;
			} else {
				if($('nr_con_fname').value != '' || $('nr_con_lname').value != '') {
alert('save contact only'); //save contact only
					save = true;
				} else {
					alert("Select or enter contact details");
				}
			}
		} else {
			if (ctype == 'i') {
alert('Type Induvidual - Go to POS')
				window.location = url;
			} else if($('nr_con_fname').value != '' || $('nr_con_lname').value != '') { 
alert('save contact only'); //save contact only
				save = true;
			} else {
			
				alert("Select or enter contact details");
			}
		}
		
	} else if($('nr_legalname').value == '' && $('nr_tradename').value == '' && $('nr_con_lname').value == '') {
		//Go to pos - no client or contact info info
alert("No cid or conid"); //No cid or conid
		window.location = "../pos.php";
	} else {
alert('Save new company and / or contact');
		//Save new company and / or contact
		save = true;	
	}
	if(save) {	
		var legalname = $('nr_legalname').value;
		var tradename = $('nr_tradename').value;
		//var abn = $('nr_abn').value;
		var compph = $('nr_comp_phone').value;
		var compfx = $('nr_comp_fax').value;
		var confname = $('nr_con_fname').value;
		var conlname = $('nr_con_lname').value;
		var conph = $('nr_con_phone').value;
		//var confax = $('nr_con_fax').value;
		var conmobile = $('nr_con_mobile').value;
		//var conpos = $('nr_con_pos').value;
		var conemail = $('nr_con_email').value;
		var addline1 = $('nr_add_line1').value;
		var addline2 = $('nr_add_line2').value;
		var addcity = $('nr_add_city').value;
		var addstate = $('nr_add_state').value;
		var addpcode = $('nr_add_pcode').value;
		//var industry = $('nr_industry').value;
		var status = $('nr_status').value;
			
		var url = 'saverecord.php';
		//var paras = 'ctype=' + ctype + '&legal=' + legalname  + '&trade=' + tradename + '&abn=' + abn + '&compph=' + compph + '&compfax=' + compfx + '&confname=' + confname + '&conlname=' + conlname + '&conph=' + conph + '&confax=' + confax + '&conmobile=' + conmobile + '&conpos=' + conpos + '&conemail=' + conemail + '&addline1=' + addline1 + '&addline2=' + addline2 + '&addcity=' + addcity + '&addstate=' + addstate + '&addpcode=' + addpcode + '&industry=' + industry + '&status=' + status;
		var paras = 'ctype=' + ctype + '&legal=' + legalname  + '&trade=' + tradename + '&compph=' + compph + '&compfax=' + compfx + '&confname=' + confname + '&conlname=' + conlname + '&conph=' + conph + '&conmobile=' + conmobile + '&conemail=' + conemail + '&addline1=' + addline1 + '&addline2=' + addline2 + '&addcity=' + addcity + '&addstate=' + addstate + '&addpcode=' + addpcode + '&status=' + status + '&cid=' + cid;

//alert(paras);
		//var MyAjax = new Ajax.Request(url,{ method: 'get', parameters: paras, onComplete: showSavedRecord });
	}
}

function showSavedRecord(originalRequest)
{
	var resp = originalRequest.responseText;
	alert(resp);
	var respArr = resp.split("#@#",2);
	processCsResult(respArr[0], respArr[1]);
	//loadCurrentRecord(cid);
	//loadLeadFormSelect('', '', '');
}

function loadLeadFormSelect(sourcee, dept, prod, cid)
{
	var url = 'formselect.php';
	var pars = 'source=' + sourcee + '&dept=' + dept + '&prod=' + prod + '&cid=' + cid;
	var myAjax = new Ajax.Request( url, { method: 'get', parameters: pars, onComplete: showFormSel });
}

function showFormSel(originalRequest)
{
	$('selectform').innerHTML = originalRequest.responseText;
}

function loadLeadForm(dept,prod,con)
{
	var url = 'leadform.php';
	var pars = 'dept=' + dept + '&prod=' + prod + '&cid=' + document.getElementById('rcid').value + '&itemid=' + document.getElementById('ritemid').value + '&con=' + con;
		
	var myAjax = new Ajax.Request( url, { method: 'get', parameters: pars, onComplete: showForm });
}

function showForm(originalRequest)
{
	$('leadform').innerHTML = originalRequest.responseText;
}

function changeCon(val,cid)
{
	if(val == -1){
		//$('slb').disabled = false;
		var loc = "../../comp/editcontact.php?action=new&cid="+cid+"&callpage=lead";
		window.open(loc,'newcon','width=700,height=350');
	} else if(val == "") {
		//$('slb').disabled = true;
	} else {
		$('con').value = val;
		$('rcon').value = val;
		//$('slb').disabled = false;
	}
}

function change_contact(val,cid)
{
	window.location = "../lead/tim/getlead.php?cid=" + cid + "&con=" + val;
}

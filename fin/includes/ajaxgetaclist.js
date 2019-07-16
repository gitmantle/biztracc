
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




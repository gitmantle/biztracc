

function ajaxGetRef(ref) {
	jQuery.ajaxSetup({async:false});

	if(ref == "") {
		alert('Please select a transaction type');
		return(false);
	}	
	
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
	
	$.get("includes/ajaxGetRef.php", {ref: ref}, function(data){
			document.getElementById('newrefno').value = data;
			//if (ref != 'JNL') {
				//document.getElementById('newtaxtype').style.visibility = 'visible';
			//}
	});
	jQuery.ajaxSetup({async:true});
}

/*
function ajaxGetXref(client,reftype,crdr) {
	
	var acc = client.split('~');
	var acno = acc[0];
	transtype = reftype;
	var draccno;
	var craccno;
	
	if (crdr == 'dr') {
		if (acno == 870) {
			document.getElementById('ndrgst').style.display = '';
		} else {
			document.getElementById('ndrgst').style.display = 'none';
		}
	}
	
	if (crdr == 'dr') {
		draccno = acno;
	}
	if (crdr == 'cr') {
		craccno = acno;
	}
	
	if (crdr == 'cr') {
		if (acno == 870) {
			document.getElementById('ncrgst').style.display = '';
		} else {
			document.getElementById('ncrgst').style.display = 'none';
		}
	}

	if (draccno == 5000 || craccno == 5000) {
		document.getElementById('newasset').style.visibility = 'visible';
		document.getElementById('newtaxtype').style.visibility = 'hidden';
		document.getElementById('newtaxtype').selectedIndex = 4;
	} else {
		document.getElementById('newasset').style.visibility = 'hidden';
		document.getElementById('newtaxtype').style.visibility = 'visible';
		document.getElementById('newtaxtype').selectedIndex = 0;
	}	
	

	$.get("includes/ajaxGetXref.php", {reftype: transtype, client: client, crdr: crdr}, function(data){
	$("#newxref").append(data);

});
}
*/


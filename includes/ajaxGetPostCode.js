

function ajaxGetPostCode(adloc,adtype,stno,sad1,bad1,po,rd,suburb,town) {
	var x=document.getElementById("pclist");
	var listlength = document.getElementById("pclist").length
	for (var i = 0; i < listlength; i ++) {
		x.remove(x[i]);
	}	
	$.get("includes/ajaxGetPostCode.php", {adloc: adloc, adtype: adtype, stno: stno, sad1: sad1, bad1: bad1, po: po, rd: rd, suburb: suburb, town: town}, function(data){
						$("#pclist").append(data);																	

	});
}





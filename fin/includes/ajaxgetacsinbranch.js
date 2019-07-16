function ajaxgetacsinbranch(branchcode) {

var x=document.getElementById("fromacc");
var listlength = document.getElementById("fromacc").length
for (var i = 0; i < listlength; i ++) {
	x.remove(x[i]);
}	
var x=document.getElementById("toacc");
var listlength = document.getElementById("toacc").length
for (var i = 0; i < listlength; i ++) {
	x.remove(x[i]);
}	

$.get("includes/ajaxgetacsinbranch.php", {branchcode: branchcode}, function(data){
	$("#fromacc").append(data);
	$("#toacc").append(data);
});
}


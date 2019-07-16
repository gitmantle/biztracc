function ajaxCheckTransDate() {
	var dt = document.getElementById('newdateh').value;
	
	$.get("includes/ajaxCheckTransDate.php", {dt: dt}, function(data){
			if (data == '') {
				return true;
			} else {
				alert(data);
				document.getElementById('newdate').focus();
				return false;
			}
	});
}


//***********************************************************************************************************
//todo.php
//***********************************************************************************************************


function edittodo(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('edittodo.php?uid='+uid,'edtd','toolbar=0,scrollbars=1,height=300,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function addtodo() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('addtodo.php','addtd','toolbar=0,scrollbars=1,height=300,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  				

function deltodo(uid) {
	 if (confirm("Are you sure you want to delete this Task")) {
		$.get("ajaxdeltodo.php", {tid: uid}, function(data){$("#todolist").trigger("reloadGrid")});																	
	  }
}

function donetodo(uid) {
	 if (confirm("Are you sure you have completed this task?")) {
		$.get("ajaxdonetodo.php", {tid: uid}, function(data){$("#todolist").trigger("reloadGrid")});																	
	 }
}

function listtasks(){ 
	var st_mask = jQuery("#lstaff").val(); 
	var un = st_mask.split('#');
	var stmask = un[0];
	var uno = un[1];
	if (stmask != 0) {
		jQuery("#todolist").setGridParam({url:"gettodo.php?st_mask="+stmask+"&uno="+uno}).trigger("reloadGrid"); 
	}
} 

function xl_todo() {
	var st_mask = jQuery("#lstaff").val(); 
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +100;
	window.open('xl_todo.php?uno='+st_mask,'xltodo','toolbar=0,scrollbars=1,height=600,width=800,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}  		

function emailtodo() {
	
	location.href='mailto:Enter recipient here'
	
}


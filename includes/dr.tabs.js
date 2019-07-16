/**
 * Creates a new tab and gets data via ajax
 * @param page String page to get data from
 * @param page_name String friendly name of page
 * @param pos Integer position to insert the tab (if you dont want it added to the end)
 */


/*
$(".addtab").click(function(){
	var href=$(this).attr("href"); 
	var title=$(this).attr("title"); 
	var tabID=$(this).attr("id"); 
	// check whether the tab already exists 
	if($("span[id^=span-"+ tabID +"]").length > 0) { 
		//tab is already loaded so find the href of the tab and load 
		var addedTab = $("span[id^=span-"+ tabID +"]").parent().parent(); 
		var addedTabHref = $(addedTab).attr("href"); 
		$("#tabs").tabs('select', addedTabHref); 
	} else { 
		//tab not loaded so add a new one 
		$("#tabs").tabs("add", href + '?' + tabID,title+' '); 
	} 
return false; });

*/


function createTab(page,page_name,pos) {
	$('#tabs').tabs("add",page,page_name);
	$('#tabs').tabs({ajaxOptions: { 
		error: function(xhr, status, index, anchor) {
			$(anchor.hash).html("Couldn't load this tab.");
		},
		data: {},
		success: function(data, textStatus) {
		},
	  }
	});
	var tabIndex = $('#tabs').tabs('length');
	$('#tabs').tabs("select",tabIndex-1);
	return false;
}


function removeCurrentTab(){
	var selected = $tabs.data('selected.tabs');
	$('#tabs').tabs('remove', selected);
}

function removeTab(tabIndex) {
	$('#tabs').tabs('remove', tabIndex);
}

function disableTab(tabIndex) {
	$('#dbosstabs > ul').tabs('disable', tabIndex);
}

function enableTab(tabIndex) {
	$('#dbosstabs > ul').tabs('enable', tabIndex);
}

function selectTab(tabIndex) {
	$('#dbosstabs > ul').tabs('select', tabIndex);
}

function confirmLogout() {
	if(confirm('Are you sure you want to log out of the system?')) {
		window.location = '?a=logout';
	}
}


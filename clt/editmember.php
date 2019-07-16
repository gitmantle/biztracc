<?php
session_start();
//ini_set('display_errors', true);

if (isset($_REQUEST['from'])) {
	$from = $_REQUEST['from'];
} else {
	$from = 'clt';
}

$usersession = $_SESSION['usersession'];

$cluid = $_SESSION['s_memberid'];

date_default_timezone_set($_SESSION['s_timezone']);

include_once("../includes/DBClass.php");
$dbem = new DBClass();

$dbem->query("select * from sessions where session = :vusersession");
$dbem->bind(':vusersession', $usersession);
$row = $dbem->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$hdate = date('Y-m-d');
$ttime = strftime("%H:%M", time());
$cltdb = $_SESSION['s_cltdb'];

$dbem->query("insert into ".$cltdb.".audit (ddate,ttime,user_id,uname,sub_id,member_id,action) values (:ddate,:ttime,:user_id,:uname,:sub_id,:member_id,:action)");
$dbem->bind(':ddate', $hdate);
$dbem->bind(':ttime', $ttime);
$dbem->bind(':user_id', $user_id);
$dbem->bind(':uname', $sname);
$dbem->bind(':sub_id', $subscriber);
$dbem->bind(':member_id', $cluid);
$dbem->bind(':action', 'Edit Member');

$dbem->execute();

// populate  staff drop down
$dbem->query("select * from users where sub_id = :subid order by ulname");
$dbem->bind(':subid', $subid);
$rows = $dbem->resultset();
$staff_options = '<option value="0">Select</option>';
foreach ($rows as $row) {
	extract($row);
	if ($row['uid'] == $user_id) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$staff_options .= '<option value="'.$row['ufname'].' '.$row['ulname'].'"'.$selected.'>'.$row['ufname'].' '.$row['ulname'].'</option>';
}

$dbem->query("select * from ".$cltdb.".members where member_id = ".$cluid);
$row = $dbem->single();
extract($row);
$mindustry = $industry_id;
$moccupation = $occupation;
$mposition = $position;
$mfirstname = $firstname;
$mmiddlename = $middlename;
$mpreferredname = $preferredname;
$mlastname = $lastname;
$mstaff = $staff;
$mdob = explode('-',$dob);
$md = $mdob[2];
$mm = $mdob[1];
$my = $mdob[0];
$mgender = $gender;
$mtitle = $title;
$mage = $age;
$mch = $checked;
$mstatus = $status;
$mtype = $client_type;
$dt = explode('-',$next_meeting);
$y = $dt[0];
$m = $dt[1];
$d = $dt[2];
$fdt = mktime(0,0,0,$m,$d,$y);
$mnextdate = date("d/m/Y",$fdt);
$hmdate = $next_meeting;

// populate industry drop down
$dbem->query("select * from ".$cltdb.".industries where sub_id = :subid");
$dbem->bind(':subid', $subid);
$rows = $dbem->resultset();
$industry_options = "<option value=\"0\">Select Industry</option>";
foreach ($rows as $row) {
	extract($row);
	if ($industry_id == $mindustry) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$industry_options .= '<option value="'.$industry_id.'"'.$selected.'>'.$industry.'</option>';
}

// populate status drop down
$dbem->query("select * from ".$cltdb.".status");
$rows = $dbem->resultset();
$status_options = "<option value=\" \">Select Status</option>";
foreach ($rows as $row) {
	extract($row);
	if ($status == $mstatus) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$status_options .= '<option value="'.$status.'"'.$selected.'>'.$status.'</option>';
}

/*
// populate type drop down
$dbem->query("select * from ".$cltdb.".client_types");
$rows = $dbem->resultset();
$type_options = "<option value=\" \">Select Client type</option>";
foreach ($rows as $row) {
	extract($row);
	if ($client_type == $mtype) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	$type_options .= '<option value="'.$client_type.'"'.$selected.'>'.$client_type.'</option>';
}
*/

// populate title list
    $arr = array('Mr', 'Mrs', 'Ms', 'Miss', 'Master', 'Dr', 'Professor', 'Reverend');
	$mtitle_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $mtitle) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$mtitle_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate day list
    $arr = array('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31');
	$mday_options = "<option value=\"00\">00</option>";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $md) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}	
		$mday_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate month list
    $arr = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
	$mmonth_options = "<option value=\"00\">00</option>";
    for($i = 0; $i < count($arr); $i++)	{
			if ($arr[$i] == $mm) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}	
		$mmonth_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate year list
	$thisyear = date("Y");
	if ($my == '0000') {
		$myear_options = "";
		for($i = $thisyear - 120; $i < $thisyear - 50; $i++)	{
			$myear_options .= '<option value="'.$i.'">'.$i.'</option>';
		}
		$myear_options .= "<option value=\"0000\" selected>0000</option>";
		for($i = $thisyear - 50; $i < $thisyear + 1; $i++)	{
			$myear_options .= '<option value="'.$i.'">'.$i.'</option>';
		}
	} else {
		$myear_options = "<option value=\"0000\">0000</option>";
		for($i = $thisyear - 120; $i < $thisyear + 1; $i++)	{
				if ($i == $my) {
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}	
			$myear_options .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
 		}
	}

// populate gender list
    $arr = array('Male', 'Female');
	$mgender_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $mgender) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$mgender_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}


// populate checked list
    $arr = array('No', 'Yes');
	$mchecked_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $mch) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$mchecked_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}


$_SESSION['s_memberid'] = $cluid;

$dbem->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Member</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script>

window.name = "editmembers";
var dialog;
var subid = <?php echo $subscriber; ?>;
var memid = <?php echo $cluid; ?>;

$(document).ready(function(){
	
   $dialog = $('<div></div>').dialog({autoOpen: false,title: 'Aide Memoir'});

 });



/**
 * DHTML date validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
 */
// Declaring valid date character, minimum year and maximum year
var dtCh= "/";
var minYear=1900;
var maxYear=2100;

function aidememoir(aide) {
	
	$dialog.dialog('close');
	$dialog.html(aide);
	$dialog.dialog('open');
	// prevent the default action, e.g., following a link
	return false;	
}

function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   } 
   return this
}

function isDate(dtStr){
	var daysInMonth = DaysArray(12)
	var pos1=dtStr.indexOf(dtCh)
	var pos2=dtStr.indexOf(dtCh,pos1+1)
	var strDay=dtStr.substring(0,pos1)
	var strMonth=dtStr.substring(pos1+1,pos2)
	var strYear=dtStr.substring(pos2+1)
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (pos1==-1 || pos2==-1){
		alert("The date format should be : dd/mm/yyyy")
		return false
	}
	if (strMonth.length<1 || month<1 || month>12){
		alert("Please enter a valid month")
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert("Please enter a valid day")
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("Please enter a valid 4 digit year between "+minYear+" and "+maxYear)
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert("Please enter a valid date")
		return false
	}
return true
}

function ValidateDate(dt){
	if (isDate(dt)==false){
		dt.focus()
		return false
	}
    return true
 }

// function hideAll()
//  hides a bunch of divs
//

function hideAllm() {
	changeObjectVisibility("mgeneral","hidden");
	changeObjectVisibility("maddresses","hidden");
	changeObjectVisibility("mquotes","hidden");
	changeObjectVisibility("mworkflow","hidden");
    changeObjectVisibility("mcommunications","hidden");
    changeObjectVisibility("mactivities","hidden");
    changeObjectVisibility("mdocuments","hidden");
    changeObjectVisibility("memails","hidden");
    changeObjectVisibility("mcomplaints","hidden");
    changeObjectVisibility("macats","hidden");
    changeObjectVisibility("mfinancials","hidden");
}

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}


function editad(uid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('editaddress.php?uid='+uid,'eddad','toolbar=0,scrollbars=1,height=450,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function delad(uid) {
  if (confirm("Are you sure you want to delete this address")) {
			$.get("includes/ajaxdeladdress.php", {tid: uid}, function(data){$("#madlist2").trigger("reloadGrid")});
	  }
}

function addad(uid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		var clientid = uid;
		window.open('addaddress.php?uid='+uid,'addad','toolbar=0,scrollbars=1,height=450,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editcomm(uid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('editcomm.php?uid='+uid,'edcom','toolbar=0,scrollbars=1,height=250,width=700,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function delcomm(uid) {
  if (confirm("Are you sure you want to delete this communication")) {
			$.get("includes/ajaxdelcomm.php", {tid: uid}, function(data){$("#mcommslist").trigger("reloadGrid")});
	  }
}

function addcomm(uid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		var clientid = uid;
		window.open('addcomm.php?uid='+uid+'&clientid='+clientid,'adcom','toolbar=0,scrollbars=1,height=250,width=700,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


function delassoc(asid) {
  if (confirm("Are you sure you want to delete this association")) {
			$.get("includes/ajaxdelassoc.php", {asid: asid}, function(data){$("#massociationslist").trigger("reloadGrid")});
	  }
}

function addassoc(uid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('addassoc.php?uid='+uid,'adcom','toolbar=0,scrollbars=1,height=400,width=700,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function delemail(uid) {
	  if (confirm("Are you sure you want to delete this email")) {
		window.open('delemail.php?uid='+uid,'delem','toolbar=0,scrollbars=1,height=10,width=10');
	  }
}


function delworkflow(uid) {
  if (confirm("Are you sure you want to delete this workflow stage")) {
			$.get("includes/ajaxdelworkflowstage.php", {tid: uid}, function(data){$("#mworkflowlist").trigger("reloadGrid")});
	  }
}

function addworkflow() {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('addworkflowstage.php','adcom','toolbar=0,scrollbars=1,height=200,width=500,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


function delctmember(id) {
	  if (confirm("Are you sure you want to delete this client type from this member")) {
		$.get("includes/ajaxdelctmember.php", {tid: id}, function(data){$("#mctlist").trigger("reloadGrid")});
	  }
}

function addct2member(uid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('addct2member.php?uid='+uid,'adctm','toolbar=0,scrollbars=1,height=200,width=500,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editactivity(uid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('editactivity.php?uid='+uid,'edact','toolbar=0,scrollbars=1,height=600,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function delactivity(uid) {
	  if (confirm("Are you sure you want to delete this activity")) {
		window.open('delactivity.php?uid='+uid,'delact','toolbar=0,scrollbars=1,height=10,width=10');
	  }
}


function addactivity() {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('addactivity.php','adact','toolbar=0,scrollbars=1,height=600,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function mapad(address) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('google.php?address='+address,'goog','toolbar=0,scrollbars=1,height=360,width=560,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


function adddoc() {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		
		window.open('adddocmultim.php','addoc','toolbar=0,scrollbars=1,height=670,width=800,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editdoc(d) {
	var readfile = "../documents/sub_"+subid+"/clients/"+d;
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
	window.open(readfile,'dna','toolbar=0,scrollbars=1,height=500,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function copypaste(d) {
	var readfile = "../documents/sub_"+subid+"/clients/"+d;
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
	window.open(readfile,'dna','toolbar=0,scrollbars=1,height=500,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function deldoc(uid) {
	  if (confirm("Are you sure you want to delete this document")) {
		$.get("includes/ajaxdeldocument.php", {tid: uid}, function(data){$("#mdoclist").trigger("reloadGrid")});
	  }
}

function addacat() {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('addacat.php','addat','toolbar=0,scrollbars=1,height=100,width=500,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function delacat(uid) {
	  if (confirm("Are you sure you want to delete this category from this member?")) {
		$.get("includes/ajaxdelacat.php", {tid: uid}, function(data){$("#acatlist").trigger("reloadGrid")});
	  }
}

function addquotecoy() {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('addquotecoy.php','addqtc','toolbar=0,scrollbars=1,height=200,width=400,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editquote(id) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('editquote.php?id='+id,'edqt','toolbar=0,scrollbars=1,height=640,width=1020,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function quote2so(ref) {
	if (confirm("Are you sure you want to convert this Quote to a Sales Order?")) {
		$.get("includes/ajaxquote2so.php", {ref: ref}, function(data){$("#mquotelist").trigger("reloadGrid")});
	}
}

function delqt(uid) {
  if (confirm("Are you sure you want to delete this quote")) {
			$.get("includes/ajaxdelquote.php", {tid: uid}, function(data){$("#mquotelist").trigger("reloadGrid")});
	  }
}

function emailtrad(rf) {
	var x = 0, y = 0; // default values	
	var tp = 'QOT';
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('PrintQuote.php?type='+tp+'&rf='+rf+'&doemail=Y','plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function printquote(rf) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	var type = 'QOT';
	window.open('PrintQuote.php?type='+type+'&rf='+rf,'plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function refreshquotegrid() {
	jQuery("#mquotelist").setGridParam({url:"getquotes.php"}).trigger("reloadGrid"); 
}

function viewemail(uid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('viewemail.php?uid='+uid+'&view=e','vem','toolbar=0,scrollbars=1,height=600,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function printnotesm() {
	var aprint = jQuery("#mactivitylist").getGridParam('selarrrow');	
	var astring = aprint.toString();
	var heading = "<?php echo $mfirstname." ".$mlastname; ?>";
	var x = 0, y = 0; // default values	
 	x = window.screenX +5;
  	y = window.screenY +265;	
		window.open('notes2pdf.php?notes='+astring+'&heading='+heading,'notepdf','toolbar=0,scrollbars=1,height=500,width=1020,resizable=yes,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


function printdetails(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('printdetails.php?uid='+uid,'pdet','toolbar=0,scrollbars=1,height=400,width=900,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


function emailmem(uid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +200;
		window.open('emailmem.php?commsid='+uid,'emmem','toolbar=0,scrollbars=1,height=730,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function editfinancials(uid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
		window.open('editfinancials.php?uid='+uid,'edfin','toolbar=0,scrollbars=1,height=300,width=800,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function exit() {
	var x = confirm('You are about to exit without saving this record. Is that what you want to do?');
	if (x == true) {
		this.close();
	} else {
		return false;
	}
}

function mdisplayitem(cnt) {
	document.getElementById('mcontent').value = '';
	if (cnt != '') {
		document.getElementById('mcontent').value = cnt;
	}
}

function pdisplayitem(cnt) {
	document.getElementById('pcontent').value = '';
	if (cnt != '') {
		document.getElementById('pcontent').value = cnt;
	}
}

// function getStyleObject(string) -> returns style object
//  given a string containing the id of an object
//  the function returns the stylesheet of that object
//  or false if it can't find a stylesheet.  Handles
//  cross-browser compatibility issues.
//
function getStyleObject(objectId) {
  // checkW3C DOM, then MSIE 4, then NN 4.
  //
  if(document.getElementById && document.getElementById(objectId)) {
	return document.getElementById(objectId).style;
   }
   else if (document.all && document.all(objectId)) {  
	return document.all(objectId).style;
   } 
   else if (document.layers && document.layers[objectId]) { 
	return document.layers[objectId];
   } else {
	return false;
   }
}


function changeObjectVisibility(objectId, newVisibility) {
    // first get a reference to the cross-browser style object 
    // and make sure the object exists
    var styleObject = getStyleObject(objectId);
    if(styleObject) {
	styleObject.visibility = newVisibility;
	return true;
    } else {
	// we couldn't find the object, so we can't change its visibility
	return false;
    }
}

function switchDivm(div_id,cell)
{

  var style_sheet = getStyleObject(div_id);
  
  if (style_sheet)
  {

	hideAllm();
    changeObjectVisibility(div_id,"visible");

	if (div_id == "mgeneral") {	
		jQuery("#massociationslist").setGridParam({url:"getassociations.php"}).trigger("reloadGrid"); 
	}	

	if (div_id == "mcommunications") {	
		jQuery("#mcommslist3").setGridParam({url:"getcomms.php"}).trigger("reloadGrid"); 
	}	

	if (div_id == "maddresses") {	
		jQuery("#madlist2").setGridParam({url:"getaddress.php"}).trigger("reloadGrid"); 
	}	

	if (div_id == "mquotes") {	
		jQuery("#mquotelist").setGridParam({url:"getquotes.php"}).trigger("reloadGrid"); 
	}	

	if (div_id == "mworkflow") {	
		jQuery("#mworkflowlist").setGridParam({url:"getworkflowm.php"}).trigger("reloadGrid"); 
	}	

	if (div_id == "mactivities") {	
		jQuery("#mactivitylist").setGridParam({url:"getactivities.php"}).trigger("reloadGrid"); 
	}	

	if (div_id == "mdocuments") {	
		jQuery("#mdoclist").setGridParam({url:"getdocs.php"}).trigger("reloadGrid"); 
	}			

	if (div_id == "memails") {	
		jQuery("#memaillist").setGridParam({url:"getemails.php"}).trigger("reloadGrid"); 
	}			

	if (div_id == "mcomplaints") {	
		jQuery("#mcomplaintlist").setGridParam({url:"getccomplaints.php"}).trigger("reloadGrid"); 
	}			

	if (div_id == "macats") {	
		jQuery("#acatlist").setGridParam({url:"getacats.php"}).trigger("reloadGrid"); 
	}			

	if (div_id == "mfinancials") {	
		jQuery("#mfinancials").setGridParam({url:"getfinancials.php"}).trigger("reloadGrid"); 
	}			

} else {
    alert("sorry, this only works in browsers that do Dynamic HTML - Member");
  }
}

function calcagem() {
	var d = new Date();
	var curr_day = d.getDate();
	var curr_month = d.getMonth()+1;
	var curr_year = d.getFullYear();
	var birthYear = document.getElementById('myear').value;
	var birthMonth = document.getElementById('mdobmonth').value;
	var birthDay = document.getElementById('mdobday').value;
	var age = curr_year - birthYear;
	if (curr_month < birthMonth || (curr_month == birthMonth && curr_day < birthDay)) {
		age = age - 1;
	}
	document.getElementById('mage').value = age;
}


function addreferred() {
		var m = document.getElementById('mreferred');
		var listlengthm = document.getElementById("mreferred").length;
		var newref = document.getElementById('mref').value;
	if (newref != '') {
		$.get("addreferred.php", {newref: newref}, function(data){
			document.getElementById('mreferred').add(new Option(newref,data), null);
			document.getElementById('mref').value = '';
		});
	}

}

function showCalculators() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +100;
	window.open('../includes/calculators.php','calc','toolbar=0,scrollbars=1,height=500,width=950,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


function viewdoc(d) {
	var readfile = "../documents/campaign/"+d;
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +100;
	window.open(readfile,'cdoc','toolbar=0,scrollbars=1,height=500,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function todo() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +275;
	window.open('../includes/updttodo.php','todo','toolbar=0,scrollbars=1,height=550,width=1000,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function post() {

	//add validation here if required.
	var lname = document.getElementById('mlastname').value;
	var ok = "Y";
	if (lname == "") {
		alert("Please enter a last name.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('edmem').submit();
	}
	
	
}

function viewaide(uid) {
		var x = 0, y = 0; // default values	
		x = window.screenX +5;
		y = window.screenY +200;
		window.open('viewaide.php?aidid='+uid,'vaid','toolbar=0,scrollbars=1,height=200,width=500,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


</script>
<!-- Deluxe Tabs -->
<noscript>
<a href="http://deluxe-tabs.com">Javascript Tabs Menu by Deluxe-Tabs.com</a>
</noscript>
<script type="text/javascript" src="tabs/client_tabs.files/dtabs.js"></script>
<!-- (c) 2009, http://deluxe-tabs.com -->
<style type="text/css">
<!--
.style1 {
	font-size: large
}
.star {
	color: #F00;
}
-->
</style>
</head>

<body>
<form name="edmem" id="edmem" method="post">
 	<input type="hidden" name="savebutton" id="savebutton" value="N">
  <div id="htop1" style="position:absolute;visibility:visible;top:1px;left:1px;height:160px;width:960px;border-width:thin thin thin thin; border-color:#999; border-style:solid;">
    <table width="950" border="0" cellspacing="0" align="left" bgcolor="<?php echo $bgcolor; ?>">
      <tr>
        <td colspan="2" align="left" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>">Member:- <?php echo substr($mfirstname,0,1).' '.$mlastname.' ('.$cluid.')'; ?></label></td>
        <td align="left" bgcolor="#FF0000"><input type="button" value="Save" name="save" onclick="post()">
          &nbsp;&nbsp;&nbsp;
          <input type="button" name="out" id="out" value="Exit" onClick="exit()"></td>
        <td bgcolor="<?php echo $bghead; ?>"  class="boxlabel"><span style="color: <?php echo $thfont; ?>">Checked &nbsp;</span>
          <select name="mchecked" id="mchecked">
            <?php echo $mchecked_options;?>
          </select></td>
        <td align="center" bgcolor="<?php echo $bghead; ?>"><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();">&nbsp;<img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
	  	  <td colspan="2" bgcolor="<?php echo $bghead; ?>" class="boxlabel"><label style="color: <?php echo $thfont; ?>"> Staff Member</label>&nbsp;
          	<select name="mstaff" id="mstaff"><?php echo $staff_options;?></select></td>
      </tr>
      <tr >
        <td class="boxlabel" ><label style="color: <?php echo $tdfont; ?>">Title</label></td>
        <td align="left"><select name="mtitle" id="mtitle">
            <?php echo $mtitle_options;?>
          </select></td>
        <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Date of Birth</label></td>
        <td colspan="4" class="boxlabelleft"><label style="color: <?php echo $tdfont; ?>">dd&nbsp;</label>
          <select name="mdobday" id="mdobday" tabindex="6">
            <?php echo $mday_options;?>
          </select>
          <label style="color: <?php echo $tdfont; ?>">&nbsp;mm</label>
          <select name="mdobmonth" id="mdobmonth" tabindex="7">
            <?php echo $mmonth_options;?>
          </select>
          <label style="color: <?php echo $tdfont; ?>">&nbsp;yyyy</label>
          <select name="myear" id="myear" tabindex="8" onChange="calcagem()">
            <?php echo $myear_options;?>
          </select>
          <label style="color: <?php echo $tdfont; ?>">&nbsp;Age</label>
          <input name="mage" type="text" id="mage" size="3" maxlength="3" value="<?php echo $mage; ?>" onChange="checkage('m')"></td>
      </tr>
      <tr>
        <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">First Name</label></td>
        <td align="left"><input name="mfirstname" id="mfirstname" type="text" size="25" maxlength="45" value="<?php echo $mfirstname; ?>" tabindex="1" onfocus="this.select();"></td>
        <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Gender</label></td>
        <td colspan="2" align="left"><select name="mgender" id="mgender">
            <?php echo $mgender_options;?>
          </select></td>
        <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Status</label></td>
        <td align="left"><select name="mstatus" id="mstatus">
          <?php echo $status_options;?>
        </select></td>
      </tr>
      <tr>
        <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Middle Name</label></td>
        <td align="left"><input name="mmiddlename" id="mmiddlename" type="text" size="25" maxlength="45" value="<?php echo $mmiddlename; ?>" tabindex="2" onfocus="this.select();"></td>
        <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Industry</label></td>
        <td colspan="2" align="left"><select name="mindustry" id="mindustry">
            <?php echo $industry_options;?>
          </select></td>
        <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">&nbsp;</label></td>
        <td align="left">&nbsp;</td>
      </tr>
      <tr>
        <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Last Name</label></td>
        <td align="left"><input name="mlastname" id="mlastname" type="text" size="25" maxlength="45" value="<?php echo $mlastname; ?>" tabindex="3" onfocus="this.select();"></td>
        <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Occupation</label></td>
        <td colspan="4" align="left"><input type="text" name="moccupation" id="moccupation" value="<?php echo $moccupation; ?>"></td>
      </tr>
      <tr>
        <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Preferred Name</label></td>
        <td align="left"><input name="mprefname" id="mprefname" type="text" size="25" maxlength="45" value="<?php echo $mpreferredname; ?>" tabindex="4" onfocus="this.select();"></td>
        <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Position</label></td>
        <td colspan="4" align="left"><input type="text" name="mposition" id="mposition"value="<?php echo $mposition; ?>"></td>
      </tr>
    </table>
  </div>
  <div id="hbot2" style="position:absolute;visibility:visible;top:168px;left:1px;height:210px;width:960px;border-width:thin thin thin thin; border-color:#999; border-style:solid;">
    <table width="950" border="0" align="left">
      <tr>
        <td><script type="text/javascript" src="tabs/client_tabsm.js"></script></td>
      </tr>
      <tr>
        <td><div id="mgeneral" >
            <table>
              <tr>
                <td><?php include "getassociations.php" ?></td>
                <td><?php include "getclienttype.php" ?></td>
              </tr>
            </table>
          </div>
          <div id="mcommunications" >
            <table>
              <tr>
                <td><?php include "getcommsm.php" ?></td>
              </tr>
            </table>
          </div>
           <div id="maddresses">
            <table>
              <tr>
                <td><?php include "getaddress.php" ?></td>
              </tr>
            </table>
          </div>
         <div id="mquotes">
            <table>
              <tr>
                <td><?php include "getquotes.php" ?></td>
              </tr>
            </table>
          </div>
           <div id="mworkflow">
            <table>
              <tr>
                <td><?php include "getworkflowm.php" ?></td>
              </tr>
            </table>
          </div>
         <div id="mactivities">
            <table>
              <tr>
                <td><?php include "getactivities.php" ?></td>
              </tr>
            </table>
          </div>
          <div id="mdocuments">
            <table>
              <tr>
                <td><?php include "getdocs.php" ?></td>
              </tr>
            </table>
          </div>
          <div id="memails">
            <table>
              <tr>
                <td><?php include "getemails.php" ?></td>
                <td><textarea name="mcontent" id="mcontent" cols="45" rows="9" readonly="readonly">&nbsp;</textarea></td>
              </tr>
            </table>
          </div>
          <div id="mcomplaints">
            <table>
              <tr>
                <td><?php include "getccomplaints.php" ?></td>
              </tr>
            </table>
          </div>
        <div id="macats">
            <table>
              <tr>
                <td><?php include "getacats.php" ?></td>
              </tr>
            </table>
         </div>
         <div id="mfinancials">
            <table>
              <tr>
                <td><?php include "getfinancials.php" ?></td>
              </tr>
            </table>
          </div>
          </td>
      </tr>
    </table>
  </div>
  <script>
		//hideAllm();
		switchDivm('mgeneral','ge');
    </script>
  <script>document.onkeypress = stopRKey;</script>
  <script>calcagem();</script>
</form>
<?php

	if(isset($_REQUEST['savebutton']) && $_REQUEST['savebutton'] == "Y") {
		include_once("../includes/cltadmin.php");
		$oCn = new cltadmin;	
		
		$oCn->sub_id = $subid;
		$oCn->checked = $_REQUEST['mchecked'];
		$oCn->staff = $_REQUEST['mstaff'];
		$oCn->firstname = $_REQUEST['mfirstname'];
		$oCn->lastname = $_REQUEST['mlastname'];
		$oCn->middlename = $_REQUEST['mmiddlename'];
		$oCn->preferredname = $_REQUEST['mprefname'];
		$oCn->title = $_REQUEST['mtitle'];
		$oCn->industry_id = $_REQUEST['mindustry'];
		//$oCn->clienttype = $_REQUEST['mclienttype'];
		$oCn->occupation = $_REQUEST['moccupation'];
		$mdob = $_REQUEST['myear'].'-'.$_REQUEST['mdobmonth'].'-'.$_REQUEST['mdobday'];
		$oCn->dob = $mdob;
		$oCn->position = $_REQUEST['mposition'];
		$oCn->gender = $_REQUEST['mgender'];
		$oCn->age = $_REQUEST['mage'];
		if ($_REQUEST['mstatus'] == ' ') {
			$oCn->status = "Lead";
		} else {
			$oCn->status = $_REQUEST['mstatus'];
		}
		$odt = $_REQUEST['mnextdate'];
		$t = explode('/',$odt);
		$d = $t[0];
		if (strlen($d) == 1) {
		  $d = '0'.$d;
		}
		$m = $t[1];
		if (strlen($m) == 1) {
		  $m = '0'.$m;
		}
		$y = $t[2];
		$ddate = $y.'-'.$m.'-'.$d;		  
		$oCn->nextmeeting = $ddate;		
		
		$oCn->uid = $cluid;
		
		$oCn->EditMember();
		
		include_once("../includes/DBClass.php");
		$dba = new DBClass();

		$hdate = date('Y-m-d');
		$ttime = strftime("%H:%M", time());
		
		$dba->query("insert into ".$cltdb.".audit (ddate,ttime,user_id,uname,sub_id,member_id,action) values (:ddate,:ttime,:user_id,:uname,:sub_id,:member_id,:action)");
		$dba->bind(':ddate', $hdate);
		$dba->bind(':ttime', $ttime);
		$dba->bind(':user_id', $user_id);
		$dba->bind(':uname', $sname);
		$dba->bind(':sub_id', $sub_id);
		$dba->bind(':member_id', $cluid);
		$dba->bind(':action', 'Edit Member');
		
		$dba->execute();
		$dba->closeDB();

		if ($from == 'clt') {
			echo '<script>';
			echo 'window.open("","updtclients").jQuery("#memlist2").trigger("reloadGrid");';
			echo 'this.close();';
			echo '</script>';
		}
		if ($from == 'fin') {
			echo '<script>';
			echo 'this.close();';
			echo '</script>';
		}
		
			
	}
	

?>
</body>
</html>

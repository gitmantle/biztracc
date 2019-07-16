<?php
session_start();
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$userid = $row['user_id'];
$uname = $row['uname'];

$compid = $_REQUEST['compid'];
$cltdb = $_SESSION['s_cltdb'];

$db->query("select * from ".$cltdb.".complaints where complaint_id = ".$compid);
$row = $db->single();
extract($row);
$snature = $nature;

$rdate = $received;

$adate = $acknowledged;

$pdate = $responded;
$cdate = $closed;

// populate via list
    $arr = array( 'Phone','Letter', 'Email', 'Verbal');
	$via_options = "";
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $via) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$via_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate complaints nature drop down
$db->query("select * from ".$cltdb.".complaint_nature order by nature");
$rows = $db->resultset();
$nature_options = '<option value="0">Select</option>';
foreach ($rows as $row) {
	extract($row);
		if ($snature == $nature) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}
	$nature_options .= '<option value="'.$nature.'"'.$selected.'>'.$nature.'</option>';
}

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");


?>
<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add Policy</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-1.8.4.custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
</head>
<script>

window.name = "addcomplainant";

	
function post() {

	//add validation here if required.
	var comp = document.getElementById('tcomplainant').value;
	var agin = document.getElementById('tagainst').value;
	var detail = document.getElementById('tdetails').value;
	
	var ok = "Y";
	if (comp == "") {
		alert("Please enter a complainant.");
		ok = "N";
		return false;
	}
	if (agin == "") {
		alert("Please enter who the complaint is against.");
		ok = "N";
		return false;
	}
	if (detail == "") {
		alert("Please enter details of the complaint.");
		ok = "N";
		return false;
	}
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('form1').submit();
	}
	
	
}
 
/**
 * DHTML date validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
 */
// Declaring valid date character, minimum year and maximum year
var dtCh= "/";
var minYear=1900;
var maxYear=2100;

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

function valdate(dt){
	if (isDate(dt.value)==false){
		dt.focus()
		return false
	}
    return true
 }

function findcomplainant() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('findcomplainant.php','fndcpt','toolbar=0,scrollbars=1,height=400,width=600,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}


function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

	 
</script>
<body>
<form name="form1" id="form1" method="post" >
  <input type="hidden" name="mid" id="mid" value=0>
  <input type="hidden" name="savebutton" id="savebutton" value="N">
  <table width="950" border="0" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td bgcolor="<?php echo $bghead; ?>">&nbsp;</td>
      <td align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>;font-size:18px;">Edit a Complaint</label></td>
      <td colspan="2" bgcolor="<?php echo $bghead; ?>" align="right"><label style="color: <?php echo $thfont; ?>;font-size:14px;">Reference No:&nbsp;<?php echo $complaint_id; ?></label></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Received</label></td>
      <td align="left"><input type="Text" id="recdate" name="recdate" maxlength="25" size="25" value="<?php echo $rdate; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">via</label></td>
      <td align="left"><select name="lvia" id="lvia">
          <option value="Phone">Phone</option>
          <option value="Letter">Letter</option>
          <option value="Email">Email</option>
          <option value="Verbal">Verbal</option>
        </select></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">From</label></td>
      <td align="left"><select name="lvia" id="lvia"><?php echo $via_options;?></select></td>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Nature of Complaint</label></td>
      <td align="left"><select name="lnature" id="lnature"><?php echo $nature_options;?></select></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Complainant</label></td>
      <td colspan="3" align="left"><input name="tcomplainant" type="text" id="tcomplainant" size="50" maxlength="50" readonly value="<?php echo $complainant; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Complaint Against</label></td>
      <td colspan="3" align="left"><input name="tagainst" type="text" id="tagainst" size="50" maxlength="50" value="<?php echo $against; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Complaint Taken By</label></td>
      <td colspan="3" align="left"><input name="ttaken" type="text" id="ttaken" size="50" maxlength="50" value="<?php echo $taken_by; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Complaint Details</label></td>
      <td colspan="3" align="left"><textarea name="tdetails" id="tdetails" cols="90" rows="3"><?php echo $details; ?></textarea></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Outcome Sought by Complainant</label></td>
      <td colspan="3" align="left"><textarea name="toutcome" id="toutcome" cols="90" rows="3"><?php echo $outcome; ?></textarea></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Acknowledgement Sent</label></td>
      <td colspan="3" align="left"><input type="Text" id="ackdate" name="ackdate" maxlength="25" size="25" value="<?php echo $adate; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Notes</label></td>
      <td colspan="3" align="left"><textarea name="tnotes" id="tnotes" cols="90" rows="3"><?php echo $notes; ?></textarea></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Response Sent</label></td>
      <td colspan="3" align="left"><input type="Text" id="resdate" name="resdate" maxlength="25" size="25" value="<?php echo $pdate; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Compensation</label></td>
      <td colspan="3" align="left"><input type="text" name="tcompensation" id="tcompensation" value="<?php echo $compensation; ?>"></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Further Action Details (if applicable)</label></td>
      <td colspan="3" align="left"><textarea name="tfurther" id="tfurther" cols="90" rows="3"><?php echo $further_action; ?></textarea></td>
    </tr>
    <tr>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Root Cause</label></td>
      <td align="left"><input name="tcause" type="text" id="tcause" size="50" maxlength="50" value="<?php echo $cause; ?>"></td>
      <td class="boxlabel"><label style="color: <?php echo $tdfont; ?>">Closed</label></td>
      <td align="left"><input type="Text" id="closedate" name="closedate" maxlength="25" size="25" value="<?php echo $cdate; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
    </tr>
    <tr>
      <td><input type="button" value="Save" name="save" onClick="post()"></td>
      <td colspan="3" align="left">&nbsp;</td>
    </tr>
  </table>
</form>
<script>document.onkeypress = stopRKey;</script>

 <script>
 	document.getElementById("recdate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
		}
	});
 </script>
 <script>
 	document.getElementById("ackdate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
		}
	});
 </script>
 <script>
 	document.getElementById("resdate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
		}
	});
 </script>
 <script>
 	document.getElementById("closedate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
		}
	});
 </script> 



<?php

	if($_REQUEST['savebutton'] == "Y") {
	  include_once("../includes/DBClass.php");
	  $db = new DBClass();

	  $agin = $_REQUEST['tagainst'];
	  $ack = $_REQUEST['ackdate'];
	  $cls = $_REQUEST['closedate'];
	  $paid = $_REQUEST['tcompensation'];
	  $med = $_REQUEST['lvia'];
	  $src = $_REQUEST['lsource'];
	  $nat = $_REQUEST['lnature'];
	  $tak = $_REQUEST['ttaken'];
	  $det = $_REQUEST['tdetails'];
	  $out = $_REQUEST['toutcome'];
	  $not = $_REQUEST['tnotes'];
	  $res = $_REQUEST['resdate'];
	  $cse = $_REQUEST['tcause'];
	  $fad = $_REQUEST['tfurther'];
	  
	  $db->query("update ".$cltdb.".complaints set against = :against,acknowledged = :acknowledged,closed = :closed,compensation = :compensation,medium = :medium,source = :source,nature = :nature,taken_by = :taken_by,details = :details,outcome = :outcome,Notes = :notes,responded = :responded,cause = :cause,further_action = :further_action where complaint_id = :complaint_id"); 
	  $db->bind(':against', $agin);
	  $db->bind(':acknowledged', $ack);
	  $db->bind(':closed', $cls);
	  $db->bind(':compensation', $paid);
	  $db->bind(':medium', $med);
	  $db->bind(':source', $src);
	  $db->bind(':nature', $nat);
	  $db->bind(':taken_by', $tak);
	  $db->bind(':details', $det);
	  $db->bind(':outcome', $out);
	  $db->bind(':notes', $not);
	  $db->bind(':responded', $res);
	  $db->bind(':cause', $cse);
	  $db->bind(':further_action', $fad);
	  $db->bind(':complaint_id', $compid);
	  
	  $db->execute();
  
	  $db->closeDB();
	  
?>
	  <script>
	  window.open("","complaints").jQuery("#complaintslist").trigger("reloadGrid");
	  this.close();
	  </script>
<?php		
			
	}

?>
</body>
</html>

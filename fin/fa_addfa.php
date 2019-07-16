<?php
session_start();

$findb = $_SESSION['s_findb'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$fagroup = $_SESSION['s_faccgroup'];
$db->query('select * from '.$findb.'.assetheadings where hcode = "'.$fagroup.'"');
$row = $db->single();
extract($row);

$db->query('select * from '.$findb.'.branch order by branchname');
$rows = $db->resultset();
// populate branches list
$branch_options = "<option value=\"\">Select Branch</option>";
foreach ($rows as $row) {
	extract($row);
	$branch_options .= "<option value=\"".$branch."\">".$branchname."</option>";
}

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

$ddate = date("d/m/Y");
$ddateh = date("Y-m-d");
$cost = 0.00;

?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Add an Asset</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script>
	 
function post() {

	//add validation here if required.
	var ass = document.getElementById('asset').value;
	var br = document.getElementById('branch').value;
	
	var ok = "Y";
	if (ass == "") {
		alert("Please enter an asset.");
		ok = "N";
		return false;
	}
	if (br == "") {
		alert("Please enter a branch.");
		ok = "N";
		return false;
	}
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('form1').submit();
	}
	
}	 
	 

</script>


<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>


<body>
<div id="bwin">
<form name="form1" id="form1" method="post" >
 <input type="hidden" name="savebutton" id="savebutton" value="N">

  <table width="700" border="0" align="center">
    <tr>
      <td colspan="2"><div align="center" class="style1"><u>Add an Asset </u></div></td>
    </tr>
    <tr>
      <td class="boxlabel">Group</td>
        <td class="boxlabelleft"> <?php echo $heading;?></td>	  
    </tr>
    <tr>
      <td class="boxlabel">Asset</td>
      <td><input name="asset" type="text" id="asset"  size="70" maxlength="70"></td>
    </tr>
	<tr>
	<td class="boxlabel">Branch</td>
	<td><select name="branch" id="branch"><?php echo $branch_options; ?></select></td>
	</tr>	
    <tr>
      <td class="boxlabel">Date Purchased</td>
        <td><input type="Text" id="bought" name="bought" value="<?php echo $ddateh; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j,Y">
	</td>
	</tr>
    <tr>
      <td class="boxlabel">Rate of Depreciation</td>
      <td><input type="text" name="rate" id="rate" value="0" >% </td>
    </tr>
    <tr>
      <td class="boxlabel">Method of Depreciation</td>
      <td><select name="way">
        <option value="D">Diminishing</option>
        <option value="F">Fixed</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Last Year's Book Value </td>
      <td><input type="text" name="lybv" value="0"></td>
    </tr>
    <tr>
      <td class="boxlabel">Blocked</td>
      <td><select name="blocked">
        <option value="N">No</option>
        <option value="Y">Yes</option>
      </select></td>
    </tr>
    <tr>
      <td class="boxlabel">Notes</td>
      <td><textarea name="notes" cols="40" rows="5" value=""></textarea></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="right"> <input type="button" value="Save" name="save" onClick="post()"  ></td>
    </tr>
  </table>
  
 <script>
 	document.getElementById("bought").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
			ajaxCheckTransDate();
		}
	});
 </script>  
  
</form>
</div>

<?php

	if($_REQUEST['savebutton'] == "Y") {
	
		  include_once("includes/accaddacc.php");
		  $oAss = new accaddacc;	
		  
		  $oAss->coy = $coyid;
		  $oAss->ahcode = $fagroup;
		  $oAss->abranch = $_REQUEST['branch'];
		  $oAss->aname = ucwords(strtolower($_REQUEST['asset']));
		  $oAss->ablocked = $_REQUEST['blocked'];
		  $oAss->away = $_REQUEST['way'];
		  $oAss->arate = $_REQUEST['rate'];
				
		  $ddate = $_REQUEST['bought'];		  
		  $oAss->abought = $ddate;
				
				
		  $oAss->anotes = $_REQUEST['notes'];
		  $oAss->alastyrbv = $_REQUEST['lybv'];
  
		  $oAss->AddAsset();

		  ?>
		  <script>
		  window.open("","updtfaaccs").jQuery("#faacclist").trigger("reloadGrid");
		  this.close();
		  </script>
		  <?php
		
	}

?>


</body>
</html>

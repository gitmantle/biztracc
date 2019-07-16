<?php
session_start();
$usersession = $_SESSION['usersession'];

$ddate = date("d/m/Y");
$hdate = date("Y-m-d");

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

// populate staff drop down
$db->query("select * from users where sub_id = ".$subscriber." order by ulname");
$rows = $db->resultsetNum();
$staff_options = "<option value=\"0\">Select Staff Member</option>";
foreach ($rows as $row) {
	extract($row);
	$selected = '';
	$staff_options .= '<option value="'.$row[2].' '.$row[3].'"'.$selected.'>'.$row[2].' '.$row[3].'</option>';
}

$cltdb = $_SESSION['s_cltdb'];

// populate industries drop down
$db->query("select * from ".$cltdb.".industries ");
$rows = $db->resultset();
$industry_options = "<option value=\"0\">Select</option>";
foreach ($rows as $row) {
	extract($row);
	$industry_options .= '<option value="'.$industry_id.'">'.$industry.'</option>';
}

// populate accounting category drop down
$db->query("select * from ".$cltdb.".acccats ");
$rows = $db->resultset();
$acat_options = "<option value=\"\">Select</option>";
foreach ($rows as $row) {
	extract($row);
	$acat_options .= '<option value="'.$acccat.'">'.$acccat.'</option>';
}

// populate status drop down
$db->query("select * from ".$cltdb.".status");
$rows = $db->resultset();
$status_options = "<option value=\" \">Select</option>";
foreach ($rows as $row) {
	extract($row);
	$status_options .= '<option value="'.$status.'">'.$status.'</option>';
}

// populate work flow drop down
$db->query("select * from ".$cltdb.".workflow order by porder");
$rows = $db->resultset();
$process_options = "<option value=\" \">Select</option>";
foreach ($rows as $row) {
	extract($row);
	$process_options .= '<option value="'.$process.'">'.$process.'</option>';
}

// populate campaign drop down
$db->query("select campaign_id,name from ".$cltdb.".campaigns");
$rows = $db->resultset();
$campaign_options = '<option value="0">Select Campaign</option>';
foreach ($rows as $row) {
	extract($row);
	$campaign_options .= '<option value="'.$campaign_id.'" >'.$name.'</option>';
}

// populate type drop down
$db->query("select * from ".$cltdb.".client_types");
$rows = $db->resultset();
$type_options = "<option value=\" \">Select Client type</option>";
foreach ($rows as $row) {
	extract($row);
	$type_options .= '<option value="'.$client_type.'">'.$client_type.'</option>';
}

$db->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Update Members</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />


<script type="text/javascript">

window.name = "updtclients";

</script>
</head>
<body>
<div style="width: 1000px;">
  <form name="updtclt" id="updtclt" method="post" action="">
    <table width="950" border="0">
      <tr>
        <td>Last Name
          <input type="text" id="searchlastname" size="10" onkeypress="doSearch1mem()" /></td>
        <td colspan="2">Phone
          <input type="text" id="searchph" size="10"/>
          <img src="../images/Search.gif" width="16" height="16" alt="Search" title="Search Phone" onclick="doSearch2mem()" /></td>
        <td colspan="2">&nbsp;</td>
        <td><input type="button" name="bunsearch" id="bunsearch" value="Reset" onclick="sresetmem()" /></td>
        <td>&nbsp;</td>
        <td><input type="button" name="sfilters" id="sfilters" value="Filters" onclick="showfiltersmem()"/></td>
        <td><input type="button" name="bufilter" id="bufilter" value="Unfilter" onclick="unfiltermem('u')"/></td>
      </tr>
      <tr>
        <td colspan="5">&nbsp;</td>
        <td><input type="button" name="printm" id="printm" value="Mailing List" onclick="printlistm()" /></td>
        <td><input type="button" name="printv" id="printv" value="Visiting List" onclick="printlistv()" /></td>
        <td><input type="button" name="emaillist" id="emaillist" value="Email List" onclick="printliste()" /></td>
        <td><input type="button" name="bnotes" id="bnotes" value="Bulk Note"  onclick="bulknotes()"/></td>
      </tr>
      <tr>
        <td colspan="5"><table align="left" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><?php include "getMembers.php" ?></td>
            </tr>
            <tr>
              <td align="left"><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();">&nbsp;
              <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
            </tr>
          </table></td>
        <td valign="top" colspan="4"><?php include "getCoClientNN.php" ?></td>
      </tr>
    </table>
  </form>
</div>
  <div id="filterpage" style="position:absolute;visibility:hidden;top:5px;left:236px;height:530px;width:700px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:#0F0; border-style:solid;">
    <table width="690" align="center" border="0" cellspacing="1" cellpadding="1">
      <tr>
        <th align="left" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $thfont; ?>">Filter by:-</label></th>
        <th align="right" bgcolor="<?php echo $bghead; ?>"><input type="button" name="bfilter" id="bfilter" value="Filter" onclick="filtermem()"/></th>
        <th align="right" bgcolor="<?php echo $bghead; ?>"><input type="button" name="bclose" id="bclose" value="Close" onclick="closefilters()"/></th>
        <th align="center" bgcolor="<?php echo $bghead; ?>"><label style="color: <?php echo $tdfont; ?>">Not</label></th>
      </tr>
      <tr>
        <td align="left"><label style="color: <?php echo $tdfont; ?>">Suburb</label></td>
        <td colspan="2" align="left"><input type="text" id="searchsuburb" size="25" /></td>
        <td align="center"><input name="notsuburb" type="checkbox" id="notsuburb" /></td>
      </tr>
      <tr>
        <td align="left"><label style="color: <?php echo $tdfont; ?>">Town</label></td>
        <td colspan="2" align="left"><input type="text" id="searchtown" size="25" /></td>
        <td align="center"><input name="nottown" type="checkbox" id="nottown" value="Y" /></td>
      </tr>
      <tr>
        <td align="left"><label style="color: <?php echo $tdfont; ?>">Post code</label></td>
        <td colspan="2" align="left"><input type="text" id="searchpostcode" size="10" /></td>
        <td align="center"><input name="notpostcode" type="checkbox" id="notpostcode" /></td>
      </tr>
      <tr>
        <td align="left"><label style="color: <?php echo $tdfont; ?>">Industry</label></td>
        <td colspan="2" align="left"><select name="searchindustry" id="searchindustry">
            <?php echo $industry_options;?>
          </select></td>
        <td align="center"><input name="notindustry" type="checkbox" id="notindustry" /></td>
      </tr>
      <tr>
        <td align="left"><label style="color: <?php echo $tdfont; ?>">Occupation</label></td>
        <td colspan="2" align="left"><input type="text" id="searchoccupation" size="25" /></td>
        <td align="center"><input name="notoccupation" type="checkbox" id="notoccupation" value="Y" /></td>
      </tr>
      <tr>
        <td align="left"><label style="color: <?php echo $tdfont; ?>">Position</label></td>
        <td colspan="2" align="left"><input type="text" id="searchposition" size="25" /></td>
        <td align="center"><input name="notposition" type="checkbox" id="notposition" value="Y" /></td>
      </tr>
      <tr>
        <td align="left"><label style="color: <?php echo $tdfont; ?>">Aged from</label></td>
        <td colspan="2" align="left"><input type="text" name="dfrom" id="dfrom" size="2" maxlength="2" value="0"/>
          to
          <input type="text" name="dto" id="dto" size="2" maxlength="2" value="0"/></td>
        <td align="center"><input name="notage" type="checkbox" id="notage" value="Y" /></td>
      </tr>
      <tr>
      <td align="left"><label style="color: <?php echo $tdfont; ?>">Birth Month</label></td>
      <td align="left"><select name="lbirthmonth" id="lbirthmonth">
          <option value="00">Select Month</option>
          <option value="01">JAN</option>
          <option value="02">FEB</option>
          <option value="03">MAR</option>
          <option value="04">APR</option>
          <option value="05">MAY</option>
          <option value="06">JUN</option>
          <option value="07">JUL</option>
          <option value="08">AUG</option>
          <option value="09">SEP</option>
          <option value="10">OCT</option>
          <option value="11">NOV</option>
          <option value="12">DEC</option>
        </select></td>
      <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td align="left"><label style="color: <?php echo $tdfont; ?>">Checked</label></td>
        <td colspan="2" align="left"><input name="recchecked" type="checkbox" id="recchecked" onChange="rcheck()"/></td>
        <td align="center"><input type="checkbox" name="notchecked" id="notchecked" onChange="ncheck()"/></td>
      </tr>
      <tr>
        <td align="left"><label style="color: <?php echo $tdfont; ?>">Text in Notes</label></td>
        <td colspan="3" align="left"><input name="notestring" type="text" id="notestring" title="Enter a word or phrase to seach for in notes" size="70" maxlength="200" value = ""/></td>
      </tr>
      <tr>
        <td align="left"><label style="color: <?php echo $tdfont; ?>">Associate</label></td>
        <td colspan="2" align="left"><select name="association" id="association">
            <option value=" ">Select Association</option>
            <option value="Child">Child</option>
            <option value="Parent">Parent</option>
            <option value="Grandchild">Grandhild</option>
            <option value="Grandarent">Grandparent</option>
            <option value="Relative">Relative</option>
            <option value="Company">Company</option>
            <option value="Stakeholder">Stakeholder</option>
            <option value="Associate">Associate</option>
            <option value="Accountant">Accountant</option>
            <option value="Lawyer">Lawyer</option>
            <option value="Account Client">Account Client</option>
            <option value="Legal Client">Legal Client</option>
            <option value="Business Partner">Business Partner</option>
            <option value="Employer">Employer</option>
            <option value="Employee">Employee</option>
            <option value="Contractor">Contractor</option>
            <option value="Contractee">Contractee</option>
            <option value="Guardian">Guardian</option>
            <option value="Ward">Ward</option>
          </select>
        <td align="center">&nbsp;</td>
      </tr>
      <tr>
        <td align="left">Represented by</td>
        <td colspan="2" align="left"><select name="searchrep" id="searchrep">
          <?php echo $staff_options;?>
        </select>      
        <td align="center">&nbsp;</td>
      </tr>
      <tr>
      <td align="left">Gender</td>
      <td align="left"><select name="searchgender" id="searchgender">
          <option value=" ">Select</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select></td>
        <td align="center">&nbsp;</td>
      </tr>
      <tr>
        <td align="left">Status</td>
        <td colspan="2" align="left"><select name="searchstatus" id="searchstatus">
          <?php echo $status_options;?>
        </select>      
        <td align="center"><input name="notstatus" type="checkbox" id="notstatus" /></td>
      </tr>
      <tr>
        <td align="left"><label style="color: <?php echo $tdfont; ?>">Acc. Category</label></td>
        <td colspan="2" align="left"><select name="searchacat" id="searchacat">
            <?php echo $acat_options;?>
          </select></td>
        <td align="center">&nbsp;</td>
      </tr>
      <tr>
      <td align="left"><label style="color: <?php echo $tdfont; ?>">Campaign</label></td>
      <td align="left" colspan="2"><select name="searchcampaign" id="searchcampaign">
          <?php echo $campaign_options;?>
        </select></td>
      <td align="center"><input name="notcampaign" type="checkbox" id="notcampaign" /></td>
    </tr>
    <tr>
      <td align="left"><label style="color: <?php echo $tdfont; ?>">Campaign Stage</label></td>
      <td align="left" colspan="2"><select name="campaignstage" id="campaignstage">
          <option value=" ">Select Stage</option>
          <option value="Not Available">Not Available</option>
          <option value="Callback">Callback</option>
          <option value="Not Interested">Not Interested</option>
          <option value="Appointment">Appointment</option>
          <option value="Rep Callback">Rep Callback</option>
          <option value="Rep Email">Rep Email</option>
          <option value="See Notes">See Notes</option>
        </select></td>
      <td align="center"><input name="notcampaignstage" type="checkbox" id="notcampaignstage" /></td>
    </tr>
      <tr>
        <td align="left"><label style="color: <?php echo $tdfont; ?>">Workflow Stage</label></td>
        <td colspan="2" align="left"><select name="searchworkflow" id="searchworkflow">
            <?php echo $process_options;?>
          </select></td>
        <td colspan="4" align="centre">Outstanding more than &nbsp; <input name="wfsdays" type="text" id="wfsdays" size="4" value="0"/>&nbsp; days. </td>
      </tr>
      <tr>
        <td align="left"><label style="color: <?php echo $tdfont; ?>">Client type</label></td>
        <td colspan="2" align="left"><select name="searchclienttype" id="searchclienttype">
            <?php echo $type_options;?>
          </select></td>
      </tr>

    </table>
  </div>
<script>
	document.getElementById('printm').style.visibility = 'hidden';
	document.getElementById('printv').style.visibility = 'hidden';
	document.getElementById('emaillist').style.visibility = 'hidden';
	document.getElementById('bnotes').style.visibility = 'hidden';
	document.getElementById('searchlastname').focus()

</script>
</body>
</html>
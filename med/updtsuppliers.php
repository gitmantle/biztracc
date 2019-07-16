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

// populate staff drop down
$db->query("select * from users where sub_id = ".$subscriber." order by ulname");
$rows = $db->resultsetNum();
$staff_options = "<option value=\"0\">Select Staff Member</option>";
foreach ($rows as $row) {
	extract($row);
	$selected = '';
	$staff_options .= '<option value="'.$row[2].' '.$row[3].'"'.$selected.'>'.$row[2].' '.$row[3].'</option>';
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
<script language="javascript" type="text/javascript" src="../includes/datetimepick/datetimepicker.js"></script>


<script type="text/javascript">

window.name = "updtsuppliers";

</script>
</head>
<body>
<div style="width: 1000px; height: 420px;">
  <form name="updtclt" id="updtclt" method="post" action="">
    <table width="950" border="0">
      <tr>
        <td>Company Name
          <input type="text" id="searchlastname" size="10" onkeypress="doSearch1mem()" /></td>
        <td colspan="2">Phone
          <input type="text" id="searchph" size="10"/>
          <img src="images/Search.gif" width="16" height="16" alt="Search" title="Search Phone" onclick="doSearch2mem()" /></td>
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
              <td><?php include "getSuppliers.php" ?></td>
            </tr>
          </table></td>
        <td valign="top" colspan="4"><?php include "getCoClientNN.php" ?></td>
      </tr>
    </table>
  </form>
</div>
  <div id="filterpage" style="position:absolute;visibility:hidden;top:5px;left:236px;height:475px;width:700px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:#0F0; border-style:solid;">
    <table width="690" align="left" border="0" cellspacing="1" cellpadding="1">
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
        <td align="left"><label style="color: <?php echo $tdfont; ?>">Occupation</label></td>
        <td colspan="2" align="left"><input type="text" id="searchoccupation" size="25" /></td>
        <td align="center"><input name="notoccupation" type="checkbox" id="notoccupation" value="Y" /></td>
      </tr>
      <tr>
        <td align="left"><label style="color: <?php echo $tdfont; ?>">Aged between</label></td>
        <td colspan="2" align="left"><input type="text" name="dfrom" id="dfrom" size="2" maxlength="2" value="0"/>
          and
          <input type="text" name="dto" id="dto" size="2" maxlength="2" value="0"/></td>
        <td align="center"><input name="notage" type="checkbox" id="notage" value="Y" /></td>
      </tr>
      <tr>
      <td align="left"><label style="color: <?php echo $tdfont; ?>">Birth Month</label></td>
      <td align="left"><select name="lbirthmonth" id="lbirthmonth">
          <option value="00">Select</option>
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
      <td align="left">Gender</td>
      <td align="left"><select name="searchgender" id="searchgender">
          <option value=" ">Select</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select></td>
        <td align="center">&nbsp;</td>
      </tr>
      <tr>
        <td align="left">Depot</td>
        <td colspan="2" align="left"><select name="searchdepot" id="searchdepot">
          <?php echo $depot_options;?>
        </select>      
        <td align="center"><input name="notdepot" type="checkbox" id="notdepot" /></td>
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
<?php
session_start();
$usersession = $_SESSION['usersession'];
date_default_timezone_set($_SESSION['s_timezone']);

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$findb = $_SESSION['s_findb'];

// populate title list
$arr = array('Select Country','Australia', 'New Zealand', 'United Kingdom');
$ctry_options = '';
for($i = 0; $i < count($arr); $i++)	{
	$ctry_options .= '<option value="'.$arr[$i].'">'.$arr[$i].'</option>';
}

$db->query("select gstfiled,gstperiod,gstno from ".$findb.".globals");
$row = $db->single();
extract($row);

$dt = explode('-',$gstfiled);
$month = $dt[1];
$day = $dt[2];
$year = $dt[0];
$gstdt = time(0,0,0,$month,$day+1,$year);
$gstdate = date('Y-m-d',$gstdt);

switch ($gstperiod) {
	case '1 Month':
		$gstp = 1;
		break;
	case '2 Months':
		$gstp = 2;
		break;
	case '3 Months':
		$gstp = 3;
		break;
	case '6 Months':
		$gstp = 6;
		break;
	case 'Annually':
		$gstp = 12;
		break;
	default:
		$gstp = 0;
		break;
}

$fdate = date("Y-m-d", strtotime( date( $gstfiled )." + 1 day"));
$edate = date("Y-m-d", strtotime( date( $gstfiled )." + ".$gstp." months"));

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>GST Report</title>
<link rel="stylesheet" type="text/css" href="../includes/flatpickr_date/dist/flatpickr.min.css">
<script src="../includes/flatpickr_date/dist/flatpickr.js"></script>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>

	window.name = "rep_gst";

</script>

</head>

<body>
<form name="form1" method="post" >
	 <input type="hidden" name="gstno" id="gstno" value=<?php echo $gstno; ?>>
<br>
  <table width="400" border="0" align="center" cellpadding="3" cellspacing="1">
    <tr>
      <td colspan="2" align="center"><strong><u></u> <?php echo $_SESSION['s_tradtax']; ?></u></strong></td>
      </tr>
    <tr>
      <td colspan="2" align="left" class="style2">Select dates for Tax report </td>
      </tr>
    <tr>
      <td align="left">From</td>
      <td><input type="Text" id="fdate" name="fdate" maxlength="25" size="25" value="<?php echo $fdate; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>  
      </tr>
    <tr>
      <td align="left"><span class="style2">To</span></td>
      <td><input type="Text" id="edate" name="edate" maxlength="25" size="25" value="<?php echo $edate; ?>" class="flatpickr" data-alt-input="true" data-alt-format="F j, Y"></td>
      </tr>
      
    <tr>
        <td align="left"><span class="style2">Tax for which Country</span></td>
    	<td><select name="country" id="country" onChange="ctry()"><?php echo $ctry_options;?></select></td>
    </tr>  
  </table>  
    <div id="ausparams" style="visibility:hidden;">
      <table width="400" border="0" align="center" cellpadding="3" cellspacing="1">
          <td colspan="2" align="left">&nbsp;</td>
        <tr>
          <td colspan="2" align="left">Please ensure you have entered all relevant transactions, sales, purchases, payroll etc. to the accounts before producing the BAS form.</td>
        </tr>
        <tr>
          <td colspan="2" align="left">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" align="left">If applicable:-</td>
        </tr>
        <tr>
          <td align="left">Adjustment to Sales</td>
          <td><input type="text" name="sadj" id="sadj" /> incl Tax  (G7)</td>
        </tr>
        <tr>
          <td align="left">Adjustment to Purchases</td>
          <td><input type="text" name="padj" id="padj" /> incl Tax (G18)</td>
        </tr>
        <tr>
          <td align="left">Estimated private purchases</td>
          <td><input type="text" name="est" id="est" /> (G15)</td>    
        </tr>  
       </table>  
    </div>
  <table width="400" border="0" align="center" cellpadding="3" cellspacing="1">
    <tr>
      <td>&nbsp;</td>
      <td align="right"><input type="button" align="right" value="Run" name="run" onclick="gst();"></td>
      </tr>
  </table>
  
 <script>
 	document.getElementById("edate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
		}
	});
 </script>
 <script>
 	document.getElementById("fdate").flatpickr({
		onChange: function(dateObj, dateStr, instance) {
		}
	});
 </script>
        
  
</form>
</body>
</html>

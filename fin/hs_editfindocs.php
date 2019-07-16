<?php
session_start();

include_once("../includes/DBClass.php");
$db = new DBClass();

$findb = $_SESSION['s_findb'];

$db->query("select coyname,bustype1,bustype2,ad1,ad2,ad3,boxno,po,email,telno,faxno,uid from ".$findb.".globals");
$globals = $db->single(); 
extract($globals);

$db->closeDB();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<title>Edit Financial Documents</title>
<script type="text/javascript">

window.name = "hs_editfindocs";

</script>
</head>
<body>

<table border="0" cellpadding="0" cellspacing="0" width="885">
  <tr bgcolor="<?php echo $bghead; ?>">
    <td><label style="color: <?php echo $thfont; ?>"><strong>Edit Financial Documents for <?php echo $coyname; ?> </strong></label></td>
    <td>&nbsp;&nbsp;&nbsp;</td>
  	<td><label style="color: <?php echo $thfont; ?>"><strong>PDF Grid Template</strong></label></td>
  </tr>
  <tr>
  	<td><p>Select the financial document to edit:-</p></td>
    <td>&nbsp;&nbsp;&nbsp;</td>
    <td><p>If required, print a template, preferably on tracing paper, to assist in laying out pdf documents.</p></td>
  </tr>
  <tr>
  	<td>
      <p>
        <label>
          <input type="radio" name="findocs" value="qot" id="findocs_0">
          Quote</label>
        <br>
        <label>
          <input type="radio" name="findocs" value="s_o" id="findocs_1">
          Sales Order</label>
        <br>
        <label>
          <input type="radio" name="findocs" value="p_o" id="findocs_2">
          Purchase Order</label>
        <br>
        <label>
          <input type="radio" name="findocs" value="grn" id="findocs_3">
          Goods Received</label>
        <br>
        <label>
          <input type="radio" name="findocs" value="inv" id="findocs_4">
          Invoice</label>
        <br>
        <label>
          <input type="radio" name="findocs" value="c_s" id="findocs_5">
          Cash Sale</label>
        <br>
        <label>
          <input type="radio" name="findocs" value="c_p" id="findocs_5">
          Cash Purchase</label>
        <br>
        <label>
          <input type="radio" name="findocs" value="rec" id="findocs_6">
          Receipt</label> 
        <br>
        <label>
          <input type="radio" name="findocs" value="crn" id="findocs_7">
          Credit Note</label>
        <br>
        <label>
          <input type="radio" name="findocs" value="pay" id="findocs_5">
          Payment</label>
        <br>
        <label>
          <input type="radio" name="findocs" value="ret" id="findocs_8">
          Goods Returned</label>
        <br>
        <label>
          <input type="radio" name="findocs" value="req" id="findocs_9">
          Requisition</label>
        <br>
        <label>
          <input type="radio" name="findocs" value="pkl" id="findocs_5">
          Picking List</label>
        <br>
        <label>
          <input type="radio" name="findocs" value="d_n" id="findocs_5">
          Delivery Note</label>
          
        <br>
    </p></td>
    <td>&nbsp;&nbsp;&nbsp;</td>
    <td><input type="button" value="Print template" name="run"  onClick="pdfgrid()" ></td>
    
    <tr>
    	<td><p><input name="bedit" type="button" value="Edit Document" onClick="editfindoc()"></p></td>
     <td>&nbsp;&nbsp;&nbsp;</td>
    <td>&nbsp;&nbsp;&nbsp;</td>
   </tr>
  </tr>
</table>
</body>
</html>

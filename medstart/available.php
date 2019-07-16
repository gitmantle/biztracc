<?php
session_start();

if( isset( $_SESSION['counter'] ) )  {
     $_SESSION['counter'] += 1;
} else {
     $_SESSION['counter'] = 1;
}

$sessionid = session_id().$_SESSION['counter'];

error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);

//$_SESSION['s_server'] = "vmcp13.digitalpacific.com.au";
$_SESSION['s_server'] = "localhost";
$_SESSION['s_admindb'] = "cmedsuco_cmeds4u";

require_once('db1.php');
$dbase = $_SESSION['s_admindb'];
mysql_select_db($dbase) or die(mysql_error());

$thisyear = date('Y');
$today = date("Y-m-d");
				
$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$avtable = 'ztmp_available';
$_SESSION['s_rectable'] = $avtable;

// Select 1 from table_name will return false if the table does not exist.
$val = mysql_query('select 1 from '.$avtable);

if($val == FALSE) {
	$query = "create table ".$avtable." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, guid varchar(50), item varchar(100), cost decimal(16,2) default 0, unit varchar(20), noinunit decimal(9,2), gitem varchar(100), gcost decimal(16,2) default 0, gunit varchar(20), gnoinunit decimal(9,2), xref int(11), entered date) engine myisam"; 
	$calc = mysql_query($query) or die(mysql_error().' '.$query);
}

$q = "select taxpcent from taxtypes where defgst = 'Yes'";
$r = mysql_query($q) or die(mysql_error());
$numrows = mysql_num_rows($r);
if ($numrows == 1) {
	$row = mysql_fetch_array($r);
	extract($row);	
	$tpcent = $taxpcent;
} else {
	$q = "select taxpcent from taxtypes where uid = 1";
	$r = mysql_query($q) or die(mysql_error());
	$row = mysql_fetch_array($r);
	extract($row);	
	$tpcent = $taxpcent;
}

$q = "select pcent from stkpricepcent where uid = 1";
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);	
$spcent = $pcent;

$markup = 1 + $spcent/100;
$tax = 1 + $tpcent/100;

$weekago = date('Y-m-d', strtotime('-1 days'));
$q = "delete from ".$avtable." where entered < '".$weekago."'";
$r = mysql_query($q) or die(mysql_error());

$q = "insert into ".$avtable." (guid, item, cost, unit, noinunit, xref, entered) select '".$sessionid."',item,case setsell when 0 then (avgcost * ".$markup.") * ".$tax." else (setsell * ".$tax.") end as cost,unit,noinunit,xref,'".$today."' from stkmast where generic = 'N' and active = 'Yes'";
$r = mysql_query($q) or die(mysql_error().' '.$q);

$q = "select uid,xref from ".$avtable." where guid = '".$sessionid."'";
$r = mysql_query($q) or die(mysql_error().' '.$q);
while ($row = mysql_fetch_array($r)) {
	extract($row);
	$sid = $uid;
	$xr = $xref;
	if ($xr > 0) {
		$qs = "select item,unit,noinunit,case setsell when 0 then (avgcost * ".$markup.") * ".$tax." else (setsell * ".$tax.") end as cost from stkmast where itemid = ".$xr;
		$rs = mysql_query($qs) or die(mysql_error().' '.$qs);
		$row = mysql_fetch_array($rs);
		extract($row);
		$qi = "update ".$avtable." set gitem = '".$item."', gcost = ".$cost.", gunit = '".$unit."', gnoinunit = ".$noinunit." where uid = ".$sid;
		$ri = mysql_query($qi) or die(mysql_error().' '.$qi);
	}
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Medicines Available</title>
<link rel="stylesheet" href="includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="includes/jquery/themes/cupertino/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="includes/jquery/themes/ui.jqgrid.css" />
<script src="includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="includes/jquery/ui/jquery.ui.widget.js"></script>

<script>

window.name = 'available';

</script>

</head>

<body>
<form name="avail" id="avail" method="post">
<div id="wrapper" >

    <div id="mainheader">
        <div id="mainlefttop"><img src="images/cmed4u_large.png" width="700" height="60"></div>
    </div>
    
 
 <div id="medicine" style="float:left;visibility:visible;height:500px;width:1000px;background-image:url('images/back.jpg');">
	<table width="900" align="center">
		<tr>
            <td colspan="2"><?php include "getavailable.php" ?></td>
        </tr>
	</table>
 </div> 
 
  
  
<div id="footer"; align="center" style="width:1000px">
         © Mantle Systems Ltd. 2014 - <?php echo $thisyear; ?>
    </div>
  
</div>
</form>

</body>
</html>
<?php
session_start();
date_default_timezone_set($_SESSION['s_timezone']);

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$plfile = 'ztmp'.$user_id.'_pl';

$findb = $_SESSION['s_findb'];

$db->query("drop table if exists ".$findb.".".$plfile);
$db->execute();

$db->query("create table ".$findb.".".$plfile." (uid integer(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, Header char(20) default '', Type char(1), AccountNumber int default 0, Branch char(4) default '', Sub int default 0, AccountName varchar(45) default '', Sbal decimal(16,2) default 0 not NULL, Bal decimal(16,2) default 0 not NULL ,crdr char(10), Total decimal(16,2) default 0 not NULL ) engine myisam"); 
$db->execute();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

// create temporary profit & loss table
$coyname = $_SESSION['s_coyname'];
$todate = $_REQUEST['edate'];
$fromdate = $_REQUEST['bdate'];

$_SESSION['s_fromdate'] = $fromdate;
$_SESSION['s_todate'] = $todate;
if (isset($_REQUEST['branch'])) {
	$brcode = $_REQUEST['branch'];
} else {
	$brcode = '1000,';
}

$branchcode = substr($_REQUEST['branch'], 0, strlen($_REQUEST['branch'])-1); 	
if ($branchcode == '*' || $branchcode == '') {
	$branchcode = '*';
} else {
		$brcodes = "";
		$br = explode(",",$branchcode);
		foreach ($br as $value) {
			$brcodes .= "'".$value."~";
		}
		$branchcode = substr($brcodes,0,strlen($brcodes)-1);
		$branchcode = str_replace('~',chr(39).',',$branchcode).chr(39);
}

$heading = "Profit & Loss - between ".$fromdate." and ".$todate;
$_SESSION['s_plheading'] = $heading;
$_SESSION['s_fromdate'] = $fromdate;
$_SESSION['s_todate'] = $todate;
$_SESSION['s_brcons'] = 'n';
$_SESSION['s_subcons'] = 'n';


// populate pl table

// income
$db->query("insert into ".$findb.".".$plfile." (Header,Type) values ('Income','H')");
$db->execute(); 
if ($branchcode == '*') {
	$db->query("select accountno as ano,branch as bno,sub as sno,account from ".$findb.".glmast where accountno < 81 order by accountno,branch,sub");
} else {
	$db->query("select accountno as ano,branch as bno,sub as sno,account from ".$findb.".glmast where branch in (".$branchcode.") and accountno < 81 order by accountno,branch,sub");
}
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$db->query("insert into ".$findb.".".$plfile." (Type,AccountNumber,Branch,Sub,AccountName) values ('D',".$ano.",'".$bno."',".$sno.",'".$account."')");
	$db->execute(); 
}

// cost of sales
$db->query("insert into ".$findb.".".$plfile." (Header,Type) values ('Cost of Sales','H')");
$db->execute(); 
if ($branchcode == '*') {
	$db->query("select accountno as ano,branch as bno,sub as sno,account from ".$findb.".glmast where accountno > 100 and accountno < 201 order by accountno,branch,sub");
} else {
	$db->query("select accountno as ano,branch as bno,sub as sno,account from ".$findb.".glmast where branch in (".$branchcode.") and accountno > 100 and accountno < 201 order by accountno,branch,sub");
}
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$db->query("insert into ".$findb.".".$plfile." (Type,AccountNumber,Branch,Sub,AccountName) values ('D',".$ano.",'".$bno."',".$sno.",'".$account."')");
	$db->execute(); 
}

// gross profit/loss
$db->query("insert into ".$findb.".".$plfile." (Header,Type) values ('Gross Profit/Loss','B')");
$db->execute(); 

// expenses
$db->query("insert into ".$findb.".".$plfile." (Header,Type) values ('Expenses','H')");
$db->execute(); 
if ($branchcode == '*') {
	$db->query("select accountno as ano,branch as bno,sub as sno,account from ".$findb.".glmast where accountno > 200 and accountno < 701 order by accountno,branch,sub");
} else {
	$db->query("select accountno as ano,branch as bno,sub as sno,account from ".$findb.".glmast where branch in (".$branchcode.") and accountno > 200 and accountno < 701 order by accountno,branch,sub");
}
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$db->query("insert into ".$findb.".".$plfile." (Type,AccountNumber,Branch,Sub,AccountName) values ('D',".$ano.",'".$bno."',".$sno.",'".$account."')");
	$db->execute(); 
}

// net profit/loss
$db->query("insert into ".$findb.".".$plfile." (Header,Type) values ('Net Profit/Loss','B')");
$db->execute(); 

// sundry income
$db->query("insert into ".$findb.".".$plfile." (Header,Type) values ('Sundry Income','H')");
$db->execute(); 
if ($branchcode == '*') {
	$db->query("select accountno as ano,branch as bno,sub as sno,account from ".$findb.".glmast where accountno > 79 and accountno < 101 order by accountno,branch,sub");
} else {
	$db->query("select accountno as ano,branch as bno,sub as sno,account from ".$findb.".glmast where branch in (".$branchcode.") and accountno > 79 and accountno < 101 order by accountno,branch,sub");
}
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$db->query("insert into ".$findb.".".$plfile." (Type,AccountNumber,Branch,Sub,AccountName) values ('D',".$ano.",'".$bno."',".$sno.",'".$account."')");
	$db->execute(); 
}

// nett profit/loss
$db->query("insert into ".$findb.".".$plfile." (Header,Type) values ('Nett Profit/Loss','B')");
$db->execute(); 

if ($fromdate == "0000-00-00") {
	$period = " and ddate <= '".$todate."'";
} else {
	$period = " and ddate >= '".$fromdate."' and ddate <= '".$todate."'";
}

$db->query("select uid, AccountNumber as ano, Branch as bno, Sub as sno from ".$findb.".".$plfile." where AccountNumber > 0 order by AccountNumber,Branch,Sub");
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$recuid = $uid;

	if ($sno == 0) {
		$db->query("select sum(debit - credit) as tot from ".$findb.".trmain where accountno = ".$ano." and branch = '".$bno."'".$period);
	} else {
		$db->query("select sum(debit - credit) as tot from ".$findb.".trmain where accountno = ".$ano." and branch = '".$bno."' and sub = ".$sno.$period);
	}
	
	$row1 = $db->single();
	extract($row1);
	
	if (empty($tot)) {
		$tot = 0;
	}
	if ($tot >= 0) {
		if ($sno == 0) {
			$db->query("update ".$findb.".".$plfile." set Bal = ".$tot.", crdr = 'D' where uid = ".$recuid);
		} else {
			$db->query("update ".$findb.".".$plfile." set Sbal = ".$tot.", crdr = 'D' where uid = ".$recuid);
		}

	} else {
		if ($tot < 0) {
			if ($sno == 0) {
				$db->query("update ".$findb.".".$plfile." set Bal = ".($tot).", crdr = 'C' where uid = ".$recuid);
			} else {
				$db->query("update ".$findb.".".$plfile." set Sbal = ".($tot).", crdr = 'C' where uid = ".$recuid);
			}	
		}
	}

	$db->execute(); 	
	
}	


$db->query("select sum(Bal) as totinc from ".$findb.".".$plfile." where AccountNumber < 81");
$row = $db->single();
extract($row);
if (empty($totinc)) {
	$totinc = 0;
}
$db->query("update ".$findb.".".$plfile." set total = ".$totinc." where Header = 'Income'");
$db->execute();
$db->query("select sum(Bal) as totcos from ".$findb.".".$plfile." where AccountNumber > 100 and AccountNumber < 201");
$row = $db->single();
extract($row);
if (empty($totcos)) {
	$totcos = 0;
}
$db->query("update ".$findb.".".$plfile." set total = ".$totcos." where Header = 'Cost of Sales'");
$db->execute();
$db->query("select sum(Bal) as totexp from ".$findb.".".$plfile." where AccountNumber > 200 and Accountnumber < 701");
$row = $db->single();
extract($row);
if (empty($totexp)) {
	$totexp = 0;
}
$db->query("update ".$findb.".".$plfile." set total = ".$totexp." where Header = 'Expenses'");
$db->execute();
$db->query("select sum(Bal) as totsinc from ".$findb.".".$plfile." where AccountNumber > 80 and AccountNumber < 101");
$row = $db->single();
extract($row);
if (empty($totsinc)) {
	$totsinc = 0;
}
$db->query("update ".$findb.".".$plfile." set total = ".$totsinc." where Header = 'Sundry Income'");
$db->execute();

$gp = ($totinc * -1) - $totcos;

if ($gp >= 0) {
	$db->query("update ".$findb.".".$plfile." set Header = 'Gross Profit', Total = ".$gp." where Header = 'Gross Profit/Loss'");
} else {
	$db->query("update ".$findb.".".$plfile." set Header = 'Gross Loss', Total = ".($gp*-1)." where Header = 'Gross Profit/Loss'");
}	
$db->execute(); 

$np = ($totinc * -1) - $totcos - $totexp;
if ($np >= 0) {
	$db->query("update ".$findb.".".$plfile." set Header = 'Net Profit', Total = ".$np." where Header = 'Net Profit/Loss'");
} else {
	$db->query("update ".$findb.".".$plfile." set Header = 'Net Loss', Total = ".($np*-1)." where Header = 'Net Profit/Loss'");
}	
$db->execute();	

$ntp = ($totinc * -1) - $totcos - $totexp + ($totsinc * -1);
if ($ntp >= 0) {
	$db->query("update ".$findb.".".$plfile." set Header = 'Nett Profit', Total = ".$ntp." where Header = 'Nett Profit/Loss'");
} else {
	$db->query("update ".$findb.".".$plfile." set Header = 'Nett Loss', Total = ".($ntp*-1)." where Header = 'Nett Profit/Loss'");
}	
$db->execute(); 	

$db->closeDB();

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Profit & Loss</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="../includes/jquery/themes/ui.multiselect.css"/> 

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>


<script>

function viewac(acno,br,sb) {
	var fdt = "<?php echo $fromdate; ?>";
	var edt = "<?php echo $todate; ?>";
	var ob = "N";
	
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	jQuery.ajaxSetup({async:false});
	$.get("../ajax/ajaxUpdtGLAcc.php", {vac: acno, vbr: br, vsb: sb, fdt: fdt, edt: edt, ob: ob}, function(data){
	});
	jQuery.ajaxSetup({async:true});
	viewac2();
}

function viewac2() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_view1gl.php','vac','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function pl2xl() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_pl2excel.php','plxl','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function pl2pdf() {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('rep_pl2pdf.php','plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}


</script>


<style type="text/css">
<!--
.style2 {font-size: 12px}
-->
</style>
</head>

<body>

<body>
    <table align="center">
        <tr>
	        <td><?php include "getpl.php"; ?></td>
        </tr>
	</table>		

</body>
</body>
</html>




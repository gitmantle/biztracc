<?php
session_start();

date_default_timezone_set($_SESSION['s_timezone']);

$dts = $_REQUEST['dts'];
$fromdate = $_REQUEST['bdate'];
$todate = $_REQUEST['edate'];

$branchcode = substr($_REQUEST['branch'], 0, strlen($_REQUEST['branch'])-1); 	
if ($branchcode == '*' || $branchcode == '') {
	$branchcode = '*';
	$brs = 'all branches';
} else {
		$brcodes = "";
		$br = explode(",",$branchcode);
		foreach ($br as $value) {
			$brcodes .= "'".$value."~";
		}
		$branchcode = substr($brcodes,0,strlen($brcodes)-1);
		$branchcode = str_replace('~',chr(39).',',$branchcode).chr(39);
		$brs = $branchcode;
}

$brcons = $_REQUEST['brcons'];
$subcons = $_REQUEST['subcons'];
$zbal = $_REQUEST['zbal'];

$_SESSION['s_brcons'] = $brcons;
$_SESSION['s_subcons'] = $subcons;
$_SESSION['s_fromdate'] = $fromdate;
$_SESSION['s_todate'] = $todate;
$_SESSION['s_sob'] = 'N';
$_SESSION['s_tbheading'] = ' from '.$fromdate.' to '.$todate.' for '.$brs;

$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$tbfile = 'ztmp'.$user_id.'_tb';

$findb = $_SESSION['s_findb'];

$db->query("drop table if exists ".$findb.".".$tbfile);
$db->execute();

$db->query("create table ".$findb.".".$tbfile." (AccountNumber int, Branch char(4) default '', Sub int default 0, AccountName varchar(45), Debit decimal(16,2) NOT NULL default 0, Credit decimal(16,2) NOT NULL default 0, Lastyear decimal(16,2) default 0)  engine myisam");
$db->execute();

$heading = '';

// populate tb table
// year to date tb
if ($dts == 'ytd') {
	$heading .= 'As at '.$todate;
	// all branches
	if ($branchcode == '*') {
		// all branches consolidated
		$heading .= ' - All Branches';
		if ($brcons == 'y') {
			// subaccounts consolidated, branches consolidated, ytd
			if ($subcons == 'y') {
				$db->query("insert into ".$findb.".".$tbfile." select accountno as ano,'' as bno,0 as sno,account,if(sum(obal)>=0,sum(obal),0) as debit,if(sum(obal)<0,sum(obal)*-1,0) as credit,sum(lastyear) as lastyear from ".$findb.".glmast where accountno < 1000 group by ano");
				$db->execute();
			} else {
			// subaccounts detailed, branches consolidated, ytd
				$db->query("insert into ".$findb.".".$tbfile." select accountno as ano,'' as bno,sub as sno,account,if(sum(obal)>=0,sum(obal),0) as debit,if(sum(obal)<0,sum(obal)*-1,0) as credit,sum(lastyear) as lastyear from ".$findb.".glmast where accountno < 1000 group by ano,sno");
				$db->execute();
			}
		} else {
		// all branches detailed
			// subaccounts consolidated, branches detailed, ytd
			if ($subcons == 'y') {
				$db->query("insert into ".$findb.".".$tbfile." select accountno as ano,branch as bno,0 as sno,account,if(sum(obal)>=0,sum(obal),0) as debit,if(sum(obal)<0,sum(obal)*-1,0) as credit,sum(lastyear) as lastyear from ".$findb.".glmast where accountno < 1000 group by ano,bno");
				$db->execute();
			} else {
			// subaccounts detailed, branches detailed, ytd
				$db->query("insert into ".$findb.".".$tbfile." select accountno as ano,branch as bno,sub as sno,account,if(sum(obal)>=0,sum(obal),0) as debit,if(sum(obal)<0,sum(obal)*-1,0) as credit,sum(lastyear) as lastyear from ".$findb.".glmast where accountno < 1000 group by ano,bno,sno");
				$db->execute();
			}
		}
	} else {
	// one or more branch
		$heading .= ' - Branches '.$branchcode;
		// one or more branch consolidated
		if ($brcons == 'y') {
			// subaccounts consolidated, branches consolidated, ytd
			if ($subcons == 'y') {
				$db->query("insert into ".$findb.".".$tbfile." select accountno as ano,'' as bno,0 as sno,account,if(sum(obal)>=0,sum(obal),0) as debit,if(sum(obal)<0,sum(obal)*-1,0) as credit,sum(lastyear) as lastyear from ".$findb.".glmast where branch in (".$branchcode.") and accountno < 1000 group by ano");
				$db->execute();
			} else {
			// subaccounts detailed, branches consolidated, ytd
				$db->query("insert into ".$findb.".".$tbfile." select accountno as ano,'' as bno,sub as sno,account,if(sum(obal)>=0,sum(obal),0) as debit,if(sum(obal)<0,sum(obal)*-1,0) as credit,sum(lastyear) as lastyear from ".$findb.".glmast where branch in (".$branchcode.") and accountno < 1000 group by ano,sno");
				$db->execute();
			}
		} else {
		// one or many branch detailed
			// subaccounts consolidated, branches detailed, ytd
			if ($subcons == 'y') {
				$db->query("insert into ".$findb.".".$tbfile." select accountno as ano,branch as bno,0 as sno,account,if(sum(obal)>=0,sum(obal),0) as debit,if(sum(obal)<0,sum(obal)*-1,0) as credit,sum(lastyear) as lastyear from ".$findb.".glmast where branch in (".$branchcode.") and accountno < 1000 group by ano,bno");
				$db->execute();
			} else {
			// subaccounts detailed, branches detailed, ytd
				$db->query("insert into ".$findb.".".$tbfile." select accountno as ano,branch as bno,sub as sno,account,if(sum(obal)>=0,sum(obal),0) as debit,if(sum(obal)<0,sum(obal)*-1,0) as credit,sum(lastyear) as lastyear from ".$findb.".glmast where branch in (".$branchcode.") and accountno < 1000 group by ano,bno,sno");
				$db->execute();
			}
		}
	}
	
} else {
	
// between dates tb
	$heading .= 'Between '.$fromdate.' to '.$todate;
	// all branches
	if ($branchcode == '*') {
		// all branches consolidated
		$heading .= ' - All Branches';
		if ($brcons == 'y') {
			// subaccounts consolidated, all branches consolidated, between dates
			if ($subcons == 'y') {
				$db->query("insert into ".$findb.".".$tbfile." SELECT glmast.accountno AS ano, '' AS bno, 0 AS sno, glmast.account, 0 debit, 0 AS credit, sum( glmast.lastyear ) AS lastyear from ".$findb.".glmast where glmast.accountno <1000 GROUP BY glmast.accountno");
				$db->execute();
				
				$db->query("select AccountNumber,Branch,Sub from ".$findb.".".$tbfile);
				$rows = $db->resultset();
				foreach ($rows as $row) {
					extract($row);
					$db->query("select ifnull(sum(debit),0) as dr, ifnull(sum(credit),0) as cr from ".$findb.".trmain where accountno = ".$AccountNumber." and ddate >= '".$fromdate."' and ddate <= '".$todate."'");
					$rowt = $db->single();
					extract($rowt);
					$bal = $dr - $cr;
					if ($bal >= 0) {
						$dr = $bal;
						$cr = 0;
					} else {
						$cr = $bal*-1;	
						$dr = 0;
					}
					$db->query("update ".$findb.".".$tbfile." set debit = ".$dr.",credit = ".$cr." where AccountNumber = ".$AccountNumber);
					$db->execute();
				}
			} else {
			// subaccounts detailed, all branches consolidated, between dates
				$db->query("insert into ".$findb.".".$tbfile." SELECT glmast.accountno AS ano, '' AS bno, sub AS sno, glmast.account, 0 debit, 0 AS credit, sum( glmast.lastyear ) AS lastyear from ".$findb.".glmast where glmast.accountno <1000 GROUP BY glmast.accountno,sno");
				$db->execute();
				
				$db->query("select AccountNumber,Branch,Sub from ".$findb.".".$tbfile);
				$rows = $db->resultset();
				foreach ($rows as $row) {
					extract($row);
					$db->query("select ifnull(sum(debit),0) as dr, ifnull(sum(credit),0) as cr from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and ddate >= '".$fromdate."' and ddate <= '".$todate."'");
					$rowt = $db->single();
					extract($rowt);
					$bal = $dr - $cr;
					if ($bal >= 0) {
						$dr = $bal;
						$cr = 0;
					} else {
						$cr = $bal*-1;	
						$dr = 0;
					}
					$db->query("update ".$findb.".".$tbfile." set debit = ".$dr.",credit = ".$cr." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
				}
			}
		} else {
		// all branches detailed
			// subaccounts consolidated,all branches detailed, between dates
			if ($subcons == 'y') {
				$db->query("insert into ".$findb.".".$tbfile." SELECT glmast.accountno AS ano, branch AS bno, 0 AS sno, glmast.account, 0 debit, 0 AS credit, sum( glmast.lastyear ) AS lastyear from ".$findb.".glmast where glmast.accountno <1000 GROUP BY glmast.accountno,bno");
				$db->execute();
				
				$db->query("select AccountNumber,Branch,Sub from ".$findb.".".$tbfile);
				$rows = $db->resultset();
				foreach ($rows as $row) {
					extract($row);
					$db->query("select ifnull(sum(debit),0) as dr, ifnull(sum(credit),0) as cr from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and ddate >= '".$fromdate."' and ddate <= '".$todate."'");
					$rowt = $db->single();
					extract($rowt);
					$bal = $dr - $cr;
					if ($bal >= 0) {
						$dr = $bal;
						$cr = 0;
					} else {
						$cr = $bal*-1;	
						$dr = 0;
					}
					$db->query("update ".$findb.".".$tbfile." set debit = ".$dr.",credit = ".$cr." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
				}
			} else {
			// subaccounts detailed, all branches detailed, between dates
				$db->query("insert into ".$findb.".".$tbfile." SELECT glmast.accountno AS ano, branch AS bno, sub AS sno, glmast.account, 0 debit, 0 AS credit, sum( glmast.lastyear ) AS lastyear from ".$findb.".glmast where glmast.accountno <1000 GROUP BY glmast.accountno,bno,sno");
				$db->execute();
				
				$db->query("select AccountNumber,Branch,Sub from ".$findb.".".$tbfile);
				$rows = $db->resultset();
				foreach ($rows as $row) {
					extract($row);
					$db->query("select ifnull(sum(debit),0) as dr, ifnull(sum(credit),0) as cr from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and ddate >= '".$fromdate."' and ddate <= '".$todate."'");
					$rowt = $db->single();
					extract($rowt);
					$bal = $dr - $cr;
					if ($bal >= 0) {
						$dr = $bal;
						$cr = 0;
					} else {
						$cr = $bal*-1;	
						$dr = 0;
					}
					$db->query("update ".$findb.".".$tbfile." set debit = ".$dr.",credit = ".$cr." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
				}
			}
		}
	} else {
	// one or many branch
		// one or many branch consolidated
		if ($brcons == 'y') {
			// subaccounts consolidated, chosen branches consolidated between dates
			if ($subcons == 'y') {
				$db->query("insert into ".$findb.".".$tbfile." SELECT glmast.accountno AS ano, '' AS bno, 0 AS sno, glmast.account, 0 debit, 0 AS credit, sum( glmast.lastyear ) AS lastyear from ".$findb.".glmast where glmast.accountno <1000 and branch in (".$branchcode.") GROUP BY glmast.accountno");
				$db->execute();
				
				$db->query("select AccountNumber,Branch,Sub from ".$findb.".".$tbfile);
				$rows = $db->resultset();
				foreach ($rows as $row) {
					extract($row);
					$db->query("select ifnull(sum(debit),0) as dr, ifnull(sum(credit),0) as cr from ".$findb.".trmain where accountno = ".$AccountNumber." and ddate >= '".$fromdate."' and ddate <= '".$todate."'");
					$rowt = $db->single();
					extract($rowt);
					$bal = $dr - $cr;
					if ($bal >= 0) {
						$dr = $bal;
						$cr = 0;
					} else {
						$cr = $bal*-1;	
						$dr = 0;
					}
					$db->query("update ".$findb.".".$tbfile." set debit = ".$dr.",credit = ".$cr." where AccountNumber = ".$AccountNumber);
					$db->execute();
				}
			} else {
			// subaccounts detailed, chosen branches consolidated between dates
				$db->query("insert into ".$findb.".".$tbfile." SELECT glmast.accountno AS ano, '' AS bno, sub AS sno, glmast.account, 0 debit, 0 AS credit, sum( glmast.lastyear ) AS lastyear from ".$findb.".glmast where glmast.accountno <1000 and branch in (".$branchcode.") GROUP BY glmast.accountno,sno");
				$db->execute();
				
				$db->query("select AccountNumber,Branch,Sub from ".$findb.".".$tbfile);
				$rows = $db->resultset();
				foreach ($rows as $row) {
					extract($row);
					$db->query("select ifnull(sum(debit),0) as dr, ifnull(sum(credit),0) as cr from ".$findb.".trmain where accountno = ".$AccountNumber." and sub = ".$Sub." and ddate >= '".$fromdate."' and ddate <= '".$todate."'");
					$rowt = $db->single();
					extract($rowt);
					$bal = $dr - $cr;
					if ($bal >= 0) {
						$dr = $bal;
						$cr = 0;
					} else {
						$cr = $bal*-1;	
						$dr = 0;
					}
					$db->query("update ".$findb.".".$tbfile." set debit = ".$dr.",credit = ".$cr." where AccountNumber = ".$AccountNumber." and sub = ".$Sub);
					$db->execute();
				}
			}
		} else {
		// one or many branch detailed
			// subaccounts consolidated, chosen branches detailed between dates
			if ($subcons == 'y') {
				$db->query("insert into ".$findb.".".$tbfile." SELECT glmast.accountno AS ano, branch AS bno, 0 AS sno, glmast.account, 0 debit, 0 AS credit, sum( glmast.lastyear ) AS lastyear from ".$findb.".glmast where glmast.accountno <1000 and branch in (".$branchcode.") GROUP BY glmast.accountno,bno");
				$db->execute();
				
				$db->query("select AccountNumber,Branch,Sub from ".$findb.".".$tbfile);
				$rows = $db->resultset();
				foreach ($rows as $row) {
					extract($row);
					$db->query("select ifnull(sum(debit),0) as dr, ifnull(sum(credit),0) as cr from ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and ddate >= '".$fromdate."' and ddate <= '".$todate."'");
					$rowt = $db->single();
					extract($rowt);
					$bal = $dr - $cr;
					if ($bal >= 0) {
						$dr = $bal;
						$cr = 0;
					} else {
						$cr = $bal*-1;	
						$dr = 0;
					}
					$db->query("update ".$findb.".".$tbfile." set debit = ".$dr.",credit = ".$cr." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."'");
					$db->execute();
				}
			} else {
			// subaccounts detailed, chosen branches detailed between dates
				$db->query("insert into ".$findb.".".$tbfile." SELECT glmast.accountno AS ano, branch AS bno, sub AS sno, glmast.account, 0 debit, 0 AS credit, sum( glmast.lastyear ) AS lastyear from ".$findb.".glmast where glmast.accountno <1000 and branch in (".$branchcode.") GROUP BY glmast.accountno,bno,sno");
				$db->execute();
				
				$db->query("select AccountNumber,Branch,Sub from ".$findb.".".$tbfile);
				$rows = $db->resultset();
				foreach ($rows as $row) {
					extract($row);
					$db->query("select ifnull(sum(debit),0) as dr, ifnull(sum(credit),0) as cr from  ".$findb.".trmain where accountno = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub." and ddate >= '".$fromdate."' and ddate <= '".$todate."'");
					$rowt = $db->single();
					extract($rowt);
					$bal = $dr - $cr;
					if ($bal >= 0) {
						$dr = $bal;
						$cr = 0;
					} else {
						$cr = $bal*-1;	
						$dr = 0;
					}
					$db->query("update ".$findb.".".$tbfile." set debit = ".$dr.",credit = ".$cr." where AccountNumber = ".$AccountNumber." and branch = '".$Branch."' and sub = ".$Sub);
					$db->execute();
				}
			}
		}
	}


}


// delete rows where debit and credit are zero
if ($zbal == 'n') {
	$db->query("delete from ".$findb.".".$tbfile." where debit = 0 and credit = 0");
	$db->execute();
}

// Add uid
$db->query("alter table ".$findb.".".$tbfile." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
$db->execute();


// Add branch names
$db->query("alter table ".$findb.".".$tbfile." add Branchname varchar(25) default ''");
$db->execute();

if ($brcons != 'y' && $subcons != 'y') {
	$db->query("select uid,branch from ".$findb.".".$tbfile);
	$rows = $db->resultset();
	foreach ($rows as $row) {
		extract($row);
		$id = $uid;
		$br = $branch;
		$db->query("select branchname from ".$findb.".branch where branch = '".$br."'");
		$row = $db->single();
		extract($row);
		$bname = $branchname;
		$db->query("update ".$findb.".".$tbfile." set Branchname = '".$bname."' where uid = ".$id);
		$db->execute();
	}
}

$db->closeDB();

$_SESSION['s_finheading'] = $heading;
$_SESSION['s_pdftable'] = $tbfile;
$_SESSION['s_fintemplate'] = 'tbtemplate';
$_SESSION['s_daterange'] = $fromdate.'~'.$todate;

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");




?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Trial Balance</title>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="../includes/jquery/themes/ui.multiselect.css"/> 

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>


<script type="text/javascript" src="js/fin.js"></script>


<script type="text/javascript">

window.name = 'tbgrid';

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

</script>
</head>
<body>
    <table align="center">
        <tr>
	        <td><?php include "gettb.php"; ?></td>
        </tr>
	</table>		

</body>
</html>
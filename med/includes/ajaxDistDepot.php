<?php
session_start();

$dlist = $_REQUEST['dlist'];
date_default_timezone_set($_SESSION['s_timezone']);

require("../../db.php");
$admindb = $_SESSION['s_admindb'];
mysql_select_db($admindb) or die(mysql_error().' db not selected');

$usersession = $_SESSION['usersession'];

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$uip = $userip;
$unm = $uname;
$subscriber = $subid;

$medstable = 'ztmp'.$user_id.'_distmeds';

$moduledb = $_SESSION['s_prcdb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "drop table if exists ".$medstable;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$medstable." ( noofunits int default 0,unit varchar(20) default '', medicine varchar(70) default '',member varchar(80) default '',phone varchar(30) default '', mobile varchar(30) default '', email varchar(70) default '', depot_id int default 0, deladdress varchar(250) default ''  )  engine myisam";
$calc = mysql_query($query) or die(mysql_error());

$q = "select processed, distperiod from distlist where uid = ".$dlist;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);

$p = explode(' ',$distperiod);
$pd = $p[0];
$q = "select period,enddate from periods where period = ".$pd;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);
$prd = $period;

$yr = date('Y');

$ddate = $enddate.'-'.$yr;
$descript = "Medicines supplied for period ".$distperiod;
$loc = 1;

$mdb = $_SESSION['s_prcdb'];
$cdb = $_SESSION['s_cltdb'];
$fdb = $_SESSION['s_findb'];

//*********************************************************************************
// create distribution lists by depot with itemised meds per member
//*********************************************************************************
	
$qdepot = "select depot_id,depot,sad1,sad2,stown,spostcode from depots";
$rdepot = mysql_query($qdepot) or die(mysql_error().' '.$qdepot);
while ($row = mysql_fetch_array($rdepot)) {
	extract($row);	
	$depid = $depot_id;
	$prt = 'N';
	$heading1 = $depot;
		
	$qm = "select ".$mdb.".distmeds.noofunits,".$mdb.".distmeds.unit,".$mdb.".distmeds.medicine,".$mdb.".distdetail.member, (select concat(".$cdb.".comms.country_code,' ',".$cdb.".comms.area_code,' ',".$cdb.".comms.comm) from ".$cdb.".comms where ".$cdb.".comms.comms_type_id = 1 and ".$cdb.".comms.member_id = ".$mdb.".distdetail.member_id) as phone, (select concat(".$cdb.".comms.country_code,' ',".$cdb.".comms.area_code,' ',".$cdb.".comms.comm) from ".$cdb.".comms where ".$cdb.".comms.comms_type_id = 3 and ".$cdb.".comms.member_id = ".$mdb.".distdetail.member_id) as mobile, (select ".$cdb.".comms.comm from ".$cdb.".comms where ".$cdb.".comms.comms_type_id = 4 and ".$cdb.".comms.member_id = ".$mdb.".distdetail.member_id) as email from ".$mdb.".distmeds,".$mdb.".distdetail where ".$mdb.".distmeds.distlist_id = ".$dlist." and ".$mdb.".distmeds.distdetail_id = ".$mdb.".distdetail.uid and  (".$mdb.".distdetail.balance + ".$mdb.".distdetail.ordered) < 0 and ".$mdb.".distmeds.noofunits > 0 and ".$mdb.".distdetail.checked = 'Yes' and ".$mdb.".distdetail.depot_id = ".$depid." order by ".$mdb.".distdetail.member" ;
	$rm = mysql_query($qm) or die(mysql_error().' '.$qm);
	$numrows = mysql_num_rows($rm);
	if ($numrows > 0) {
		while ($row = mysql_fetch_array($rm)) {
			extract($row);	
				
			$qmi = "insert into ".$medstable." (noofunits,unit,medicine,member,phone,mobile,depot_id) values (".$noofunits.",'".$unit."','".$medicine."','".$member."','".$phone."','".$mobile."',".$depid.")";
			$rmi = mysql_query($qmi) or die(mysql_error().' '.$qmi);
			
			$prt = 'Y';
		}
	}
	
	if ($prt == 'Y') {
		
		$qa = "select depot,sad1,sad2,stown,spostcode from depots where depot_id = ".$depid;
		$ra = mysql_query($qa) or die(mysql_error().' '.$qa);
		$row = mysql_fetch_array($ra);
		extract($row);
		$postaladdress = "";
		$deliveryaddress = trim($depot)."\n";
		$deliveryaddress .= trim($sad1)."\n";
		$deliveryaddress .= trim($sad2)."\n";
		$deliveryaddress .= mysql_real_escape_string(trim($stown.' '.$spostcode));
		
		$qu = 'update '.$medstable.' set deladdress = "'.$deliveryaddress.'" where depot_id = '.$depid;
		$ru = mysql_query($qu) or die(mysql_error().' '.$qu);
		
		
		$template = "depottemplate";
		$footmessage = '';
		
		$coyid = $_SESSION['s_coyid'];
		$templatedb = 'infinint_med'.$subscriber.'_'.$coyid;
		
		$_SESSION['sp_templatedb'] = $templatedb;
		$_SESSION['sp_template'] = $template;
		$_SESSION['sp_footmessage'] = $footmessage;
		$_SESSION['sp_detailstable'] = $medstable;
		$_SESSION['watermark'] = 'N';		
		$_SESSION['sp_heading1'] = $descript;
		
		header("Location: ajaxDepotMeds.php");
		

	}
	
	$qmd = "delete from ".$medstable;
	$rmd = mysql_query($qmd) or die(mysql_error().' '.$qmd);
	
}
	
?>

<?php
// function to update distdetails with all active members with orders for medicine
session_start();

$dlist = $_SESSION['s_distlist'];

$mdb = $_SESSION['s_prcdb'];
$cdb = $_SESSION['s_cltdb'];
$fdb = $_SESSION['s_findb'];

require("../../db.php");
$moduledb = $_SESSION['s_prcdb'];
mysql_select_db($moduledb) or die(mysql_error());
	
// check to see period has not been processed

$q = "select processed, startdate from distlist where uid = ".$dlist;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);

$sdate = $startdate;

if ($processed == 'No') {
	
	// delete from distlist and distmeds for selected period if not processed
	$q = "delete from distmeds where distlist_id = ".$dlist;
	$r = mysql_query($q) or die(mysql_error().' '.$q);
	
	$q = "delete from distdetail where distlist_id = ".$dlist;
	$r = mysql_query($q) or die(mysql_error().' '.$q);
	
	// rebuild records for unprocessed period
	
	$moduledb = $_SESSION['s_prcdb'];
	mysql_select_db($moduledb) or die(mysql_error());

	// Set checked to No where any prescription expires before beginning of next period
	$qc = "select patientid from requirements where expdate < ".$sdate;
	$rc = mysql_query($qc) or die(mysql_error().' '.$qc);
	while ($row = mysql_fetch_array($rc)) {
		extract($row);
			
		$moduledb = $_SESSION['s_cltdb'];
		mysql_select_db($moduledb) or die(mysql_error());

		$qk = "update members set checked = 'No' where member_id = ".$patientid;
		$rk = mysql_query($qk) or die(mysql_error().' '.$qk);
	}
	
	$moduledb = $_SESSION['s_prcdb'];
	mysql_select_db($moduledb) or die(mysql_error());

	$qi = "insert into distdetail (distlist_id, member_id, member, depot_id, checked) select ".$dlist.", ".$cdb.".members.member_id, concat(".$cdb.".members.firstname,' ',".$cdb.".members.lastname), ".$cdb.".members.depot, ".$cdb.".members.checked from ".$cdb.".members where ".$cdb.".members.membertype = 'M'";
	$ri = mysql_query($qi) or die(mysql_error().' '.$qi);
	$dd = mysql_insert_id();
		
	$qi = "select uid, member_id, depot_id from distdetail";
	$ri = mysql_query($qi) or die(mysql_error().' '.$qi);
	while ($row = mysql_fetch_array($ri)) {
		extract($row);
		$mid = $member_id;
		$did = $depot_id;
		$distdetail_id = $uid;
		
		$moduledb = $_SESSION['s_cltdb'];
		mysql_select_db($moduledb) or die(mysql_error());
	
		$qb = "select (current + d30 + d60 + d90 + d120) as bal, priceband from client_company_xref where client_id = ".$mid;
		$rb = mysql_query($qb) or die(mysql_error().' '.$qb);
		$row = mysql_fetch_array($rb);
		extract($row);
		$pband = $priceband;
		
		$moduledb = $_SESSION['s_findb'];
		mysql_select_db($moduledb) or die(mysql_error());
		
		$q = "select pcent from stkpricepcent where uid = ".$pband;
		$r = mysql_query($q) or die(mysql_error());
		$row = mysql_fetch_array($r);
		extract($row);	
		$spcent = $pcent;
		
		$markup = 1 + $spcent/100;	
		
		$moduledb = $_SESSION['s_prcdb'];
		mysql_select_db($moduledb) or die(mysql_error());
	
		$qbal = "update distdetail set balance = ".$bal." where member_id = ".$mid;
		$rbal = mysql_query($qbal) or die(mysql_error().' '.$qbal);
		$row = mysql_fetch_array($rbal);
		extract($row);
			
		$ord = 0;
		$qm = "select ".$mdb.".requirements.dosage, ".$mdb.".requirements.qty,".$fdb.".stkmast.noinunit,  case ".$fdb.".stkmast.setsell when 0 then (".$fdb.".stkmast.avgcost * ".$markup.") else (".$fdb.".stkmast.setsell) end as cost,".$fdb.".stkmast.deftax from ".$mdb.".requirements, ".$fdb.".stkmast where ".$mdb.".requirements.medicineid = ".$fdb.".stkmast.itemid and ".$mdb.".requirements.patientid = ".$mid;
		$rm = mysql_query($qm) or die(mysql_error().' '.$qm);
		while ($row = mysql_fetch_array($rm)) {
			extract($row);
			$cst = $cost;
			$qt = $qty;
											
			switch ($dosage) {
				case 'Month';
					$qtyreq = $qty;
				break;
				case 'Week';
					$qtyreq = ($qty * 4);
				break;
				case 'Day';
					$qtyreq = ($qty * 28);
				break;
			}
				
			// get relevant tax	
			$moduledb = $_SESSION['s_findb'];
			mysql_select_db($moduledb) or die(mysql_error());
		
			$qt = "select taxpcent from taxtypes where uid = ".$deftax;
			$rt = mysql_query($qt) or die($qt);
			$rowt = mysql_fetch_array($rt);
			extract($rowt);
			
			$moduledb = $_SESSION['s_prcdb'];
			mysql_select_db($moduledb) or die(mysql_error());
				
			// calculate unit/packs required
				
			$unitsreq = ceil($qtyreq/$noinunit);
			$mcost = ($unitsreq * $cost) * (1 + ($taxpcent / 100));
			
			$ord = $ord + $mcost;
		}
		
		$moduledb = $_SESSION['s_prcdb'];
		mysql_select_db($moduledb) or die(mysql_error());

		$qord = "update distdetail set ordered = ".$ord." where member_id = ".$mid;
		$rord = mysql_query($qord) or die(mysql_error().' '.$qord);
			
		if ($did <> 0) {
			$qd = "select depot from depots where depot_id = ".$did;
			$rd = mysql_query($qd) or die(mysql_error().' '.$qd);
			$row = mysql_fetch_array($rd);
			extract($row);
			$d = $depot;
				
			$qdid = "update distdetail set depot = '".mysql_real_escape_string($d)."' where member_id = ".$mid;
			$rdid = mysql_query($qdid) or die(mysql_error().' '.$qdid);
		}
			
	}
		
	// insert records of meds into distmeds for each member in distdetail
	
		$moduledb = $_SESSION['s_prcdb'];
		mysql_select_db($moduledb) or die(mysql_error());

		$q = "select uid, member_id from distdetail where distlist_id = ".$dlist;
		$r = mysql_query($q) or die(mysql_error().' '.$q);
		while ($row = mysql_fetch_array($r)) {
			extract($row);
			$id = $uid;
			$mid = $member_id;
			
			$mdb = $_SESSION['s_prcdb'];
			$fdb = $_SESSION['s_findb'];

			$qr = "select ".$mdb.".requirements.req_id, ".$mdb.".requirements.medicineid, ".$mdb.".requirements.dosage, ".$mdb.".requirements.qty, ".$fdb.".stkmast.item, ".$fdb.".stkmast.unit, ".$fdb.".stkmast.noinunit,  case ".$fdb.".stkmast.setsell when 0 then (".$fdb.".stkmast.avgcost * ".$markup.") else (".$fdb.".stkmast.setsell) end as cost,".$fdb.".stkmast.deftax from ".$mdb.".requirements, ".$fdb.".stkmast where ".$fdb.".stkmast.itemid = ".$mdb.".requirements.medicineid and ".$mdb.".requirements.patientid = ".$mid;
			$rr = mysql_query($qr) or die(mysql_error().' '.$qr);
			$numrows = mysql_num_rows($rr);
			if ($numrows > 0) {
				while ($rowr = mysql_fetch_array($rr)) {
					extract($rowr);
					$rid = $req_id;
					$medid = $medicineid;
					$ds = $dosage;
					$it = $item;
					$q = $qty;
					$u = $unit;
					$non = $noinunit;
					$cst = $cost;
					
					// get relevant tax	
					$moduledb = $_SESSION['s_findb'];
					mysql_select_db($moduledb) or die(mysql_error());
			
					$qt = "select taxpcent from taxtypes where uid = ".$deftax;
					$rt = mysql_query($qt) or die(mysql_error().' '.$qt);
					$rowt = mysql_fetch_array($rt);
					extract($rowt);
					
					$moduledb = $_SESSION['s_prcdb'];
					mysql_select_db($moduledb) or die(mysql_error());
					
					// calculate unit/packs required
					
					switch ($ds) {
						case 'Month';
							$qtyreq = $q;
						break;
						case 'Week';
							$qtyreq = ($q * 4);
						break;
						case 'Day';
							$qtyreq = ($q * 28);
						break;
					}
					
					// calculate unit/packs required
					
					$unitsreq = ceil($qtyreq/$non);
					$mcost = ($unitsreq * $cst) * (1 + ($taxpcent / 100));
					
					$moduledb = $_SESSION['s_prcdb'];
					mysql_select_db($moduledb) or die(mysql_error());
		
					$qd = "insert into distmeds (distlist_id,distdetail_id,req_id,patientid,medicineid,medicine,qty,per,unit,noinunit,price,noofunits,topay) values (";
					$qd .= $dlist.",";
					$qd .= $id.",";
					$qd .= $rid.",";
					$qd .= $mid.",";
					$qd .= $medid.",";
					$qd .= "'".$it."',";
					$qd .= $q.",";
					$qd .= "'".$dosage."',";
					$qd .= "'".$u."',";
					$qd .= $non.",";
					$qd .= $cst.",";
					$qd .= $unitsreq.",";
					$qd .= $mcost.")";
					
					$rd = mysql_query($qd) or die(mysql_error().' '.$qd);
					
				}
				
			} else {
				
				$rid = 0;
				$medid = 0;
				$ds = '';
				$it = '';
				$q = 0;
				$u = '';
				$non = 0;
				$cst = 0;
				$mcost = 0;
				$unitsreq = 0;
			
				$moduledb = $_SESSION['s_prcdb'];
				mysql_select_db($moduledb) or die(mysql_error());
	
				$qd = "insert into distmeds (distlist_id,distdetail_id,req_id,patientid,medicineid,medicine,qty,per,unit,noinunit,price,noofunits,topay) values (";
				$qd .= $dlist.",";
				$qd .= $id.",";
				$qd .= $rid.",";
				$qd .= $mid.",";
				$qd .= $medid.",";
				$qd .= "'".$it."',";
				$qd .= $q.",";
				$qd .= "'".$dosage."',";
				$qd .= "'".$u."',";
				$qd .= $non.",";
				$qd .= $cst.",";
				$qd .= $unitsreq.",";
				$qd .= $mcost.")";
				
				$rd = mysql_query($qd) or die(mysql_error().' '.$qd);
			}
			
		}
		
	$ret =  "This period list updated";
	echo $ret;

} else {
	$ret =  "This period has been processed. No updates allowed.";
	echo $ret;
}
?>


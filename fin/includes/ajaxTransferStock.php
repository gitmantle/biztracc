<?php
session_start();

$findb = $_SESSION['s_findb'];
$ref = 'TSF';

$qtyout = $_REQUEST['qtyout'];
$scodeout = $_REQUEST['scodeout'];
$locout = $_REQUEST['locout'];
$qtyin = $_REQUEST['qtyin'];
$scodein = $_REQUEST['scodein'];
$locin = $_REQUEST['locin'];
$costin = $_REQUEST['costin'];

$ddate = date('Y-m-d');

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select ".$ref." from ".$findb.".numbers");
$row = $db->single();
extract($row);
$refno = $$ref + 1;
$db->query("update ".$findb.".numbers set ".$ref." = :refno");
$db->bind(':refno', $refno);
$db->execute();

$reference = 'TSF'.$refno;
	
$db->query("select groupid as groupout, catid as catout, item as itemout, avgcost as costout, unit as unitout from ".$findb.".stkmast where itemcode = '".$scodeout."'");
$row = $db->single();
extract($row);

$db->query("select groupid as groupin, catid as catin, item as itemin from ".$findb.".stkmast where itemcode = '".$scodein."'");
$row = $db->single();
extract($row);

$db->query("select avgcost,onhand-uncosted as avail from ".$findb.".stkmast where itemcode = '".$scodein."'");
$row = $db->single();
extract($row);
$newtotval = ($avgcost*$avail) + ($costin * $qtyin);  
$newtotqty = $avail + $qtyin;	
$newavgcost = $newtotval/$newtotqty;
					
$db->query("update ".$findb.".stkmast set avgcost = ".$newavgcost." where itemcode = '".$scodein."'");
$db->execute();

$db->query("update ".$findb.".stkmast set onhand = onhand - ".$qtyout." where itemcode = '".$scodeout."'");
$db->execute();
$db->query("update ".$findb.".stkmast set onhand = onhand + ".$qtyin." where itemcode = '".$scodein."'");
$db->execute();


$db->query("insert into ".$findb.".stktrans (groupid,catid,itemcode,item,locid,ddate,increase,decrease,ref_no,transtype,amount) values (:groupid,:catid,:itemcode,:item,:locid,:ddate,:increase,:decrease,:ref_no,:transtype,:amount)");
$db->bind(':groupid', $groupout);
$db->bind(':catid', $catout);
$db->bind(':itemcode', $scodeout);
$db->bind(':item', $itemout);
$db->bind(':locid', $locout);
$db->bind(':ddate', $ddate);
$db->bind(':increase', 0);
$db->bind(':decrease', $qtyout);
$db->bind(':ref_no', $reference);
$db->bind(':transtype', 'TSF');
$db->bind(':amount', $costout * $qtyout);
		
$db->execute();

$db->query("insert into ".$findb.".stktrans (groupid,catid,itemcode,item,locid,ddate,increase,decrease,ref_no,transtype,amount) values (:groupid,:catid,:itemcode,:item,:locid,:ddate,:increase,:decrease,:ref_no,:transtype,:amount)");
$db->bind(':groupid', $groupin);
$db->bind(':catid', $catin);
$db->bind(':itemcode', $scodein);
$db->bind(':item', $itemin);
$db->bind(':locid', $locin);
$db->bind(':ddate', $ddate);
$db->bind(':increase', $qtyin);
$db->bind(':decrease', 0);
$db->bind(':ref_no', $reference);
$db->bind(':transtype', 'TSF');
$db->bind(':amount', $costin * $qtyin);
		
$db->execute();

$db->closeDB();

?>
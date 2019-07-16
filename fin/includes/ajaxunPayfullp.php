<?php
session_start();

$usersession = $_SESSION['usersession'];

$id = $_REQUEST['id'];
$unalloc = $_REQUEST['unalloc'];
$refno = $_REQUEST['refno'];
$acc = $_REQUEST['acc'];
$a = explode('~',$acc);
$ac = $a[1];
$sb = $a[2];

$ddate = date('Y-m-d');

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$findb = $_SESSION['s_findb'];

$db->query("update ".$findb.".invtrans set paid = paid - ".$unalloc." where uid = ".$id);
$db->execute();

$db->query("select cash as csh, cheque as chq, eftpos as eft, ccard as crd from ".$findb.".invhead where ref_no = '".$refno."'");
$row = $db->single();
extract($row);

$tounalloc = $unalloc;
$rev = 0;

if ($csh > 0) {
	if ($csh >= $tounalloc) {
		$rev = $tounalloc;
	} else {
		$rev = $csh;
	}
	$db->query('update '.$findb.'.invhead set cash = cash - '.$rev.' where ref_no = "'.$refno.'"');
	$db->execute();
	$tounalloc = $tounalloc - $rev;
}

if ($tounalloc > 0) {
	if ($chq > 0) {
		if ($chq >= $tounalloc) {
			$rev = $tounalloc;
		} else {
			$rev = $chq;
		}
		$db->query('update '.$findb.'.invhead set cheque = cheque - '.$rev.' where ref_no = "'.$refno.'"');
		$db->execute();
		$tounalloc = $tounalloc - $rev;
	}	
}

if ($tounalloc > 0) {
	if ($eft > 0) {
		if ($eft >= $tounalloc) {
			$rev = $tounalloc;
		} else {
			$rev = $eft;
		}
		$db->query('update '.$findb.'.invhead set eftpos = eftpos - '.$rev.' where ref_no = "'.$refno.'"');
		$db->execute();
		$tounalloc = $tounalloc - $rev;
	}	
}

if ($tounalloc > 0) {
	if ($crd > 0) {
		if ($crd >= $tounalloc) {
			$rev = $tounalloc;
		} else {
			$rev = $crd;
		}
		$db->query('update '.$findb.'.invhead set ccard = ccard - '.$rev.' where ref_no = "'.$refno.'"');
		$db->execute();
		$tounalloc = $tounalloc - $rev;
	}	
}

$db->query("update ".$findb.".trmain set allocated = allocated - ".$unalloc." where accountno = ".$ac." and sub = ".$sb." and reference = '".$refno."'");
$db->execute();

$db->query('insert into '.$findb.'.allocations (ddate,amount,fromref,toref) values ("'.$ddate.'",'.$unalloc.',"'.$refno.'","Unallocate")');
$db->execute();

$db->closeDB();

?>

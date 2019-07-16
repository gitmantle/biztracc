<?php
session_start();
$id = $_REQUEST['id'];
$fxcode = $_REQUEST['fxcode'];
$fxrate = $_REQUEST['fxrate'];
$topay = $_REQUEST['topay'] * $fxrate;
$refno = $_REQUEST['refno'];
$transref = $_SESSION['s_transref'];
$ddate = $_REQUEST['ddate'];
$payref = $_REQUEST['payref'];
$usersession = $_SESSION['usersession'];

$ddateh = date("Y-m-d");

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$paytable = 'ztmp'.$user_id.'_payments';

$findb = $_SESSION['s_findb'];

$db->query("update ".$findb.".invtrans set paid = paid + ".$topay." where uid = ".$id);
$db->execute();

$db->query("select ref_no from ".$findb.".invtrans where uid = ".$id);
$row = $db->single();
extract($row);
$ref = $ref_no;

$db->query("update ".$findb.".invhead set cheque = cheque + ".$topay." where ref_no = '".$refno."'");
$db->execute();

$db->query("select accountno,sub from ".$findb.".invhead where ref_no = '".$ref."'");
$row = $db->single();
extract($row);
$ac = $accountno;
$sb = $sub;

$db->query("update ".$findb.".trmain set allocated = allocated + ".$topay." where accountno = ".$ac." and sub = ".$sb." and reference = '".$ref."'");
$db->execute();

$db->query("select ".$findb.".invhead.ddate,".$findb.".invtrans.item,".$findb.".invtrans.quantity,".$findb.".invtrans.price,".$findb.".invtrans.value,".$findb.".invtrans.tax,(".$findb.".invtrans.value + ".$findb.".invtrans.tax) as total, ".$findb.".invtrans.paid from ".$findb.".invhead,".$findb.".invtrans where ".$findb.".invhead.ref_no = ".$findb.".invtrans.ref_no and ".$findb.".invtrans.uid = ".$id);
$row = $db->single();
extract($row);

$db->query("insert into ".$findb.".".$paytable." (refno,ddate,item,quantity,price,value,gst,total,paid) values (:refno,:ddate,:item,:quantity,:price,:value,:gst,:total,:paid)");
$db->bind(':refno', $ref);
$db->bind(':ddate', $ddateh);
$db->bind(':item', $item);
$db->bind(':quantity', $quantity);
$db->bind(':price', $price);
$db->bind(':value', $value);
$db->bind(':gst', $tax);
$db->bind(':total', $total);
$db->bind(':paid', $paid);

$db->execute();

$db->query("update ".$findb.".trmain set inv = '".$refno."' where accountno = ".$ac." and sub = ".$sb." and reference = '".$transref."'");
$db->execute();

$db->query('insert into '.$findb.'.allocations (ddate,amount,fromref,toref,currency,rate) values ("'.$ddateh.'",'.$topay.',"'.$payref.'","'.$ref.'","'.$fxcode.'",'.$fxrate.')');
$db->execute();

$db->closeDB();
?>

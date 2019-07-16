<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$table = 'ztmp'.$user_id.'_trans';

$acc2dr = $_REQUEST['acc2dr'];
$subdr = $_REQUEST['subdr'];
$brdr = $_REQUEST['brdr'];
$acc2cr = $_REQUEST['acc2cr'];
$subcr = $_REQUEST['subcr'];
$brcr = $_REQUEST['brcr'];
$ddate = $_REQUEST['ddate'];
$descript1 = $_REQUEST['descript1'];
$reference = $_REQUEST['reference'];
$yourref = $_REQUEST['yourref'];
$amount = $_REQUEST['amount'];
$tax = $_REQUEST['tax'];
$taxtype = $_REQUEST['taxtype'];
$taxpcent = $_REQUEST['taxpcent'];
$total = $_REQUEST['total'];
$refindex = $_REQUEST['refindex'];
$taxindex = $_REQUEST['taxindex'];
$a2d = $_REQUEST['a2d'];
$a2c = $_REQUEST['a2c'];
$drgst = $_REQUEST['drgst'];
$crgst = $_REQUEST['crgst'];
$fxcode = $_REQUEST['fxcode'];
$fxrate = $_REQUEST['fxrate'];

$findb = $_SESSION['s_findb'];

// set local currency
$db->query("select currency from ".$findb.".forex where def_forex = 'Yes'");
$row = $db->single();
extract($row);
$_SESSION['s_localcurrency'] = $currency;

$fxcode = ' ';
$fxrate = 1;


/*
$acc2dr = 751;
$subdr = 0;
$brdr = '1000';
$acc2cr = 30000004;
$subcr = 0;
$brcr = '1000';
$ddate = '2015-03-01';
$descript1 = 'test';
$reference = 'REC35';
$amount = 9.10;
$tax = 0.90;
$taxtype = 'GST';
$taxpcent = 10.00;
$total = 10.00;
$refindex = 1;
$taxindex = 1;
$a2d = 0;
$a2c = 0;
$drgst = 'Y';
$crgst = 'Y';



echo $acc2dr."<br>";
echo $subdr."<br>";
echo $brdr."<br>";
echo $acc2cr."<br>";
echo $subcr."<br>";
echo $brcr."<br>";
echo $ddate."<br>";
echo $descript1."<br>";
echo $reference."<br>";
echo $amount."<br>";
echo $tax."<br>";
echo $taxtype."<br>";
echo $taxpcent."<br>";
echo $total."<br>";
echo $refindex."<br>";
echo $taxindex."<br>";
echo $a2d."<br>";
echo $a2c."<br>";
echo $drgst."<br>";
echo $crgst."<br>";
*/

$db->query("insert into ".$findb.".".$table." (acc2dr,subdr,brdr,acc2cr,subcr,brcr,ddate,descript1,reference,amount,tax,taxtype,taxpcent,total,refindex,taxindex,a2d,a2c,drgst,crgst,currency,rate,your_ref) values (:acc2dr,:subdr,:brdr,:acc2cr,:subcr,:brcr,:ddate,:descript1,:reference,:amount,:tax,:taxtype,:taxpcent,:total,:refindex,:taxindex,:a2d,:a2c,:drgst,:crgst,:currency,:rate,:your_ref)");
$db->bind(':acc2dr', $acc2dr);
$db->bind(':subdr', $subdr);
$db->bind(':brdr', $brdr);
$db->bind(':acc2cr', $acc2cr);
$db->bind(':brcr', $brcr);
$db->bind(':subcr', $subcr);
$db->bind(':ddate', $ddate);
$db->bind(':descript1', $descript1);
$db->bind(':reference', $reference);
$db->bind(':amount', $amount);
$db->bind(':tax', $tax);
$db->bind(':taxtype', $taxtype);
$db->bind(':taxpcent', $taxpcent);
$db->bind(':total', $total);
$db->bind(':refindex', $refindex);
$db->bind(':taxindex', $taxindex);
$db->bind(':a2d', $a2d);
$db->bind(':a2c', $a2c);
$db->bind(':drgst', $drgst);
$db->bind(':crgst', $crgst);
$db->bind(':currency', $fxcode);
$db->bind(':rate', $fxrate);
$db->bind(':your_ref', $yourref);
		  
$db->execute();

$db->closeDB();


?>

<?php
session_start();

$usersession = $_SESSION['usersession'];

require_once("../db.php");
$admindb = $_SESSION['s_admindb'];
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

/*

// Load jSignature library to con
require_once('jSignature_Tools_Base30.php');
 
// Get signature string from _POST
$data = $_POST['signature'];
$data = str_replace('image/jsignature;base30,', '', $data);
 
// Create jSignature object
$signature = new jSignature_Tools_Base30();
 
// Decode base30 format
$a = $signature-&gt;Base64ToNative($data);
 
// Create a image            
$im = imagecreatetruecolor(1295, 328);
 
// Save transparency for PNG
imagesavealpha($im, true);
 
// Fill background with transparency
$trans_colour = imagecolorallocatealpha($im, 0, 0, 0, 127);
imagefill($im, 0, 0, $trans_colour);
 
// Set pen thickness
imagesetthickness($im, 5);
 
// Set pen color to blue            
$blue = imagecolorallocate($im, 0, 0, 255);
 
// Loop through array pairs from each signature word
for ($i = 0; $i &lt; count($a); $i++)
{
    // Loop through each pair in a word
    for ($j = 0; $j &lt; count($a[$i]['x']); $j++)
    {
         // Make sure we are not on the last coordinate in the array
         if ( ! isset($a[$i]['x'][$j]) or ! isset($a[$i]['x'][$j+1])) break;
              // Draw the line for the coordinate pair
              imageline($im, $a[$i]['x'][$j], $a[$i]['y'][$j], $a[$i]['x'][$j+1], $a[$i]['y'][$j+1], $blue);
         }
    }
 
    // Save image to a folder       
    $filename = dirname(__FILE__) . '/signature.png'; // Make folder path is writeable
    imagepng($im, $filename); // Removing $filename will output to browser instead of saving
 
    // Clean up
    imagedestroy($im);
	
*/




$gstinvpay = $_REQUEST['gstinvpay'];
$clientid = $_REQUEST['clientid'];
$st1 = $_REQUEST['st1'];	
$st2 = $_REQUEST['st2'];
$st3 = $_REQUEST['st3'];
$st4 = $_REQUEST['st4'];
$sig = $_REQUEST['sig'];
$acc = $_REQUEST['acc']; 
$note = $_REQUEST['note'];
$email = $_REQUEST['email'];
$address = $_REQUEST['address'];
$phone = $_REQUEST['phone'];
$qty1 = $_REQUEST['qty1'];
$qty2 = $_REQUEST['qty2'];
$qty3 = $_REQUEST['qty3'];
$qty4 = $_REQUEST['qty4'];
$amt1 = $_REQUEST['amt1'];
$amt2 = $_REQUEST['amt2'];
$amt3 = $_REQUEST['amt3'];
$amt4 = $_REQUEST['amt4'];

$tradetable = 'ztmp'.$user_id.'_trading';

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "drop table if exists ".$tradetable;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$tradetable." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, itemcode varchar(30) default '',item varchar(100) default '',price decimal(16,2) default 0, unit varchar(20) default '',quantity decimal(16,2) default 0,tax decimal(16,2) default 0, value decimal(16,2) default 0, discount decimal(16,2) default 0, disctype char(1) default '', discamount decimal(16,2) default 0, tot decimal(16,2) default 0, taxindex int(11) default 0, taxtype char(3) default '', taxpcent decimal(5,2) default 0, sellacc int(11) default 0, sellbr char(4) default '', sellsub int(4) default 0, purchacc int(11) default 0, purchbr char(4) default '', purchsub int(4) default 0, groupid int(11) default 0, catid int(11) default 0, loc int(11) default 0 )  engine myisam";
$calc = mysql_query($query) or die(mysql_error());

// build records in temp trading table

if ($st1 != "") {
	$s = explode('~',$st1);
	$stkcode = $s[0];
	$stkname = $s[1];
	$stkunit = $s[2];
	$stax = $s[3];
	$sac = $s[4];
	$sbr = $s[5];
	$ssb = $s[6];
	$pac = $s[7];
	$pbr = $s[8];
	$psb = $s[9];
	$grp = $s[10];
	$cat = $s[11];
	$cost = $s[12];
	$setsell = $s[13];
	$trackserial = $s[14];
	$staxpcent = $s[15];
	
	$val = $amt1 * 100/(100 + $staxpcent);
	$st = $amt1 - $val;

/*
itemcode,item,         price, unit,quantity,tax,value,tot, taxtype,taxpcent,sellacc,sellbr,sellsub,purchacc,purchbr,purchsub,groupid,catid,loc) 'CAT6',  'Cat 6 Cable',3.00,'Metre',4,      3.6, 24 , 27.60,'1',   15.00,    1,      '',    0,      101,    '',     0,       Cat 6 Cable,3,1)
*/

	$q = "insert into ".$tradetable." (itemcode,item,price,unit,quantity,tax,value,tot,taxtype,taxpcent,sellacc,sellbr,sellsub,purchacc,purchbr,purchsub,groupid,catid,loc) values ('".$stkcode."','".$stkname."',".$cost.",'".$stkunit."',".$qty1.",".$st.",".$val.",".$amt1.",'".$stax."',".$staxpcent.",".$sac.",'".$sbr."',".$ssb.",".$pac.",'".$pbr."',".$psb.",".$grp.",".$cat.",1)";
$r = mysql_query($q) or die(mysql_error().' '.$q);
}

if ($st2 != "") {
	$s = explode('~',$st2);
	$stkcode = $s[0];
	$stkname = $s[1];
	$stkunit = $s[2];
	$stax = $s[3];
	$sac = $s[4];
	$sbr = $s[5];
	$ssb = $s[6];
	$pac = $s[7];
	$pbr = $s[8];
	$psb = $s[9];
	$grp = $s[10];
	$cat = $s[11];
	$cost = $s[12];
	$setsell = $s[13];
	$trackserial = $s[14];
	$staxpcent = $s[15];
	
	$val = $amt2 * 100/(100 + $staxpcent);
	$st = $amt2 - $val;
	
	$q = "insert into ".$tradetable." (itemcode,item,price,unit,quantity,tax,value,tot,taxtype,taxpcent,sellacc,sellbr,sellsub,purchacc,purchbr,purchsub,groupid,catid,loc) values ('".$stkcode."','".$stkname."',".$cost.",'".$stkunit."',".$qty1.",".$st.",".$val.",".$amt1.",'".$stax."',".$staxpcent.",".$sac.",'".$sbr."',".$ssb.",".$pac.",'".$pbr."',".$psb.",".$grp.",".$cat.",1)";
$r = mysql_query($q) or die(mysql_error().' '.$q);
}

if ($st3 != "") {
	$s = explode('~',$st3);
	$stkcode = $s[0];
	$stkname = $s[1];
	$stkunit = $s[2];
	$stax = $s[3];
	$sac = $s[4];
	$sbr = $s[5];
	$ssb = $s[6];
	$pac = $s[7];
	$pbr = $s[8];
	$psb = $s[9];
	$grp = $s[10];
	$cat = $s[11];
	$cost = $s[12];
	$setsell = $s[13];
	$trackserial = $s[14];
	$staxpcent = $s[15];
	
	$val = $amt3 * 100/(100 + $staxpcent);
	$st = $amt3 - $val;
	
	$q = "insert into ".$tradetable." (itemcode,item,price,unit,quantity,tax,value,tot,taxtype,taxpcent,sellacc,sellbr,sellsub,purchacc,purchbr,purchsub,groupid,catid,loc) values ('".$stkcode."','".$stkname."',".$cost.",'".$stkunit."',".$qty1.",".$st.",".$val.",".$amt1.",'".$stax."',".$staxpcent.",".$sac.",'".$sbr."',".$ssb.",".$pac.",'".$pbr."',".$psb.",".$grp.",".$cat.",1)";
$r = mysql_query($q) or die(mysql_error().' '.$q);
}

if ($st4 != "") {
	$s = explode('~',$st4);
	$stkcode = $s[0];
	$stkname = $s[1];
	$stkunit = $s[2];
	$stax = $s[3];
	$sac = $s[4];
	$sbr = $s[5];
	$ssb = $s[6];
	$pac = $s[7];
	$pbr = $s[8];
	$psb = $s[9];
	$grp = $s[10];
	$cat = $s[11];
	$cost = $s[12];
	$setsell = $s[13];
	$trackserial = $s[14];
	$staxpcent = $s[15];
	
	$val = $amt4 * 100/(100 + $staxpcent);
	$st = $amt4 - $val;
	
	$q = "insert into ".$tradetable." (itemcode,item,price,unit,quantity,tax,value,tot,taxtype,taxpcent,sellacc,sellbr,sellsub,purchacc,purchbr,purchsub,groupid,catid,loc) values ('".$stkcode."','".$stkname."',".$cost.",'".$stkunit."',".$qty1.",".$st.",".$val.",".$amt1.",'".$stax."',".$staxpcent.",".$sac.",'".$sbr."',".$ssb.",".$pac.",'".$pbr."',".$psb.",".$grp.",".$cat.",1)";
$r = mysql_query($q) or die(mysql_error().' '.$q);
}

// post trading transaction

$ddate = date('Y-m-d');


// get next invoice number

$query = "lock tables numbers write";
$result = mysql_query($query) or die($query);
$query = "select inv from numbers";
$result = mysql_query($query) or die(mysql_error().' '.$query);
$row = mysql_fetch_array($result);
extract($row);
$refno = $inv + 1;
$query = "update numbers set inv = ".$refno;
$result = mysql_query($query) or die($query);
$query = "unlock tables";
$result = mysql_query($query) or die($query);
$a = explode('~',$acc);
$clientac = $a[1];
$clientsb = $a[2];
$clientname = $a[0];
$reference = 'INV'.$refno;

include_once("../fin/includes/classPostTrade.php");

$oInv = new classPostTrade;	

$oInv->transtype = 'INV';
$oInv->ddate = $ddate;
$oInv->descript1 = $note;
$oInv->reference = $ref;
$oInv->acc = $clientac;
$oInv->asb = $clientsb;
$oInv->postaladdress = $postaladdress;
$oInv->client = $clientname;

 $oInv->posttrade();
 

 

// email invoice
//require '../fin/PrintTrading.php?type="inv"&tradingref='.$ref.'&doemail=Y';


//$fpdf->Image('data://image/png;base64,'.$image_data,null, null, 0, 0, 'png');



?>


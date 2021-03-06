<?php
session_start();

require("../db.php");

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select gsttype as gstinvpay,coyname,email from globals";
$r = mysql_query($q) or die (mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$company_name = $coyname;
$company_email = $email;

$ddate = date('Y-m-d');

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice from Device</title>

    <!-- Bootstrap -->
    <link href="bootstrap_files/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>
    <link href="bootstrap_files/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<link rel="stylesheet" type="text/css" media="screen" href="../includes/jquery/themes/ui.multiselect.css"/> 

<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.core.js"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.position.js"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.menu.js"></script>
<script type="text/javascript" src="../includes/jquery/external/jquery.bgiframe-2.1.1.js"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>

    
<!--[if lt IE 9]>
<script type="text/javascript" src="js/flashcanvas.js"></script>
[endif]-->
<script src="js/jSignature.min.js"></script>

<script>
$(document).ready(function(){
  var $sigdiv = $("#signature")

  $sigdiv.jSignature()

  $('#clearSignature').click(function (){
    $sigdiv.jSignature("reset");
  })
/*
  $('#acceptSignature').click(function(){
    var datapair =   $("#signature").jSignature("getData", "svgbase64")
    $("#signatureBytes").val("data:" + datapair[0] + "," + datapair[1] )
  })
*/
})
/*
    $(document).ready(function() {
        $("#signature").jSignature()
       // var $sigdiv = $("#signature")
       // var datapair = $sigdiv.jSignature("getData", "base30")
    })
	
*/
</script>    
    

<script>

window.name = "trade_inv";

function emailtrading(type,rf) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +10;
	window.open('../fin/PrintTrading.php?type='+type+'&tradingref='+rf+'&doemail=Y'+'&noscreen=Y','tipdf','toolbar=0,scrollbars=1,height=10,width=10,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}		



function inv() {
	
	var clientid = document.getElementById('clientid').value;

	jQuery.ajaxSetup({async:false});

	$.ajax({
        type: 'POST',
        url: "ajaxSaveSig.php",
        data: { signature: $('#signature').jSignature("getData", "base30"), clientid: clientid }
    });
		

	var sig = document.getElementById('signatureBytes');
	var gstinvpay = document.getElementById('gstinvpay').value;
	var note = document.getElementById('tnote').value;
	var st1 = document.getElementById('st1').value;
	var st2 = document.getElementById('st2').value;
	var st3 = document.getElementById('st3').value;
	var st4 = document.getElementById('st4').value;
	var acct = document.getElementById('acc').value;
	var email = document.getElementById('temail').value;
	var address = document.getElementById('tadd').value;
	var phone = document.getElementById('tphone').value;
	var qty1 = document.getElementById('tqty1').value;
	var qty2 = document.getElementById('tqty2').value;
	var qty3 = document.getElementById('tqty3').value;
	var qty4 = document.getElementById('tqty4').value;
	var amt1 = document.getElementById('tamt1').value;
	var amt2 = document.getElementById('tamt2').value;
	var amt3 = document.getElementById('tamt3').value;
	var amt4 = document.getElementById('tamt4').value;
	
	var ok = "Y";
	if (acc == "") {
		alert("Please enter a client.");
		ok = "N";
		return false;
	}
	if (email == "") {
		alert("Please enter an email address.");
		ok = "N";
		return false;
	}
	if (st1 == "" && st2 == "" && st3 == "" && st4 == "") {

		alert("Please enter at least one item.");
		ok = "N";
		return false;
	}
	
	
	if (ok == "Y") {
		var ac = acct.split('~');
		var client = ac[0];
		var acno = ac[1];
		var acsb = ac[2];
		var loc = 1;
		var paymethod = '';
		var type = 'inv';
	  	var ddate = "<?php echo $ddate; ?>";
		var descript = document.getElementById('tnote').value;
		
/*	
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('ajaxinv.php?gstinvpay='+gstinvpay+"&clientid="+clientid+"&st1="+st1+"&st2="+st2+"&st3="+st3+"&st4="+st4+"&amt1="+amt1+"&amt2="+amt2+"&amt3="+amt3+"&amt4="+amt4+"&qty1="+qty1+"&qty2="+qty2+"&qty3="+qty3+"&qty4="+qty4,'addinv','toolbar=0,scrollbars=1,height=170,width=800,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
			
*/		
				
			
		$.get("ajaxinv.php", {gstinvpay:gstinvpay,clientid:clientid,st1:st1,st2:st2,st3:st3,st4:st4,qty1:qty1,qty2:qty2,qty3:qty3,qty4:qty4,amt1:amt1,amt2:amt2,amt3:amt3,amt4:amt4}, function(data){rno= data});
	

/*
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +375;
	window.open('../fin/includes/ajaxPostTrade.php?type='+type+"&ddate="+ddate+"&descript="+descript+"&ref="+refno+"&acc="+acno+"&asb="+acsb+"&loc="+loc+"&postaladdress="+address+"&deliveryaddress="+address+"&clt="+client+"&paymentod="+paymethod,'pinv','toolbar=0,scrollbars=1,height=170,width=800,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);
*/			
		
			
		$.get("../fin/includes/ajaxPostTrade.php", {type:type,ddate:ddate,descript:descript,ref:rno,acc:acno,asb:acsb,loc:loc,postaladdress:address,deliveryaddress:address,clt:client,paymethod:paymethod}, function(data){});
		
		
		emailtrading(type,rno);
		
		jQuery.ajaxSetup({async:true});

	}	


}


function transvisibledr() {
	var source = 'inv';
	$.get("../ajax/ajaxUpdtSource.php", {source: source}, function(data){
	});
	document.getElementById('drselect').style.visibility = 'visible';											
	document.getElementById('searchdr').value = "";
	document.getElementById('searchdr').focus();
}	

function sboxhidedr() {
	document.getElementById('drselect').style.visibility = 'hidden';											
}

function gridReload1dr(){ 
	var dr_mask = jQuery("#searchdr").val(); 
	dr_mask = dr_mask.toUpperCase();
	jQuery("#selectdrlist").setGridParam({url:"selectdr.php?dr_mask="+dr_mask}).trigger("reloadGrid"); 
} 

function doSearchdr(){ 
		var timeoutHnd = setTimeout(gridReload1dr,500); 
	} 
	
function sboxhidestk(n) {
	switch(n) {
		case 1:	
		document.getElementById('stkselect1').style.visibility = 'hidden';
		break;
		case 2:
		document.getElementById('stkselect2').style.visibility = 'hidden';
		break;
		case 3:
		document.getElementById('stkselect3').style.visibility = 'hidden';
		break;
		case 4:
		document.getElementById('stkselect4').style.visibility = 'hidden';
		break;
	}
}

function stkvisible(n) {
	switch(n) {
		case 1:	
		document.getElementById('stkselect1').style.visibility = 'visible';		
		break;
		case 2:	
		document.getElementById('stkselect2').style.visibility = 'visible';		
		break;
		case 3:	
		document.getElementById('stkselect3').style.visibility = 'visible';		
		break;
		case 4:	
		document.getElementById('stkselect4').style.visibility = 'visible';		
		break;
	}
}

function gridReloadstk1(){ 
	var st_mask = jQuery("#searchstk1").val(); 
	st_mask = st_mask.toUpperCase();
	jQuery("#selectstklist1").setGridParam({url:"selectstk1.php?st_mask="+st_mask}).trigger("reloadGrid"); 
} 
function gridReloadstk2(){ 
	var st_mask = jQuery("#searchstk2").val(); 
	st_mask = st_mask.toUpperCase();
	jQuery("#selectstklist2").setGridParam({url:"selectstk2.php?st_mask="+st_mask}).trigger("reloadGrid"); 
} 
function gridReloadstk3(){ 
	var st_mask = jQuery("#searchstk3").val(); 
	st_mask = st_mask.toUpperCase();
	jQuery("#selectstklist3").setGridParam({url:"selectstk3.php?st_mask="+st_mask}).trigger("reloadGrid"); 
} 
function gridReloadstk4(){ 
	var st_mask = jQuery("#searchstk4").val(); 
	st_mask = st_mask.toUpperCase();
	jQuery("#selectstklist4").setGridParam({url:"selectstk4.php?st_mask="+st_mask}).trigger("reloadGrid"); 
} 

function doSearchstk(n){ 
	switch(n) {
		case 1:	
		var timeoutHnd = setTimeout(gridReloadstk1,500); 
		break;
		case 2:	
		var timeoutHnd = setTimeout(gridReloadstk2,500); 
		break;
		case 3:	
		var timeoutHnd = setTimeout(gridReloadstk3,500); 
		break;
		case 4:	
		var timeoutHnd = setTimeout(gridReloadstk4,500); 
		break;
	}
}
	
function setselect(acc,ledger) {
	
	var a = acc.split('~');
	var ac = a[0];
	var br = a[1];
	var sb = a[2];
	var acname = a[3];
	var cid = a[4];
	var priceband = a[5];
	var memberid = cid;
	document.getElementById('clientid').value = cid;
	document.getElementById('priceband').value = priceband;
	var acc = acname+'~'+ac+'~'+sb+'~'+br;
	document.getElementById('acc').value = acc;
	document.getElementById('DRaccount').value = acname;
	jQuery.ajaxSetup({async:false});
	$.get("ajaxGetDrDetails.php", {acc:ac,sb:sb}, function(data){
		var e = data.split('~');
		var ph = e[0];
		var em = e[1];
		var ad = e[2];
		document.getElementById('tphone').value = ph;
		document.getElementById('temail').value = em;
		document.getElementById('tadd').value = ad;
	});
	jQuery.ajaxSetup({async:true});
	document.getElementById('drselect').style.visibility = 'hidden';											
}

function setstkselect1(stk1) {
	var a = stk1.split('~');
	var scode = a[0];
	var sname = a[1];
	var cost = a[12];
	var setsell = a[13];
	var trackserial = a[14];
	var staxpcent = a[15];
	var s = scode+'~'+sname;
	var qty = document.getElementById('tqty1').value;
	var priceband = document.getElementById('priceband').value;
	
	document.getElementById('titem1').value = s;
	document.getElementById('st1').value = stk1;
	
	if (setsell > 0 ) {
		var numb = parseFloat(qty)*parseFloat(setsell)*parseFloat(1+staxpcent/100);	
		document.getElementById('tamt1').value = numb.toFixed(2);
	} else {
		$.get("../fin/includes/ajaxgetpricepcent.php", {priceband: priceband}, function(data){
			var addpcent = data;	
			var sellat = cost * (1 + addpcent/100);
			var numb = parseFloat(qty)*parseFloat(sellat)*parseFloat(1+staxpcent/100);
			document.getElementById('tamt1').value = numb.toFixed(2);
		});
	}
		
	document.getElementById('stkselect1').style.visibility = 'hidden';
}

function setstkselect2(stk2) {
	var a = stk2.split('~');
	var scode = a[0];
	var sname = a[1];
	var cost = a[12];
	var setsell = a[13];
	var trackserial = a[14];
	var staxpcent = a[15];
	var s = scode+'~'+sname;
	var qty = document.getElementById('tqty2').value;
	var priceband = document.getElementById('priceband').value;
	
	document.getElementById('titem2').value = s;
	document.getElementById('st2').value = stk2;
	
	if (setsell > 0 ) {
		var numb = parseFloat(qty)*parseFloat(setsell)*parseFloat(1+staxpcent/100);	
		document.getElementById('tamt2').value = numb.toFixed(2);
	} else {
		$.get("../fin/includes/ajaxgetpricepcent.php", {priceband: priceband}, function(data){
			var addpcent = data;	
			var sellat = cost * (1 + addpcent/100);
			var numb = parseFloat(qty)*parseFloat(sellat)*parseFloat(1+staxpcent/100);
			document.getElementById('tamt2').value = numb.toFixed(2);
		});
	}
		
	document.getElementById('stkselect2').style.visibility = 'hidden';
}

function setstkselect3(stk3) {
	var a = stk3.split('~');
	var scode = a[0];
	var sname = a[1];
	var cost = a[12];
	var setsell = a[13];
	var trackserial = a[14];
	var staxpcent = a[15];
	var s = scode+'~'+sname;
	var qty = document.getElementById('tqty3').value;
	var priceband = document.getElementById('priceband').value;
	
	document.getElementById('titem3').value = s;
	document.getElementById('st3').value = stk3;
	
	if (setsell > 0 ) {
		var numb = parseFloat(qty)*parseFloat(setsell)*parseFloat(1+staxpcent/100);	
		document.getElementById('tamt3').value = numb.toFixed(2);
	} else {
		$.get("../fin/includes/ajaxgetpricepcent.php", {priceband: priceband}, function(data){
			var addpcent = data;	
			var sellat = cost * (1 + addpcent/100);
			var numb = parseFloat(qty)*parseFloat(sellat)*parseFloat(1+staxpcent/100);
			document.getElementById('tamt3').value = numb.toFixed(2);
		});
	}
		
	document.getElementById('stkselect3').style.visibility = 'hidden';
}


function setstkselect4(stk4) {
	var a = stk4.split('~');
	var scode = a[0];
	var sname = a[1];
	var cost = a[12];
	var setsell = a[13];
	var trackserial = a[14];
	var staxpcent = a[15];
	var s = scode+'~'+sname;
	var qty = document.getElementById('tqty4').value;
	var priceband = document.getElementById('priceband').value;
	
	document.getElementById('titem4').value = s;
	document.getElementById('st4').value = stk4;
	
	if (setsell > 0 ) {
		var numb = parseFloat(qty)*parseFloat(setsell)*parseFloat(1+staxpcent/100);	
		document.getElementById('tamt4').value = numb.toFixed(2);
	} else {
		$.get("../fin/includes/ajaxgetpricepcent.php", {priceband: priceband}, function(data){
			var addpcent = data;	
			var sellat = cost * (1 + addpcent/100);
			var numb = parseFloat(qty)*parseFloat(sellat)*parseFloat(1+staxpcent/100);
			document.getElementById('tamt4').value = numb.toFixed(2);
		});
	}
		
	document.getElementById('stkselect4').style.visibility = 'hidden';
}

function tojob() {
	document.getElementById('client').style.visibility = 'hidden';
	document.getElementById('job').style.visibility = 'visible';
	document.getElementById('pay').style.visibility = 'hidden';
}
function toclient() {
	document.getElementById('client').style.visibility = 'visible';
	document.getElementById('job').style.visibility = 'hidden';
	document.getElementById('pay').style.visibility = 'hidden';
}
function topay() {
	document.getElementById('client').style.visibility = 'hidden';
	document.getElementById('job').style.visibility = 'hidden';
	document.getElementById('pay').style.visibility = 'visible';
}



function pay() {
	document.getElementById('client').style.visibility = 'hidden';
	document.getElementById('job').style.visibility = 'hidden';
	document.getElementById('pay').style.visibility = 'hidden';
	var payemail = document.getElementById('temail').value;
	var amt1 = document.getElementById('tamt1').value;
	var amt2 = document.getElementById('tamt2').value;
	var amt3 = document.getElementById('tamt3').value;
	var amt4 = document.getElementById('tamt4').value;
	var amt = parseFloat(amt1)+parseFloat(amt2)+parseFloat(amt3)+parseFloat(amt4);
	var amt = amt.toFixed(2);
	var itmn = "<?php echo $company_name; ?>"+" Parts and Labour";
	var cem = "<?php echo $company_email; ?>";
	
	document.getElementById('business').value = cem;
	document.getElementById('amount').value = amt;
	document.getElementById('item_name').value = itmn;
	
	document.getElementById('paynow').style.visibility = 'visible';
}





</script>

  </head>
  <body>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
  <input type="hidden" name="acc" id="acc" value="">
  <input type="hidden" name="gstinvpay" id="gstinvpay" value=<?php echo $gstinvpay; ?>>
  <input type="hidden" name="trading" id="trading" value="inv">
  <input type="hidden" name="clientid" id="clientid" value="0">
  <input type="hidden" name="priceband" id="priceband" value="0">
  <input type="hidden" name="st1" id="st1" value="">
  <input type="hidden" name="st2" id="st2" value="">
  <input type="hidden" name="st3" id="st3" value="">
  <input type="hidden" name="st4" id="st4" value="">
  <input type="hidden" name="sig" id="sig" value="">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" id="business" value="">
<input type="hidden" name="lc" value="NZ">
<input type="hidden" name="item_name" id="item_name" value="">
<input type="hidden" name="amount" id="amount" value="0">
<input type="hidden" name="currency_code" value="NZD">
<input type="hidden" name="button_subtype" value="services">
<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHosted">

<div id = "client" style="position:absolute;visiblity:visible;top:5px;left:15px;">
<div class="container-fluid">
      <div class="row">
        <div class="span3" >
          <h4>Name</h4>
        </div>
        <div class="span9">
			<input type="text" name="DRaccount" id="DRaccount"  />
      <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="drsearch" onClick="transvisibledr()">
       </div>
      </div>
      
      <div class="row">
        <div class="span3">
          <h4>Email</h4>
        </div>
        <div class="span9">
			<input type="text" name="temail" id="temail" />
       </div>
      </div>
      
      <div class="row">
        <div class="span3">
          <h4>Phone</h4>
        </div>
        <div class="span9">
			<input type="text" name="tphone" id="tphone"  />
       </div>
      </div>
      
      <div class="row">
        <div class="span3">
          <h4>Address</h4>
        </div>
        <div class="span9">
			<input type="text" name="tadd" id="tadd"  />
       </div>
      <div class="row">
        <div class="span3">
        	&nbsp;
        </div>
        <div class="span9">
          <a class="btn pull-right" href="javascript: tojob();">Next</a>
       </div>
      </div>
</div>
</div>

<div id = "job" style="position:absolute;visibility:hidden;top:5px;left:15px;">
<div class="container">
      <div class="row">
        <div class="span3">
			<input type="text" name="tqty1" id="tqty1" placeholder="Quantity" class="input-small"/>
    <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="stsearch" onClick="stkvisible(1)">
       </div>
        <div class="span6">
			<input type="text" name="titem1" id="titem1" placeholder="Item" class="input-xlarge"/>
       </div>
        <div class="span3">
			<input type="text" name="tamt1" id="tamt1"  placeholder="Amount" value="0" class="input-small"/>
      </div>
     </div>
      <div class="row">
        <div class="span3">
			<input type="text" name="tqty2" id="tqty2" placeholder="Quantity" class="input-small"/>
    <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="stsearch" onClick="stkvisible(2)">
       </div>
        <div class="span6">
			<input type="text" name="titem2" id="titem2" placeholder="Item" class="input-xlarge"/>
       </div>
        <div class="span3">
			<input type="text" name="tamt2" id="tamt2"  placeholder="Amount" value="0" class="input-small"/>
      </div>
     </div>
      <div class="row">
        <div class="span3">
			<input type="text" name="tqty3" id="tqty3" placeholder="Quantity" class="input-small"/>
    <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="stsearch" onClick="stkvisible(3)">
       </div>
        <div class="span6">
			<input type="text" name="titem3" id="titem3" placeholder="Item" class="input-xlarge"/>
       </div>
        <div class="span3">
			<input type="text" name="tamt3" id="tamt3"  placeholder="Amount" value="0" class="input-small"/>
      </div>
     </div>
      <div class="row">
        <div class="span3">
			<input type="text" name="tqty4" id="tqty4" placeholder="Hours" class="input-small"/>
    <img src="../images/Search.gif" width="16" height="16" alt="Lookup" id="stsearch" onClick="stkvisible(4)">
       </div>
        <div class="span6">
			<input type="text" name="titem4" id="titem4" placeholder="Labour" class="input-xlarge"/>
       </div>
        <div class="span3">
			<input type="text" name="tamt4" id="tamt4"  placeholder="Amount" value="0" class="input-small"/>
      </div>
     </div>
      <div class="row">
        <div class="span3">
          <h4>Job Description</h4>
      	</div>
        <div class="span9">
			<input type="text" name="tnote" id="tnote" value="" class="input-xlarge"/>
       	</div>
      </div>
      <div class="row">
        <div class="span6">
          <a class="btn" href="javascript: toclient();">Back</a>
          <a class="btn pull-right" href="javascript: topay();">Next</a>
        </div>
        <div class="span3">
          &nbsp;
       </div>
       </div>
</div>
</div>

<div id = "pay" style="position:absolute;visibility:hidden;top:5px;left:15px;">
<div class="container">
      <div class="row">
        <div class="span3">
          <h4>Client Sign off</h4>
      	</div>
        <div class="span9">
            <div id="signature"></div>
            <a href="#" class="btn" id="clearSignature">Clear Signature</a>
            <input type="hidden" id="signatureBytes" value="" />
  			</div>
       	</div>
      </div>
      
      <div class="row">
        <div class="span3">
          &nbsp;
      	</div>
        <div class="span9">
			          <a class="btn" href="javascript: inv()" >Create Invoice</a>
			          <a class="btn pull-right" id="bpay" href="javascript: pay();">Pay Now</a>
        </div>
      </div>
      
     
      <div class="row">
        <div class="span3">
              <a class="btn" href="javascript: tojob();">Back</a>
        </div>
       		<div class="span9">
        	&nbsp;
            </div>
      </div>
      
<div id = "paynow" style="position:absolute;visibility:hidden;top:5px;left:15px;">
<div class="container">
     
     <div class="row">
        <div class="span12">

<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"><img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</div>
</div>


</div>
</div>    
      
      
      
      
</div>
</div>

  <div id="drselect" style="position:absolute;visibility:hidden;top:20px;left:20px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchdr" size="50" onKeyPress="doSearchdr()" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="crclose" onClick="sboxhidedr()">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectdr.php"; ?></td>
      </tr>
    </table>
  </div>
 
  <div id="stkselect1" style="position:absolute;visibility:hidden;top:20px;left:20px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchstk1" size="50" onKeyPress="doSearchstk(1)" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="stkclose" onClick="sboxhidestk(1)">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectstk1.php"; ?></td>
      </tr>
    </table>
  </div>

  <div id="stkselect2" style="position:absolute;visibility:hidden;top:50px;left:20px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchstk2" size="50" onKeyPress="doSearchstk(2)" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="stkclose" onClick="sboxhidestk(2)">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectstk2.php"; ?></td>
      </tr>
    </table>
  </div>

  <div id="stkselect3" style="position:absolute;visibility:hidden;top:50px;left:20px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchstk3" size="50" onKeyPress="doSearchstk(3)" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="stkclose" onClick="sboxhidestk(3)">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectstk3.php"; ?></td>
      </tr>
    </table>
  </div>

  <div id="stkselect4" style="position:absolute;visibility:hidden;top:50px;left:20px;height:239px;width:451px;background-color:<?php echo $bgcolor; ?>;border-width:thick thick thick thick; border-color:<?php echo $bghead; ?>; border-style:solid;">
    <table>
      <tr>
        <td><input type="text" id="searchstk4" size="50" onKeyPress="doSearchstk(4)" /></td>
        <td align="right"><img src="../images/close.png" width="16" height="16" alt="Lookup" id="stkclose" onClick="sboxhidestk(4)">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php include "selectstk4.php"; ?></td>
      </tr>
    </table>
  </div>


</form>
    <script src="bootstrap_files/bootstrap-transition.js"></script>
    <script src="bootstrap_files/bootstrap-alert.js"></script>
    <script src="bootstrap_files/bootstrap-modal.js"></script>
    <script src="bootstrap_files/bootstrap-dropdown.js"></script>
    <script src="bootstrap_files/bootstrap-scrollspy.js"></script>
    <script src="bootstrap_files/bootstrap-tab.js"></script>
    <script src="bootstrap_files/bootstrap-tooltip.js"></script>
    <script src="bootstrap_files/bootstrap-popover.js"></script>
    <script src="bootstrap_files/bootstrap-button.js"></script>
    <script src="bootstrap_files/bootstrap-collapse.js"></script>
    <script src="bootstrap_files/bootstrap-carousel.js"></script>
    <script src="bootstrap_files/bootstrap-typeahead.js"></script>

  </body>
</html>



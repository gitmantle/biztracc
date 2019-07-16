<?php
session_start();
//ini_set('display_errors', true);

$dlist = $_SESSION['s_distlist'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error().' '.$query);
$row = mysql_fetch_array($result);
extract($row);

$nftable = 'ztmp'.$user_id.'_nofunds';

$moduledb = $_SESSION['s_prcdb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "drop table if exists ".$nftable;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$nftable." (uid integer(11), member_id integer(11), depot varchar(50), depot_id int(11), member varchar(75), balance decimal(16,2) default 0, ordered decimal(16,2) default 0, phone varchar (50), mobile varchar(50), email varchar(70)) engine myisam"; 
$calc = mysql_query($query) or die($query);

$q = "insert into ".$nftable." (uid,member_id,depot,depot_id,member,balance,ordered) select uid,member_id,depot,depot_id,member,balance,ordered from distdetail where (distdetail.ordered + distdetail.balance > 0) and distdetail.distlist_id = ".$dlist;
$r = mysql_query($q) or die(mysql_error().' '.$q);

$q = "select uid,member_id from ".$nftable;
$r = mysql_query($q) or die(mysql_error().' '.$q);
while ($row = mysql_fetch_array($r)) {
	extract($row);
	$mid = $member_id;
	$id = $uid;
	
	$moduledb = $_SESSION['s_cltdb'];
	mysql_select_db($moduledb) or die(mysql_error());

	$qp = "select concat(country_code,' (',area_code,') ',comm) as ph from comms where member_id = ".$mid." and comms_type_id = 1 and preferred = 'Y'";
	$rp = mysql_query($qp) or die(mysql_error().' '.$qp);
	$numrows = mysql_num_rows($rp);
	if ($numrows == 0) {
		$qp = "select concat(country_code,' (',area_code,') ',comm) as ph from comms where member_id = ".$mid." and comms_type_id = 1 limit 1";
		$rp = mysql_query($qp) or die(mysql_error().' '.$qp);
		$nrows = mysql_num_rows($rp);
		if ($nrows == 1) {
			$row = mysql_fetch_array($rp);
			extract($row);
			$p = $ph;
		} else {
			$p = '';	
		}
	} else {
		$row = mysql_fetch_array($rp);
		extract($row);
		$p = $ph;
	}
	
	$moduledb = $_SESSION['s_prcdb'];
	mysql_select_db($moduledb) or die(mysql_error());

	$qu = "update ".$nftable." set phone = '".$p."' where uid = ".$id;
	$ru = mysql_query($qu) or die(mysql_error().' '.$qu);
	
	$moduledb = $_SESSION['s_cltdb'];
	mysql_select_db($moduledb) or die(mysql_error());

	$qm = "select comm as mob from comms where member_id = ".$mid." and comms_type_id = 3 and preferred = 'Y'";
	$rm = mysql_query($qm) or die(mysql_error().' '.$qm);
	$numrows = mysql_num_rows($rm);
	if ($numrows == 0) {
		$qm = "select comm as mob from comms where member_id = ".$mid." and comms_type_id = 3 limit 1";
		$rm = mysql_query($qm) or die(mysql_error().' '.$qm);
		$nrows = mysql_num_rows($rm);
		if ($nrows == 1) {
			$row = mysql_fetch_array($rm);
			extract($row);
			$m = $mob;
		} else {
			$m = '';	
		}
	} else {
		$row = mysql_fetch_array($rm);
		extract($row);
		$m = $mob;
	}
 
	$moduledb = $_SESSION['s_prcdb'];
	mysql_select_db($moduledb) or die(mysql_error());

	$qu = "update ".$nftable." set mobile = '".$m."' where uid = ".$id;
	$ru = mysql_query($qu) or die(mysql_error().' '.$qu);
	
	$moduledb = $_SESSION['s_cltdb'];
	mysql_select_db($moduledb) or die(mysql_error());

	$qe = "select comm as em from comms where member_id = ".$mid." and comms_type_id = 4 and preferred = 'Y'";
	$re = mysql_query($qe) or die(mysql_error().' '.$qe);
	$numrows = mysql_num_rows($re);
	if ($numrows == 0) {
		$qes = "select comm as em from comms where member_id = ".$mid." and comms_type_id = 4 limit 1";
		$res = mysql_query($qes) or die(mysql_error().' '.$qes);
		$nrows = mysql_num_rows($res);
		if ($nrows == 1) {
			$row = mysql_fetch_array($res);
			extract($row);
			$e = $em;
		} else {
			$e = '';	
		}
	} else {
		$row = mysql_fetch_array($res);
		extract($row);
		$e = $em;
	}
	
	$moduledb = $_SESSION['s_prcdb'];
	mysql_select_db($moduledb) or die(mysql_error());

	$qu = "update ".$nftable." set email = '".$e."' where uid = ".$id;
	$ru = mysql_query($qu) or die(mysql_error().' '.$qu);		

}

include 'jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");
// Get the needed parameters passed from the main grid

//construct where clause 
$_SESSION['s_distlist'] = $dlist;

$dep = $_SESSION['s_udepot'];
if ($dep == 0) {
	$where = " 1 = 1 "; 
} else {
	$where = " depot_id = ".$dep; 
}


// Create the jqGrid instance
$grid = new jqGridRender($conn);

// the actual query for the grid data
$grid->SelectCommand = "select uid,member_id,depot,member,balance,ordered,phone,mobile,email from ".$nftable." where ".$where;
																							
																				
// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getnofunds.php');



// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Members with insufficient funds",
    "rowNum"=>18,
    "sortname"=>"depot, member",
    "rowList"=>array(18,50,100),
	"height"=>420,
	"width"=>800
    ));


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>25, "hidden"=>true));
$grid->setColProperty("member_id", array("label"=>"mid", "width"=>25, "hidden"=>true));
$grid->setColProperty("depot", array("label"=>"Depot", "width"=>100));
$grid->setColProperty("member", array("label"=>"Member", "width"=>125));
$grid->setColProperty("balance", array("label"=>"Balance", "width"=>80, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("ordered", array("label"=>"Ordered", "width"=>80, "align"=>"right", "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("phone", array("label"=>"Phone", "width"=>100));
$grid->setColProperty("mobile", array("label"=>"Mobile", "width"=>100));
$grid->setColProperty("email", array("label"=>"Email", "width"=>120));

$ldevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#nofundlist").getDataIDs(); 
	for(var i=0;i<ids.length;i++){ 
		var cl = ids[i]; 
		be = '<img src="../images/edit.png" title="Edit" onclick="javascript:editmed('+cl+')" >'; 
		se = '<img src="../images/delete.png" title="Delete" onclick="javascript:delmed('+cl+')" >'; 
		jQuery("#nofundlist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp;'+se}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$ldevent);


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// add a custom button via the build in callGridMethod
// note the js: before the function

$buttonoptions1 = array("#nofundpager",
    array("buttonicon"=>"ui-icon-comment","caption"=>"","position"=>"last","title"=>"Email and sms members who have email and mobiles", "onClickButton"=>"js: function(){nfemail();}")
);
$grid->callGridMethod("#nofundlist", "navButtonAdd", $buttonoptions1); 

// Run the script
$grid->renderGrid('#nofundlist','#nofundpager',true, null, null,true,true,true);



?>





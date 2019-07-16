<?php
session_start();
$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$glfile = 'ztmp'.$user_id.'_1gl';

$query = "drop table if exists ".$glfile;
$result = mysql_query($query) or die(mysql_error());

$querygl = "create table ".$glfile." ( ddate date, accountno int(11),branch char(4),sub int(11),accno int(11),br char(4),subbr int(11),debit decimal(16,2),credit decimal(16,2),runbal decimal(16,2), reference varchar(15),descript1 varchar(50)) engine myisam"; 
$calc = mysql_query($querygl) or die($querygl);

$vac = $_SESSION['s_viewac'];
$va = explode('~',$vac);
$ac = $va[0];
$br = trim($va[1]);
$sb = $va[2];

$fromdate = $_SESSION['s_fromdate'];
$todate = $_SESSION['s_todate'];

$brcons = $_SESSION['s_brcons'];
$subcons = $_SESSION['s_subcons'];

$where = " where 1";

// account < 1000, consolidated branches, consolidated sub accounts
if ($brcons == 'y' && $subcons == 'y') {
	$where .= ' and accountno = '.$ac;
}
// account < 1000, consolidated branches, detailed sub accounts
if ($brcons == 'y' && $subcons == 'n') {
	$where .= ' and accountno = '.$ac.' and sub = '.$sb;
}
// account < 1000, detailed branches, consolidated sub accounts
if ($brcons == 'n' && $subcons == 'y') {
	$where .= " and accountno = ".$ac." and branch = '".$br."'";
}
// account < 1000, detailed branches, detailed sub accounts
if ($brcons == 'n' && $subcons == 'n') {
	$where .= " and accountno = ".$ac." and branch = '".$br."' and sub = ".$sb;
}

$where .= " and ddate >= '".$fromdate."' and ddate <= '".$todate."'";
$where1 = $where.' order by ddate';

$q = "select account from glmast where accountno = ".$ac." and branch = '".$br."' and sub = ".$sb;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$row = mysql_fetch_array($r);
extract($row);

$heading = $_SESSION['s_coyname'].' - '.$account." - between '".$fromdate."' and '".$todate."'";

$q = "insert into ".$glfile." select ddate,accountno,branch,sub,accno,br,subbr,debit,credit,0 as runbal,reference,descript1 from trmain ".$where1;
$r = mysql_query($q) or die(mysql_error().' '.$q);

// add uid field
$q = "alter table ".$glfile." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY";
$r = mysql_query($q) or die(mysql_error().' '.$q);

$q = "select uid,debit,credit from ".$glfile;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$rbal = 0;
while ($row = mysql_fetch_array($r)) {
	extract($row);
	$id = $uid;
	$rbal = $rbal + $debit - $credit;
	$qu = "update ".$glfile. " set runbal = ".$rbal." where uid = ".$id;
	$ru = mysql_query($qu) or die(mysql_error().' '.$qu);
}

include 'jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);


// the actual query for the grid data
$grid->SelectCommand = "select ddate,accountno,branch,sub,accno,br,subbr,debit,credit,runbal,reference,descript1 from ".$glfile.$where;

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('get1gl.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>$heading,
    "rowNum"=>20,
    "sortname"=>"ddate",
    "rowList"=>array(20,100,200),
	"height"=>465,
	"width"=>900
    ));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));



// Change some property of the field(s)
$grid->setColProperty("ddate", array("label"=>"Date", "width"=>60, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("accountno", array("label"=>"Account", "width"=>30));
$grid->setColProperty("branch", array("label"=>"Branch", "width"=>25));
$grid->setColProperty("sub", array("label"=>"Sub", "width"=>25));
$grid->setColProperty("accno", array("label"=>"Acc", "width"=>30));
$grid->setColProperty("br", array("label"=>"Br", "width"=>25));
$grid->setColProperty("subbr", array("label"=>"Sb", "width"=>25));
$grid->setColProperty("debit", array("label"=>"Debit", "width"=>40, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("credit", array("label"=>"Credit", "width"=>40, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("runbal", array("label"=>"Balance", "width"=>40, "formatter"=>"number","formatoptions"=>array("thousandsSeparator"=>",","decimalPlaces"=>2)));
$grid->setColProperty("reference", array("label"=>"Ref.", "width"=>25));
$grid->setColProperty("descript1", array("label"=>"Description", "width"=>100));


// At end call footerData to put total  label
$grid->callGridMethod('#gl1list', 'footerData', array("set",array("acc"=>"Balances:")));
// Set which parameter to be sumarized
$summaryrows = array("debit"=>array("debit"=>"SUM"),"credit"=>array("credit"=>"SUM"));


// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

$buttonoptions = array("#gl1pager",
    array("buttonicon"=>"ui-icon-newwin","caption"=>"","position"=>"last","title"=>"Output to Excel.", "onClickButton"=>"js: function(){gl12xl();}")
);
$grid->callGridMethod("#gl1list", "navButtonAdd", $buttonoptions); 

$buttonoptions1 = array("#gl1pager",
    array("buttonicon"=>"ui-icon-print","caption"=>"","position"=>"last","title"=>"Output to PDF.", "onClickButton"=>"js: function(){gl12pdf();}")
);
$grid->callGridMethod("#gl1list", "navButtonAdd", $buttonoptions1); 

$buttonoptions2 = array("#gl1pager",
    array("buttonicon"=>"ui-icon-calculator","caption"=>"","position"=>"last","title"=>"Calculators", "onClickButton"=>"js: function(){showCalculators();}")
);
$grid->callGridMethod("#gl1list", "navButtonAdd", $buttonoptions2); 

// Run the script
$grid->renderGrid('#gl1list','#gl1pager',true, $summaryrows, null,true,true,true);
$conn = null;

?>





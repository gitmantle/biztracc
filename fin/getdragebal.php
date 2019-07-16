<?php
session_start();

$cltdb = $_SESSION['s_cltdb'];
$coyid = $_SESSION['s_coyid'];

include '../clt/jq-config.php';

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);


// the actual query for the grid data
$grid->SelectCommand = "SELECT client_company_xref.uid,client_company_xref.drno,client_company_xref.drsub,concat(members.firstname,' ',members.lastname) as coyname,client_company_xref.current,client_company_xref.d30,client_company_xref.d60,client_company_xref.d90,client_company_xref.d120,(client_company_xref.current+client_company_xref.d30+client_company_xref.d60+client_company_xref.d90+client_company_xref.d120) as bal from ".$cltdb.".client_company_xref,".$cltdb.".members where (client_company_xref.client_id = members.member_id) and (client_company_xref.drno <> 0) and (client_company_xref.company_id = ".$coyid.")";

// set the ouput format to json
$grid->dataType = 'json';

// Let the grid create the model
$grid->setColModel(null);

// Set the url from where we obtain the data
$grid->setUrl('getdragebal.php');


// Set some grid options
$grid->setGridOptions(array(
    "caption"=>"Debtors Aged Balances",
    "rowNum"=>12,
    "sortname"=>"coyname",
    "rowList"=>array(12,50,100,200),
	"height"=>290,
	"width"=>900
    ));

// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));


// Change some property of the field(s)
$grid->setColProperty("uid", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("drno", array("label"=>"Account", "width"=>50));
$grid->setColProperty("drsub", array("label"=>"Sub", "width"=>30));
$grid->setColProperty("coyname", array("label"=>"Debtor", "width"=>150));
$grid->setColProperty("current", array("label"=>"Current", "width"=>50, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("d30", array("label"=>"30 Day", "width"=>50, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("d60", array("label"=>"60 Day", "width"=>50, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("d90", array("label"=>"90 Day", "width"=>50, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("d120", array("label"=>"120 Day +", "width"=>50, "align"=>"right","formatter"=>"number"));
$grid->setColProperty("bal", array("label"=>"Balance", "width"=>50, "align"=>"right","formatter"=>"number"));


// Set which parameter to be sumarized
$summaryrows = array("current"=>array("current"=>"SUM"),"d30"=>array("d30"=>"SUM"),"d60"=>array("d60"=>"SUM"),"d90"=>array("d90"=>"SUM"),"d120"=>array("d120"=>"SUM"));


$buttonoptions = array("#dragedpager",
    array("buttonicon"=>"ui-icon-newwin","caption"=>"","position"=>"last","title"=>"Output to Excel.", "onClickButton"=>"js: function(){draged2xl();}")
);
$grid->callGridMethod("#dragedlist", "navButtonAdd", $buttonoptions); 

$buttonoptions1 = array("#dragedpager",
    array("buttonicon"=>"ui-icon-print","caption"=>"","position"=>"last","title"=>"Output to PDF.", "onClickButton"=>"js: function(){draged2pdf();}")
);
$grid->callGridMethod("#dragedlist", "navButtonAdd", $buttonoptions1); 

$buttonoptions2 = array("#dragedpager",
    array("buttonicon"=>"ui-icon-calculator","caption"=>"","position"=>"last","title"=>"Calculators", "onClickButton"=>"js: function(){showCalculators();}")
);
$grid->callGridMethod("#dragedlist", "navButtonAdd", $buttonoptions2); 

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));


// Run the script
$grid->renderGrid('#dragedlist','#dragedpager',true, $summaryrows, null,true,true,true);
$conn = null;

?>





<?php
session_start();
//ini_set('display_errors', true);
$usersession = $_SESSION['usersession'];

include_once("DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$cltdb = 'infinint_sub'.$subid;

$host = $_SESSION['s_server'];
define('DB_DSN','mysql:host='.$host.';dbname='.$cltdb);
define('DB_USER', 'infinint_sagana');     // Your MySQL username
define('DB_PASSWORD', 'dun480can'); // ...and password

define('ABSPATH', dirname(__FILE__).'/');

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Create the jqGrid instance
$grid = new jqGridRender($conn);

// enable debugging
//$grid->debug = true;



// Write the SQL Query
// We suppose that mytable exists in your database
$grid->SelectCommand = "SELECT link,description from links";

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('../includes/getvlinks.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Links",
    "rowNum"=>12,
    "sortname"=>"description",
    "rowList"=>array(12,30,50),
    "width"=>400,
	"height"=>200
	));

// Change some property of the field(s)
$grid->setColProperty("link", array("label"=>"Link", "width"=>100, "hidden"=>true));
$grid->setColProperty("description", array("label"=>"Links - Double click to open", "width"=>300));



$dclickevent = <<<DBLCLICK
function(rowid) {
	getlink(rowid);
}
DBLCLICK;
$grid->setGridEvent('ondblClickRow',$dclickevent);

// Run the script
$grid->renderGrid('#linkslist','#linkslistpager',true, null, null, true,true,true);



?>




